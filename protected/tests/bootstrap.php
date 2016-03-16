<?php

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);
// change the following paths if necessary
$dirname=dirname(__FILE__);
if (file_exists($dirname . '/../../vendor/yiisoft/yii/framework/yiit.php')) {
    $yiit = $dirname . '/../../vendor/yiisoft/yii/framework/yiit.php';
} else {
    $yiit = $dirname . '/../../protected/yii/framework/yiit.php';
}
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);

Yii::createWebApplication($config);

Yii::app()->event->observers = array();
