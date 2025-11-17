<?php

$db = require __DIR__ . '/db.php';
$db['dsn'] = 'sqlite:' . dirname(__DIR__) . '/data/library_test.db';
$db['enableSchemaCache'] = false;

return $db;
