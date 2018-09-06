<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\components\Sms;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "Verification".
 *
 * @property integer $id
 * @property integer $verificationCode
 * @property string $phoneNumber
 */
class Verification extends Base
{
    const SCENARIO_API_CREATE = 'apiCreate';
    const SCENARIO_API_VERIFY = 'apiVerify';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Verification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            // API common
            [['phoneNumber'], 'string', 'max' => 20, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_VERIFY]],
            [['phoneNumber'], 'match', 'pattern' => '/^\+\d+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_VERIFY]],
            [['verificationCode'], 'integer'],

            // API - Send verification code/Verify code
            [['verificationCode', 'phoneNumber'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_VERIFY]],
            [['phoneNumber'], 'isValidCountryCode', 'message' => ApiStatusMessages::INVALID_PHONE_NUMBER,
                'on' => [self::SCENARIO_API_CREATE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'verificationCode' => Yii::t('app', 'Verification Code'),
            'phoneNumber' => Yii::t('app', 'Phone Number'),
        ];
    }

    /**
     * Retrieve model by phone number
     * @param string $phoneNumber
     * @return mixed
     */
    public function getModelByPhone($phoneNumber)
    {
        $model = Verification::find()->where(['phoneNumber' => $phoneNumber])->one();
        return $model;
    }

    /**
     * Send verification SMS
     */
    public function sendVerificationSms()
    {
        $status = false;
        $sms = new Sms(Yii::$app->params);
        $this->verificationCode = rand(1000,9999);
        if ($this->validateModel()) {
            if ($this->saveModel()) {
                if ($sms->sendNumberVerifySms($this->phoneNumber, $this->verificationCode)) {
                    Yii::$app->appLog->writeLog('SMS verification code sent.', ['code' => $this->verificationCode]);
                    $status = true;
                }
            }
        }

        return  $status;
    }

    /**
     * Check whether country code is allowed
     */
    public function isValidCountryCode()
    {
        $isValid = false;
        $codes = Yii::$app->params['countryCodes'];

        foreach ($codes as $name => $code) {
            if (substr($this->phoneNumber, 0, strlen($code)) == $code) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $this->addError('phoneNumber', ApiStatusMessages::INVALID_PHONE_NUMBER);
        }
    }
}
