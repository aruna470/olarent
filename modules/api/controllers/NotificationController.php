<?php

namespace app\modules\api\controllers;

class NotificationController extends ApiBaseController
{
    public function actions(){
        return array(
            'search' => 'app\modules\api\controllers\actions\notification\Search',
            'update' => 'app\modules\api\controllers\actions\notification\Update'
        );
    }
}
