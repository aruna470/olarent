<?php

namespace app\modules\api\controllers\actions\user;

use app\models\File;
use app\models\User;
use app\components\Mail;
use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $userFiles = @$params['files'];
        $mail = new Mail();
        $model = new User();
        $model->scenario = User::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $model->status = User::ACTIVE;
        $model->timeZone = Yii::$app->params['defaultTimeZone'];
        $model->isOnBhf = User::ON_BEHALF_NO;
        if (null != $model->password) {
            $model->formPassword = $model->password;
            $model->password = $model->encryptPassword($model->formPassword);
        }

        if ($model->isAnySignupParamExists()) {
            $transaction = Yii::$app->db->beginTransaction();
            $isAllSuccess = true;
            $getProfPic = $model->getProfPic();
            $model->profileImage = @$getProfPic['profileImage'];
            $model->profileImageThumb = @$getProfPic['profileImageThumb'];

            if ($model->saveModel()) {
                // Add files
                $isAllSuccess = File::addFiles($userFiles, $model->id);
                if ($isAllSuccess) {
                    $transaction->commit();
                    $statusCode = ApiStatusMessages::SUCCESS;
                    $mail->language = $model->language;
                    $mail->sendSignupEmail($model->email, $model->getFullName());
                    Yii::$app->appLog->writeLog('All success. Transaction commit.');
                } else {
                    $transaction->rollBack();
                    Yii::$app->appLog->writeLog('Some transactions failed. Transaction rollback.');
                }
            }
        } else {
            $statusCode = ApiStatusMessages::MISSING_MANDATORY_FIELD;
            $statusMsg = 'Missing social login id or email/password combination';
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>