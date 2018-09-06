<?php

namespace app\modules\api\controllers\actions\property;


use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\models\Property;
use app\models\User;

class OnBehalfOfCreate extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;

        // Assign user object params
        $owner = new User();
        $owner->scenario = User::SCENARIO_API_ON_BH_CREATE;
        $owner->attributes = @$params['user'];
        $owner->status = User::ACTIVE;
        $owner->type = User::OWNER;
        $owner->timeZone = Yii::$app->params['defaultTimeZone'];
        $owner->isOnBhf = User::ON_BEHALF_YES;

        // Assign property object params
        $property = new Property();
        $property->code = $property->generatePropertyCode();
        $property->name = '-';
        $property->scenario = Property::SCENARIO_API_ON_BH_CREATE;
        $property->attributes = $params;
        $property->createdById = $user->id;
        $property->isOnBhf = Property::ON_BEHALF_YES;
        $property->status = Property::STATUS_AVAILABLE;
        $property->chargingCycle = Property::CS_MONTHLY;
        $property->paymentStatus = Property::PS_PENDING;
        $property->chargingAttemptCount = 0;

        if ($owner->validateModel()) {
            if ($property->validateModel()) {
                if ($property->createOnBehalfOfProperty($owner, $user)) {
                    $statusCode = ApiStatusMessages::SUCCESS;
                }
            } else {
                $statusCode = $property->statusCode;
                $statusMsg = $property->statusMessage;
            }
        } else {
            $statusCode = $owner->statusCode;
            $statusMsg = $owner->statusMessage;
        }

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>