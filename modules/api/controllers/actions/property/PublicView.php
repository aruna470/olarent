<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class PublicView extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $propertyId = Yii::$app->request->get('id');
        $model = Property::findOne($propertyId);
        $response = [];

        if (!empty($model)) {
            $user = User::findOne($model->ownerUserId);
            $userDetails = !empty($user) ? Messages::userMin($user) : [];
            $extraParams['isEditable'] = $model->isEditable();
            $extraParams['imageList'] = true;
            $response = Messages::property($model, $userDetails, [], $extraParams);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>