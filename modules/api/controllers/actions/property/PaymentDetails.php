<?php

namespace app\modules\api\controllers\actions\property;

use app\models\Property;
use Yii;
use yii\base\Action;
use app\models\PropertySearch;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;

class PaymentDetails extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $response = [];

        $propertySearch = new PropertySearch();
        $propertySearch->scenario = PropertySearch::SCENARIO_API_SEARCH;
        $propertySearch->load(['PropertySearch' => Yii::$app->request->get()]);
        $propertySearch->ownerUserId = $user->id;

        if ($propertySearch->validateModel()) {
            $result = $propertySearch->apiSearch();
            $properties = $result['properties'];
            $total = $result['total'];
            $propList = [];
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    $owner = $property->ownerUser;
                    $tenant = $property->tenantUser;
                    $ownerDetails = !empty($owner) ? Messages::userMin($owner) : [];
                    $tenantDetails = !empty($tenant) ? Messages::userMin($tenant) : [];

                    $lpStatus = Property::LPS_NOT_RENTED;
                    $paymentDate = ''; // Either last success payment date or payment due date
                    switch ($property->paymentStatus) {
                        case Property::PS_SUCCESS:
                            $lpStatus = Property::LPS_SUCCESS;
                            $paymentDate = $property->lastPaymentDate;
                            break;
                        case Property::PS_FAILED:
                            $lpStatus = Property::LPS_FAILED;
                            $paymentDate = $property->nextChargingDate;
                            break;
                        case Property::PS_PENDING:
                            if ($property->status == Property::STATUS_NOT_AVAILABLE) {
                                $lpStatus = Property::LPS_PENDING;
                                $paymentDate = $property->nextChargingDate;
                            }
                            break;
                    }

                    if ("" != $paymentDate) {
                        $paymentDate = Yii::$app->util->getLocalDateTime($paymentDate, $user->timeZone);
                        $paymentDate = date('Y-m-d', strtotime(($paymentDate)));
                    }

                    $propList[] = Messages::property($property, $ownerDetails, $tenantDetails,
                        ['lastPaymentStatus' => $lpStatus, 'paymentDate' => $paymentDate]);
                }
            }
            $response = Messages::searchResult($total, $propList);
        } else {
            $response = Messages::commonStatus($propertySearch->statusCode, $propertySearch->statusMessage);
        }

        $this->controller->sendResponse($response);
    }
}
?>