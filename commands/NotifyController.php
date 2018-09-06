<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\PropertyRequest;
use app\models\NotificationQueue;
use app\models\Notification;
use app\components\Mail;

/*
 * This command send queued notifications to intended user
 * Command runs on each minute
 */

class NotifyController extends Controller
{
    public $notificationQueue;
    public $mail;
    public $notification;

    public function actionNotify()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        Yii::$app->appLog->writeLog('Start');

        $this->notificationQueue = new NotificationQueue();
        $this->mail = new Mail();
        $this->notification = new Notification();

        $notifyQueues = $this->notificationQueue->getPendingQueueList();
        if (!empty($notifyQueues)) {
            foreach ($notifyQueues as $notifyQueue) {
                $notifyQueue->updateQueue($notifyQueue->id, NotificationQueue::STATUS_IN_PROGRESS);
                switch ($notifyQueue->type) {
                    case NotificationQueue::TYPE_ASSIGN_ANOTHER:
                        $this->propertyAssignNotify($notifyQueue);
                        break;
                }
                $notifyQueue->updateQueue($notifyQueue->id, NotificationQueue::STATUS_COMPLETED);
            }
        } else {
            Yii::$app->appLog->writeLog('Queue is empty');
        }

        Yii::$app->appLog->writeLog('Stop');
    }

    /**
     * Send notification to those who request for same property but it is given to another
     *  - Send email notification
     *  - Mark particular property request as rejected
     * @param NotificationQueue $notifyQueue
     */
    private function propertyAssignNotify($notifyQueue)
    {
        Yii::$app->appLog->writeLog('Sending property assign notification');

        $data = json_decode($notifyQueue->data);
        $propertyRequest = new PropertyRequest();
        $propertyRequests = $propertyRequest->getPendingPropertyRequests($data->propertyId);

        if (!empty($propertyRequests)) {
            foreach ($propertyRequests as $propReq) {
                $propReq->status = PropertyRequest::STATUS_REJECTED;
                if ($propReq->saveModel()) {
                    $logParams = ['email' => $propReq->tenantUser->email, 'tenantUserId' => $propReq->tenantUser->id,
                        'ownerUserId' => $propReq->ownerUser->id];

                    Yii::$app->appLog->writeLog('Sending property assign email notification.', $logParams);

                    $emailStatus = $this->mail->sendPropRejectNotificationTenant($propReq->tenantUser->email, $propReq->ownerUser->getFullName(),
                        $propReq->tenantUser->getFullName(), $propReq->property->code);

                    // Add notification
                    $this->notification->addNotification(Notification::OWNER_REJ_PROP_REQ, $propReq->tenantUser->id,
                        ['ownerName' => $propReq->ownerUser->getFullName(), 'code' => $propReq->property->code]);
                }
            }
        } else {
            Yii::$app->appLog->writeLog('No more property requests.');
        }
    }
}
