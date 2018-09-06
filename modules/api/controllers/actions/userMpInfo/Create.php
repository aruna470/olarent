<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\UserMpInfo;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $model = new UserMpInfo();
        $model->scenario = UserMpInfo::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $extraParams = [];

        $model->userId = $user->id;
        $userMpInfo = $model->getUserMpInfo($model->userId);

        if (empty($userMpInfo)) {
            if ($model->validateModel()) {
                if ($model->createMpAccounts(Yii::$app->params['defCurrency'])) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                    $extraParams = ['userMpInfoId' => $model->id];
                } else {
                    if ($model->mpErrorField == 'IBAN') {
                        $statusCode = ApiStatusMessages::INVALID_IBAN;
                    }
                }
            }
        } else {
            $statusCode = ApiStatusMessages::RECORD_EXISTS;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg, $extraParams);
        $this->controller->sendResponse($response);
    }
}
?>