<?php

$dbPath = dirname(__DIR__) . '/data/database.db';

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . $dbPath,
    // 'dsn' => 'mysql:host=localhost;dbname=example',
    // 'username' => 'root',
    // 'password' => '',
    'charset' => 'utf8',
];
