<?php

namespace app\modules\api\controllers\actions\paymentPlan;

use Yii;
use yii\base\Action;
use app\models\PaymentPlan;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Delete extends Action
{
    public function run()
    {
        $planId = Yii::$app->request->get('id');
        $user = $this->controller->user;
        $paymentPlan = new PaymentPlan();

        $model = $paymentPlan->getPaymentPlan($user->id, $planId);

        $response = [];
        $code = ApiStatusMessages::FAILED;

        if (!empty($model)) {
            if ($model->deletePlan()) {
                $code = ApiStatusMessages::SUCCESS;
            }
        } else {
            $code = ApiStatusMessages::RECORD_NOT_EXISTS;
            Yii::$app->appLog->writeLog('Record not exists or not allowed');
        }

        $response = Messages::commonStatus($code, null);
        $this->controller->sendResponse($response);
    }
}
?>