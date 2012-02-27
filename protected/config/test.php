<?php
/**
* OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2012
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2012, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

$config = CMap::mergeArray(
	require(dirname(__FILE__).'/common.php'),
	array(
		'name'=>'OpenEyes Test',
		'import'=>array(
			'application.modules.admin.controllers.*',
		),
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=openeyestest',
				'username' => 'root',
				'password' => '',
			),
			'db_pas' => array(
				'connectionString' => 'mysql:host=localhost;dbname=openeyestestpas',
				'username' => 'root',
				'password' => '',
			),
		),
		'params'=>array(
			'auth_source' => 'BASIC',
		),
	)
);

// Check for local test config 
$local_test = dirname(__FILE__).'/local/test.php';
if(file_exists($local_test)) {
	$config = CMap::mergeArray(
		$config,
		require($local_test)
	);
}

return $config;