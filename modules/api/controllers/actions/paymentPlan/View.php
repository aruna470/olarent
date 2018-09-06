<?php

namespace app\modules\api\controllers\actions\paymentPlan;

use Yii;
use yii\base\Action;
use app\models\PaymentPlan;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class View extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $paymentPlan = new PaymentPlan();
        $model = $paymentPlan->getPaymentPlanByUserId($user->id);
        $response = [];

        if (!empty($model)) {
            $response = Messages::paymentPlan($model);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>