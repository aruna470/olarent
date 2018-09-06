<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\UserMpInfo;
use app\models\UserMpInfoFile;

class CreateFile extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $model = new UserMpInfoFile();
        $model->scenario = UserMpInfoFile::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $extraParams = [];

        $userMpInfo = new UserMpInfo();

        $model->userId = $user->id;
        $userMpInfo = $userMpInfo->getUserMpInfo($model->userId);
        $model->userMpInfoId = $userMpInfo->id;

        if ($model->validateModel()) {
            if ($model->createFile($userMpInfo)) {
                $statusCode = ApiStatusMessages::SUCCESS;
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg, $extraParams);
        $this->controller->sendResponse($response);
    }
}
?>