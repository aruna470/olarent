<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use app\modules\api\components\ApiStatusMessages;
use app\modules\api\components\Messages;

class DefaultController extends ApiBaseController
{
    public function actionError()
    {
        $statusCode = ApiStatusMessages::FAILED;
        $commonResponse = Messages::commonStatus($statusCode, Yii::t('app', 'An error occur while processing the request'));
        $this->sendResponse($commonResponse);
    }
}
