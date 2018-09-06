<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class PayNow extends Action
{
    public function run()
    {
        $propertyId = Yii::$app->request->get('id');
        $user = $this->controller->user;
        $model = Property::findOne($propertyId);
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $response = [];

        if (!empty($model)) {
            if ($user->id == $model->tenantUserId && $model->isPayNow()) {
                if ($model->payNow($user)) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                }
            } else {
                $statusCode = ApiStatusMessages::PAY_NOW_NOT_ALLOWED;
                Yii::$app->appLog->writeLog('Paynow not allowed. Invalid user or not met pay now conditions.');
            }
        } else {
            $statusCode = ApiStatusMessages::RECORD_NOT_EXISTS;
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>