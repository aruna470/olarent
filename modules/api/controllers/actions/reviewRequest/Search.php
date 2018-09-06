<?php

namespace app\modules\api\controllers\actions\reviewRequest;

use Yii;
use yii\base\Action;
use app\models\ReviewRequestSearch;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class Search extends Action
{
    public function run()
    {
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $user = $this->controller->user;
        $reviewRequestSearch = new ReviewRequestSearch();
        $reviewRequestSearch->scenario = ReviewRequestSearch::SCENARIO_API_SEARCH;

        $reviewRequestSearch->load(['ReviewRequestSearch' => Yii::$app->request->get()]);
        $reviewRequestSearch->receiverUserId = $user->id;
        $reviewRequestSearch->status = ReviewRequestSearch::STATUS_PENDING;

        if ($reviewRequestSearch->validateModel()) {
            $result = $reviewRequestSearch->apiSearch();
            $reviewRequests = $result['reviewRequests'];
            $total = $result['total'];
            $reviewReqList = [];
            if (!empty($reviewRequests)) {
                foreach ($reviewRequests as $reviewRequest) {
                    $requester = $reviewRequest->requesterUser;
                    $receiver = $reviewRequest->receiverUser;
                    $requesterDetails = !empty($requester) ? Messages::userMin($requester) : [];
                    $receiverDetails = !empty($receiver) ? Messages::userMin($receiver) : [];
                    $reviewReqList[] = Messages::reviewRequest($reviewRequest, $requesterDetails, $receiverDetails);
                }
            }
            $response = Messages::searchResult($total, $reviewReqList);
        } else {
            $response = Messages::commonStatus($reviewRequestSearch->statusCode, $reviewRequestSearch->statusMessage);
        }

        $this->controller->sendResponse($response);
    }
}
?>