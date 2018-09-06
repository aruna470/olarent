<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\controllers\BaseController;
use app\components\Aws;
use app\components\Mail;
use app\components\Adyen;
use yii\helpers\Html;

class SiteController extends BaseController
{

    public function behaviors()
    {
        return [
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    public function allowed()
    {
        return [
            'Site.Index',
            'Site.Login',
            'Site.AccessDenied',
            'Site.Error',
            'Site.Logout',
            'Site.Captcha',
            'Site.Home',
            'Site.Unsubscribe',
        ];
    }

    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            if (Yii::$app->user->can('Dashboard.Dashboard')) {
                return $this->redirect(['dashboard/dashboard']);
            } else {
                return $this->redirect(['site/home']);
            }
        } else {
            return $this->redirect(['login']);
        }
    }

    public function actionHome()
    {
        return $this->render('home', [
        ]);
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->appLog->writeLog('Login success');
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionAccessDenied()
    {
        return $this->render('accessDenied', []);
    }

    public function actionAwsTest()
    {
        $aws = new Aws();
       // $result = $aws->s3UploadObject("timecheck.jpg", 'e:\tmp\test.jpg');
        $result = $aws->s3GetSignedObjectUrl("timecheck.jpg");
        print_r($result);
    }

    public function actionMailTest()
    {
        $mail = new Mail();
        //$mail->language = 'en-US';
        $mail->language = 'fr-FR';


        $email = 'aruna@app-monkeyz.com';
        $ownerName = 'Aruna Attanayake';
        $tenantName = 'Esandu Attanayake';
        $code = 'ERT234';
        $fromName = 'Aruna Attanayake';
        $toName = 'Esandu Attanayake';
        $name = 'Aruna Attanayake';
        $amount = 10;
        $currency = 'EUR';
        $nextAttemptDate = '2016-02-28';
        $expDate = '2016-02-28';
        $isLastAttempt = true;
        $link = Html::a(Yii::t('mail', 'Reset password', [], $mail->language), 'http://staging.olarent.io/app');
        $iban = 'FR3998923787823472348';

        //$mail->sendPropRejectNotificationTenant($email, $ownerName, $tenantName, $code);
        //$mail->sendReviewReqNotification($email, $fromName, $toName);
        //$mail->sendReviewFeedbackNotification($email, $fromName, $toName);
        //$mail->sendChargeSuccessNotificationTenant($email, $toName, $code, $amount, $currency);
        $mail->sendChargeSuccessNotificationOwner($email, $toName, $code, $amount, $currency);
        //$mail->sendChargeFailNotificationTenant($email, $toName, $code, $nextAttemptDate, $amount, $currency, $isLastAttempt);
        //$mail->sendChargeFailNotificationOwner($email, $toName, $code, $amount, $currency, $nextAttemptDate, $isLastAttempt);
        //$mail->sendForgotPasswordEmail($email, $link);
        //$mail->sendPasswordResetEmail($email, $name);
        //$mail->sendSignupEmail($email, $name);
        //$mail->sendCardExpiryEmail($email, $name, $expDate);
        //$mail->sendPaymentNotifyEmail($email, $name, $code, $nextAttemptDate);
        //$mail->sendPropTerminateEmail($email, $fromName, $code, $toName);
        //$mail->noMpAccount($email, $ownerName);
        //$mail->payoutSuccess($email, $ownerName, $amount, $currency, $code, $iban);
        //$mail->payoutFail($email, $ownerName, $amount, $currency, $code, $iban);
        //$mail->documentValidateFail($email, $ownerName);
        //$mail->sendPropReqNotificationTenant($email, $ownerName, $tenantName, $code);
        //$mail->sendAllPendingPaymentPayEmailTenant($email, $tenantName, $code, $amount, $currency);
        //$mail->sendAllPendingPaymentRcvEmailOwner($email, $tenantName, $code, $amount, $currency);
        //$mail->sendPropTerminateEmailToTerminator($email, $tenantName, $code);
        //$mail->sendPropTerminateEmail($email, $tenantName, $code, $ownerName);
    }

    public function actionAdyenTest()
    {
//        print_r(Yii::$app->params);
//        exit;
        $adyen = new Adyen(Yii::$app->params);
        $res = $adyen->submitRecurringPayment("8414532961448285", 'EUR', '199', time(), 'aruna470@gmail.com', '1234');
        print_r($res);
    }

    public function actionTimeTest()
    {
        $curTimeUser = Yii::$app->util->getLocalDateTime(date('Y-m-d H:i:s'), 'Australia/Victoria',
            Yii::$app->params['phpIniTimeZone']);
        //echo date('m', strtotime($curTimeUser));
       // echo $curTimeUser;
        //echo (int)date('j', strtotime($curTimeUser));

        $date = date('Y-m-d', strtotime("+3 days", strtotime(Yii::$app->util->getUtcDateTime())));
        echo $date;
    }

    /*
     * Unsubscribe from email
     */
    public function actionUnsubscribe()
    {
        $this->layout = 'guest';
        return $this->render('unsubscribe', []);
    }

    public function actionLogout()
    {
        Yii::$app->appLog->writeLog('Logout success');
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }
}
