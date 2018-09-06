<?php

namespace app\modules\api\controllers\actions\user;

use Yii;
use yii\base\Action;
use app\models\User;
use app\models\File;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class View extends Action
{
    public function run()
    {
        $userId = Yii::$app->request->get('id');
        $model = User::findOne($userId);
        $response = [];

        if (!empty($model)) {
            $fileList = File::getFileListByUserId($model->id);
            $response = Messages::user($model, $fileList);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>