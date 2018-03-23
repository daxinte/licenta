<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';

$app->run();
