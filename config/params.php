<?php

return [
    'productName' => 'Olarent',
    'productNameEmail' => 'Olarent property management system.',
    'copyright' => 'Copyright © ' . date('Y') . ' Olarent. All Rights Reserved.',
    'telephone' => '+xxxx',
    'salt' => 'xxxx',
    'allowEmpty' => true,
    'accessDeniedUrl' => ['site/access-denied'],
    'tempPath' => dirname(__DIR__) . '/runtime/temp/',
    'consoleCmdPath' => 'yii',
    'contentBaseUrl' => 'http://localhost/contents/',
    'appDownloadLink' => 'http://staging.olarent.io/app/#/index',
    'api' => [
        'apiKey' => 'xxx',
        'apiSecret' => 'xxxx',
        'guestActions' => [
            // Actions those not require user access token
            'Api.User.Create',
            'Api.User.Authenticate',
            'Api.User.SendVerifyCode',
            'Api.User.VerifyCode',
            'Api.Util.S3upload',
            'Api.User.ForgotPassword',
            'Api.User.ResetPassword',
            'Api.Util.Faq',
            'Api.Util.AppSetting',
            'Api.Property.PublicView'
        ],
        // No authentication required for following actions
        'noAuth' => [
            'Api.Util.AdyenNoti',
            'Api.Property.GetShareMetaData'
        ]
    ],
    'supportEmail' => 'xxxx@olarent.io',
    'adminEmail' => 'xxxx@olarent.io',
    'defaultTimeZone' => 'Europe/Paris', // Default timezone for user when not specified. Assign when creating a user.
    'phpIniTimeZone' => date_default_timezone_get(),
    'logoUrlInEmail' => 'http://xxxx/olarent.png',
    'emailUnsubLink' => 'http://xxxx/bo-dev/site/unsubscribe',
    'passwordRestUrl' => 'http://xxxxxapp/#/reset-password',
    'commission' => 2, // Commission percentage
    'aws' => [
        'credentialFilePath' => '/var/www/.aws/credentials',
        's3' => [
            'region' => 'us-east-1',
            'bucketName' => 'sssiles1'
        ]
    ],
    'adyen' => [
        'wsUsername' => 'xxxx@Company.Olarent',
        'wsPassword' => 'xxxxxx/HtJ2RrBD^xZ7KLRF\w<Y',
        'merchantAccount' => 'xxxxx',
        'hmacKey' => 'xxxxx',
        'recurringApiUrl' => 'https://xxxx/pal/servlet/Recurring/v12/',
        'paymentApiUrl' => 'https://pxxxxxyen.com/pal/servlet/Payment/v12/'
    ],
    'stripe' => [
        'apiKey' => 'xxxxx'
    ],
    'clickatel' => [
        'token' => 'xxxxx.umkK.AiRY6_clE2zoCGzf3NRneZlsuifzz4Qz.sah2OigQwPq',
        'appId' => '3584471'
    ],
    'mailgun' => [
        'apiEndPoint' => 'https://api.mailgun.net/v3/xxxxxx/',
        'apiUsername' => 'xxxxx',
        'apiPassword' => 'key-xxxxxx'
    ],
    'mangoPay' => [
        'clientId' => 'xxxxx',
        'clientPassword' => 'xxxxx',
        'tempPath' => '@app/runtime/temp/'
    ],
    // Card expiry notification alerts
    'cardExpNotiDays' => [
        1 => 14, // First alert before 14 days
        2 => 7   // Second alert before 7 days
    ],
    // Monthly payment notification alerts
    'paymentNotiDays' => [
        1 => 7,  // First alert before 7 days
        2 => 3   // Second alert before 3 days
    ],
    // Default system currency
    'defCurrency' => 'EUR',
    // Maximum number of recurring charging attempts
    'maxChargingAttempts' => 3,
    // Valid country codes
    'countryCodes' => [
        'fr' => '+33',
        //'lk' => '+94'
    ],
    // Maximum payout retries
    'maxRetryPayout' => 3
];
