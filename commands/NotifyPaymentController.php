<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Notification;
use app\models\Property;
use app\components\Mail;

/*
 * This command send monthly payment reminders.
 * Command should run once a day
 */

class NotifyPaymentController extends Controller
{
    public $mail;
    public $notification;
    public $property;

    public function actionNotify()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        Yii::$app->appLog->writeLog('Start');

        $this->property = new Property();
        $this->mail = new Mail();
        $this->notification = new Notification();

        $notiDays = Yii::$app->params['paymentNotiDays'];

        foreach ($notiDays as $notiDay) {

            Yii::$app->appLog->writeLog("Start sending payment notifications before:{$notiDay} day(s)");

            $page = 1;

            do {
                $date = date('Y-m-d', strtotime("+{$notiDay} days", strtotime(Yii::$app->util->getUtcDateTime())));

                Yii::$app->appLog->writeLog("Checking date:{$date}");

                $properties = $this->property->getPaymentNotifyProperties($date, $page);
                if (!empty($properties)) {
                    foreach ($properties as $property) {
                        Yii::$app->appLog->uniqid = uniqid();
                        Yii::$app->appLog->writeLog('Sending payment reminder notification for;', ['propertyId' => $property->id,
                            'userId' => $property->tenantUser->id]);

                        $nextChargingDateLocal = Yii::$app->util->getLocalDateTime($property->nextChargingDate, $property->tenantUser->timeZone);
                        $nextChargingDateLocal = date('Y-m-d', strtotime($nextChargingDateLocal));

                        // Send email notification
                        $this->mail->language = $property->tenantUser->language;
                        $this->mail->sendPaymentNotifyEmail($property->tenantUser->email, $property->tenantUser->getFullName(),
                            $property->code, $nextChargingDateLocal);

                        // Add notification
                        $this->notification->addNotification(Notification::TENANT_NEXT_PAYMENT, $property->tenantUser->id,
                            ['date' => $nextChargingDateLocal, 'code' => $property->code]);
                    }
                } else {
                    Yii::$app->appLog->writeLog('No records found');
                }
                $page++;
            } while (!empty($properties));
        }

        Yii::$app->appLog->writeLog('Stop');
    }
}
