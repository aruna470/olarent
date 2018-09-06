<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Delete extends Action
{
    public function run()
    {
        $propertyId = Yii::$app->request->get('id');
        $user = $this->controller->user;

        $model = Property::find()->where('id = :id AND ownerUserId = :ownerUserId', [':id' => $propertyId,
            ':ownerUserId' => $user->id])->one();

        $response = [];
        $code = ApiStatusMessages::SUCCESS;

        if (!empty($model)) {
            if ($model->status != Property::STATUS_NOT_AVAILABLE) {
                if (!$model->deleteModel()) {
                    $code = ApiStatusMessages::FAILED;
                }
            } else {
                $code = ApiStatusMessages::PROPERTY_IN_USE;
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