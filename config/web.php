<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
	'homeUrl' => ['site/index'],
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! Insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '123654',
        ],
        'i18n' => [
            'translations' => [
                'mail' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US'
                ],
                'noti' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US'
                ],
                'faq' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US'
                ],
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '-',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\components\WebUser',
            'identityClass' => 'app\models\Auth',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login']
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mailgun.org',
                'username' => 'postmaster@mail.olarent.io',
                'password' => '07caa55c4558f13a35f7a61bec82c63e',
                'port' => '25',
                'encryption' => 'tls',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'util' => [
            'class' => 'app\components\Util',
        ],
        'appLog' => [
            'class' => 'app\components\AppLogger',
            'logType' => 1,
            'logParams' => [
                1 => [
                    'logPath' => dirname(__DIR__) . '/runtime/logs/',
                    'logName' => '-activity.log',
                    'logLevel' => 3, // Take necessary value from apploger class
                    'logSocket' => '',
                    'isConsole' => false
                ],
                2 => [
                    'logPath' => dirname(__DIR__) . '/runtime/logs/',
                    'logName' => '-api.log',
                    'logLevel' => 3, // Take necessary value from apploger class
                    'logSocket' => '',
                    'isConsole' => false
                ]
            ]
        ],
        'view' => [
            'class' => 'app\components\View',
            'theme' => [
                'pathMap' => ['@app/views' => '@webroot/themes/default/views'],
                'baseUrl' => '@web/themes/default',
				'basePath' => '@app/web/themes/default',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'book/read/<t>/<c>' => 'book/read',
                'site/registerInterest' => 'site/register-interest',

                // User
                'POST api/user' => 'api/user/create',
                'GET api/user' => 'api/user/index',
                'GET api/user/<id:\d+>' => 'api/user/view',
                'PUT api/user/<id:\d+>' => 'api/user/update',
                'DELETE api/user/<id:\d+>' => 'api/user/delete',

                // Property
                'POST api/property' => 'api/property/create',
                'PUT api/property/<id:\d+>' => 'api/property/update',
                'GET api/property/<id:\d+>' => 'api/property/view',
                'DELETE api/property/<id:\d+>' => 'api/property/delete',
                'GET api/properties' => 'api/property/search',
                'PUT api/property/terminate/<id:\d+>' => 'api/property/terminate',
                'PUT api/property/pay-now/<id:\d+>' => 'api/property/pay-now',
                'PUT api/property/on-behalf-of-update/<id:\d+>' => 'api/property/on-behalf-of-update',
                'GET api/property/public-view/<id:\d+>' => 'api/property/public-view',
                'GET api/property/get-share-meta-data/<id:\d+>' => 'api/property/get-share-meta-data',

                // Property request
                'POST api/property-request' => 'api/property-request/create',
                'GET api/property-requests' => 'api/property-request/search',
                'GET api/property-request/<id:\d+>' => 'api/property-request/view',
                'PUT api/property-request/accept/<id:\d+>' => 'api/property-request/accept',
                'PUT api/property-request/reject/<id:\d+>' => 'api/property-request/reject',
                'DELETE api/property-request/<id:\d+>' => 'api/property-request/delete',

                // Notification
                'GET api/notifications' => 'api/notification/search',
                'PUT api/notification/<id:\d+>' => 'api/notification/update',

                // Review request
                'POST api/review-request' => 'api/review-request/create',
                'GET api/review-requests' => 'api/review-request/search',

                // User review
                'POST api/user-review' => 'api/user-review/create',
                'GET api/user-reviews' => 'api/user-review/search',

                // Util
                'POST api/s3upload' => 'api/s3upload',

                // Payment plan
                'POST api/payment-plan' => 'api/payment-plan/create',
                'GET api/payment-plan' => 'api/payment-plan/view',
                'DELETE api/payment-plan/<id:\d+>' => 'api/payment-plan/delete',

                // User Mp Info
                'POST api/user-mp-info' => 'api/user-mp-info/create',
                'PUT api/user-mp-info/<id:\d+>' => 'api/user-mp-info/update',
                'GET api/user-mp-info' => 'api/user-mp-info/view',
            ]
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Api',
        ],
    ],
    'aliases' => [
        '@defaultTheme' => '/themes/default/',
        '@api' => '@app/modules/api',
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    $config['components']['urlManager']['showScriptName'] = true;
}

return $config;
