<?php

namespace app\modules\api\controllers\actions\reviewRequest;

use Yii;
use yii\base\Action;
use app\models\ReviewRequest;
use app\components\Mail;
use app\models\Notification;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $notification = new Notification();
        $mail = new Mail();
        $model = new ReviewRequest();
        $model->scenario = ReviewRequest::SCENARIO_API_CREATE;

        $model->attributes = $params;
        $model->requesterUserId = $user->id;
        $model->status = ReviewRequest::STATUS_PENDING;

        if ($model->saveModel()) {
            $statusCode = ApiStatusMessages::SUCCESS;
            $receiver = User::findOne($model->receiverUserId);

            // Add notification
            $notification->addNotification(Notification::RCV_REVIEW_REQ, $receiver->id,
                ['senderName' => $user->getFullName()]);

            // Send email notification
            $mail->language = $receiver->language;
            $mail->sendReviewReqNotification($receiver->email, $user->getFullName(), $receiver->getFullName());
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>