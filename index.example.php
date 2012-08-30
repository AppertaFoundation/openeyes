<?php

$depth = 1;

while (1) {
	$yii=dirname(__FILE__).'/'.str_repeat('../',$depth).'yii/framework/yii.php';

	if (file_exists($yii)) break;

	$depth++;

	if ($depth >= 100) {
		die("yii directory not found. please install yii below the docroot.");
	}
}

// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/main.php';

$common_config=dirname(__FILE__).'/protected/config/core/common.php';
$local_common_config=dirname(__FILE__).'/protected/config/local/common.php';

foreach (array($common_config,$local_common_config) as $configfile) {
	foreach (@file($configfile) as $line) {
		if (preg_match('/^[\s\t]+\'environment\'[\s\t]*=>[\s\t]*\'([a-z]+)\'/',$line,$m)) {
			$environment = $m[1];
		}
	}
}

if ($environment == 'dev') {
	define('YII_DEBUG',true);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
