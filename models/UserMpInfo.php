<?php

namespace app\models;

use app\components\Mp;
use Yii;
use app\models\Base;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "UserMpInfo".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $mpUserId
 * @property string $mpWalletId
 * @property string $mpBankAccountId
 * @property string $address
 * @property string $nationality
 * @property string $countryOfResidence
 * @property integer $incomeRange
 * @property string $occupation
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $birthDate
 * @property string $iban
 * @property string $city
 * @property string $postalCode
 *
 * @property User $user
 */
class UserMpInfo extends Base
{
    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';
    const SCENARIO_API_UPDATE = 'apiUpdate';

    // MangoPay error field
    public $mpErrorField = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserMpInfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            // Common
            [['userId', 'incomeRange'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['address'], 'string', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['createdAt', 'updatedAt'], 'safe', 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['mpUserId', 'mpWalletId', 'mpBankAccountId', 'postalCode'], 'string', 'max' => 10, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['nationality', 'countryOfResidence'], 'string', 'max' => 3, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['occupation', 'city'], 'string', 'max' => 60, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['postalCode'], 'match', 'pattern' => '/^[a-zA-Z0-9]+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['iban'], 'match', 'pattern' => '/^[a-zA-Z]{2}\d{2}\s*(\w{4}\s*){2,7}\w{1,4}\s*$/', 'message' => ApiStatusMessages::INVALID_IBAN,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],

            // API Create/Update
            [['userId', 'address', 'nationality', 'countryOfResidence', 'email', 'firstName', 'lastName', 'birthDate',
                'incomeRange', 'occupation', 'createdAt', 'iban', 'city', 'postalCode'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'mpUserId' => Yii::t('app', 'MP User ID'),
            'mpWalletId' => Yii::t('app', 'MP Wallet ID'),
            'mpBankAccountId' => Yii::t('app', 'MP Bank Account ID'),
            'address' => Yii::t('app', 'Address'),
            'nationality' => Yii::t('app', 'Nationality'),
            'countryOfResidence' => Yii::t('app', 'Country Of Residence'),
            'incomeRange' => Yii::t('app', 'Income Range'),
            'occupation' => Yii::t('app', 'Occupation'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * Create necessary MangoPay accounts(User, Bank & Wallet) and locally save details
     * @param string $currency Currency format for the wallet
     * @return boolean
     */
    public function createMpAccounts($currency)
    {
        $status = false;

        $mp = new Mp(Yii::$app->params['mangoPay']);
        $resNatUser = $mp->createNaturalUser($this->attributes);
        if (isset($resNatUser->Id)) {
            $resWallet = $mp->createWallet($resNatUser->Id, $currency, 'Wallet for ' . $this->firstName);
            if (isset($resWallet->Id)) {
                $resBankAcc = $mp->createBankAccount($this->firstName . ' ' . $this->lastName, $resNatUser->Id,
                    $this->address, $this->iban, $this->city, $this->countryOfResidence, $this->postalCode);
                if (isset($resBankAcc->Id)) {
                    $this->mpUserId = $resNatUser->Id;
                    $this->mpWalletId = $resWallet->Id;
                    $this->mpBankAccountId = $resBankAcc->Id;
                    $status = $this->saveModel();
                } else {
                    $this->mpErrorField = $mp->getErrorField();
                }
            }
        }

        return $status;
    }

    /**
     * Update necessary MangoPay accounts(User, Bank & Wallet) and locally save details
     * @params array $oldAttributes Attributes before update. Use for rollback in case of failure.
     * @return boolean
     */
    public function updateMpAccounts($oldAttributes)
    {
        $status = false;
        $rbBa = false;
        $rbNatUser = false;
        $baCreated = false;

        $mp = new Mp(Yii::$app->params['mangoPay']);

        // Update MP natural user details
        $resNatUser = $mp->updateNaturalUser($this->attributes);
        if (isset($resNatUser->Id)) {
            $rbNatUser = true;
            if ($this->anyChanged($oldAttributes, $this->attributes, ['firstName', 'lastName', 'address',
                'iban', 'city', 'countryOfResidence', 'postalCode'])) {
                $this->log->writeLog('Bank account details are changed.');
                // Create new bank account with these details. There is no update method
                $resBankAcc = $mp->createBankAccount($this->firstName . ' ' . $this->lastName, $resNatUser->Id,
                    $this->address, $this->iban, $this->city, $this->countryOfResidence, $this->postalCode);

                if (isset($resBankAcc->Id)) {
                    $baCreated = true;
                    $rbBa = true;
                    $this->mpBankAccountId = $resBankAcc->Id;
                } else {
                    $this->mpErrorField = $mp->getErrorField();
                }
            } else {
                $this->log->writeLog('Bank account details not changed.');
                $baCreated = true;
            }

            if ($baCreated) {
                if ($this->saveModel()) {
                    $status = true;
                    //TODO: disactive existing bank account - But no method implemented in MP SDK
                }
            }

            if (!$status && $rbBa) {
                $this->log->writeLog('Rollback MP bank account.');
                //TODO: disactavate newly created bank account - But no method implemented in MP SDK
            }

            if (!$status && $rbNatUser) {
                $this->log->writeLog('Rollback MP natural user.');
                $resNatUser = $mp->updateNaturalUser($oldAttributes);
                $this->attributes = $oldAttributes;
                $this->saveModel();
            }
        }

        return $status;
    }

    /**
     * Check whether any attribute get changed in order to create new bank account
     * @params array $oldAttributes Attributes before update. Use for rollback in case of failure.
     * @params array $newAttrib New attributes.
     * @params array $attribList Attributes to be checked.
     * @return boolean
     */
    public function anyChanged($oldAttrib, $newAttrib, $attribList)
    {
        $anyChaged = false;
        foreach ($attribList as $attrib) {
            if ($oldAttrib[$attrib] != $newAttrib[$attrib]) {
                $anyChaged = true;
                break;
            }
        }

        return $anyChaged;
    }


    /**
     * Retrieve model by userId
     * @param string $userId UserId
     * @return boolean
     */
    public function getUserMpInfo($userId)
    {
        return self::find()->where(['userId' => $userId])->one();
    }

    /**
     * Retrieve model by id
     * @param string $id Object id
     * @return boolean
     */
    public function getUserMpInfoById($id)
    {
        return self::find()->where(['id' => $id])->one();
    }
}
