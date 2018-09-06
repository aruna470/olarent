<?php

namespace app\modules\api\controllers\actions\user;


use Yii;
use yii\base\Action;
use app\models\User;
use app\models\Verification;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class sendVerifyCode extends Action
{
    public function run()
    {
        $user = new User();
        $params = json_decode(Yii::$app->request->rawBody, true);
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $user = $user->getUserByPhone(@$params['phoneNumber']);

        if (empty($user)) {
            $verification = new Verification();
            $model = $verification->getModelByPhone(@$params['phoneNumber']);
            if (empty($model)) {
                $model = new Verification();
            }
            $model->scenario = Verification::SCENARIO_API_CREATE;
            $model->phoneNumber = @$params['phoneNumber'];

            if ($model->sendVerificationSms()) {
                $statusCode = ApiStatusMessages::SUCCESS;
            }
        } else {
            $statusCode = ApiStatusMessages::PHONE_EXISTS;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>