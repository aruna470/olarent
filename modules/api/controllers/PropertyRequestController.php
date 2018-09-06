<?php

namespace app\modules\api\controllers;

class PropertyRequestController extends ApiBaseController
{
    public function actions(){

        return array(
            'create' => 'app\modules\api\controllers\actions\propertyRequest\Create',
            'search' => 'app\modules\api\controllers\actions\propertyRequest\Search',
            'view' => 'app\modules\api\controllers\actions\propertyRequest\View',
            'accept' => 'app\modules\api\controllers\actions\propertyRequest\Accept',
            'reject' => 'app\modules\api\controllers\actions\propertyRequest\Reject',
            'delete' => 'app\modules\api\controllers\actions\propertyRequest\Delete',
        );
    }
}
