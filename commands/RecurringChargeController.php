<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\Mail;
use app\models\Property;

/*
 * This command charge user on particular date
 * Command runs once a day
 */

class RecurringChargeController extends Controller
{
    public $mail;
    public $property;

    public function actionCharge()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        Yii::$app->appLog->writeLog('Start');

        $this->property = new Property();
        $this->mail = new Mail();

        $page = 1;

        do {
            $properties = $this->property->getPropertiesToBeCharged($page);
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    Yii::$app->appLog->uniqid = uniqid();
                    Yii::$app->appLog->writeLog('Charging for;', ['propertyId' => $property->id,
                        'userId' => $property->tenantUserId]);
                    $property->doRecurringCharge($property);
                }
            } else {
                Yii::$app->appLog->writeLog('No properties to be charged');
            }
            $page++;
        } while (!empty($properties));


        Yii::$app->appLog->writeLog('Stop');
    }
}
