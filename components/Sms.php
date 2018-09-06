<?php

namespace app\components;

use Yii;
use yii\base\Component;

class Sms extends Component
{
    public $apiUrl = 'https://api.clickatell.com/rest/message';
    public $token;

    public function __construct($config)
    {
        $this->token = $config['clickatel']['token'];
    }

    /**
     * Send mobile number verification SMS
     * @param string $to Recipient number
     * @param integer $verificationCode Verification code
     * @return boolean
     */
    public function sendNumberVerifySms($to, $verificationCode)
    {
        $text = Yii::t('app', 'Your verification code for OlaRent is:{code}', ['code' => $verificationCode]);
        $res = $this->send(['to' => [$to], 'text' => $text]);
        $res = json_decode($res);


        if ($res->data->message[0]->accepted == '1') {
            return true;
        }

       return false;
    }

    /**
     * Send SMS
     * @param array $params Values to be submitted
     * @return mixed API response
     */
    public function send($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Version: 1',
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $result = curl_exec($ch);
        $erroNo = curl_errno($ch);
        curl_close($ch);

        Yii::$app->appLog->writeLog('SMS gateway response.', ['response' => json_decode($result, true)]);

        return $result;
    }
}