<?php

namespace app\modules\api\controllers;

class ReviewRequestController extends ApiBaseController
{
    public function actions(){

        return array(
            'create' => 'app\modules\api\controllers\actions\reviewRequest\Create',
            'search' => 'app\modules\api\controllers\actions\reviewRequest\Search',
        );
    }
}
