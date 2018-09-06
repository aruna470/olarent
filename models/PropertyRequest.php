<?php

namespace app\models;


use Yii;
use app\models\Base;
use app\models\Property;
use app\models\NotificationQueue;
use app\models\Notification;
use app\components\Adyen;
use app\models\PaymentPlan;
use app\models\Payment;
use app\modules\api\components\ApiStatusMessages;
use app\components\Mail;
use app\components\StripeApi;

/**
 * This is the model class for table "PropertyRequest".
 *
 * @property integer $id
 * @property integer $propertyId
 * @property string $code
 * @property integer $tenantUserId
 * @property integer $ownerUserId
 * @property integer $status
 * @property string $createdAt
 * @property integer $payDay
 * @property integer $bookingDuration
 * @property integer $payKeyMoneyCc
 * @property Property $property
 * @property User $tenantUser
 */
class PropertyRequest extends Base
{
    // Request statuses
    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';

    // Keymoney pay
    const PAY_KEY_MONEY_CC_YES = 1;
    const PAY_KEY_MONEY_CC_NO = 0;

    // Special error codes
    const CHARGING_FAILED = 1;

    // Adyen charging responses data
    private $chargingResponse;

    public $customErrorCode = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PropertyRequest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // API Create
            [['code', 'tenantUserId', 'ownerUserId', 'payDay', 'bookingDuration', 'payKeyMoneyCc'], 'required',
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
            [['propertyId', 'tenantUserId', 'ownerUserId', 'status', 'payDay', 'bookingDuration'], 'integer',
                'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE]],
            [['code'], 'isExists', 'message' => ApiStatusMessages::RECORD_EXISTS,
                'on' => [self::SCENARIO_API_CREATE]],
            [['payKeyMoneyCc'], 'match', 'pattern' => '/^0|1$/', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['createdAt', 'status'], 'safe'],
            [['payDay'], 'validatePayDay', 'on' => [self::SCENARIO_API_CREATE]],
            [['code'], 'string', 'max' => 11, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'propertyId' => Yii::t('app', 'Property ID'),
            'code' => Yii::t('app', 'Code'),
            'tenantUserId' => Yii::t('app', 'Tenant User ID'),
            'ownerUserId' => Yii::t('app', 'Owner User ID'),
            'status' => Yii::t('app', 'Status'),
            'createdAt' => Yii::t('app', 'Created At'),
            'payDay' => Yii::t('app', 'Pay Day'),
            'bookingDuration' => Yii::t('app', 'Booking Duration'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'propertyId'])
            ->from(Property::tableName() . ' p');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTenantUser()
    {
        return $this->hasOne(User::className(), ['id' => 'tenantUserId'])
            ->from(User::tableName() . ' tu');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerUserId'])
            ->from(User::tableName() . ' ou');
    }

    /**
     * Validate request existence
     */
    public function isExists()
    {
        if ($this->isAlreadyRequested($this->code, $this->tenantUserId)) {
            $this->addError('code', ApiStatusMessages::RECORD_EXISTS);
        }
    }

    /**
     * Validate pay day
     */
    public function validatePayDay()
    {
        if ($this->payDay < 1 || $this->payDay > 30) {
            $this->addError('payDay', ApiStatusMessages::VALIDATION_FAILED);
        }
    }

    /**
     * Check whether user has already made a property request
     * @param string $code Property code
     * @param integer $tenantUserId User id of the tenant
     * @return boolean
     */
    public function isAlreadyRequested($code, $tenantUserId)
    {
        $model = PropertyRequest::find()
            ->andWhere('code = :code', [':code' => $code])
            ->andWhere('status = :status', [':status' => self::STATUS_PENDING])
            ->andWhere('tenantUserId = :tenantUserId', [':tenantUserId' => $tenantUserId])
            ->one();

        if (!empty($model)) {
            return true;
        }

        return false;
    }

    /**
     * Charge initial payment keymoney+rent
     * @param Property $property Property object
     * @param User $tenant Tenant details
     * @param array $initialChargingInfo Initial charging details. Ex whether charge rent now or later depend on date
     * @param string $currency Currency code
     * @return boolean
     */
    public function initialCharge($property, $tenant, $initialChargingInfo, $currency)
    {
        $response = [
            'status' => false,
            'paymentData' => [
                'reference' => null,
                'pspReference' => null,
                'initialCost' => 0,
                'initialCostWithCom' => 0,
                'keyMoneyWithCom' => 0,
                'stripeReference' => null,
                'currency' => $currency
            ]
        ];

        $paymentPlan = new PaymentPlan();
        $paymentPlan = $paymentPlan->getPaymentPlanByUserId($tenant->id);

        if (!empty($paymentPlan)) {
            $payingAmount = 0;
            $initialCost = 0;
            $initialCostWithCom = 0; // Initial cost with commission
            $keyMoneyWithCom = 0; // Keymoney with commission

            if ($initialChargingInfo['chargeNow'] || $this->payKeyMoneyCc == PropertyRequest::PAY_KEY_MONEY_CC_YES) {
                if ($initialChargingInfo['chargeNow']) {
                    Yii::$app->appLog->writeLog('Paying date is equal to current date and charge rental');
                    $initialCost = $property->cost;
                    $comInfo = $property->getFinalPayment($initialCost);
                    $initialCostWithCom = $comInfo['totalAmount'];
                    $payingAmount = $initialCostWithCom;
                }

                if ($this->payKeyMoneyCc == PropertyRequest::PAY_KEY_MONEY_CC_YES) {
                    Yii::$app->appLog->writeLog('Key money charge - Yes');
                    $comInfo = $property->getFinalPayment($property->keyMoney);
                    $keyMoneyWithCom = $comInfo['totalAmount'];
                    $payingAmount += $keyMoneyWithCom;
                } else {
                    Yii::$app->appLog->writeLog('Key money charge - No');
                }

                Yii::$app->appLog->writeLog("Total payment with commission:{$payingAmount}");

                $chargeResponse = $paymentPlan->charge($payingAmount, $tenant, $property, $currency);

                $response = [
                    'status' => $chargeResponse['status'],
                    'paymentData'=>[
                        'reference' => $chargeResponse['reference'],
                        'pspReference' => $chargeResponse['pspReference'],
                        'initialCost' => $initialCost,
                        'initialCostWithCom' => $initialCostWithCom,
                        'keyMoneyWithCom' => $keyMoneyWithCom,
                        'stripeReference' => $chargeResponse['stripeReference'],
                        'currency' => $currency
                    ]
                ];
            } else {
                $response['status'] = true;
                Yii::$app->appLog->writeLog('Key money or rent charging is not necessary.');
            }
        } else {
            Yii::$app->appLog->writeLog('No plan configured.', ['tenantUserId' => $tenant->id]);
        }

        return $response;
    }

    /**
     * Accept property request. Within this function
     *  - Update property request status
     *  - Key money payment
     *  - Update property status
     *  - Send notification email to tenant
     * @return boolean
     */
    public function accept()
    {
        $mail = new Mail();
        $notificationQueue = new NotificationQueue();
        $notification = new Notification();
        $payment = new Payment();
        $paymentPlan = new PaymentPlan();
        $allSuc = false;
        $currency = Yii::$app->params['defCurrency'];

        $property = Property::findOne($this->propertyId);
        $tenant = User::findOne($this->tenantUserId);
        $owner = User::findOne($property->ownerUserId);

        if (!empty($property) && !empty($tenant) && !empty($owner)) {
            if ($this->status == self::STATUS_PENDING) {
                $initialChargingInfo = $this->getInitialChargingInfo($property->chargingCycle, $this->payDay, $tenant->timeZone);
                Yii::$app->appLog->writeLog('Initial charging info;', $initialChargingInfo);
                $chargeResponse = $this->initialCharge($property, $tenant, $initialChargingInfo, $currency);
                if ($chargeResponse['status']) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $this->status = self::STATUS_ACCEPTED;
                    $totalPayment = $chargeResponse['paymentData']['initialCostWithCom'];
                    $initialCost = $chargeResponse['paymentData']['initialCost'];
                    $property->tenantUserId = $tenant->id;
                    $property->status = Property::STATUS_NOT_AVAILABLE;
                    if ($initialCost > 0) {
                        $property->paymentStatus = Property::PS_SUCCESS;
                        $property->lastPaymentDate = Yii::$app->util->getUtcDateTime();
                    } else {
                        $property->paymentStatus = Property::PS_PENDING;
                    }

                    $property->reservedAt = Yii::$app->util->getUtcDateTime();
                    $property->nextChargingDate = $initialChargingInfo['nextChargingDateUtc'];
                    $property->nextChargingAttemptDate = $property->nextChargingDate;
                    $property->currentRentDueAt = $this->getCurrentRentDueAt($this->bookingDuration);
                    $property->reachMaxAttempts = Property::REACH_MAX_ATT_NO;
                    $property->payDay = $this->payDay;
                    if ($property->saveModel()) {

                        $keyMoney = 0;
                        if ($this->payKeyMoneyCc) {
                            $keyMoney = $property->keyMoney;
                            $totalPayment += $chargeResponse['paymentData']['keyMoneyWithCom'];;
                        }

                        // Add Payment details
                        $ccPaymentSaveStatus = true;
                        if ($this->payKeyMoneyCc) {
                            // Key money
                            $commission = ($chargeResponse['paymentData']['keyMoneyWithCom'] - $property->keyMoney);
                            $ccPaymentSaveStatus = $payment->addPayment($tenant->id, $owner->id, $property->id, $property->keyMoney, Payment::TYPE_KEY_MONEY,
                                $commission, $chargeResponse['paymentData']);
                        }

                        // Rent
                        $rentPaymentSaveStatus = true;
                        if ($initialChargingInfo['chargeNow']) {
                            $chargeResponse['paymentData']['paymentForDate'] = $initialChargingInfo['paymentForDate'];
                            $commission = ($chargeResponse['paymentData']['initialCostWithCom'] - $initialCost);
                            $paymentSaveStatus = $payment->addPayment($tenant->id, $owner->id, $property->id, $initialCost, Payment::TYPE_RENTAL,
                                $commission, $chargeResponse['paymentData']);
                        }

                        // Save property request details
                        $propReqSaveStatus = $this->saveModel();

                        if ($ccPaymentSaveStatus && $rentPaymentSaveStatus  && $propReqSaveStatus) {
                            $allSuc = true;
                        }
                    }

                    if ($allSuc) {
                        $transaction->commit();
                        Yii::$app->appLog->writeLog('Commit transaction');

                        // Send Email alert to Tenant
                        $mail->language = $tenant->language;
                        $mail->sendPropAcceptNotificationTenant($tenant->email, $owner->getFullName(),
                            $tenant->getFullName(), $property->code, $chargeResponse['paymentData']['keyMoneyWithCom'],
                            $chargeResponse['paymentData']['initialCostWithCom'], $currency, $property->isOnBhf);

                        // Send Email alert to Owner
                        $mail->language = $owner->language;
                        $mail->sendPropAcceptNotificationOwner($owner->email, $owner->getFullName(),
                            $tenant->getFullName(), $property->code, $chargeResponse['paymentData']['keyMoneyWithCom'],
                            $chargeResponse['paymentData']['initialCostWithCom'], $currency, $property->isOnBhf);

                        // Send notification to other tenants those who request for this property
                        $queueData = ['propertyId' => $this->propertyId];
                        $notificationQueue->addQueue(NotificationQueue::TYPE_ASSIGN_ANOTHER, json_encode($queueData));

                        // Add notification
                        if ($property->isOnBhf) {
                            $notification->addNotification(Notification::TENANT_CRT_PROP_ONBHF, $tenant->id,
                                ['ownerName' => $owner->getFullName(), 'code' => $property->code]);
                        } else {
                            $notification->addNotification(Notification::OWNER_ACPT_PRP_REQ, $tenant->id,
                                ['ownerName' => $owner->getFullName(), 'code' => $property->code]);

                            $notification->addNotification(Notification::OWNER_ACPT_PRP_REQ_OWNER, $owner->id,
                                ['tenantName' => $tenant->getFullName(), 'code' => $property->code]);
                        }
                    } else {
                        $transaction->rollBack();
                        Yii::$app->appLog->writeLog('Rollback transaction');
                        $paymentPlan->refund($tenant->id, $totalPayment, $chargeResponse['paymentData']['pspReference'],
                            $chargeResponse['paymentData']['stripeReference'], $currency);
                    }
                } else {
                    $this->customErrorCode = self::CHARGING_FAILED;
                    Yii::$app->appLog->writeLog('Charging failed.');
                }
            } else {
                Yii::$app->appLog->writeLog('Property not in pending state');
            }
        } else {
            Yii::$app->appLog->writeLog('Tenant or Property not found');
        }

        return $allSuc;
    }

    /**
     * Calculate first next charging date based on pay day
     * @param integer $chargingCycle Charging cycle
     * @param integer $payDay Paying day of the month
     * @param string $userTz Timezone of the user. Expecting valid php timezone string
     * @return string
     */
    public function getInitialChargingInfo($chargingCycle, $payDay, $userTz)
    {
        $nextChargingDateUtc = null;
        $chargeNow = false;
        $paymentForDate = null;
        $property = new Property();

        switch ($chargingCycle) {
            case Property::CS_MONTHLY:
                // Convert current time to local time of particular user, based on his timezone.
                $curDateTimeLocal = Yii::$app->util->getTzSpecificDateTime(date('Y-m-d H:i:s'), $userTz,
                    Yii::$app->params['phpIniTimeZone']);
                $curDateTimeLocalTs = strtotime($curDateTimeLocal);
                $curDay = (int)date('j', $curDateTimeLocalTs);
                $curMonth = date('m', $curDateTimeLocalTs);
                $year = date('Y', $curDateTimeLocalTs);
                $curYear = date('Y', $curDateTimeLocalTs);

                Yii::$app->appLog->writeLog('Days', ['current' => $curDay, 'payDay' => $payDay]);

                if ($payDay < $curDay || $payDay == $curDay) {
                    // Paying date is less than current(accepting) date
                    $nextMonth = $curMonth + 1;
                    if ($nextMonth > 12) {
                        $nextMonth = 1;
                        $year = $year + 1;
                    }
                    $pDay = $property->getPayDay($payDay, $year, $nextMonth) - 1; // Since strtotime("{$year}-{$nextMonth}") is equivalent to 1st day of month
                    $nextChargingDateLocal = date('Y-m-d', strtotime("+{$pDay} days", strtotime("{$year}-{$nextMonth}")));
                    $nextChargingDateUtc = Yii::$app->util->getTzSpecificDateTime($nextChargingDateLocal, 'UTC', $userTz);
                    if ($payDay == $curDay) {
                        $chargeNow = true;
                        $paymentForDate = "{$curYear}-{$curMonth}-{$curDay}";
                    }
                } else if ($payDay > $curDay) {
                    // Paying date is greater than current(accepting) date
                    $pDay = $property->getPayDay($payDay, $year, $curMonth) - 1; // Since strtotime("{$year}-{$nextMonth}") is equivalent to 1st day of month
                    $nextChargingDateLocal = date('Y-m-d', strtotime("+{$pDay} days", strtotime("{$year}-{$curMonth}")));
                    $nextChargingDateUtc = Yii::$app->util->getTzSpecificDateTime($nextChargingDateLocal, 'UTC', $userTz);
                }

                break;
        }

        $nextChargingInfo = ['nextChargingDateUtc' => $nextChargingDateUtc, 'chargeNow' => $chargeNow, 'paymentForDate' => $paymentForDate];

        return $nextChargingInfo;
    }

    /**
     * Retrieve current rent due date
     * @param integer $duration Reserved duration in months
     * @return string Date
     */
    public function getCurrentRentDueAt($duration)
    {
        return gmdate('Y-m-d', strtotime("+{$duration} months", strtotime(Yii::$app->util->getUtcDateTime())));
    }

    /**
     * Reject property request. Within this function
     *  - Update property request status
     *  - Send notification email to tenant
     * @return boolean
     */
    public function reject()
    {
        $mail = new Mail();
        $allSuc = false;
        $notification = new Notification();

        if ($this->status == self::STATUS_PENDING) {
            $this->status = self::STATUS_REJECTED;
            if ($this->saveModel()) {
                $property = Property::findOne($this->propertyId);
                $tenant = User::findOne($this->tenantUserId);
                $owner = User::findOne($property->ownerUserId);
                $mail->language = $tenant->language;
                $mail->sendPropRejectNotificationTenant($tenant->email, $owner->getFullName(),
                    $tenant->getFullName(), $property->code);

                // Add notification
                $notification->addNotification(Notification::OWNER_REJ_PROP_REQ, $tenant->id,
                    ['ownerName' => $owner->getFullName(), 'code' => $property->code]);

                $allSuc = true;
            }
        } else {
            Yii::$app->appLog->writeLog('Not in pending state');
        }

        return $allSuc;
    }

    /**
     * Get pending property requests
     * @param integer $propertyId Id of the property
     * @return mixed
     */
    public function getPendingPropertyRequests($propertyId)
    {
        $query = PropertyRequest::find();
        $query->andFilterWhere([self::tableName() . '.status' => self::STATUS_PENDING]);
        $query->andFilterWhere(['propertyId' => $propertyId]);
        $query->joinWith(['ownerUser', 'tenantUser', 'property']);

        return $query->all();
    }

    /**
     * Calculate the initial charge when accepting the property
     * Intial charge done only for remaining days of the month
     * @param float $cost Property cost
     *
     * @return mixed
     */
    private function getInitialCharge($cost)
    {
        $timestamp = strtotime(gmdate('Y-m-d'));
        $daysRemaining = (int)date('t', $timestamp) - (int)date('j', $timestamp);

        $costPerDay = $cost/30;
        $costForRemainingDays = $costPerDay * $daysRemaining;

        return number_format((float)$costForRemainingDays, 2, '.', '');
    }
}
