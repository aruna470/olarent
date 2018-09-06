<?php

namespace app\modules\api\controllers\actions\user;

use Yii;
use yii\base\Action;
use app\models\User;
use app\models\File;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class Update extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $userFiles = @$params['files'];
        $userId = $user->id;
        $model = User::findOne($userId);
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        if (!empty($model) && Yii::$app->request->get('id') == $userId) {
            $model->scenario = User::SCENARIO_API_UPDATE;
            $model->attributes = $params;

            if ('' != @$params['password']) {
                $model->formPassword = $model->password;
                $model->password = $model->encryptPassword($model->formPassword);
            }

            $transaction = Yii::$app->db->beginTransaction();
            $isAllSuccess = true;

            if ($model->saveModel()) {
                // Delete all files of particular user and add new files
                if (isset($params['files'])) {
                    File::deleteAll('userId = :userId', [':userId' => $model->id]);
                }
                // Add files
                $isAllSuccess = File::addFiles($userFiles, $model->id);

                if ($isAllSuccess) {
                    $transaction->commit();
                    $statusCode = ApiStatusMessages::SUCCESS;
                    Yii::$app->appLog->writeLog('All success. Transaction commit.');
                } else {
                    $transaction->rollBack();
                    Yii::$app->appLog->writeLog('Some transactions failed. Transaction rollback.');
                }
            }
        } else {
            Yii::$app->appLog->writeLog('Record not exists or not allowed');
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>