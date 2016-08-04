<?php


// change the following paths if necessary
$dirname = dirname(__FILE__);

if (file_exists($dirname.'/../vendor/autoload.php')) {
    require_once $dirname.'/../vendor/autoload.php';
}

$yiic = $dirname.'/yii/framework/yiic.php';
$config = $dirname.'/config/console.php';

$n = 2;
while (!file_exists($yiic)) {
    $yiic = $dirname.str_repeat('/..', $n++).'/yii/framework/yiic.php';

    if ($n >= 15) {
        $yiic = $dirname.'/../vendor/yiisoft/yii/framework/yiic.php';
        if (!file_exists($yiic)) {
            echo "Couldn't find yiic.php.\n";
            exit;
        }
    }
}

require_once $yiic;
