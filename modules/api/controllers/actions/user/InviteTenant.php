<?php

namespace app\modules\api\controllers\actions\user;

use Yii;
use yii\base\Action;
use app\models\User;
use app\components\Mail;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class InviteTenant extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $model = new User();
        $mail = new Mail();
        $model->scenario = User::SCENARIO_API_INVITE_TENANT;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $model->attributes = $params;

        if ($model->validateModel()) {
            $message = Yii::$app->util->convertTextUrlsToLinks($model->message);
            $mail->language = $user->language;
            $emailStatus = $mail->inviteTenant($model->email, $message, $user->getFullName());
            if ($emailStatus) {
                $statusCode = ApiStatusMessages::SUCCESS;
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>