<?php

namespace app\commands;

use app\models\PaymentPlan;
use Yii;
use yii\console\Controller;
use app\models\Notification;
use app\components\Mail;

/*
 * This command send payment card expiry notifications.
 * Command should run once a day
 */

class NotifyCardExpController extends Controller
{
    public $mail;
    public $notification;
    public $paymentPlan;

    public function actionNotify()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        Yii::$app->appLog->writeLog('Start');

        $this->paymentPlan = new PaymentPlan();
        $this->mail = new Mail();
        $this->notification = new Notification();

        $notiDays = Yii::$app->params['cardExpNotiDays'];

        foreach ($notiDays as $notiDay) {

            Yii::$app->appLog->writeLog("Start sending CC expiry notifications before:{$notiDay} day(s)");

            $page = 1;

            do {
                $date = gmdate('Y-m-d', strtotime("+{$notiDay} days", strtotime(Yii::$app->util->getUtcDateTime())));

                Yii::$app->appLog->writeLog("Checking date:{$date}");

                $paymentPlans = $this->paymentPlan->getCardExpiringUsers($date, $page);
                if (!empty($paymentPlans)) {
                    foreach ($paymentPlans as $paymentPlan) {
                        Yii::$app->appLog->uniqid = uniqid();
                        Yii::$app->appLog->writeLog('Sending card expiry notification for;', ['planId' => $paymentPlan->id,
                            'userId' => $paymentPlan->user->id]);
                        // Send email alert
                        $this->mail->language = $paymentPlan->user->language;
                        $this->mail->sendCardExpiryEmail($paymentPlan->user->email, $paymentPlan->user->getFullName(),
                            $paymentPlan->expire);

                        // Add notification
                        $this->notification->addNotification(Notification::CC_EXP, $paymentPlan->user->id,
                            ['date' => $paymentPlan->expire]);
                    }
                } else {
                    Yii::$app->appLog->writeLog('No records found');
                }
                $page++;
            } while (!empty($paymentPlans));
        }

        Yii::$app->appLog->writeLog('Stop');
    }
}
