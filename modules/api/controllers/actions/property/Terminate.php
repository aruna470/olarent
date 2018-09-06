<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Terminate extends Action
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
            if ($model->status == Property::STATUS_NOT_AVAILABLE) {
                if ($user->id == $model->ownerUserId || $user->id == $model->tenantUserId) {
                    if ($model->terminateProperty($user)) {
                        $statusCode = ApiStatusMessages::SUCCESS;
                    }
                } else {
                    $statusCode = ApiStatusMessages::PROPERTY_TERMINATE_NOT_ALLOWED;
                    Yii::$app->appLog->writeLog('Only owner or tenant can terminate the property');
                }
            } else {
                $statusCode = ApiStatusMessages::PROPERTY_TERMINATE_NOT_ALLOWED;
                Yii::$app->appLog->writeLog('Only rented properties can terminate');
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