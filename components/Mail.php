<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\base\View;
use app\components\RestClient;


class Mail extends Component
{
	public $emailTemplatePath = '@app/views/email-template/notificationTemplate';
	public $emailContPath = '@app/mail/';
    public $apiEndPoint;
    public $apiUsername;
    public $apiPassword;
	public $fromEmail;
	public $fromName;
	public $language = 'en-US';
    public $defLang = 'en-US';
	public $view;
	
	public function __construct()
	{
		$this->fromEmail = Yii::$app->params['supportEmail'];
		$this->fromName = Yii::$app->params['productName'];
        $this->apiEndPoint = Yii::$app->params['mailgun']['apiEndPoint'];
        $this->apiUsername = Yii::$app->params['mailgun']['apiUsername'];
        $this->apiPassword = Yii::$app->params['mailgun']['apiPassword'];
		$this->view = new View();
	}

	/**
	 * Send property request notification email
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param string $tenantName Name of the tenant
	 * @param string $code Property code
	 * @return boolean true/false
	 */
	public function sendPropReqNotification($email, $ownerName, $tenantName, $code)
	{
		$subject = Yii::t('mail', 'Rental request from {tenantName}', ['tenantName' => $tenantName], $this->language);
		$message = Yii::t('mail',
'Ola {ownerName},
<p></p>
<p>You have received a rental request from {tenantName}
<br>rental code is {propertyCode}
<br>Please respond as soon as you can to this inquiry.
<br>Thank you.</p>',
				[
				'ownerName' => $ownerName,
				'tenantName' => $tenantName,
				'propertyCode' => $code
		], $this->language);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send property accept notification email to tenant
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param string $tenantName Name of the tenant
	 * @param string $code Property code
	 * @param float $keyMoney Key money charged
	 * @param float $cost Monthly payment
	 * @param string $currency Currency type
	 * @param boolean $isOnBhf Is on behalf of property creation
	 * @return boolean true/false
	 */
	public function sendPropAcceptNotificationTenant($email, $ownerName, $tenantName, $code, $keyMoney, $cost, $currency, $isOnBhf = false)
	{
		if ($isOnBhf) {
			$subject = Yii::t('mail', 'Rental creation confirmation', [], $this->language);
		} else {
			$subject = Yii::t('mail', 'Rental request accepted from {ownerName}', ['ownerName' => $ownerName], $this->language);
		}

		$msgKeymoney = '';
		if (0 != $keyMoney) {
			$msgKeymoney = Yii::t('mail', '<br>Deposit charged:{amount}({currency})',
                ['amount' => $keyMoney, 'currency' => $currency]);
		}

		$msgInitRent = '';
		if (0 != $cost) {
			$msgInitRent = Yii::t('mail', '<br>Initial rent charged:{cost}({currency})',
					['cost' => $cost, 'currency' => $currency]);
		}

		if ($isOnBhf) {
			$message = Yii::t('mail', 'Ola {tenantName},
<p></p>
<p>Congratulations ! You have successfully created your rental page for your landlord {ownerName}.
<br>The rental code is {propertyCode}
{msgKeymoney}
{msgInitRent}
<br>Thank you.</p>', [
					'ownerName' => $ownerName,
					'tenantName' => $tenantName,
					'propertyCode' => $code,
					'msgKeymoney' => $msgKeymoney,
					'msgInitRent' => $msgInitRent
			], $this->language);
		} else {
			$message = Yii::t('mail', 'Ola {tenantName},
<p></p>
<p>Congratulations ! Your rental request was accepted by {ownerName}
<br>The rental code is {propertyCode}
{msgKeymoney}
{msgInitRent}
<br>Thank you.</p>', [
					'ownerName' => $ownerName,
					'tenantName' => $tenantName,
					'propertyCode' => $code,
					'msgKeymoney' => $msgKeymoney,
					'msgInitRent' => $msgInitRent
			], $this->language);
		}

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send property reject notification email to tenant
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param string $tenantName Name of the tenant
	 * @param string $code Property code
	 * @return boolean true/false
	 */
	public function sendPropRejectNotificationTenant($email, $ownerName, $tenantName, $code)
	{
		$subject = Yii::t('mail', 'The request was not accepted for the rental {propertyCode}', ['propertyCode' => $code], $this->language);
		$message = Yii::t('mail', 'Ola {tenantName},
<p></p>
<p>Sorry your request for {propertyCode} was not accepted by {ownerName}, however he wishes you the best of luck in your search.
<br>We hope you will find another place you like on Olarent.
<br>Thank you.</p>', [
				'ownerName' => $ownerName,
				'tenantName' => $tenantName,
				'propertyCode' => $code
		], $this->language);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send tenant invitation
	 * @param string $email Recipient email
	 * @param string $ownerMessage Owner comment
	 * @param string $ownerName Owner name
	 * @return boolean true/false
	 */
	public function inviteTenant($email, $ownerMessage, $ownerName)
	{
		$subject = Yii::t('mail', 'Olarent Invitation from {ownerName}', ['ownerName' => $ownerName], $this->language);

		$message = Yii::t('mail', 'Ola,
<p></p>
<p>{ownerName} has invited you to join Olarent to get to know you better and to make paying your rent easy and problem free.
<br>With Olarent you can pay your rent via Credit/Debit card and automate monthly payments.</p>
<p>Message from {ownerName}
<br><br>{message}<br>
<br>Go to our web app here:{appDownloadLink}
<br>Thank you.</p>', [
				'ownerName' => $ownerName,
				'message' => nl2br($ownerMessage),
				'appDownloadLink' => Html::a(Yii::$app->params['appDownloadLink'], Yii::$app->params['appDownloadLink']),
		], $this->language);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send review request notification email
	 * @param string $email Recipient email
	 * @param string $fromName Name of the sender
	 * @param string $toName Name of the receiver
	 * @return boolean true/false
	 */
	public function sendReviewReqNotification($email, $fromName, $toName)
	{
		$subject = Yii::t('mail', 'Review request from {fromName}', ['fromName' => $fromName], $this->language);
		$message = Yii::t('mail', 'Ola {toName},
<p></p>
<p>You have received a review request from your former Tenant {fromName}
<br>Thank you for your contribution !</p>', [
				'toName' => $toName,
				'fromName' => $fromName
		], $this->language);

