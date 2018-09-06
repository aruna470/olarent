<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\models\Payment;
use app\components\Adyen;
use app\components\StripeApi;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "PaymentPlan".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $expire
 * @property string $cardType
 * @property string $cardHolderName
 * @property string $cardNumber
 * @property string $adyenPspReference
 * @property string $adyenShopperReference
 * @property integer $paymentGateway
 * @property string $createdAt
 *
 * @property User $user
 */
class PaymentPlan extends Base
{
    // Payment gateway identifiers
    const PG_ADYEN = 1;
    const PG_STRIPE = 2;

    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';

    // Just for refunding purpose
    public $amount;
    public $currency;
    public $stripeToken;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PaymentPlan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            // API Create
            [['userId', 'paymentGateway'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD,
                'on' => [self::SCENARIO_API_CREATE]],
            [['cardType', 'adyenPspReference', 'adyenShopperReference', 'amount', 'currency'], 'required', 'when' => function ($model) {
                    return $model->paymentGateway == self::PG_ADYEN;
                },
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
            [['stripeToken'], 'required', 'when' => function ($model) {
                return $model->paymentGateway == self::PG_STRIPE;
            },
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
            [['userId', 'paymentGateway'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['paymentGateway'], 'in', 'range' => [self::PG_ADYEN, self::PG_STRIPE], 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['expire'], 'validateExpireDate', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['cardType'], 'string', 'max' => 10, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['adyenPspReference'], 'string', 'max' => 25, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['adyenShopperReference'], 'string', 'max' => 20, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['cardHolderName'], 'string', 'max' => 30, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['userId'], 'validatePlanExists', 'on' => [self::SCENARIO_API_CREATE]],
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
            'expire' => Yii::t('app', 'Expire'),
            'cardType' => Yii::t('app', 'Card Type'),
            'cardHolderName' => Yii::t('app', 'Card Holder Name'),
            'cardNumber' => Yii::t('app', 'Card Number'),
            'adyenPspReference' => Yii::t('app', 'Adyen Psp Reference'),
            'adyenShopperReference' => Yii::t('app', 'Adyen Shopper Reference'),
            'paymentGateway' => Yii::t('app', 'Payment Gateway'),
            'createdAt' => Yii::t('app', 'Created At'),
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
     * Check whether user has already a plan
     */
    public function validatePlanExists()
    {
        $model = PaymentPlan::find()
            ->where(['userId' => $this->userId])
            ->one();

        if (!empty($model)) {
            $this->addError('userId', ApiStatusMessages::PLAN_EXISTS);
        }
    }

    /**
     * Validate card expiry date
     */
    public function validateExpireDate()
    {
        if (!empty($this->expire)) {
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->expire)) {
                $this->addError('expire', ApiStatusMessages::VALIDATION_FAILED);
            } else if(strtotime($this->expire) < time()) {
                $this->addError('expire', ApiStatusMessages::VALIDATION_FAILED);
            }
        }
    }

    /**
     * Retrieve card expiry date when month and year given
     * @param string expiry Card expiry date yyyy-mm
     * @return integer End date of given month
     */
    public function getCardExpiryDate($expiry)
    {
        $ts = strtotime($expiry);
        return date('t', $ts);
    }

    /**
     * Retrieve payment plan of particular user
     * @param integer $userId User id
     * @return PaymentPlan PaymentPlan object
     */
    public function getPaymentPlanByUserId($userId)
    {
        return PaymentPlan::find()->where(['userId' => $userId])->one();
    }

    /**
     * Retrieve payment plan of particular user
     * @param integer $userId User id
     * @param integer $planId Plan id
     * @return PaymentPlan PaymentPlan object
     */
    public function getPaymentPlan($userId, $planId)
    {
        return PaymentPlan::find()->where(['userId' => $userId, 'id' => $planId])->one();
    }

    /**
     * Create payment plan
     * @param string $email User email
     * @return boolean true/false
     */
    public function createPlan($email = null)
    {
        $payment = new Payment();
        $allSuc = false;
        $currency = Yii::$app->params['defCurrency'];

        if ($this->validateModel()) {
            switch ($this->paymentGateway) {

                // Adyen
                case PaymentPlan::PG_ADYEN:
                    $adyen = new Adyen(Yii::$app->params);
                    $adyenRes = $adyen->getRecurringContract($this->adyenShopperReference);
                    $adyenRes = json_decode($adyenRes);
                    Yii::$app->appLog->writeLog("Adyen response:", [$adyenRes]);
                    if (isset($adyenRes->details[0]->RecurringDetail->recurringDetailReference)) {
                        $this->cardHolderName = $adyenRes->details[0]->RecurringDetail->card->holderName;
                        $this->cardNumber = $adyenRes->details[0]->RecurringDetail->card->number;
                        $this->adyenRecurringDetailReference = $adyenRes->details[0]->RecurringDetail->recurringDetailReference;
                        $year = $adyenRes->details[0]->RecurringDetail->card->expiryYear;
                        $month = sprintf("%02d", $adyenRes->details[0]->RecurringDetail->card->expiryMonth);
                        $expire = "{$year}-{$month}";
                        $this->expire = "{$expire}-{$this->getCardExpiryDate($expire)}";

                        if ($this->saveModel()) {
                            $allSuc = true;
                        }
                    } else {
                        Yii::$app->appLog->writeLog("Adyen recurring details retrieval failed");
                    }

                    // Refund initial amount that submit when creating the plan
                    $reference = rand(10,100) . uniqid();
                    $adyenRes = $adyen->refund($this->amount, $this->currency, $this->adyenPspReference, $reference);
                    $adyenRes = json_decode($adyenRes);
                    Yii::$app->appLog->writeLog("Adyen refund response:", [$adyenRes]);

                    if ($adyenRes->response == '[refund-received]') {
                        Yii::$app->appLog->writeLog("Refund success.", ['amount' => $this->amount]);
                    }
                    break;

                // Stripe
                case PaymentPlan::PG_STRIPE:
                    $stripeApi = new StripeApi(Yii::$app->params['stripe']['apiKey']);
                    $customerDetails = $stripeApi->createCustomer($this->stripeToken, $email);
                    if (!empty($customerDetails)) {
                        Yii::$app->appLog->writeLog("Stripe customer create success.");
                            $this->cardType = $customerDetails['cardInfo']['brand'];
                            $this->cardNumber = $customerDetails['cardInfo']['last4'];
                            $this->stripeCustomerId = $customerDetails['customerId'];
                            $this->stripeCardId = $customerDetails['cardInfo']['cardId'];
                            $year = $customerDetails['cardInfo']['expYear'];
                            $month = sprintf("%02d", $customerDetails['cardInfo']['expMonth']);
                            $expire = "{$year}-{$month}";
                            $this->expire = "{$expire}-{$this->getCardExpiryDate($expire)}";

                            if ($this->saveModel()) {
                                $allSuc = true;
                            }
                    } else {
                        Yii::$app->appLog->writeLog("Stripe customer create failed.");
                    }
                    break;
            }
        }

        return $allSuc;
    }

    /**
     * Remove payment plan
     * @return boolean true/false
     */
    public function deletePlan()
    {
        $status = false;
        $adyen = new Adyen(Yii::$app->params);

        switch ($this->paymentGateway) {
            case PaymentPlan::PG_ADYEN:
                $adyenRes = $adyen->disableRecurringContract($this->adyenShopperReference, $this->adyenRecurringDetailReference);
                $adyenRes = json_decode($adyenRes);
                Yii::$app->appLog->writeLog("Adyen response:", [$adyenRes]);
                if (isset($adyenRes->response) && $adyenRes->response == '[detail-successfully-disabled]') {
                    if ($this->deleteModel()) {
                        $status = true;
                    }
                } else {
                    Yii::$app->appLog->writeLog("Adyen recurring plan disable failed.");
                }
                break;

            case PaymentPlan::PG_STRIPE:
                // We do not delete the customer from stripe, but remove the card
                $stripeApi = new StripeApi(Yii::$app->params['stripe']['apiKey']);
                if ($stripeApi->deleteCard($this->stripeCardId, $this->stripeCustomerId)) {
                    if ($this->deleteModel()) {
                        $status = true;
                    }
                } else {
                    Yii::$app->appLog->writeLog("Stripe card delete failed.");
                }

                break;
        }

        return $status;
    }

    /**
     * Retrieve list of card expiring users
     * @param integer $date Expiry date
     * @param integer $page Pagination number
     * @return mixed
     */
    public function getCardExpiringUsers($date, $page)
    {
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $paymentPlans = PaymentPlan::find()
            ->andWhere('expire = :expire', [':expire' => $date])
            ->joinWith(['user'])
            ->limit($limit)
            ->offset($offset)
            ->all();

        return $paymentPlans;
    }

    /**
     * Refund initial charge if anything goes wrong
     * @param integer $userId User id
     * @param float $amount Total payment made
     * @param string $chargingRefAdyen Psp reference value received from previous payment.
     * @param string $chargingRefStripe Stripe charging reference received from previous payment.
     * @param string $currency Currency code.
     */
    public function refund($userId, $amount, $chargingRefAdyen, $chargingRefStripe, $currency)
    {
        Yii::$app->appLog->writeLog('Refunding payment.', ['amount' => $amount]);

        $paymentPlan = new PaymentPlan();
        $paymentPlan = $paymentPlan->getPaymentPlanByUserId($userId);

        if (!empty($paymentPlan)) {

            switch ($paymentPlan->paymentGateway) {

                case PaymentPlan::PG_ADYEN:

                    $adyen = new Adyen(Yii::$app->params);
                    $reference = time() . '-' . $userId;
                    $adyenRes = $adyen->refund($amount, $currency, $chargingRefAdyen, $reference);
                    $adyenRes = json_decode($adyenRes);
                    Yii::$app->appLog->writeLog("Adyen refund response:", [$adyenRes]);
                    if ($adyenRes->response == '[refund-received]') {
                        Yii::$app->appLog->writeLog("Adyen refund success.", ['amount' => $amount]);
                    }

                    break;

                case PaymentPlan::PG_STRIPE:

                    $stripeApi = new StripeApi(Yii::$app->params['stripe']['apiKey']);
                    $refundRes = $stripeApi->refund($chargingRefStripe, ($amount * 100));

                    if ($refundRes) {
                        Yii::$app->appLog->writeLog("Stripe refund success.", ['amount' => $amount]);
                    }

                    break;
            }
        } else {
            Yii::$app->appLog->writeLog('No plan configured.', ['userId' => $userId]);
        }
    }

    /**
     * Access relevant charging API and perform charge
     * @param float $payingAmount Amount to be charged
     * @param User $tenant
     * @param Property $property
     * @param string $currency Currency format
     * @return boolean
     */
    public function charge($payingAmount, $tenant, $property, $currency)
    {
        $response = ['status' => false, 'reference' => null, 'pspReference' => null, 'stripeReference' => null];

        switch ($this->paymentGateway) {

            case PaymentPlan::PG_ADYEN:

                $adyen = new Adyen(Yii::$app->params);
                $reference = time() . '-' . $tenant->id . '-' . $property->id;
                $chargeResJson = $adyen->submitRecurringPayment($this->adyenRecurringDetailReference, $currency,
                    ($payingAmount * 100), $reference, $tenant->email,
                    $this->adyenShopperReference);
                Yii::$app->appLog->writeLog('Charging response.', [json_decode($chargeResJson, true)]);
                $chargeRes = json_decode($chargeResJson);

                if (@$chargeRes->resultCode == 'Authorised') {
                    $response['status'] = true;
                    $response['reference'] = $reference;
                    $response['pspReference'] = $chargeRes->pspReference;
                    Yii::$app->appLog->writeLog('Adyen charging success');
                } else {
                    Yii::$app->appLog->writeLog('Adyen charging failed');
                }

                break;

            case PaymentPlan::PG_STRIPE:

                $stripeApi = new StripeApi(Yii::$app->params['stripe']['apiKey']);
                $chargeRes = $stripeApi->charge(($payingAmount * 100), $currency, $this->stripeCustomerId);
                if (@$chargeRes['status']) {
                    $response['status'] = true;
                    $response['stripeReference'] = $chargeRes['chargingReference'];
                    Yii::$app->appLog->writeLog('Stripe charging success');
                } else {
                    Yii::$app->appLog->writeLog('Stripe charging failed');
                }

                break;
        }

        return $response;
    }

    /**
     * Check whether card is valid when adding new card. Charge 1 EUR and refund it
     * @param string $paymentGateway Payment gateway identifier
     * @param string $currency Currency format
     * @param float $amount amount to be charged to check card validity
     * @param string $currency Currency format
     * @param string $stripeCustomerId Stripe customer id
     * @return boolean
     */
    public function verifyCard($paymentGateway, $currency, $amount, $stripeCustomerId)
    {
        $status = false;
        switch ($this->paymentGateway) {
            case PaymentPlan::PG_STRIPE:
                $stripeApi = new StripeApi(Yii::$app->params['stripe']['apiKey']);
                // Charge 1
                $chargeRes = $stripeApi->charge(($amount * 100), $currency, $stripeCustomerId, null);
                if (@$chargeRes['status']) {
                    // Refund 1
                    $refundRes = $stripeApi->refund($chargeRes['chargingReference'], ($amount * 100));
                    if ($refundRes) {
                        $status = true;
                    }
                }
                break;
        }

        return $status;
    }
}
