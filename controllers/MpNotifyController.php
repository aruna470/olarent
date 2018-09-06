<?php

namespace app\controllers;

use Yii;
use app\models\UserMpInfoFile;
use app\models\Payout;

/**
 * MangoPay notifications accept by the controller.
 */
class MpNotifyController extends BaseController
{
    public function behaviors()
    {
        return [

        ];
    }

    public function allowed()
    {
        return [
            'MpNotify.EventRouter'
        ];
    }

    public function actionEventRouter()
    {
        Yii::$app->appLog->writeLog('Event received.', [Yii::$app->request->get()]);

        $resourceId = Yii::$app->request->get('RessourceId');
        $eventType = Yii::$app->request->get('EventType');
        switch ($eventType) {
            case 'KYC_SUCCEEDED':
            case 'KYC_FAILED':
                $model = new UserMpInfoFile();
                $model->updateDocumentStatus($resourceId, $eventType);
                break;

            case 'PAYOUT_NORMAL_SUCCEEDED':
            case 'PAYOUT_NORMAL_FAILED':
                $model = new Payout();
                $model->updatePayoutStatus($resourceId, $eventType);
                break;
        }
    }
}
