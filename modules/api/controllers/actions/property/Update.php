<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Update extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $propertyId = Yii::$app->request->get('id');
        $model = Property::find()->where('id = :id AND ownerUserId = :ownerUserId', [':id' => $propertyId,
            ':ownerUserId' => $user->id])->one();
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        if (!empty($model)) {
            if ($model->isEditable()) {
                $model->scenario = Property::SCENARIO_API_UPDATE;
                $model->attributes = $params;
                $model->ownerUserId = $user->id;
                $model->updatedById = $user->id;
                if (!empty($params['images'])) {
                    $model->images = json_encode(@$params['images']);
                }

                if ($model->saveModel()) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                }
            } else {
                $statusCode = ApiStatusMessages::PROPERTY_UPDATE_NOT_ALLOWED;
            }
        } else {
            Yii::$app->appLog->writeLog('Record not exists or update not allowed');
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>