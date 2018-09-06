<?php

namespace app\modules\api\controllers\actions\notification;

use Yii;
use yii\base\Action;
use app\models\NotificationSearch;
use app\models\Notification;
use app\modules\api\components\Messages;

class Search extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        Yii::$app->language = $user->language;
        $notificationSearch = new NotificationSearch();
        $notificationSearch->scenario = NotificationSearch::SCENARIO_API_SEARCH;
        $notificationSearch->load(['NotificationSearch' => array_merge(Yii::$app->request->get(), ['userId' => $user->id])]);
        if ($notificationSearch->validate()) {
            $result = $notificationSearch->apiSearch();
            $notifications = $result['notifications'];
            $total = $result['total'];
            $notiList = [];
            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    $message = $notification->getMessageByCode($notification->messageCode, json_decode($notification->params));
                    $notiList[] = Messages::notification($notification, $message);
                }
            }

            $response = Messages::searchResult($total, $notiList);

        } else {
            $errors = $notificationSearch->getLastError();
            $statusCode = $errors['message'];
            $statusMsg = $errors['attribute'];
            $response = Messages::commonStatus($statusCode, $statusMsg);
        }

        $this->controller->sendResponse($response);
    }
}
?>