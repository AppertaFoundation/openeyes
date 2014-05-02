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
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

$config = array(
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=openeyes',
			'username' => 'root',
			'password' => '',
		),
		'session' => array(
			'timeout' => 86400
		),
		/*
		'cacheBuster' => array(
			'time' => '2013062101',
		),
		'log' => array(
			'routes' => array(
				 // SQL logging
				'system' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error',
					'categories' => 'system.db.CDbCommand',
					'logFile' => 'sql.log',
				),
				// System logging
				'system' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error',
					'categories' => 'system.*',
					'logFile' => 'system.log',
				),
				// Profiling
				'profile' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'profile',
					'logFile' => 'profile.log',
				),
				// User activity logging
				'user' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'user',
					'logfile' => 'user.log',
					'filter' => array(
						'class' => 'CLogFilter',
						'prefixSession' => false,
						'prefixUser' => true,
						'logUser' => true,
						'logVars' => array('_GET','_POST'),
					),
				),
				// Log to browser
				'browser' => array(
					'class' => 'CWebLogRoute',
				),
			),
		),
		*/
	),

	'modules' => array(
	),

	'params'=>array(
		//'pseudonymise_patient_details' => false,
		//'ab_testing' => false,
		'auth_source' => 'LDAP',
		// This is used in contact page
		//'alerts_email' => 'alerts@example.com',
		//'adminEmail' => 'webmaster@example.com',
		'ldap_server' => 'ldap.example.com',
		//'ldap_port' => '',
		'ldap_admin_dn' => 'CN=openeyes,CN=Users,dc=example,dc=com',
		'ldap_password' => '',
		'ldap_dn' => 'CN=Users,dc=example,dc=com',
		'environment' => 'live',
		//'watermark' => '',
		//'watermark_admin' => 'You are logged in as admin. So this is OpenEyes Goldenrod Edition',
		//'watermark_description' => '',
		'helpdesk_email' => 'helpdesk@example.com',
		'helpdesk_phone' => '12345678',
		'google_analytics_account' => '',
		'local_users' => array('admin','username'),
		//'log_events' => true,
		'specialty_codes' => array(130),
		//'default_site_code' => '',
		'specialty_sort' => array(130, 'SUP')
	),
);

return $config;
