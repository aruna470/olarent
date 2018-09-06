<?php

namespace app\modules\api\controllers;

class UserReviewController extends ApiBaseController
{
    public function actions(){
        return array(
            'create' => 'app\modules\api\controllers\actions\userReview\Create',
            'search' => 'app\modules\api\controllers\actions\userReview\Search',
        );
    }
}
