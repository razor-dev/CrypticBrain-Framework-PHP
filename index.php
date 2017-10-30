<?php

defined('APP_MODE') or define('APP_MODE', 'production'); // production | debug | demo | off
defined('APP_PATH') or define('APP_PATH', dirname(__FILE__));
defined('DS') or define('DS', DIRECTORY_SEPARATOR);


$config = APP_PATH . '/protected/config/';

require_once(APP_PATH . '/application/CrypticBrain.php');

CrypticBrain::init($config)->run();