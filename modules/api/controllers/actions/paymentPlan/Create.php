<?php

namespace app\modules\api\controllers\actions\paymentPlan;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\models\PaymentPlan;
use app\modules\api\components\ApiStatusMessages;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $model = new PaymentPlan();
        $model->scenario = PaymentPlan::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $model->userId = $user->id;
        if ($model->createPlan($user->email)) {
            $statusCode = ApiStatusMessages::SUCCESS;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>