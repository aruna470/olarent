<?php

// Production environment
$dbParms = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=OlaRent',
    'username' => 'olarent',
    'password' => '01arent',
    'charset' => 'utf8',
];

return $dbParms;
