<?php

namespace app\modules\api\controllers\actions\propertyRequest;

use Yii;
use yii\base\Action;
use app\models\PropertyRequest;
use app\models\Property;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Accept extends Action
{
    public function run()
    {
        $code = ApiStatusMessages::SUCCESS;
        $propertyRequestId = Yii::$app->request->get('id');
        $user = $this->controller->user;

        $model = PropertyRequest::find()
            ->andWhere('id = :id', [':id' => $propertyRequestId])
            ->andWhere('ownerUserId = :ownerUserId', [':ownerUserId' => $user->id])
            ->with(['ownerUser', 'tenantUser', 'property'])
            ->one();

        $response = [];

        if (!empty($model)) {
            if ($model->property->status == Property::STATUS_NOT_AVAILABLE) {
                $code = ApiStatusMessages::PROPERTY_NOT_AVAILABLE;
                Yii::$app->appLog->writeLog('Property already rented out');
            } else {
                if (!$model->accept()) {
                    if ($model->customErrorCode == PropertyRequest::CHARGING_FAILED) {
                        $code = ApiStatusMessages::CHARGING_FAILED;
                    } else {
                        $code = ApiStatusMessages::FAILED;
                    }
                }
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