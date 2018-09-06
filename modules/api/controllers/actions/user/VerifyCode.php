<?php

namespace app\modules\api\controllers\actions\user;


use Yii;
use yii\base\Action;
use app\models\Verification;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class VerifyCode extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $model = new Verification();
        $model->scenario = Verification::SCENARIO_API_VERIFY;
        $model->attributes = $params;

        if ($model->validateModel()) {
            $verification = $model->getModelByPhone($model->phoneNumber);
            if (!empty($verification)) {
                if ($model->verificationCode == $verification->verificationCode) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                    Yii::$app->appLog->writeLog('Verification success');
                } else {
                    Yii::$app->appLog->writeLog('Verification failed');
                }
            } else {
                Yii::$app->appLog->writeLog('Record not exists');
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>