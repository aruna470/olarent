<?php

namespace app\modules\api\controllers;

class PaymentPlanController extends ApiBaseController
{
    public function actions(){
        return array(
            'create' => 'app\modules\api\controllers\actions\paymentPlan\Create',
            'view' => 'app\modules\api\controllers\actions\paymentPlan\View',
            'delete' => 'app\modules\api\controllers\actions\paymentPlan\Delete'
        );
    }
}