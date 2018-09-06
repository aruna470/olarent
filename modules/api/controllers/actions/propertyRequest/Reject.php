<?php

namespace app\modules\api\controllers\actions\propertyRequest;

use Yii;
use yii\base\Action;
use app\models\PropertyRequest;
use app\models\Property;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Reject extends Action
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
            if (!$model->reject()) {
                $code = ApiStatusMessages::FAILED;
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