<?php

namespace app\modules\api\controllers\actions\util;


use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class AdyenMerchantSig extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);

        $hmacKey = Yii::$app->params['adyen']['hmacKey'];
        $merchantSig = base64_encode(hash_hmac('sha256', @$params['sigString'], pack("H*" , $hmacKey),true));
        $response = Messages::adyenMerchantSig($merchantSig);

        $this->controller->sendResponse($response);
    }
}
?>