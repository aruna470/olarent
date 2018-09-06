<?php

namespace app\modules\api\controllers\actions\userReview;

use app\models\UserReview;
use Yii;
use yii\base\Action;
use app\models\UserReviewSearch;
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
        $userReviewSearch = new UserReviewSearch();
        $userReviewSearch->scenario = UserReviewSearch::SCENARIO_API_SEARCH;

        $userReviewSearch->load(['UserReviewSearch' => Yii::$app->request->get()]);
        $userReviewSearch->userId = $user->id;

        if ($userReviewSearch->validateModel()) {
            $result = $userReviewSearch->apiSearch();
            $userReviews = $result['userReviews'];
            $total = $result['total'];
            $userRevList = [];
            if (!empty($userReviews)) {
                foreach ($userReviews as $userReview) {
                    $reviewedUser = $userReview->reviewedUser;
                    $reviewedUserDetails = !empty($reviewedUser) ? Messages::userMin($reviewedUser) : [];
                    $userRevList[] = Messages::userReview($userReview, $reviewedUserDetails);
                }
            }
            $response = Messages::searchResult($total, $userRevList);
        } else {
            $response = Messages::commonStatus($userReviewSearch->statusCode, $userReviewSearch->statusMessage);
        }

        $this->controller->sendResponse($response);
    }
}
?>