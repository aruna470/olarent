<?php

namespace app\modules\api\controllers\actions\user;

use Yii;
use yii\base\Action;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Authenticate extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);

        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $token = null;
        $userData = [];

        $model = new User();
        $model->scenario = User::SCENARIO_API_AUTH;
        $model->attributes = $params;

        if ($model->validateModel()) {
            // Validate user according to login type
            $model = $model->authUser();
            if ($model) {
                $token = $model->id . '-' . uniqid();
                $model->userToken = $token;
                $model->lastAccess = Yii::$app->util->getUtcDateTime();
                if ($model->saveModel()) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                    $userData = Messages::user($model, []);
                }
            } else {
                Yii::$app->appLog->writeLog('Invalid password or invalid user type');
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $commonResponse = Messages::commonStatus($statusCode, $statusMsg);
        $response = Messages::authenticationResponse($commonResponse, $token, $userData);

        $this->controller->sendResponse($response);
    }
}
?>