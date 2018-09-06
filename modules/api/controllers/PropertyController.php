<?php

namespace app\modules\api\controllers;

class PropertyController extends ApiBaseController
{
    public function actions(){

        return array(
            'create' => 'app\modules\api\controllers\actions\property\Create',
            'search' => 'app\modules\api\controllers\actions\property\Search',
            'view' => 'app\modules\api\controllers\actions\property\View',
            'update' => 'app\modules\api\controllers\actions\property\Update',
            'delete' => 'app\modules\api\controllers\actions\property\Delete',
            'payment-details' => 'app\modules\api\controllers\actions\property\PaymentDetails',
            'terminate' => 'app\modules\api\controllers\actions\property\Terminate',
            'pay-now' => 'app\modules\api\controllers\actions\property\PayNow',
            'due-payment' => 'app\modules\api\controllers\actions\property\DuePayment',
            'on-behalf-of-create' => 'app\modules\api\controllers\actions\property\OnBehalfOfCreate',
            'on-behalf-of-update' => 'app\modules\api\controllers\actions\property\OnBehalfOfUpdate',
            'public-view' => 'app\modules\api\controllers\actions\property\PublicView',
            'get-share-meta-data' => 'app\modules\api\controllers\actions\property\GetShareMetaData',
        );
    }
}
