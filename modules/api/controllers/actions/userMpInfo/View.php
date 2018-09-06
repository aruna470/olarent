<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\UserMpInfo;


class View extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $userId = $user->id;
        $model = new UserMpInfo();
        $model = $model->getUserMpInfo($userId);
        $response = [];

        if (!empty($model)) {
            $response = Messages::userMpInfo($model);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>