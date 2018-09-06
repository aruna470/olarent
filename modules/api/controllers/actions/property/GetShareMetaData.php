<?php

namespace app\modules\api\controllers\actions\property;

use Yii;
use yii\base\Action;
use app\models\Property;
use app\models\User;
use app\modules\api\components\Messages;


class GetShareMetaData extends Action
{
    public function run()
    {
        $propertyId = Yii::$app->request->get('id');
        $model = Property::findOne($propertyId);
        $metaData = '';

        if (!empty($model)) {
            $user = User::findOne($model->ownerUserId);
            $userDetails = !empty($user) ? Messages::userMin($user) : [];
            $response = Messages::property($model, $userDetails, []);
            $metaData = $this->controller->view->render('@api/controllers/actions/property/views/shareMetaData',
                ['description' => $response['description'], 'title' => $response['name'], 'image' => $response['imageUrl']], true);
        } else {
            $metaData = '';
            Yii::$app->appLog->writeLog('Record not exists.');
        }

        echo $metaData;

        Yii::$app->end();
    }
}
?>