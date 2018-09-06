<?php

namespace app\modules\api\controllers;

class StatisticController extends ApiBaseController
{
    public function actions(){
        return array(
            'owner-dashboard' => 'app\modules\api\controllers\actions\statistic\OwnerDashboard',
        );
    }
}