<?php

$appPath = realpath(dirname(__FILE__) . '/../../../');
//$config = $appPath . '/modules/OphInVisualfields/config/test.php';
require_once($appPath . '/yii/framework/yiit.php');
require_once($appPath . '/components/FhirMarshal.php');
echo 'APP PATH: ' . $appPath .  '/yii/framework/yiit.php' . PHP_EOL;
require_once($appPath . '/yii/framework/test/CTestCase.php');
//require_once(dirname(__FILE__).'/WebTestCase.php');
//Yii::createWebApplication($config);
?>
