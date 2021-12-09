<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$config = array(
    'name' => 'OpenEyes Main',
    'components' => array(),
    );

// Enable logging of php errors to brwser console
// Can be either "true", or can provide the error levels to output (e.g, one or more of trace, error, warning, info, notice)
if (!empty(getenv('LOG_TO_BROWSER'))) {
    $browserlog = array(
    'log' => array(
        'class' => 'CLogRouter',
        'routes' => array(
            'browser' => array(
                'class' => 'CWebLogRoute',
                'levels' => strtolower(trim(getenv('LOG_TO_BROWSER'))) == "true" ? 'error, warning, notice' : trim(getenv('LOG_TO_BROWSER')),
                'showInFireBug' => true,
            ),
        ),
    ),
    );
    // $config['components']['log']['routes'] = array_merge_recursive($config['components']['log']['routes'], $browserlog);
    $config['components'] = CMap::mergeArray($browserlog, $config['components']);
}

// Enable the YII debug bar (appears in top-right of browser)
// To enable for all connections, set YII_DEBUG_BAR_IPS to 0.0.0.0/0
// Can be set to comma separated lists, using the following formats: '127.0.0.1','192.168.1.*', 88.23.23.0/24 (note the '' when using full IPs)
if (!empty(getenv('YII_DEBUG_BAR_IPS'))) {
    $yiidebugbar = array(
        'preload' => array(
            'debug',
        ),
        'components' => array(
            'debug' => array(
                'class' => 'ext.yii2-debug.Yii2Debug',
                'allowedIPs' => array(getenv('YII_DEBUG_BAR_IPS')),
                'showConfig' => true,
            ),
            'db' => array(
                'enableProfiling' => true,
                'enableParamLogging' => true,
            ),
        ),
    );

    $config = CMap::mergeArray($yiidebugbar, $config);
}

return $config;
