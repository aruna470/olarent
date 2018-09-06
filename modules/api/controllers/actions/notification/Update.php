<?php

namespace app\modules\api\controllers\actions\notification;

use Yii;
use yii\base\Action;
use app\models\Notification;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Update extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $notificationId = Yii::$app->request->get('id');
        $model = Notification::findOne($notificationId);
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        if (!empty($model) && $model->userId == $user->id) {
            $model->scenario = Notification::SCENARIO_API_UPDATE;
            $model->attributes = $params;
            if ($model->saveModel()) {
                $statusCode = ApiStatusMessages::SUCCESS;
            }
        } else {
            $statusCode = ApiStatusMessages::RECORD_NOT_EXISTS;
            Yii::$app->appLog->writeLog('Record not exist or not allowed.');
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>