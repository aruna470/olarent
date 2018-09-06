<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\UserMpInfo;

class Update extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $userMpInfoId = Yii::$app->request->get('id');
        $user = $this->controller->user;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $extraParams = [];

        $model = new UserMpInfo();
        $model = $model->getUserMpInfoById($userMpInfoId);

        if (!empty($model)) {
            $model->scenario = UserMpInfo::SCENARIO_API_UPDATE;
            $oldAttributes = $model->attributes;
            $model->attributes = $params;

            if ($model->validateModel()) {
                if ($model->updateMpAccounts($oldAttributes)) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                    $extraParams = ['userMpInfoId' => $model->id];
                } else {
                    if ($model->mpErrorField == 'IBAN') {
                        $statusCode = ApiStatusMessages::INVALID_IBAN;
                    }
                }
            }
        } else {
            $statusCode = ApiStatusMessages::RECORD_NOT_EXISTS;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg, $extraParams);
        $this->controller->sendResponse($response);
    }
}
?>