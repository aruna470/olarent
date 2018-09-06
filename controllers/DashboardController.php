<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Payment;

/**
 * PropertyController implements the CRUD actions for Property model.
 */
class DashboardController extends BaseController
{
    public function behaviors()
    {
        return [

        ];
    }

    /**
     * Dashboard.
     * @return mixed
     */
    public function actionDashboard()
    {
//        echo date_default_timezone_get();
//        echo gmdate('Y-m-d', strtotime('+31 days', strtotime('2016-01')));

        $user = new User();
        $payment = new Payment();

        $regCounts = $user->getRegCountsByDate();
        $totPayments = $payment->getTotPaymentsByDate();

        return $this->render('dashboard', [
            'regCounts' => json_encode($regCounts),
            'totPayments' => json_encode($totPayments)
        ]);
    }

}
