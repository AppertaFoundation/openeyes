<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);
// change the following paths if necessary
$dirname = dirname(__FILE__);
if (file_exists($dirname.'/../../vendor/yiisoft/yii/framework/yiit.php')) {
    $yiit = $dirname.'/../../vendor/yiisoft/yii/framework/yiit.php';
} else {
    $yiit = $dirname.'/../../protected/yii/framework/yiit.php';
}
$config = dirname(__FILE__).'/../config/test.php';

require_once $yiit;

Yii::createWebApplication($config);

Yii::app()->event->observers = array();

// PHPUnit dies silently with FATAL ERRORS which makes it hard to debug the tests.
register_shutdown_function('PHPUnit_shutdownFunction');
function PHPUnit_shutdownFunction()
{
    // http://www.php.net/manual/en/errorfunc.constants.php
    $error = error_get_last();
    if (!is_null($error)) {
        if ($error['type'] & (E_ERROR + E_PARSE + E_CORE_ERROR + E_COMPILE_ERROR + E_USER_ERROR + E_RECOVERABLE_ERROR)) {
            echo 'Test Bootstrap: Caught untrapped fatal error: ';
            var_export($error);
        }
    }
}
