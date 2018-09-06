<?php

namespace app\modules\api\controllers\actions\userReview;


use Yii;
use yii\base\Action;
use app\components\Mail;
use app\models\Notification;
use app\models\User;
use app\models\ReviewRequest;
use app\models\UserReview;
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
        $allSuc = true;

        $reviewRequest = new ReviewRequest();
        $notification = new Notification();
        $mail = new Mail();
        $model = new UserReview();
        $model->scenario = UserReview::SCENARIO_API_CREATE;

        $model->attributes = $params;
        $model->reviewedUserId = $user->id;
        $model->reviewRequestId = @$params['reviewRequestId'];

        $transaction = Yii::$app->db->beginTransaction();

        if ($model->saveModel()) {
            $receiver = User::findOne($model->userId);

            // Mark review request as reviewed
            $allSuc = $reviewRequest->updateStatus($model->reviewRequestId, ReviewRequest::STATUS_REVIEWED);

            // Update user rating
            $allSuc = $receiver->updateRating($receiver->id);

            if ($allSuc) {
                $statusCode = ApiStatusMessages::SUCCESS;
                $transaction->commit();
                Yii::$app->appLog->writeLog('All success. Transaction commit.');

                // Add notification
                $notification->addNotification(Notification::RCV_REVIEW_FB, $receiver->id,
                    ['senderName' => $user->getFullName()]);
                // Send email notification
                $mail->language = $receiver->language;
                $mail->sendReviewFeedbackNotification($receiver->email, $user->getFullName(), $receiver->getFullName());
            } else {
                $transaction->rollBack();
                Yii::$app->appLog->writeLog('Some transactions failed. Transaction rollback.');
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>