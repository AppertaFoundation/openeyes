<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
$dirname = dirname(__FILE__);
if (file_exists($dirname . '/vendor/autoload.php')){
    require_once($dirname . '/vendor/autoload.php');
}

if (file_exists($dirname . '/vendor/yiisoft/yii/framework/yii.php')) {
    $yii = $dirname . '/vendor/yiisoft/yii/framework/yii.php';
} else {
    $yii = $dirname . '/protected/yii/framework/yii.php';
}
$config = $dirname . '/protected/config/main.php';
$common_config = $dirname . '/protected/config/core/common.php';
$local_common_config = $dirname . '/protected/config/local/common.php';

foreach (array($common_config, $local_common_config) as $configfile) {
    foreach (@file($configfile) as $line) {
        if (preg_match('/^[\s\t]+\'environment\'[\s\t]*=>[\s\t]*\'([a-z]+)\'/', $line, $m)) {
            $environment = $m [1];
        }
    }
}

if (isset ($environment) && $environment === 'dev') {
    define('YII_DEBUG', true);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once($yii);
Yii::createWebApplication($config)->run();