		return $this->send($email, $subject, $message);
	}

    /**
     * Send review feedback notification email
     * @param string $email Recipient email
     * @param string $fromName Name of sender
     * @param string $toName Name of the receiver
     * @return boolean true/false
     */
    public function sendReviewFeedbackNotification($email, $fromName, $toName)
    {
        $subject = Yii::t('mail', 'Review feedback', [], $this->language);
        $message = Yii::t('mail', 'Ola {toName},
<p></p>
<p>You have received a review feedback from your former landlord {fromName}
<br>Thank you.</p>', [
            'toName' => $toName,
            'fromName' => $fromName
        ], $this->language);

        return $this->send($email, $subject, $message);
    }

    /**
     * Send monthly charge success notification email to Tenant
     * @param string $email Recipient email
     * @param string $toName Name of recipient
     * @param string $propertyCode Property code
     * @param string $amount Charged amount
     * @param string $currency Currency format
     * @return boolean true/false
     */
    public function sendChargeSuccessNotificationTenant($email, $toName, $propertyCode, $amount, $currency)
    {
        $subject = Yii::t('mail', 'Rental payment confirmation', [], $this->language);
        $message = Yii::t('mail', 'Ola {toName}
<p></p>
<p>This message to inform you, your monthly rent have been charged {amount} ({currency}) for the property "{code}"
<br>Thank you.</p>', [
            'toName' => $toName,
            'amount' => $amount,
            'currency' => $currency,
            'code' => $propertyCode
        ], $this->language);

        return $this->send($email, $subject, $message);
    }

    /**
     * Send monthly charge success notification email to Owner
     * @param string $email Recipient email
     * @param string $toName Name of recipient
     * @param string $propertyCode Property code
     * @param string $amount Charged amount
     * @param string $currency Currency format
     * @return boolean true/false
     */
    public function sendChargeSuccessNotificationOwner($email, $toName, $propertyCode, $amount, $currency)
    {
        $subject = Yii::t('mail', 'Rental  payment confirmation for "{code}"', ['code' => $propertyCode], $this->language);
        $message = $this->getEmailContent('charge-success-owner', [
            '{toName}' => $toName,
            '{amount}' => $amount,
            '{currency}' => $currency,
            '{code}' => $propertyCode
        ]);

        return $this->send($email, $subject, $message);
    }

    /**
     * Send monthly charge fail notification email to Tenant
     * @param string $email Recipient email
     * @param string $toName Name of recipient
     * @param string $propertyCode Property code
     * @param string $nextAttemptDate Next attempt date
	 * @param string $amount Charged amount
	 * @param string $currency Currency format
     * @return boolean true/false
     */
    public function sendChargeFailNotificationTenant($email, $toName, $propertyCode, $nextAttemptDate, $amount, $currency, $isLastAttempt = false)
    {
        $subject = Yii::t('mail', 'Failed rental payment for "{code}"', ['code' => $propertyCode], $this->language);

		if (!$isLastAttempt) {
			$message = Yii::t('mail', 'Ola {toName}
<p></p>
<p>This message to inform you that the {amount} ({currency}) payment to the property "{code}" has failed. Please check your payment plan. We will try to complete the payment on {date} again.
<br>Thank you.</p>', [
					'date' => $nextAttemptDate,
					'code' => $propertyCode,
					'toName' => $toName,
					'amount' => $amount,
					'currency' => $currency
			], $this->language);
		} else {
			$subject = Yii::t('mail', 'All rental payment attempts have failed for "{code}"', ['code' => $propertyCode], $this->language);
			$message = Yii::t('mail', 'Ola {toName},
<p></p>
<p>This message to inform you that all charging attempts made to the property "{code}" have failed. Please get in touch
with your landlord and check with your bank or update your Credit/Debit card information.
<br>Thank you.</p>', [
					'code' => $propertyCode,
					'toName' => $toName
			], $this->language);
		}

        return $this->send($email, $subject, $message);
    }

	/**
	 * Send monthly charge success notification email to Owner
	 * @param string $email Recipient email
	 * @param string $toName Name of recipient
	 * @param string $propertyCode Property code
	 * @param string $amount Charged amount
	 * @param string $currency Currency format
	 * @param string $nextAttemptDate Next attempt date
	 * @return boolean true/false
	 */
	public function sendChargeFailNotificationOwner($email, $toName, $propertyCode, $amount, $currency, $nextAttemptDate, $isLastAttempt = false)
	{
		$subject = Yii::t('mail', 'Rental payment attempt has failed for "{code}"', ['code' => $propertyCode], $this->language);

		if (!$isLastAttempt) {
			$message = Yii::t('mail', 'Ola {toName},
<p></p>
<p>This message to inform you that the {amount} ({currency}) payment for the property "{code}" has failed.
We will try again to complete the payment on {date}.
<br>Thank you.</p>', [
					'toName' => $toName,
					'amount' => $amount,
					'currency' => $currency,
					'code' => $propertyCode,
					'date' => $nextAttemptDate
			], $this->language);
		} else {
			$subject = Yii::t('mail', 'All rental payment attempts have failed for "{code}"', ['code' => $propertyCode], $this->language);
			$message = Yii::t('mail', 'Ola {toName},
<p></p>
<p>This message to inform you that all rental payment attempts have failed for "{code}".
Please get in touch with your tenant, we have advised him to update his payment method on Olarent.
<br>Thank you.</p>', [
					'toName' => $toName,
					'code' => $propertyCode
			], $this->language);
		}

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send forgot password email
	 * @param string $email Recipient email
	 * @param string $link Password reset link
	 * @return boolean true/false
	 */
	public function sendForgotPasswordEmail($email, $link)
	{
		$subject = Yii::t('mail', 'Reset password', [], $this->language);
        $message = $this->getEmailContent('forgot-password', [
            '{link}' => $link
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send password reset email
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @return boolean true/false
	 */
	public function sendPasswordResetEmail($email, $name)
	{
		$subject = Yii::t('mail', 'Reset password', [], $this->language);
        $message = $this->getEmailContent('password-reset', [
            '{name}' => $name,
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send signup email
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @return boolean true/false
	 */
	public function sendSignupEmail($email, $name)
	{
		$subject = Yii::t('mail', 'Welcome to Olarent {name}', ['name' => $name], $this->language);
        $message = $this->getEmailContent('signup', [
            '{name}' => $name,
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send payment card expiry notification email
	 * @param string $email Recipient email
	 * @param string $name Recipient name
     * @param string $expDate Card expiry date
	 * @return boolean true/false
	 */
	public function sendCardExpiryEmail($email, $name, $expDate)
	{
		$subject = Yii::t('mail', 'Card expiration on {date}', ['date' => $expDate], $this->language);
        $message = $this->getEmailContent('card-expiry', [
            '{name}' => $name,
            '{date}' => $expDate
        ]);

        return $this->send($email, $subject, $message);
	}

    /**
     * Send payment reminder email
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $code Property code
     * @param string $nextChargingDate Charging date
     * @return boolean true/false
     */
    public function sendPaymentNotifyEmail($email, $name, $code, $nextChargingDate)
    {
        $subject = Yii::t('mail', 'This month rental payment on {nextChargingDate}', ['nextChargingDate' => $nextChargingDate], $this->language);
        $message = $this->getEmailContent('payment-notify', [
            '{name}' => $name,
            '{code}' => $code,
            '{nextChargingDate}' => $nextChargingDate
        ]);

        return $this->send($email, $subject, $message);
    }

	/**
	 * Send property termination email
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @param string $code Property code
	 * @param string $terminatorName Name of the terminated user
	 * @return boolean true/false
	 */
	public function sendPropTerminateEmail($email, $name, $code, $terminatorName)
	{
		$subject = Yii::t('mail', 'Rental termination for "{code}"', ['code' => $code], $this->language);
        $message = $this->getEmailContent('property-terminate', [
            '{name}' => $name,
            '{code}' => $code,
            '{terminatorName}' => $terminatorName
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send property termination email to terminator
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @param string $code Property code
	 * @return boolean true/false
	 */
	public function sendPropTerminateEmailToTerminator($email, $name, $code)
	{
		$subject = Yii::t('mail', 'Rental termination confirmation for "{code}"', ['code' => $code], $this->language);
        $message = $this->getEmailContent('property-terminate-terminator', [
            '{name}' => $name,
            '{code}' => $code,
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send all pending payment received email to owner
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @param string $code Property code
	 * @param float $amount Paid amount
	 * @param string $currency Currency code
	 * @return boolean true/false
	 */
	public function sendAllPendingPaymentRcvEmailOwner($email, $name, $code, $amount, $currency)
	{
		$subject = Yii::t('mail', 'Pending payments have been received for "{code}"', ['code' => $code], $this->language);
        $message = $this->getEmailContent('all-pending-payments-owner', [
            '{name}' => $name,
            '{code}' => $code,
            '{amount}' => $amount,
            '{currency}' => $currency,
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send all pending payment paid email to tenant
	 * @param string $email Recipient email
	 * @param string $name Recipient name
	 * @param string $code Property code
	 * @param float $amount Paid amount
	 * @param string $currency Currency code
	 * @return boolean true/false
	 */
	public function sendAllPendingPaymentPayEmailTenant($email, $name, $code, $amount, $currency)
	{
		$subject = Yii::t('mail', 'Pending payments have been made for "{code}"', ['code' => $code], $this->language);
        $message = $this->getEmailContent('all-pending-payments-tenant', [
            '{name}' => $name,
            '{code}' => $code,
            '{amount}' => $amount,
            '{currency}' => $currency,
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send property accept notification email to tenant
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param string $tenantName Name of the tenant
	 * @param string $code Property code
	 * @param float $keyMoney Key money charged
	 * @param float $cost Monthly payment
	 * @param string $currency Currency type
	 * @param boolean $isOnBhf Is on behalf of property creation
	 * @return boolean true/false
	 */
	public function sendPropAcceptNotificationOwner($email, $ownerName, $tenantName, $code, $keyMoney, $cost, $currency, $isOnBhf)
	{
		if ($isOnBhf) {
			$subject = Yii::t('mail', 'Rental creation confirmation', [], $this->language);
		} else {
			$subject = Yii::t('mail', 'You have accepted a rental request for "{code}"', ['code' => $code], $this->language);
		}

		$msgKeymoney = '';
		if (0 != $keyMoney) {
			$msgKeymoney = Yii::t('mail', '<br>Deposit charged:{amount}({currency})',
					['amount' => $keyMoney, 'currency' => $currency]);
		}

		$msgInitRent = '';
		if (0 != $cost) {
			$msgInitRent = Yii::t('mail', '<br>Initial rental charged:{cost}({currency})',
					['cost' => $cost, 'currency' => $currency]);
		}

		if ($isOnBhf) {
			$message = Yii::t('mail', 'Ola {ownerName},
<p>Tenant {tenantName} has successfully created a rental page for your property in Olarent.
{msgKeymoney}
{msgInitRent}
<br>Thank you.</p>', [
					'ownerName' => $ownerName,
					'tenantName' => $tenantName,
					'propertyCode' => $code,
					'msgKeymoney' => $msgKeymoney,
					'msgInitRent' => $msgInitRent
			], $this->language);
		} else {
			$message = Yii::t('mail', 'Ola {ownerName},
<p>You have accepted the rental request of {tenantName} for the rental {propertyCode}.
{msgKeymoney}
{msgInitRent}
<br>Thank you.</p>', [
					'ownerName' => $ownerName,
					'tenantName' => $tenantName,
					'propertyCode' => $code,
					'msgKeymoney' => $msgKeymoney,
					'msgInitRent' => $msgInitRent
			], $this->language);
		}

		return $this->send($email, $subject, $message);
	}

	/**
	 * Send property request notification email to Tenant
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param string $tenantName Name of the tenant
	 * @param string $code Property code
	 * @return boolean true/false
	 */
	public function sendPropReqNotificationTenant($email, $ownerName, $tenantName, $code)
	{
		$subject = Yii::t('mail', 'Rental request to {ownerName}', ['ownerName' => $ownerName], $this->language);
		$message = $this->getEmailContent('property-request-tenant', [
				'{ownerName}' => $ownerName,
				'{tenantName}' => $tenantName,
				'{propertyCode}' => $code
		]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Owner did not create a MangoPay account for bank transfer
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @return boolean true/false
	 */
	public function noMpAccount($email, $ownerName)
	{
		$subject = Yii::t('mail', 'Missing bank details', [], $this->language);
		$message = $this->getEmailContent('missing-bank-details', ['{ownerName}' => $ownerName]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Payout success
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
     * @param float $amount Transferred amount
     * @param string $currencyCode Currency type
     * @param string $propertyCode Property code
	 * @param string $iban IBAN
	 * @return boolean true/false
	 */
	public function payoutSuccess($email, $ownerName, $amount, $currencyCode, $propertyCode, $iban)
	{
		$subject = Yii::t('mail', 'Fund transfer success', [], $this->language);
		$message = $this->getEmailContent('payout-success', [
				'{ownerName}' => $ownerName,
				'{amount}' => $amount,
				'{currencyCode}' => $currencyCode,
				'{propertyCode}' => $propertyCode,
				'{iban}' => $iban
		]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * Payout failed
	 * @param string $email Recipient email
	 * @param string $ownerName Name of the property owner
	 * @param float $amount Transferred amount
	 * @param string $currencyCode Currency type
	 * @param string $propertyCode Property code
	 * @param string $iban IBAN
	 * @return boolean true/false
	 */
	public function payoutFail($email, $ownerName, $amount, $currencyCode, $propertyCode, $iban)
	{
		$subject = Yii::t('mail', 'Fund transfer failed', [], $this->language);
        $message = $this->getEmailContent('payout-fail', [
            '{ownerName}' => $ownerName,
            '{amount}' => $amount,
            '{currencyCode}' => $currencyCode,
            '{propertyCode}' => $propertyCode,
            '{iban}' => $iban
        ]);

		return $this->send($email, $subject, $message);
	}

	/**
	 * KYC document validation failed
	 * @param string $email Recipient email
	 * @param string $name Document owner name
	 * @return boolean true/false
	 */
	public function documentValidateFail($email, $name)
	{
		$subject = Yii::t('mail', 'Proof document validation failed', [], $this->language);
        $message = $this->getEmailContent('doc-validate-fail', ['{name}' => $name]);

		return $this->send($email, $subject, $message);
	}

    /**
     * Send email
     * @param string $toEmail Recipient email
     * @param string $subject Email subject
     * @param string $content Email body
     * @param string $fromEmail Sender email
     * @param string $fromName Sender name
     * @return boolean true/false
     */
    public function send($toEmail, $subject, $content, $fromEmail = null, $fromName = null)
    {
        $view = new View();
        Yii::$app->language = $this->language;

        if (null == $fromEmail) {
            $fromEmail = $this->fromEmail;
        }

        if (null == $fromName) {
            $fromName = $this->fromName;
        }

        $restClient = new RestClient($this->apiUsername, $this->apiPassword, $this->apiEndPoint);
        $restClient->sendRequest('messages', [
            'from' => "$fromName <$fromEmail>",
            'to' => $toEmail,
            'subject' => $subject,
            'html' => $view->render($this->emailTemplatePath, ['content' => $content], true)
        ], 'POST');

        $res = $restClient->response;

        Yii::$app->appLog->writeLog('Email API response.', ['response' => $res]);

        if (!empty($res)) {
            $res = json_decode($res);
            if (strstr(@$res->message, 'Queued')) {
                Yii::$app->appLog->writeLog('Email sent.', ['from' => $fromEmail, 'to' => $toEmail]);
                return true;
            }
        }

        Yii::$app->appLog->writeLog('Email sending failed.', ['from' => $fromEmail, 'to' => $toEmail]);

        return false;
    }

	/**
	 * Retrieve email content
	 * @param string $templateName Email template name
	 * @return boolean true/false
	 */
	private function getEmailContent($templateName, $params)
	{
        $language = '' == $this->language ? $this->defLang : $this->language;
		$content = $this->view->render($this->emailContPath . $language . "/{$templateName}", [], true);
		$keys = array_keys($params);
		$values = array_values($params);

		return str_replace($keys, $values, $content);
	}

	/**
     * Send email
     * @param string $toEmail Recipient email
	 * @param string $subject Email subject
	 * @param string $content Email body
	 * @param string $fromEmail Sender email
	 * @param string $fromName Sender name
     * @return boolean true/false
     */
	/*public function send($toEmail, $subject, $content, $fromEmail = null, $fromName = null)
	{
		Yii::$app->language = $this->language;

		if (null == $fromEmail) {
			$fromEmail = $this->fromEmail;
		}
		
		if (null == $fromName) {
			$fromName = $this->fromName;
		}

		$error = '';
		$response = false;
		try {
			$response = Yii::$app->mailer
					->compose($this->emailTemplatePath, ['content' => $content])
					->setFrom([$fromEmail => $fromName])
					->setTo($toEmail)
					->setSubject($subject)
					->send();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		if ($response) {
			Yii::$app->appLog->writeLog('Email sent.', ['from' => $fromEmail, 'to' => $toEmail]);
			return true;
		}

		Yii::$app->appLog->writeLog('Email sending failed.', ['from' => $fromEmail, 'to' => $toEmail, 'error' => $error]);
		return false;
	}*/
}