<?php

namespace app\modules\api\controllers\actions\propertyRequest;

use Yii;
use yii\base\Action;
use app\models\PropertySearch;
use app\models\PropertyRequestSearch;
use app\models\User;
use app\modules\api\components\Messages;

class Search extends Action
{
    public function run()
    {
        $propertyRequestSearch = new PropertyRequestSearch();
        $propertyRequestSearch->scenario = PropertyRequestSearch::SCENARIO_API_SEARCH;
        $user = $this->controller->user;

        $propertyRequestSearch->load(['PropertyRequestSearch' => Yii::$app->request->get()]);
        if (User::TENANT == $user->type) {
            $propertyRequestSearch->tenantUserId = $user->id;
        } else {
            $propertyRequestSearch->ownerUserId  = $user->id;
        }

        if ($propertyRequestSearch->validate()) {
            $result = $propertyRequestSearch->apiSearch();
            $propertyRequests = $result['propertyRequests'];
            $total = $result['total'];
            $propReqList = [];
            if (!empty($propertyRequests)) {
                foreach ($propertyRequests as $propertyRequest) {
                    $owner = $propertyRequest->ownerUser;
                    $tenant = $propertyRequest->tenantUser;
                    $property = $propertyRequest->property;
                    $ownerDetails = !empty($owner) ? Messages::userMin($owner, []) : [];
                    $tenantDetails = !empty($tenant) ? Messages::userMin($tenant, []) : [];
                    $propertyDetails = !empty($property) ? Messages::property($property,[],[]) : [];
                    $propReqList[] = Messages::propertyRequest($propertyRequest, $ownerDetails, $tenantDetails, $propertyDetails);
                }
            }

            $response = Messages::searchResult($total, $propReqList);

        } else {
            $errors = $propertyRequestSearch->getLastError();
            $statusCode = $errors['message'];
            $statusMsg = $errors['attribute'];
            $response = Messages::commonStatus($statusCode, $statusMsg);
        }

        $this->controller->sendResponse($response);
    }
}
?>