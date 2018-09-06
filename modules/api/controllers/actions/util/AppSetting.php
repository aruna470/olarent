<?php

namespace app\modules\api\controllers\actions\util;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;


class AppSetting extends Action
{
    public function run()
    {
        $response = Messages::appSetting(Yii::$app->params['commission']);
        $this->controller->sendResponse($response);
    }
}
?>