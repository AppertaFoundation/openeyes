<?php


// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../yii/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

$n = 3;
while (!file_exists($yiic)) {
	$yiic=dirname(__FILE__).str_repeat('/..',$n++).'/yii/framework/yiic.php';

	if ($n >= 15) {
		echo "Couldn't find yiic.php.\n";
		exit;
	}
}

require_once($yiic);
