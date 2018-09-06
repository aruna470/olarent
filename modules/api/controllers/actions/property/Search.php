<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\PropertySearch;
use app\models\User;
use app\modules\api\components\Messages;

class Search extends Action
{
    public function run()
    {
        $propertySearch = new PropertySearch();
        $propertySearch->scenario = PropertySearch::SCENARIO_API_SEARCH;
        $propertySearch->load(['PropertySearch' => Yii::$app->request->get()]);
        if ($propertySearch->validateModel()) {
            if (!empty($propertySearch->smartSearchParams)) {
                $result = $propertySearch->apiSmartSearch();
            } else {
                $result = $propertySearch->apiSearch();
            }
            $properties = $result['properties'];
            $total = $result['total'];
            $propList = [];
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    $owner = $property->ownerUser;
                    $tenant = $property->tenantUser;
                    $ownerDetails = !empty($owner) ? Messages::userMin($owner, []) : [];
                    $tenantDetails = !empty($tenant) ? Messages::userMin($tenant, []) : [];
                    $propList[] = Messages::property($property, $ownerDetails, $tenantDetails);
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