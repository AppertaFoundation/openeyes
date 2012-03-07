<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

$local_config=dirname(__FILE__).'/protected/config/local/common.php';

foreach (@file($local_config) as $line) {
	if (preg_match('/^[\s\t]+\'environment\'[\s\t]*=>[\s\t]*\'([a-z]+)\'/',$line,$m)) {
		$environment = $m[1];
	}
}

if ($environment == 'dev') {
	define('YII_DEBUG',true);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
