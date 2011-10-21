<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
$params=dirname(__FILE__).'/protected/config/params.php';

// Default to live so stacktraces are not displayed with exceptions by default
$environment = 'live';

foreach (@file($params) as $line) {
	if (preg_match('/^[\s\t]+\'environment\'[\s\t]*=>[\s\t]*\'([a-z]+)\'/',$line,$m)) {
		switch ($m[1]) {
			case 'live':
			case 'staging':
			case 'training':
			case 'dev':
				$environment = $m[1];
				break;
		}
		break;
	}
}

if ($environment == 'dev') {
	defined('YII_DEBUG') or define('YII_DEBUG',true);
}
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
