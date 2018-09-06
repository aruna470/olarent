<?php

namespace app\modules\api\controllers\actions\propertyRequest;

use Yii;
use yii\base\Action;
use app\models\PropertyRequest;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class View extends Action
{
    public function run()
    {
        $propertyRequestId = Yii::$app->request->get('id');
        $user = $this->controller->user;

        $tenantUserId = '';
        $ownerUserId = '';

        if (User::TENANT == $user->type) {
            $tenantUserId = $user->id;
        } else {
            $ownerUserId  = $user->id;
        }

        $model = PropertyRequest::find()
            ->andFilterWhere(['ownerUserId' => $ownerUserId])
            ->andFilterWhere(['tenantUserId' => $tenantUserId])
            ->andWhere('id = :id', [':id' => $propertyRequestId])
            ->with(['ownerUser', 'tenantUser', 'property'])
            ->one();

        $response = [];

        if (!empty($model)) {
            $owner = $model->ownerUser;
            $tenant = $model->tenantUser;
            $property = $model->property;
            $ownerDetails = !empty($owner) ? Messages::user($owner, []) : [];
            $tenantDetails = !empty($tenant) ? Messages::user($tenant, []) : [];
            $propertyDetails = !empty($property) ? Messages::property($property,[],[]) : [];
            $response = Messages::propertyRequest($model, $ownerDetails, $tenantDetails, $propertyDetails);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists or not allowed');
        }

        $this->controller->sendResponse($response);
    }
}
?>