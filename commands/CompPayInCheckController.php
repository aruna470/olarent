<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\Mail;
use app\models\CompanyPayIn;
use app\components\Mp;

/*
 * This command periodically checks whether company bank wire succeeded
 * Command runs on each hour
 */

class CompPayInCheckController extends Controller
{
    public function actionDoCheck()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        Yii::$app->appLog->writeLog('Start');

        $companyPayIns = CompanyPayIn::getPendingPayIns();
        $mp = new Mp(Yii::$app->params['mangoPay']);

        if (!empty($companyPayIns)) {
            foreach ($companyPayIns as $companyPayIn) {
                $payInInfo = $mp->getPayIn($companyPayIn->mpPayInId);
                if (!empty($payInInfo)) {
                    $companyPayIn->status = $payInInfo->Status;
                    $companyPayIn->saveModel();
                }
            }
        }

        Yii::$app->appLog->writeLog('Stop');
    }
}
