<?php

namespace app\modules\api\controllers\actions\property;


use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\Property;
use app\models\User;

class OnBehalfOfUpdate extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;
        $property = new Property();
        $propertyId = Yii::$app->request->get('id');

        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        $property = $property->getOnBehalfProperty($propertyId, $user->id);

        if (!empty($property)) {
            $owner = $user->getUserById($property->ownerUserId);
            if (!empty($owner)) {
                $owner->scenario = User::SCENARIO_API_ON_BH_UPDATE;
                $owner->attributes = $params['user'];
                if ($owner->saveModel()) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                } else {
                    $statusCode = $owner->statusCode;
                    $statusMsg = $owner->statusMessage;
                }
            } else {
                Yii::$app->appLog->writeLog('User record not exists');
                $statusCode = ApiStatusMessages::RECORD_NOT_EXISTS;
            }
        } else {
            Yii::$app->appLog->writeLog('Property record not exists');
            $statusCode = ApiStatusMessages::RECORD_NOT_EXISTS;
        }

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>