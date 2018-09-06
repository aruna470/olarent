<?php

namespace app\modules\api\controllers\actions\property;

use app\models\Property;
use Yii;
use yii\base\Action;
use app\models\PropertySearch;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class DuePayment extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $response = [];

        $propertySearch = new PropertySearch();
        $propertySearch->tenantUserId = $user->id;
        $propertySearch->isOnBhf = Yii::$app->request->get('isOnBhf');

        $result = $propertySearch->getPaymentDueProperties();
        $properties = $result['properties'];
        $total = $result['total'];
        $propList = [];
        if (!empty($properties)) {
            foreach ($properties as $property) {
                $owner = $property->ownerUser;
                $tenant = $property->tenantUser;
                $ownerDetails = !empty($owner) ? Messages::userMin($owner) : [];
                $tenantDetails = !empty($tenant) ? Messages::userMin($tenant) : [];

                $payNowEnable = Property::ENB_PAY_NOW;
                if ($property->paymentStatus == Property::PS_FAILED && $property->reachMaxAttempts == Property::REACH_MAX_ATT_YES) {
                    $pendingPaymentInfo = $property->getPendingPaymentInfo($property, $tenant->timeZone, $property->payDay);
                    $totalDue = ($pendingPaymentInfo['paymentDueMonthCnt'] * $property->cost);
                    $payNowEnable = Property::ENB_PAY_NOW;
                } else {
                    $totalDue = 0;
                    $payNowEnable = Property::DISB_PAY_NOW;
                }

                $propList[] = Messages::property($property, $ownerDetails, $tenantDetails,
                    ['totalPendingPayments' => $totalDue, 'payDay' => $property->payDay, 'payNowEnable' => $payNowEnable]);
            }
        }
        $response = Messages::searchResult($total, $propList);

        $this->controller->sendResponse($response);
    }
}
?>