<?php

namespace app\components;

use Yii;
use yii\base\Component;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Refund;

/**
 * Wrapper class for accessing Stripe API
 */
class StripeApi extends Component
{
    public $apiKey;
    public $response;
    public $errorCode;

    /**
     * Class constructor
     * @param string $apiKey Stripe API key
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        Stripe::setApiKey($this->apiKey);
    }

    /**
     * Retrieve token details
     * @param string $token Stripe token
     * @return array Card details
     */
    public function getTokenDetails($token)
    {
        $tokenInfo = [];
        try {
            $response = Token::retrieve($token);
            Yii::$app->appLog->writeLog('Stripe token retrieve response.', ['response' => $response]);
            if ($response instanceof Token) {
                $tokenInfo['cardId'] = $response->card->id;
                $tokenInfo['brand'] = $response->card->brand;
                $tokenInfo['expMonth'] = $response->card->exp_month;
                $tokenInfo['expYear'] = $response->card->exp_year;
                $tokenInfo['last4'] = $response->card->last4;
            }
        } catch(\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe token details retrieval failed.', ['error' => $e->getMessage()]);
        }

        return $tokenInfo;
    }

    /**
     * Creates a customer with token
     * @param string $token Stripe token
     * @param string $email Customer email
     * @return string Stripe customer id
     */
    public function createCustomer($token, $email)
    {
        $customerInfo = [];
        try {
            $response = Customer::create(['source' => $token, 'email' => $email]);
            Yii::$app->appLog->writeLog('Stripe customer creates response.', ['response' => $response]);
            if ($response instanceof Customer) {
                $cardInfo = $response->sources->data[0];
                $customerInfo = [
                    'customerId' => $response->id,
                    'cardInfo' => [
                        'cardId' => $cardInfo->id,
                        'brand' => $cardInfo->brand,
                        'expMonth' => $cardInfo->exp_month,
                        'expYear' => $cardInfo->exp_year,
                        'last4' => $cardInfo->last4,
                    ]
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe customer creates failed.', ['error' => $e->getMessage()]);
        }

        return $customerInfo;
    }

    /**
     * Remove stripe customer
     * @param string $customerId Stripe customer id
     * @return boolean
     */
    public function deleteCustomer($customerId)
    {
        $status = false;
        try {
            $customer = Customer::retrieve($customerId);
            $response = $customer->delete();
            if (@$response->deleted) {
                $status = true;
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe customer delete failed.', ['error' => $e->getMessage()]);
        }

        return $status;
    }

    /**
     * Remove card
     * @param string $cardId Stripe card id
     * @param string $customerId Stripe customer id
     * @return array
     */
    public function deleteCard($cardId, $customerId)
    {
        $status = false;
        try {
            $customer = Customer::retrieve($customerId);
            $response = $customer->sources->retrieve($cardId)->delete();
            Yii::$app->appLog->writeLog('Stripe card delete response.', ['response' => $response]);
            if (@$response->deleted) {
                $status = true;
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe card delete failed.', ['error' => $e->getMessage()]);
        }

        return $status;
    }

    /**
     * Create charge
     * @param integer $amount Amount to be charged(multiplied by 100. Eg:1$ -> 100)
     * @param string $currency Currency code
     * @param string $customerId Stripe customer id
     * @return array
     */
    /*public function charge($amount, $currency, $customerId)
    {
        $chargeResponse = [];
        try {
            $response = Charge::create([
                'amount' => $amount,
                'currency' => $currency,
                'customer' => $customerId
            ]);

            Yii::$app->appLog->writeLog('Stripe charge response.', ['response' => $response]);
            if (@$response->status == "succeeded") {
                $chargeResponse = [
                    'status' => true,
                    'chargingReference' => $response->id
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe charging failed.', ['error' => $e->getMessage()]);
        }

        return $chargeResponse;
    }*/


    /**
     * Create charge using customer or token
     * @param integer $amount Amount to be charged(multiplied by 100. Eg:1$ -> 100)
     * @param string $currency Currency code
     * @param string $customerId Stripe customer id
     * @param string $token Stripe card token
     * @return array
     */
    public function charge($amount, $currency, $customerId = null, $token = null)
    {
        $chargeResponse = [];
        try {
            $params = [
                'amount' => $amount,
                'currency' => $currency
            ];

            if ($customerId != null) {
                $params['customer'] = $customerId;
            }

            if ($token != null) {
                $params['source'] = $token;
            }

            $response = Charge::create($params);

            Yii::$app->appLog->writeLog('Stripe charge response.', ['response' => $response]);
            if (@$response->status == "succeeded") {
                $chargeResponse = [
                    'status' => true,
                    'chargingReference' => $response->id
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe charging failed.', ['error' => $e->getMessage()]);
        }

        return $chargeResponse;
    }

    /**
     * Refund
     * @param string $chargeId Charging reference
     * @param integer $amount Amount to be charged(multiplied by 100. Eg:1$ -> 100)
     * @return boolean
     */
    public function refund($chargeId, $amount)
    {
        $status = false;
        try {
            $response = Refund::create(['charge' => $chargeId]);
            Yii::$app->appLog->writeLog('Stripe refund response.', ['response' => $response]);
            if ($response instanceof Refund) {
                $status = true;
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('Stripe refund failed.', ['error' => $e->getMessage()]);
        }

        return $status;
    }
}