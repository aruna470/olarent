<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class View extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $propertyId = Yii::$app->request->get('id');
        $model = Property::findOne($propertyId);
        $response = [];

        if (!empty($model)) {
            $user = User::findOne($model->ownerUserId);
            $tenant = User::findOne($model->tenantUserId);
            $userDetails = !empty($user) ? Messages::userMin($user, ['bankName', 'bankAccountName', 'iban', 'swift']) : [];
            $tenantDetails = !empty($tenant) ? Messages::userMin($tenant) : [];
            $extraParams['isEditable'] = $model->isEditable();
            $extraParams['imageList'] = true;
            if ($model->nextChargingDate != null) {
                $nextChargingDate = Yii::$app->util->getLocalDateTime($model->nextChargingDate, $user->timeZone);
                $extraParams['paymentDueAt'] = date('Y-m-d', strtotime($nextChargingDate));
            }
            $response = Messages::property($model, $userDetails, $tenantDetails, $extraParams);
        } else {
            $response = Messages::commonStatus(ApiStatusMessages::RECORD_NOT_EXISTS, null);
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        $this->controller->sendResponse($response);
    }
}
?>