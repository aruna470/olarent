<?php

namespace app\modules\api\controllers\actions\statistic;

use Yii;
use yii\base\Action;
use app\models\Payment;
use app\modules\api\components\Messages;


class OwnerDashboard extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $payment = new Payment();
        $curMonthSummary = $payment->getOwnerCurMonthPaymentSummary($user->id);
        $monthlyIncomeSummary = $payment->getMonthlyIncomeSummary($user->id);

        $response = Messages::statistic($curMonthSummary, $monthlyIncomeSummary);
        $this->controller->sendResponse($response);
    }
}
?>