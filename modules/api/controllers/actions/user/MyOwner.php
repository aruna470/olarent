<?php

namespace app\modules\api\controllers\actions\user;

use Yii;
use yii\base\Action;
use app\models\User;
use app\models\ReviewRequest;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class MyOwner extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $reviewRequest = new ReviewRequest();

        $response = [];

        if (!empty($user)) {
            $myOwners = $user->getMyOwners($user->id);
            $ownerList = [];
            if (!empty($myOwners)) {
                foreach ($myOwners as $myOwner) {
                    $myOwner->isRequestedForReview = User::R4R_NO;
                    if ($reviewRequest->isAlreadyRequested($user->id, $myOwner->id)) {
                        $myOwner->isRequestedForReview = User::R4R_YES;
                    }
                    $ownerList[] = Messages::userMin($myOwner);
                }
            }
            $response = Messages::searchResult(count($ownerList), $ownerList);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>