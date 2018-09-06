<?php

namespace app\models;

use app\components\AppLogger;
use app\components\Mail;
use Yii;
use yii\helpers\Html;
use app\models\Base;
use app\models\PaymentPlan;
use app\components\Aws;
use app\components\Adyen;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "Property".
 *
 * @property integer $id
 * @property integer $ownerUserId
 * @property integer $tenantUserId
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $geoLocation
 * @property double $cost
 * @property integer $status
 * @property string $imageName
 * @property string $thumbImageName
 * @property string $currentRentDueAt
 * @property integer $paymentStatus
 * @property string $zipCode
 * @property integer $noOfRooms
 * @property double $size
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $createdById
 * @property integer $updatedById
 * @property string $reservedAt
 * @property string $nextChargingDate
 * @property integer $chargingCycle
 * @property User $ownerUser
 * @property User $tenantUser
 * @property double $keyMoney
 * @property string $city
 * @property integer $payDay
 * @property integer $chargingAttemptCount
 * @property string $nextChargingAttemptDate
 * @property string $lastPaymentDate
 * @property integer $reachMaxAttempts
 * @property integer $commissionPlan
 * @property integer $images
 */
class Property extends Base
{
    // Charging cycle
    const CS_MONTHLY = 1;

    // Property availability statuses
    const STATUS_AVAILABLE = 1;
    const STATUS_NOT_AVAILABLE = 2;

    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';
    const SCENARIO_API_UPDATE = 'apiUpdate';
    const SCENARIO_API_PAYNOW = 'apiPayNow';
    const SCENARIO_API_ON_BH_CREATE = 'apiOnBehalfCreate';

    // Last payment statuses
    const PS_PENDING = 0;
    const PS_SUCCESS = 1;
    const PS_FAILED = 2;

    // Last payment statuses for payment section.
    // This is not maintained in the db but uses when sending API responses
    const LPS_SUCCESS = 1;
    const LPS_FAILED = 2;
    const LPS_PENDING = 3;
    const LPS_NOT_RENTED = 4;

    const EDITABLE_YES = 1;
    const EDITABLE_NO = 0;

    // Whether recurring charge reached max attempts
    const REACH_MAX_ATT_YES = 2;
    const REACH_MAX_ATT_NO = 1;

    // Enable/Disable paynow
    const ENB_PAY_NOW = 1;
    const DISB_PAY_NOW = 0;

    // Commission plans
    const CP_RENTER = 1;   // Renter pays the commission
    const CP_OWNER = 2;    // Owner pays the commission
    const CP_SPLIT = 3;    // Split commission among both

    // ON Behalf of
    const ON_BEHALF_YES = 1; // Property created on behalf of owner
    const ON_BEHALF_NO = 0;

    public $paymentStatuses;
    public $payKeyMoney;

