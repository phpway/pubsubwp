<?php
// set default timezone to suppress PHP warnings
date_default_timezone_set(@date_default_timezone_get());

// autoload library and test classes
$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';
$autoloader->addPsr4('PubSubWP\Tests\\', __DIR__);
