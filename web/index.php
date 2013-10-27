<?php

$autoloader = require(dirname(__DIR__) . '/app/bootstrap.php');
$app = require(dirname(__DIR__).'/app/app.php');
$app->run();