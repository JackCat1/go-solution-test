<?php

$testDb = require __DIR__ . '/test_db.php';
$config = require __DIR__ . '/web.php';

$config['id'] = 'basic-tests';
$config['components']['db'] = $testDb;
$config['components']['request']['cookieValidationKey'] = 'test';
$config['components']['request']['enableCsrfValidation'] = false;
$config['components']['assetManager']['basePath'] = __DIR__ . '/../web/assets';

return $config;
