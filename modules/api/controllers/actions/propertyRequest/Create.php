<?php

namespace app\modules\api\controllers\actions\propertyRequest;

use app\models\Notification;
use Yii;
use yii\base\Action;
use app\models\PropertyRequest;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;
use app\components\Mail;
use app\modules\api\components\ApiStatusMessages;

class Create extends Action
{
    public function run()
    {
        $params = json_decode(Yii::$app->request->rawBody, true);
        $user = $this->controller->user;

        $model = new PropertyRequest();
        $mail = new Mail();

        $notification = new Notification();

        $model->scenario = PropertyRequest::SCENARIO_API_CREATE;
        $model->attributes = $params;

        $statusCode = ApiStatusMessages::SUCCESS;
        $statusMsg = null;

        $model->status = PropertyRequest::STATUS_PENDING;

        if ($model->validateModel(['code'])) {
            $property = Property::find()->where('code = :code', [':code' => $model->code])->one();
            if (!empty($property)) {
                $model->propertyId = $property->id;
                $model->ownerUserId = $property->ownerUserId;
                $model->tenantUserId = $user->id;

                if ($property->status == Property::STATUS_AVAILABLE) {
                    if ($model->saveModel()) {
                        $owner = User::findOne($property->ownerUserId);
                        $tenant = User::findOne($model->tenantUserId);

                        // Add notification
                        $notification->addNotification(Notification::OWNER_RCV_PROP_REQ, $owner->id,
                            ['tenantName' => $user->getFullName(), 'code' => $property->code]);

                        // Send email notification
                        $mail->language = $owner->language;
                        $mail->sendPropReqNotification($owner->email, $owner->getFullName(),
                            $tenant->getFullName(),$property->code);

                        $mail->sendPropReqNotificationTenant($tenant->email, $owner->getFullName(),
                            $tenant->getFullName(),$property->code);

                    } else {
                        $statusCode = ApiStatusMessages::FAILED;
                    }
                } else {
                    Yii::$app->appLog->writeLog('Property not available.');
                    $statusCode = ApiStatusMessages::PROPERTY_NOT_AVAILABLE;
                }
            } else {
                Yii::$app->appLog->writeLog('Invalid property code.');
                $statusCode = ApiStatusMessages::INVALID_PROPERTY_CODE;
            }
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg);
        $this->controller->sendResponse($response);
    }
}
?>