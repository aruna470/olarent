<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use app\components\Mp;

class GetMpFormInfo extends Action
{
    public function run()
    {
        $lang = Yii::$app->request->get('lang');
        Yii::$app->language = $lang;

        $mp = new Mp(Yii::$app->params['mangoPay']);
        $response = Messages::mpFormInfo($mp->getIncomeRanges(), $mp->getCountryCodes(), $mp->getNationalities());

        $this->controller->sendResponse($response);
    }
}
?>