    public function init()
    {
        $this->paymentStatuses = [
            self::PS_PENDING => Yii::t('app', 'Pending'),
            self::PS_SUCCESS => Yii::t('app', 'Success'),
            self::PS_FAILED => Yii::t('app', 'Failed'),
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currentRentDueAt', 'createdAt', 'updatedAt', 'reservedAt', 'nextChargingDate'], 'safe'],

            // API Create
            [['ownerUserId', 'name', 'address', 'cost', 'createdAt', 'createdById', 'keyMoney', 'city',/*, 'commissionPlan'*/], 'required',
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
            [['ownerUserId', 'tenantUserId', 'status', 'paymentStatus', 'createdById', 'updatedById', 'chargingCycle',
                'paymentStatus', 'noOfRooms'/*, 'commissionPlan'*/], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['size'], 'number', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['cost', 'keyMoney'], 'number', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE ,
                self::SCENARIO_API_UPDATE]],
            [['code', 'zipCode'], 'string', 'max' => 11, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['city'], 'string', 'max' => 30, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['geoLocation', 'imageName'], 'string', 'max' => 30,
                'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['name'], 'string', 'max' => 150, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['thumbImageName'], 'string', 'max' => 40,
                'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['address'], 'string', 'max' => 90, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['description', 'images'], 'safe', 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            /*[['commissionPlan'], 'in', 'range' => [self::CP_OWNER, self::CP_RENTER, self::CP_SPLIT], 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],*/

            // API on behalf create
            [['cost', 'createdAt', 'createdById', 'payDay', 'duration', 'payKeyMoney'], 'required',
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_ON_BH_CREATE]],
            [['cost', 'keyMoney', 'payDay', 'duration', 'payKeyMoney'], 'number', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_ON_BH_CREATE]],
            [['description'], 'safe', 'on' => [self::SCENARIO_API_ON_BH_CREATE]],

            // API Pay Now
            [['id'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_PAYNOW]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ownerUserId' => Yii::t('app', 'Owner'),
            'tenantUserId' => Yii::t('app', 'Tenant'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'address' => Yii::t('app', 'Address'),
            'geoLocation' => Yii::t('app', 'Geo Location'),
            'cost' => Yii::t('app', 'Cost ({currency})', ['currency' => Yii::$app->params['defCurrency']]),
            'keyMoney' => Yii::t('app', 'Key Money ({currency})', ['currency' => Yii::$app->params['defCurrency']]),
            'status' => Yii::t('app', 'Status'),
            'imageName' => Yii::t('app', 'Image Name'),
            'currentRentDueAt' => Yii::t('app', 'Current Rent Due At'),
            'paymentStatus' => Yii::t('app', 'Payment Status'),
            'zipCode' => Yii::t('app', 'Zip Code'),
            'noOfRooms' => Yii::t('app', 'No Of Rooms'),
            'size' => Yii::t('app', 'Size (sq.ft)'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'createdById' => Yii::t('app', 'Created By ID'),
            'updatedById' => Yii::t('app', 'Updated By ID'),
            'reservedAt' => Yii::t('app', 'Reserved At'),
            'nextChargingDate' => Yii::t('app', 'Next Charging Date'),
            'chargingCycle' => Yii::t('app', 'Charging Cycle'),
            'city' => Yii::t('app', 'City'),
            'payDay' => Yii::t('app', 'Payment Date of the Month'),
            'isOnBhf' => Yii::t('app', 'Is On Behalf'),
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getTenantUser()
    {
        return $this->hasOne(User::className(), ['id' => 'tenantUserId'])
            ->from(User::tableName() . ' tu');
    }

    /**
     * Generate a property code
     * @return string $code Code for property
     */
    public function generatePropertyCode()
    {
        do {
            $code = strtoupper(substr(md5(microtime()), rand(0, 26), 6));
            $model = Property::find()->where('code = :code', [':code' => $code])->one();
        } while(!empty($model));

        return $code;
    }

    /**
     * Return property status options
     */
    public function getStatusList()
    {
        return [
            self::STATUS_AVAILABLE => Yii::t('app', 'Available'),
            self::STATUS_NOT_AVAILABLE => Yii::t('app', 'Not Available')
        ];
    }

    /**
     * Property picture
     * @param integer $width Image width
     * @param integer $height Image height
     * @return string picture URL
     */
    public function getPropertyImg($width = 40, $height = 40)
    {
        $propImgUrl = '';

        if (!empty($this->imageName)) {
            $aws = new Aws();
            $propImgUrl = $aws->s3GetObjectUrl($this->imageName, false);
        } else {
            $propImgUrl = '';
        }

        if (!empty($propImgUrl)) {
            return Html::img($propImgUrl, ['width' => $width, 'height' => $height]);
        }

        return '';
    }

    /**
     * Property picture
     * @param integer $page Page number
     * @return mixed Matching property list
     */
    public function getPropertiesToBeCharged($page)
    {
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $curDateTime = Yii::$app->util->getUtcDateTime();

        $properties = Property::find()
            ->andWhere('nextChargingAttemptDate <= :nextChargingAttemptDate', [':nextChargingAttemptDate' => $curDateTime])
            ->andWhere('chargingAttemptCount < :maxAttempts', [':maxAttempts' => Yii::$app->params['maxChargingAttempts']])
            ->andWhere('reachMaxAttempts = :reachMaxAttempts', [':reachMaxAttempts' => Property::REACH_MAX_ATT_NO])
            ->joinWith(['ownerUser', 'tenantUser'])
            ->limit($limit)
            ->offset($offset)
            ->all();

        return $properties;
    }

    /**
     * Monthly charging for property
     * @param mixed $property Property object along with Tenant/Owner relations
     */
    public function doRecurringCharge($property)
    {
        $chargingStatus = false;
        $paymentPlan = new PaymentPlan();
        $payment = new Payment();
        $currency = Yii::$app->params['defCurrency'];

        $paymentPlan = $paymentPlan->getPaymentPlanByUserId($property->tenantUserId);

        if (!empty($paymentPlan)) {
            $comInfo = $property->getFinalPayment($property->cost);
            $chargeResponse = $paymentPlan->charge($comInfo['totalAmount'], $property->tenantUser, $property, $currency);

            if ($chargeResponse['status']) {
                // Get next charging date
                $nextChargingDate = $this->getNextChargingDate($property->nextChargingDate, $property->payDay,
                    Property::CS_MONTHLY, $property->tenantUser->timeZone);
                Yii::$app->appLog->writeLog("Charging success", ['nextChargingDateUTC' => $nextChargingDate]);

                $property->paymentStatus = Property::PS_SUCCESS;
                $paymentForDate = $property->nextChargingDate;
                $property->nextChargingDate = $nextChargingDate;
                $property->nextChargingAttemptDate = $nextChargingDate;
                $property->chargingAttemptCount = 0; // Reset charging attempt count
                $property->lastPaymentDate = Yii::$app->util->getUtcDateTime();

                // Add payment details
                $paymentData = [
                    'reference' => $chargeResponse['reference'],
                    'pspReference' => $chargeResponse['pspReference'],
                    'paymentForDate' => $paymentForDate,
                    'stripeReference' => $chargeResponse['stripeReference']
                ];
                $payment->addPayment($property->tenantUserId, $property->ownerUserId, $property->id, $property->cost,
                    Payment::TYPE_RENTAL, $comInfo['commission'], $paymentData);

                // Sending charging success email/notification messages
                $this->sendChargeSuccessMessages($property, $currency);
            } else {
                Yii::$app->appLog->writeLog('Charging failed');
                $property->paymentStatus = Property::PS_FAILED;
                $property->chargingAttemptCount += 1;
                $property->nextChargingAttemptDate = $this->getNextAttemptDate($property->nextChargingAttemptDate);

                // Sending charging success email/notification messages
                $this->sendChargeFailMessages($property, $property->nextChargingAttemptDate, $currency);
            }
        } else {
            Yii::$app->appLog->writeLog('No plan');
            $property->paymentStatus = Property::PS_FAILED;
            $property->chargingAttemptCount += 1;
            $property->nextChargingAttemptDate = $this->getNextAttemptDate($property->nextChargingAttemptDate);
            $this->sendChargeFailMessages($property, $property->nextChargingAttemptDate, $currency);
        }

        if ($property->chargingAttemptCount == Yii::$app->params['maxChargingAttempts']) {
            // Set that it has reached maximum attempt counts. This flag will avoid execution of charging again
            // when accidently increase the max attempt count in configuration
            $property->reachMaxAttempts = Property::REACH_MAX_ATT_YES;
        }

        if (!$property->saveModel()) {
            Yii::$app->appLog->writeLog("********* Charging details save failed ********", AppLogger::CRITICAL);
        }
    }

    /**
     * Send charging fail email/application messages
     * @param mixed $property Property object along with Tenant/Owner relations
     * @param string $nextAttemptDate Next charging attempt date
     * @param string $currency Currency code
     */
    private function sendChargeFailMessages($property, $nextAttemptDate, $currency)
    {
        $mail = new Mail();
        $notification = new Notification();
        $comInfo = $property->getFinalPayment($property->cost);
        $costWithCommission = $comInfo['totalAmount'];

        $isLastAttempt = $property->chargingAttemptCount >= Yii::$app->params['maxChargingAttempts'] ? true : false;

        // Retrieve next attempt date
        $nextAttemptDateLocal = Yii::$app->util->getTzSpecificDateTime($nextAttemptDate, $property->tenantUser->timeZone);
        $nextAttemptDate = date('Y-m-d', strtotime($nextAttemptDateLocal));

        // Send charging fail email notification to Tenant
        $mail->language = $property->tenantUser->language;
        $mail->sendChargeFailNotificationTenant($property->tenantUser->email, $property->tenantUser->getFullName(),
            $property->code, $nextAttemptDate, $costWithCommission, $currency, $isLastAttempt);

        // Send charging fail email notification to Owner
        $mail->language = $property->ownerUser->language;
        $mail->sendChargeFailNotificationOwner($property->ownerUser->email, $property->ownerUser->getFullName(),
            $property->code, $costWithCommission, $currency, $nextAttemptDate, $isLastAttempt);

        // Add notification to Tenant
        $notification->addNotification(Notification::TENANT_MON_PAY_FAIL, $property->tenantUserId,
            ['amount' => $costWithCommission, 'currency' => $currency, 'code' => $property->code]);

        // Add notification to Owner
        $notification->addNotification(Notification::OWNER_MON_PAY_FAIL, $property->ownerUserId,
            ['amount' => $costWithCommission, 'currency' => $currency, 'code' => $property->code]);
    }

    /**
     * Send charging success email/application messages
     * @param mixed $property Property object along with Tenant/Owner relations
     * @param string $currency Currency code
     */
    private function sendChargeSuccessMessages($property, $currency)
    {
        $mail = new Mail();
        $notification = new Notification();
        $comInfo = $property->getFinalPayment($property->cost);
        $costWithCommission = $comInfo['totalAmount'];

        // Send charging success email notification to Tenant
        $mail->language = $property->tenantUser->language;
        $mail->sendChargeSuccessNotificationTenant($property->tenantUser->email, $property->tenantUser->getFullName(),
            $property->code, $costWithCommission, $currency);

        // Send charging success email notification to Owner
        $mail->language = $property->ownerUser->language;
        $mail->sendChargeSuccessNotificationOwner($property->ownerUser->email, $property->ownerUser->getFullName(),
            $property->code, $costWithCommission, $currency);

        // Add notification to Tenant
        $notification->addNotification(Notification::TENANT_MON_PAY_DEB, $property->tenantUserId,
            ['amount' => $costWithCommission, 'currency' => $currency, 'code' => $property->code]);

        // Add notification to Owner
        $notification->addNotification(Notification::OWNER_MON_PAY_CRDT, $property->ownerUserId,
            ['amount' => $costWithCommission, 'currency' => $currency, 'code' => $property->code]);
    }

    /**
     * Calculate next charging date
     * @param string $lastChargingDate Last charged date
     * @param integer $payDay Pay day of the month
     * @param integer $chargingCycle Whether monthly or other
     * @param string $userTz Timezone of the user
     * @return string Next charging date
     */
    public function getNextChargingDate($lastChargingDate, $payDay, $chargingCycle, $userTz)
    {
        $nextChargingDateUtc = null;
        $property = new Property();

        switch ($chargingCycle) {
            case Property::CS_MONTHLY:
                // Convert current time to local time of particular user, based on his timezone.
                $lcDateTimeLocal = Yii::$app->util->getTzSpecificDateTime($lastChargingDate, $userTz, 'UTC');
                $lcDateTimeLocalTs = strtotime($lcDateTimeLocal);
                $lcMonth = date('m', $lcDateTimeLocalTs);
                $lcYear = date('Y', $lcDateTimeLocalTs);

                $ncMonth = $lcMonth + 1;
                $ncYear = $lcYear;

                if ($ncMonth > 12) {
                    $ncMonth = 1;
                    $ncYear = $lcYear + 1;
                }

                $pDay = $property->getPayDay($payDay, $ncYear, $ncMonth) - 1; // Since strtotime("{$year}-{$nextMonth}") is equivalent to 1st day of month
                $nextChargingDateLocal = date('Y-m-d', strtotime("+{$pDay} days", strtotime("{$ncYear}-{$ncMonth}")));
                $nextChargingDateUtc = Yii::$app->util->getTzSpecificDateTime($nextChargingDateLocal, 'UTC', $userTz);
                break;
        }

        return $nextChargingDateUtc;
    }

    /**
     * Calculate date of the next charging attempt
     * @param string $lastChargingAttemptDate Last attempt date
     * @return string Next attempt date
     */
    public function getNextAttemptDate($lastChargingAttemptDate)
    {
        return date('Y-m-d H:i:s', strtotime("+1 days", strtotime($lastChargingAttemptDate)));
    }

    /**
     * Retrieve property image URL
     * @param string $imageName Name of the image
     * @return string S3 image URL
     */
    public function getImageUrl($imageName = null)
    {
        $imgName = null == $imageName ? $this->imageName : $imageName;
        $propImgUrl = '';
        $aws = new Aws();
        if (null != $imgName) {
            $propImgUrl = $aws->s3GetObjectUrl($imgName, false);
        }
        return $propImgUrl;
    }

    /**
     * Retrieve property thumbnail image URL
     * @param string $thumbImageName Thumbnail name of the image
     * @return string S3 image URL
     */
    public function getThumbImageUrl($thumbImageName = null)
    {
        $imgName = null == $thumbImageName ? $this->thumbImageName : $thumbImageName;
        $propImgUrl = '';
        $aws = new Aws();
        if (null != $imgName) {
            $propImgUrl = $aws->s3GetObjectUrl($imgName, false);
        }
        return $propImgUrl;
    }

    /**
     * Retrieve list properties to send payment reminders
     * @param integer $date Next charging date
     * @param integer $page Pagination number
     * @return mixed
     */
    public function getPaymentNotifyProperties($date, $page)
    {
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $properties = Property::find()
            ->andWhere('DATE(nextChargingDate) = :nextChargingDate', [':nextChargingDate' => $date])
            ->joinWith(['tenantUser'])
            ->limit($limit)
            ->offset($offset)
            ->all();

        return $properties;
    }

    /**
     * Check whether property is editiable. If there are any pending property requests or
     * it is rented out, not allow to edit.
     * @return boolean
     */
    public function isEditable()
    {
        $model = PropertyRequest::find()
            ->andWhere(['status' => PropertyRequest::STATUS_PENDING])
            ->andWhere(['propertyId' => $this->id])
            ->one();

        if (!empty($model) || $this->status == self::STATUS_NOT_AVAILABLE) {
            return self::EDITABLE_NO;
        }

        return self::EDITABLE_YES;
    }

    /**
     * Terminate property agreement. Within this function
     * - Release property from tenant and make available again
     * - Add entry to property history
     * @return boolean
     */
    public function terminateProperty($user)
    {
        $mail = new Mail();
        $notification = new Notification();
        $propertyHistory = new PropertyHistory();
        $owner = User::findOne($this->ownerUserId);
        $tenant = User::findOne($this->tenantUserId);

        $propertyHistory->tenantUserId = $this->tenantUserId;
        $propertyHistory->ownerUserId = $this->ownerUserId;
        $propertyHistory->propertyId = $this->id;
        $propertyHistory->fromDate = $this->reservedAt;
        $propertyHistory->toDate = Yii::$app->util->getUtcDateTime();

        $this->tenantUserId = null;
        $this->status = self::STATUS_AVAILABLE;
        $this->currentRentDueAt = null;
        $this->paymentStatus = Property::PS_PENDING;
        $this->reservedAt = null;
        $this->nextChargingDate = null;
        $this->payDay = null;
        $this->chargingAttemptCount = 0;
        $this->nextChargingAttemptDate = null;
        $this->lastPaymentDate = null;
        $this->reachMaxAttempts = null;

        $allSuc = false;

        $transaction = Yii::$app->db->beginTransaction();

        if ($propertyHistory->saveModel()) {
            if ($this->saveModel()) {
                $allSuc = true;
            }
        }

        if ($allSuc) {
            $transaction->commit();
            Yii::$app->appLog->writeLog('Commit transaction');

            // Send email notification to opposite party
            if ($user->id == $owner->id) {
                $mail->language = $tenant->language;
                $mail->sendPropTerminateEmail($tenant->email, $tenant->getFullName(), $this->code, $user->getFullName());
            } else {
                $mail->language = $owner->language;
                $mail->sendPropTerminateEmail($owner->email, $owner->getFullName(), $this->code, $user->getFullName());
            }

            // Send email notification to terminator
            $mail->language = $user->language;
            $mail->sendPropTerminateEmailToTerminator($user->email, $user->getFullName(), $this->code);

            // Add notification to Tenant
            $notification->addNotification(Notification::PROP_TERMINATE, $tenant->id, ['code' => $this->code]);

            // Add notification to Owner
            $notification->addNotification(Notification::PROP_TERMINATE, $owner->id, ['code' => $this->code]);

        } else {
            $transaction->rollBack();
            Yii::$app->appLog->writeLog('Rollback transaction');
        }

        return $allSuc;
    }

    /**
     * Pay all pending payments up to now
     * @param User $user Tenant user object
     * @return boolean
     */
    public function payNow($user)
    {
        $paymentPlan = new PaymentPlan();
        $payment = new Payment();
        $mail = new Mail();
        $notification = new Notification();
        $allSuc = false;
        $currency = Yii::$app->params['defCurrency'];

        $paymentPlan = $paymentPlan->getPaymentPlanByUserId($user->id);

        if (!empty($paymentPlan)) {
            $pendingPaymentInfo = $this->getPendingPaymentInfo($this, $user->timeZone, $this->payDay);
            $totalPendingPayments = $this->cost * $pendingPaymentInfo['paymentDueMonthCnt'];
            $comInfo = $this->getFinalPayment($totalPendingPayments);
            $totalPendingPaymentsWithCom = $comInfo['totalAmount'];
            $commission = $comInfo['commission'];

            $chargeResponse = $paymentPlan->charge($totalPendingPaymentsWithCom, $user, $this, $currency);
            if ($chargeResponse['status']) {
                $transaction = Yii::$app->db->beginTransaction();

                $chargeResponse = ['paymentData' => [
                    'reference' => $chargeResponse['reference'],
                    'pspReference' => $chargeResponse['pspReference'],
                    'stripeReference' => $chargeResponse['stripeReference'],
                ]];

                Yii::$app->appLog->writeLog('Pending payment charging success', ['amount' => $totalPendingPaymentsWithCom]);

                $this->paymentStatus = Property::PS_SUCCESS;
                $this->nextChargingDate = $pendingPaymentInfo['nextChargingDateUtc'];
                $this->nextChargingAttemptDate = $this->nextChargingDate;
                $this->reachMaxAttempts = Property::REACH_MAX_ATT_NO;
                $this->chargingAttemptCount = 0;

                // Update property details
                if ($this->saveModel()) {
                    // Add payment details
                    $chargeResponse['paymentData']['paymentForDate'] = $this->getPaymentForDate($this->payDay, $user->timeZone);
                    $paymentSaveStatus = $payment->addPayment($this->tenantUserId, $this->ownerUserId, $this->id,
                        $totalPendingPayments, Payment::TYPE_RENTAL, $commission, $chargeResponse['paymentData']);

                    if ($paymentSaveStatus) {
                        $allSuc = true;
                    }
                }

                if ($allSuc) {
                    $transaction->commit();
                    Yii::$app->appLog->writeLog('Commit transaction');

                    // Send email to owner
                    $ownerUser = User::findOne($this->ownerUserId);
                    $mail->language = $ownerUser->language;
                    $mail->sendAllPendingPaymentRcvEmailOwner($ownerUser->email, $ownerUser->getFullName(), $this->code,
                        $totalPendingPaymentsWithCom, $currency);

                    // Send email to tenant
                    $mail->language = $user->language;
                    $mail->sendAllPendingPaymentPayEmailTenant($user->email, $user->getFullName(), $this->code,
                        $totalPendingPaymentsWithCom, $currency);

                    // Add notification owner
                    $notification->addNotification(Notification::OWNER_MON_PAY_CRDT, $ownerUser->id,
                        ['amount' => $totalPendingPaymentsWithCom, 'currency' => $currency, 'code' => $this->code]);

                    // Add notification tenant
                    $notification->addNotification(Notification::TENANT_MON_PAY_DEB, $user->id,
                        ['amount' => $totalPendingPaymentsWithCom, 'currency' => $currency, 'code' => $this->code]);
                } else {
                    $transaction->rollBack();
                    Yii::$app->appLog->writeLog('Rollback transaction');
                    $paymentPlan->refund($user->id, $totalPendingPaymentsWithCom, $chargeResponse['paymentData']['pspReference'],
                        $chargeResponse['paymentData']['stripeReference'], $currency);
                }
            } else {
                Yii::$app->appLog->writeLog('Pending payment charging failed');
            }
        } else {
            Yii::$app->appLog->writeLog('No payment plan configured');
        }

        return $allSuc;
    }

    /**
     * Find payment for date in paynow. At the date of pay now, if current date is greater than
     * pay date, that means system has charged rent for current month as well, we set payfordate as
     * current month payment date. This is useful when calculating pendin and received funds of ownere
     * in dashboard.
     * @param integer $payDay Pay date of the month
     * @param string $timezone User`s timezone
     * @return string payForDate
     */
    public function getPaymentForDate($payDay, $timezone)
    {
        $paymentForDate = null;
        $curDateLocal = Yii::$app->util->getTzSpecificDateTime(date('Y-m-d H:i:s'), $timezone, Yii::$app->params['phpIniTimeZone']);
        $curDateLocalTs = strtotime($curDateLocal);
        $curDayLc = date('j', $curDateLocalTs);
        $curMonthLc = date('m', $curDateLocalTs);
        $curYearLc = date('Y', $curDateLocalTs);

        if ($payDay <= $curDayLc) {
            $paymentForDate = "{$curYearLc}-{$curMonthLc}-{$payDay}";
        }

        return $paymentForDate;
    }

    /**
     * Calculate number of due payments from last payment date
     * @param Property $property Property object
     * @param string $userTz Timezone of the user
     * @param integer $payDay Paying date of the month
     * @return boolean
     */
    public function getPendingPaymentInfo($property, $userTz, $payDay)
    {
        $paymentDueMonthCnt = 0;
        $nextChargingDateUtc = null;

        // Last charging date according to users timezone
        $lcDateTimeLocal = Yii::$app->util->getTzSpecificDateTime($property->nextChargingDate, $userTz, 'UTC');
        $lcDateTimeLocalTs = strtotime($lcDateTimeLocal);

        // Current datetime according to user`s timezone
        $curDateTimeLocal = Yii::$app->util->getTzSpecificDateTime(date('Y-m-d H:i:s'), $userTz,
            Yii::$app->params['phpIniTimeZone']);
        $curDateTimeLocalTs = strtotime($curDateTimeLocal);

        $curMonth = date('m', $curDateTimeLocalTs);
        $curYear = date('Y', $curDateTimeLocalTs);
        $curDay = date('j', $curDateTimeLocalTs);

        $lcDay = (int)date('j', $lcDateTimeLocalTs);
        $lcMonth = date('m', $lcDateTimeLocalTs);
        $lcYear = date('Y', $lcDateTimeLocalTs);
        $lcHour = date('H', $lcDateTimeLocalTs);
        $lcMin = date('i', $lcDateTimeLocalTs);
        $lcSec = date('s', $lcDateTimeLocalTs);

        if ($curMonth == $lcMonth && $lcYear == $curYear) {
            // Pay now on same month
            $paymentDueMonthCnt = 1;
            $nextChargingDateUtc = $this->getNextChargingDate($property->nextChargingDate, $payDay, Property::CS_MONTHLY, $userTz);
        } else {
            if ($payDay < $curDay || $payDay == $curDay) {
                // Paying date is passed on current month. So we charge upto payday of current month and set next charging date on
                // next month
                $endDate = new \DateTime("{$curYear}-{$curMonth}-{$payDay} {$lcHour}:{$lcMin}:{$lcSec}");
                $nextMonth = $curMonth + 1;
                $year = $curYear;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $year = $year + 1;
                }
                $pDay = $property->getPayDay($payDay, $year, $nextMonth) - 1; // Since strtotime("{$year}-{$nextMonth}") is equivalent to 1st day of month
                $nextChargingDateLocal = date('Y-m-d', strtotime("+{$pDay} days", strtotime("{$year}-{$nextMonth}")));
                $nextChargingDateUtc = Yii::$app->util->getTzSpecificDateTime($nextChargingDateLocal, 'UTC', $userTz);
            } else if ($payDay > $curDay) {
                // Paying date is not passed on current month. So we set next charging date on this month
                $endDate = new \DateTime("{$curYear}-{$curMonth}-{$curDay} {$lcHour}:{$lcMin}:{$lcSec}");
                $pDay = $property->getPayDay($payDay, $curYear, $curMonth) - 1; // Since strtotime("{$year}-{$nextMonth}") is equivalent to 1st day of month
                $nextChargingDateLocal = date('Y-m-d', strtotime("+{$pDay} days", strtotime("{$curYear}-{$curMonth}")));
                $nextChargingDateUtc = Yii::$app->util->getTzSpecificDateTime($nextChargingDateLocal, 'UTC', $userTz);
            }

            $stDate = new \DateTime(date('Y-m-d H:i:s', $lcDateTimeLocalTs));
            $paymentDueMonthCnt = $stDate->diff($endDate)->m + 1;
        }

        return [
            'nextChargingDateUtc' => $nextChargingDateUtc,
            'paymentDueMonthCnt' => $paymentDueMonthCnt
        ];
    }

    /**
     * Check whether property meets paynow conditions
     * @return boolean
     */
    public function isPayNow()
    {
        if ($this->reachMaxAttempts == Property::REACH_MAX_ATT_YES && $this->paymentStatus == Property::PS_FAILED) {
            return true;
        }

        return false;
    }

    /**
     * Check whether pay day falls on particular month.Ex: if pay day between 28-31, then it can be fallen on next month
     * We need to avoid it. If it falls on next month, set payday to last date of current month
     * @param integer $payDay User preferred payday
     * @param integer $month Month of the next payment
     * @return integer
     */
    public function getPayDay($payDay, $year, $month)
    {
        $nextPayDay = $payDay;
        if ($payDay > 28) {
            $lastDateOfMonth = date("t", strtotime("{$year}-{$month}-01"));
            if ($payDay > $lastDateOfMonth) {
                $nextPayDay = $lastDateOfMonth;
            }
        }

        return $nextPayDay;
    }

    /**
     * Calculate final payment when tenant making a payment
     * @param float $amount Paying amount
     * @param integer $comPlan Commission plan
     * @return array
     */
    /*public function getFinalPayment($amount, $comPlan)
    {
        $comInfo = ['commission' => 0, 'totalAmount' => $amount, 'comRenter' => 0, 'comOwner' => 0];

        switch ($comPlan) {
            case self::CP_RENTER:
                $commission = ($amount * Yii::$app->params['commission'])/100;
                $commission = number_format($commission, 2, '.', '');
                $comInfo = [
                    'commission' => $commission,
                    'totalAmount' => $amount + $commission,
                    'comRenter' => $commission,
                    'comOwner' => 0
                ];
                break;

            case self::CP_OWNER:
                $commission = ($amount * Yii::$app->params['commission'])/100;
                $commission = number_format($commission, 2, '.', '');
                $comInfo = [
                    'commission' => 0,
                    'totalAmount' => $amount,
                    'comRenter' => 0,
                    'comOwner' => $commission
                ];
                break;

            case self::CP_SPLIT:
                $splittedComPer = Yii::$app->params['commission']/2;
                $commission = ($amount * $splittedComPer)/100;
                $commission = number_format($commission, 2, '.', '');
                $comInfo = [
                    'commission' => $commission,
                    'totalAmount' => $amount + $commission,
                    'comRenter' => $commission,
                    'comOwner' => $commission,
                ];
                break;
        }

        return $comInfo;
    }*/

    /**
     * Calculate final payment (payment + commission)
     * @param float $amount Paying amount
     * @return array
     */
    public function getFinalPayment($amount)
    {
        $commission = ($amount * Yii::$app->params['commission'])/100;
        $commission = number_format($commission, 2, '.', '');
        $totalAmount = $amount + $commission;
        return ['commission' => $commission, 'totalAmount' => $totalAmount];
    }

    /**
     * Tenant creates property on behalf of user
     *  - Add owner
     *  - Add property
     *  - Accept property
     * @param User $owner User object
     * @param User $tenant User object
     * @return boolean
     */
    public function createOnBehalfOfProperty($owner, $tenant)
    {
        $isAllSuc = false;
        $transaction = Yii::$app->db->beginTransaction();
        if ($owner->saveModel()) {
            $this->ownerUserId = $owner->id;
            if ($this->saveModel()) {
                $propertyRequest = new PropertyRequest();
                $propertyRequest->propertyId = $this->id;
                $propertyRequest->code = $this->code;
                $propertyRequest->tenantUserId = $tenant->id;
                $propertyRequest->ownerUserId = $owner->id;
                $propertyRequest->status = PropertyRequest::STATUS_PENDING;
                $propertyRequest->payDay = $this->payDay;
                $propertyRequest->bookingDuration = $this->duration;
                $propertyRequest->payKeyMoneyCc = $this->payKeyMoney;

                if ($propertyRequest->saveModel()) {
                    $propertyRequest->isNewRecord = false;
                    //$propertyRequest = PropertyRequest::findOne($propertyRequest->id);
                    if ($propertyRequest->accept()) {
                        $isAllSuc = true;
                    }
                }
            }
        }

        if ($isAllSuc) {
            $transaction->commit();
            Yii::$app->appLog->writeLog('Commit transaction');
        } else {
            $transaction->rollBack();
            Yii::$app->appLog->writeLog('Rollback transaction');
        }

        return $isAllSuc;
    }

    /**
     * Retrieve on behalf property
     * @param integer $id Property id
     * @param integer $tenantId User id of the tenant
     * @return Property
     */
    public function getOnBehalfProperty($id, $tenantId)
    {
        $model = Property::find()->where('id = :id AND createdById = :createdById', [':id' => $id,
            ':createdById' => $tenantId])->one();

        return $model;
    }

    /**
     * Retrieve property image list
     * @return Property
     */
    public function getImageList()
    {
        $imageList = [];
        if (!empty($this->images)) {
            $images = json_decode($this->images, true);
            foreach ($images as $image) {
                if ('' != @$image['imageName']) {
                    $imageList[] = [
                        'imageName' => $image['imageName'],
                        'imageUrl' => $this->getImageUrl($image['imageName']),
                        'thumbImageName' => $image['thumbImageName'],
                        'thumbImageUrl' => $this->getThumbImageUrl($image['thumbImageName']),
                        'isDefault' => $image['isDefault'],
                    ];
                }
            }
        }

        // Set default image first
        $sortedList = [];
        $def = [];
        foreach ($imageList as $_image) {
            if (@$_image['isDefault']) {
                $def[] = $_image;
            } else {
                $sortedList[] = $_image;
            }
        }

        return array_merge($def, $sortedList);
    }

    /**
     * Get properties
     * @return mixed
     */
    public function getProperties($page)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = Property::find();
        $query->limit($limit);
        $query->offset($offset);

        $properties = $query->all();

        return $properties;
    }
}
