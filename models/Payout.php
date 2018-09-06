<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\models\Payment;
use app\models\UserMpInfo;
use app\components\Mail;
use app\components\Mp;


/**
 * This is the model class for table "Payout".
 *
 * @property integer $id
 * @property integer $paymentId
 * @property string $mpTransferId
 * @property string $mpTransferStatus
 * @property integer $userId
 * @property string $mpPayoutId
 * @property string $mpPayoutStatus
 * @property string $createdAt
 * @property string $mpBankAccountId
 * @property integer $mpPayoutExecutionDate
 * @property integer $retryCount
 * @property integer $eligibilityStatus
 * @property string $mpTransferMessage
 * @property string $mpPayoutMessage
 * @property integer $maxRetry
 */
class Payout extends Base
{
    // Eligibility statuses
    const ES_SUCCESS = 1;
    const ES_NO_COMPANY_WALLET = 2;
    const ES_NO_BANK_DETAILS = 3;

    public $propertyCode;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Payout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paymentId', 'userId', 'createdAt'], 'required'],
            [['paymentId', 'userId', 'mpPayoutExecutionDate', 'retryCount'], 'integer'],
            [['createdAt'], 'safe'],
            [['mpTransferId', 'mpPayoutId', 'mpBankAccountId'], 'string', 'max' => 10],
            [['mpTransferStatus', 'mpPayoutStatus'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'paymentId' => Yii::t('app', 'Payment ID'),
            'mpTransferId' => Yii::t('app', 'Mp Transfer ID'),
            'mpTransferStatus' => Yii::t('app', 'Mp Transfer Status'),
            'userId' => Yii::t('app', 'Owner Name'),
            'mpPayoutId' => Yii::t('app', 'Mp Payout ID'),
            'mpPayoutStatus' => Yii::t('app', 'Mp Payout Status'),
            'createdAt' => Yii::t('app', 'Created At'),
            'mpBankAccountId' => Yii::t('app', 'Mp Bank Account ID'),
            'mpPayoutExecutionDate' => Yii::t('app', 'Mp Payout Execution Date'),
            'retryCount' => Yii::t('app', 'Retry Count'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMpInfo()
    {
        return $this->hasOne(UserMpInfo::className(), ['userId' => 'userId']);
    }

    /**
     * Try initial payout from company wallet to owner's bank account
     * @param Payment $payment Payment object
     * @return array
     */
    public function initPayOut($payment)
    {
        $mail = new Mail();
        $companyWallet = CompanyWallet::find()->where([])->one();
        $owner = User::findOne($payment->payeeUserId);

        $userMpInfo = new UserMpInfo();
        $userMpInfo = $userMpInfo->getUserMpInfo($payment->payeeUserId);

        $payout = new Payout();
        $payout->paymentId = $payment->id;
        $payout->userId = $payment->payeeUserId;
        $payout->createdAt = Yii::$app->util->getUtcDateTime();
        $payout->retryCount = 1;
        $payout->maxRetry = Yii::$app->params['maxRetryPayout'];

        if (!empty($payment) && !empty($companyWallet) && !empty($userMpInfo)) {
            $payout->eligibilityStatus = self::ES_SUCCESS;
            $mp = new Mp(Yii::$app->params['mangoPay']);
            $resTransfer = $mp->transfer($companyWallet->mpUserId, $userMpInfo->mpUserId, ($payment->amount + $payment->commssion),
                $payment->currencyType, $payment->commssion, $companyWallet->mpWalletId, $userMpInfo->mpWalletId);
            if (isset($resTransfer->Id)) {
                $payout->mpTransferMessage = $resTransfer->ResultMessage;
                $resPayOut = $mp->payOut($userMpInfo->mpUserId, $userMpInfo->mpWalletId, $payment->amount,
                    $payment->currencyType, 0, $userMpInfo->mpBankAccountId, $payment->property->code);

                $payout->mpTransferId = $resTransfer->Id;
                $payout->mpTransferStatus = $resTransfer->Status;

                if (isset($resPayOut->Id)) {
                    $payout->mpPayoutId = $resPayOut->Id;
                    $payout->mpPayoutStatus = $resPayOut->Status;
                    $payout->mpBankAccountId = $userMpInfo->mpBankAccountId;
                    $payout->mpPayoutExecutionDate = $resPayOut->ExecutionDate;
                    $payout->mpPayoutMessage = $resPayOut->ResultMessage;
                }
            }
        } else {
            $this->log->writeLog('Payment record or company wallet or user`s MangoPay account not found',
                ['paymentId' => $payment->id, 'userId' => $payment->payeeUserId]);

            if (empty($companyWallet)) {
                $payout->eligibilityStatus = self::ES_NO_COMPANY_WALLET;
            } else if (empty($userMpInfo)) {
                $payout->eligibilityStatus = self::ES_NO_BANK_DETAILS;
            }

            if (empty($userMpInfo)) {
                $mail->language = $owner->language;
                $mail->noMpAccount($owner->email, $owner->firstName);
            }
        }

        return $payout->saveModel();
    }

    /**
     * Get failed payout to be processed.
     * @param integer $pageNo Pagination number
     * @param integer $limit Number of records
     * @return mixed $results
     */
    public function getFailedPayouts($pageNo, $limit = 50)
    {
        $tableName = self::tableName();
        $results = Payout::find()
            ->where("retryCount < maxRetry
                AND (
                       {$tableName}.mpTransferStatus = :transferStatus
                    OR {$tableName}.mpTransferStatus IS NULL
                    OR {$tableName}.mpPayoutStatus = :payoutStatus
                    OR {$tableName}.eligibilityStatus != :eligibilityStatus
                )",
                ['transferStatus' => Mp::TR_FAILED, 'payoutStatus' =>  Mp::PO_FAILED,
                    'eligibilityStatus' => self::ES_SUCCESS]
            )
            ->offset($pageNo * $limit)
            ->limit($limit)
            ->all();

        return $results;
    }

    /**
     * Retry payout
     * @param Payout $payout Payout object
     * @return mixed $results
     */
    public function retryPayout($payout)
    {
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $mail = new Mail();
        $companyWallet = CompanyWallet::find()->where([])->one();
        $owner = User::findOne($payout->userId);
        $payment = Payment::find()->where([Payment::tableName() . '.id' => $payout->paymentId])->joinWith('property')->one();

        $userMpInfo = new UserMpInfo();
        $userMpInfo = $userMpInfo->getUserMpInfo($payout->userId);

        $payout->retryCount += 1;

        if (!empty($companyWallet) && !empty($userMpInfo)) {
            $payout->eligibilityStatus = self::ES_SUCCESS;
            if ($payout->mpTransferStatus == Mp::TR_FAILED || $payout->mpTransferStatus == null) {
                // If transfer failed then need to re execute both transfer and payout
                $resTransfer = $mp->transfer($companyWallet->mpUserId, $userMpInfo->mpUserId, ($payment->amount + $payment->commssion),
                    $payment->currencyType, $payment->commssion, $companyWallet->mpWalletId, $userMpInfo->mpWalletId);

                if (isset($resTransfer->Id)) {
                    $payout->mpTransferMessage = $resTransfer->ResultMessage;
                    $resPayOut = $mp->payOut($userMpInfo->mpUserId, $userMpInfo->mpWalletId, $payment->amount,
                        $payment->currencyType, 0, $userMpInfo->mpBankAccountId, $payment->property->code);

                    $payout->mpTransferId = $resTransfer->Id;
                    $payout->mpTransferStatus = $resTransfer->Status;

                    if (isset($resPayOut->Id)) {
                        $payout->mpPayoutId = $resPayOut->Id;
                        $payout->mpPayoutStatus = $resPayOut->Status;
                        $payout->mpBankAccountId = $userMpInfo->mpBankAccountId;
                        $payout->mpPayoutExecutionDate = $resPayOut->ExecutionDate;
                        $payout->mpPayoutMessage = $resPayOut->ResultMessage;
                    }
                }
            } else if($payout->mpPayoutStatus == Mp::PO_FAILED) {
                // Only payout is failed and try to re execute it
                $resPayOut = $mp->payOut($userMpInfo->mpUserId, $userMpInfo->mpWalletId, $payment->amount,
                    $payment->currencyType, 0, $userMpInfo->mpBankAccountId, $payment->property->code);

                if (isset($resPayOut->Id)) {
                    $payout->mpPayoutId = $resPayOut->Id;
                    $payout->mpPayoutStatus = $resPayOut->Status;
                    $payout->mpBankAccountId = $userMpInfo->mpBankAccountId;
                    $payout->mpPayoutExecutionDate = $resPayOut->ExecutionDate;
                    $payout->mpPayoutMessage = $resPayOut->ResultMessage;
                }
            }
        } else {
            $this->log->writeLog('Company wallet or user`s MangoPay account not found',
                ['userId' => $payout->userId]);

            if (empty($companyWallet)) {
                $payout->eligibilityStatus = self::ES_NO_COMPANY_WALLET;
            } else if (empty($userMpInfo)) {
                $payout->eligibilityStatus = self::ES_NO_BANK_DETAILS;
            }

            if (empty($userMpInfo)) {
                $mail->language = $owner->language;
                $mail->noMpAccount($owner->email, $owner->firstName);
            }
        }

        return $payout->saveModel();
    }

    /**
     * Update payout callback status
     * @param string $mpPayoutId Payout resource id
     * @param string $eventType Event type
     * @return array
     */
    public function updatePayoutStatus($mpPayoutId, $eventType)
    {
        $model = Payout::find()
            ->andWhere(['mpPayoutId' => $mpPayoutId])
            ->joinWith(['payment', 'user', 'payment.property', 'userMpInfo'])
            ->one();

        $mail = new Mail();

        if (!empty($model)) {
            switch ($eventType) {
                case "PAYOUT_NORMAL_SUCCEEDED":
                    $model->mpPayoutStatus = Mp::PO_SUCCEEDED;
                    $mail->payoutSuccess($model->user->email, $model->user->firstName,
                        $model->payment->amount, $model->payment->currencyType,
                        $model->payment->property->code, $model->userMpInfo->iban);
                    break;
                case "PAYOUT_NORMAL_FAILED":
                    $model->mpPayoutStatus = Mp::PO_FAILED;
                    $mail->payoutFail($model->user->email, $model->user->firstName,
                        $model->payment->amount, $model->payment->currencyType,
                        $model->payment->property->code, $model->userMpInfo->iban);
                    break;
            }
            $model->saveModel();
        }
    }

    /**
     * Retrieve transfer status reason to display
     * @return string
     */
    public function getTransferDescription()
    {
        $message = '';
        switch ($this->mpTransferStatus) {
            case Mp::TR_SUCCEEDED:
                $message = Yii::t('app', 'Funds successfully transferred from company wallet to owner\'s wallet');
                break;

            case Mp::TR_CREATED:
                $message = Yii::t('app', 'Funds transfer from company wallet to owner\'s wallet was created and waiting for execution');
                break;

            default:
                $message = $this->mpTransferMessage;
                break;
        }

        return $message;
    }

    /**
     * Retrieve payout status reason to display
     * @return string
     */
    public function getPayoutDescription()
    {
        $message = '';
        switch ($this->mpPayoutStatus) {
            case Mp::PO_SUCCEEDED:
                $message = Yii::t('app', 'Funds successfully transferred from owner\'s wallet to his bank account');
                break;

            case Mp::PO_CREATED:
                $message = Yii::t('app', 'Funds transfer from owner\'s wallet to his bank account was created and waiting for execution');
                break;

            default:
                $message = $this->mpPayoutMessage;
                break;
        }

        return $message;
    }

    /**
     * Get eligibility status
     * @return string
     */
    public function getEligibilityDescription()
    {
        $message = '';
        switch ($this->eligibilityStatus) {
            case self::ES_NO_BANK_DETAILS:
                $message = Yii::t('app', 'Owner has not provided bank account details.');
                break;

            case self::ES_NO_COMPANY_WALLET:
                $message = Yii::t('app', 'Company wallet has not configured yet.');
                break;

            case self::ES_SUCCESS:
                $message = Yii::t('app', 'Company wallet and Owner\'s bank account details are available to go ahead with payout.');
                break;
        }

        return $message;
    }
}
