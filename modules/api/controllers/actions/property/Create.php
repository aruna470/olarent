<?php

namespace app\modules\api\controllers\actions\property;

use app\models\Property;
use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $model = new Property();
        $model->scenario = Property::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $model->code = $model->generatePropertyCode();
        $model->ownerUserId = $user->id;
        $model->createdById = $user->id;
        $model->status = Property::STATUS_AVAILABLE;
        $model->chargingCycle = Property::CS_MONTHLY;
        $model->paymentStatus = Property::PS_PENDING;
        $model->isOnBhf = Property::ON_BEHALF_NO;
        $model->chargingAttemptCount = 0;
        if (null != @$params['images']) {
            $model->images = json_encode($params['images']);
        }

        if ($model->saveModel()) {
            $statusCode = ApiStatusMessages::SUCCESS;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>