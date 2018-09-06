<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\Mail;
use app\models\Payment;
use app\models\Payout;

/*
 * This command periodically checks for new payments and try for payouts
 * Command runs on each hour
 */

class PayoutController extends Controller
{
    public function init()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        parent::init();
    }

    /*
     * Performs initial payout attempt
     * Command runs on each 5 min
     */
    public function actionDoInitPayout()
    {
        Yii::$app->appLog->writeLog('Start');
        $payment = new Payment();
        $payout = new Payout();
        $pageNo = 0;
        do {
            $payments = $payment->getPendingPayouts($pageNo);
            if (!empty($payments)) {
                foreach ($payments as $_payment) {
                    $status = $payout->initPayOut($_payment);
                    if ($status) {
                        $_payment->isPayoutProcessed = Payment::PAYOUT_PROCESSED;
                        $_payment->saveModel();
                    }
                }
            } else {
                Yii::$app->appLog->writeLog('No pending initial payouts to be processed');
            }

            $pageNo++;
        } while(!empty($payments));

        Yii::$app->appLog->writeLog('Stop');
    }

    /*
     * Retry failed payouts
     * Command runs once a day
     */
    public function actionRetryPayout()
    {
        Yii::$app->appLog->writeLog('Start');
        $payout = new Payout();
        $pageNo = 0;
        do {
            $payouts = $payout->getFailedPayouts($pageNo);
            if (!empty($payouts)) {
                foreach ($payouts as $_payout) {
                    $payout->retryPayout($_payout);
                }
            } else {
                Yii::$app->appLog->writeLog('No retry payouts to be processed');
            }

            $pageNo++;
        } while(!empty($payouts));

        Yii::$app->appLog->writeLog('Stop');
    }
}
