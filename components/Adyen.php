<?php

namespace app\components;

use Yii;
use yii\base\Component;

class Adyen extends Component
{
    public $wsPassword;
    public $wsUsername;
    public $merchantAccount;
    public $response;
    public $errorCode;

    public $apiUrl;
    public $recurringApiUrl;
    public $paymentApiUrl;

    /**
     * Class constructor
     * @param array $config Yii app configuration array
     */
    public function __construct($config)
    {
        $this->wsUsername = $config['adyen']['wsUsername'];
        $this->wsPassword = $config['adyen']['wsPassword'];
        $this->merchantAccount = $config['adyen']['merchantAccount'];
        $this->recurringApiUrl = $config['adyen']['recurringApiUrl'];
        $this->paymentApiUrl = $config['adyen']['paymentApiUrl'];
        $this->apiUrl = $this->recurringApiUrl;
    }

    /**
     * Retrieve recurring payment details of a shopper
     * @param string $shopperReference Unique identifier for particular shopper
     * @return string JSON response of the API
     */
    public function getRecurringContract($shopperReference)
    {
        $params = [
            "merchantAccount" => $this->merchantAccount,
            "shopperReference" => $shopperReference,
            "recurring" => ["contract" => "RECURRING"]
        ];

        return $this->sendRequest('listRecurringDetails', $params, 'POST_JSON');
    }

    /**
     * Submit recurring payment
     * Sample response {"pspReference":"8514536095065963","resultCode":"Authorised","authCode":"74753"}
     * @param string $selectedRecurringDetailReference Recurring reference obtained via getRecurringDetails method
     * @param string $currency Currency identifier
     * @param integer $value Paying amount
     * @param string $reference Unique id for this transaction
     * @param string $shopperEmail Email address of the shopper
     * @param string $shopperReference Unique reference assigned to this user while creating recurring payment
     * @return string JSON response of the API
     */
    public function submitRecurringPayment($selectedRecurringDetailReference, $currency, $value, $reference, $shopperEmail, $shopperReference)
    {
//        return json_encode(['resultCode'=>'bow']);
        $this->apiUrl = $this->paymentApiUrl;

        $params = [
            'selectedRecurringDetailReference' => $selectedRecurringDetailReference,
            'recurring' => ['contract' => 'RECURRING'],
            'merchantAccount' => $this->merchantAccount,
            'amount' => ['currency' => $currency, 'value' => $value],
            'reference' => $reference,
            'shopperEmail' => $shopperEmail,
            'shopperReference' => $shopperReference,
            'shopperInteraction' => 'ContAuth',
            'fraudOffset' => '',
            'shopperIP' => '',
            'shopperStatement' => ''
        ];

        return $this->sendRequest('authorise', $params, 'POST_JSON');
    }

    /**
     * Disable recurring payment
     * @param string $shopperReference Unique reference assigned to this user while creating recurring payment
     * @param string $recurringDetailReference Recurring reference obtained via getRecurringDetails method
     * @return string JSON response of the API
     */
    public function disableRecurringContract($shopperReference, $recurringDetailReference)
    {
        $params = [
            'merchantAccount' => $this->merchantAccount,
            'shopperReference' => $shopperReference,
            'recurringDetailReference' => $recurringDetailReference
        ];

        return $this->sendRequest('disable', $params, 'POST_JSON');
    }

    /**
     * Refund
     * @param integer $amount Amount to be refunded
     * @param string $currency Currency format
     * @param string $originalReference pspReference received when making the payment
     * @param string $reference Unique reference for this transaction(any unique id)
     * @return string JSON response of the API
     */
    public function refund($amount, $currency, $originalReference, $reference)
    {
        $this->apiUrl = $this->paymentApiUrl;

        $params = [
            'merchantAccount' => $this->merchantAccount,
            'modificationAmount' => ['value' => $amount, 'currency' => $currency],
            'originalReference' => $originalReference,
            'reference' => $reference
        ];

        return $this->sendRequest('refund', $params, 'POST_JSON');
    }

    public function StoreAndSubmitPayout()
    {

    }

    /**
     * Make CURL request to API
     * @param string $url Endpoint URL
     * @param array $curlOptions CURL options
     * @param integer $timeout Request timeout in seconds
     * @return string JSON response of the API
     */
    private function httpRequest($url, $curlOptions=array(), $timeout=60)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->wsUsername}:{$this->wsPassword}");

        foreach ($curlOptions as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $this->response = curl_exec($ch);
        $this->errorCode = curl_errno($ch);
        curl_close($ch);

        return $this->response;
    }

    /**
     * Prepare request according to the given method
     * @param string $endPoint End point URL which needs to be appended to main URL
     * @param array $params Request parameters
     * @param string $method Request method
     * @return string JSON response of the API
     */
    public function sendRequest($endPoint, $params, $method='GET')
    {
        $curlOptions = array();
        $requestUri = '';
        switch ($method) {
            case 'POST':
                $queryString = $this->buildQueryString($params);
                $pramCount = count(explode('&',$queryString));
                $curlOptions[CURLOPT_POST] = $pramCount;
                $curlOptions[CURLOPT_POSTFIELDS] = $queryString;
                $requestUri = $this->apiUrl . $endPoint;
                break;

            case 'GET':
                $queryString = $this->buildQueryString($params);
                $requestUri = $this->apiUrl . $endPoint . '?' . $queryString;
                break;

            case 'POST_JSON':
                $jsonParams = json_encode($params);
                $curlOptions[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonParams)
                );
                $curlOptions[CURLOPT_POSTFIELDS] = $jsonParams;
                $requestUri = $this->apiUrl . $endPoint;
                break;

            case 'DELETE':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = "DELETE";
                $requestUri = $this->apiUrl . $endPoint;
                break;
        }

        return $this->httpRequest($requestUri, $curlOptions);
    }

    /**
     * Build query string from array given
     * @param array $params Request parameters
     * @return string query params
     */
    private function buildQueryString($params)
    {
        $queryString = '';
        foreach ($params as $key => $value) {
            if (is_array($value) && 'multiple' == $value[0]) {
                $queryString .= "{$value[1]}&";
            } else {
                $queryString .= "{$key}={$value}&";
            }
        }

        return rtrim($queryString, '&');
    }
}