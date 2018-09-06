<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
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
            ],
        ],
        'appLog' => [
            'class' => 'app\components\AppLogger',
            'logType' => 2,
            'logParams' => [
                3 => [
                    'logPath' => dirname(__DIR__) . '/runtime/logs/',
                    'logName' => '-daemon.log',
                    'logLevel' => 3, // Take necessary value from apploger class
                    'logSocket' => '',
                    'isConsole' => true
                ],
            ]
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mailgun.org',
                'username' => 'ssss@xxxx',
                'password' => 'xxxxx',
                'port' => '25',
                'encryption' => 'tls',
            ]
        ],
        'util' => [
            'class' => 'app\components\Util',
        ],
        'db' => $db,
    ],
    'params' => $params,
];
