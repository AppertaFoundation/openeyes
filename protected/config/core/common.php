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

return array(
	'name' => 'OpenEyes',

	// Preloading 'log' component
	'preload' => array('log'),

	// Autoloading model and component classes
	'import' => array(
		'application.vendors.*',
		'application.modules.*',
		'application.models.*',
		'application.models.elements.*',
		'application.components.*',
		'application.components.summaryWidgets.*',
		'application.extensions.tcpdf.*',
		'application.services.*',
		'application.modules.*',
		'application.commands.*',
		'application.commands.shell.*',
		'application.behaviors.*',
		'application.widgets.*',
		'application.controllers.*',
		'application.helpers.*',
		'application.gii.*',
		'system.gii.generators.module.*',
	),

	'modules' => array(
		// Gii tool
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'openeyes',
			'ipFilters'=> array('*')
		),
		'oldadmin',
	),

	// Application components
	'components' => array(
		'mailer' => array(
			'class' => 'Mailer',
			'mode' => 'sendmail',
		),
		'moduleAPI' => array(
			'class' => 'ModuleAPI',
		),
		'request' => array(
			'enableCsrfValidation' => true,
			'class'=>'HttpRequest',
			'noCsrfValidationRoutes'=>array(
				'site/login', //disabled csrf check on login form
			),
		),
		'event' => array(
			'class' => 'OEEventManager',
			'observers' => array(),
		),
		'clientScript' => array(
			'class' => 'ClientScript',
			'packages' => array(
				'flot' => array(
					'js' => array(
						'jquery.flot.js',
						'jquery.flot.time.js',
						'jquery.flot.navigate.js',
					),
					'baseUrl' => 'components/flot',
					'depends' => array('jquery'),
				),
			),
		),
		'user' => array(
			'class' => 'CWebUser',
			// Enable cookie-based authentication
			'allowAutoLogin' => true,
		),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'' => 'site/index',
				'patient/viewpas/<pas_key:\d+>' => 'patient/viewpas',
				'file/view/<id:\d+>/<dimensions:\d+(x\d+)?>/<name:\w+\.\w+>' => 'protectedFile/thumbnail',
				'file/view/<id:\d+>/<name:\w+\.\w+>' => 'protectedFile/view',
				'<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<hospital_num:\d+>' => 'patient/results',
			),
		),
		'db' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=openeyes',
			'emulatePrepare' => true,
			'username' => 'oe',
			'password' => '_OE_PASSWORD_',
			'charset' => 'utf8',
			'schemaCachingDuration' => 300,
		),
		'authManager' => array(
			'class' => 'AuthManager',
			'connectionID' => 'db',
		),
		'cache' => array(
			'class' => 'system.caching.CFileCache',
			'cachePath' => 'cache',
			'directoryLevel' => 1
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'FlushableLogRouter',
			'autoFlush' => 1,
			'routes' => array(
				// Normal logging
				'application' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'info, warning, error',
					'logFile' => 'application.log',
					'maxLogFiles' => 30,
				),
				// Action log
				'action' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'info, warning, error',
					'categories' => 'application.action.*',
					'logFile' => 'action.log',
					'maxLogFiles' => 30,
				),
				// Development logging (application only)
				'debug' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error',
					'categories' => 'application.*',
					'logFile' => 'debug.log',
					'maxLogFiles' => 30,
				),
			),
		),
		'session' => array(
			'class' => 'CDbHttpSession',
			'connectionID' => 'db',
			'sessionTableName' => 'user_session',
			'autoCreateSessionTable' => false
			/*'cookieParams' => array(
				'lifetime' => 300,
			),*/
		),
		'cacheBuster' => array(
			'class'=>'CacheBuster',
		),
		'assetManager' => array(
			'class'=>'AssetManager',
			// Use symbolic links to publish the assets when in debug mode.
			'linkAssets' => defined('YII_DEBUG') && YII_DEBUG,
		),
	),
	'params'=>array(
		'pseudonymise_patient_details' => false,
		'ab_testing' => false,
		'auth_source' => 'BASIC', // Options are BASIC or LDAP.
		// This is used in contact page
		'alerts_email' => 'alerts@example.com',
		'adminEmail' => 'webmaster@example.com',
		'ldap_server' => '',
		'ldap_port' => '',
		'ldap_admin_dn' => '',
		'ldap_password' => '',
		'ldap_dn' => '',
		'ldap_method' => 'native', // use 'zend' for the Zend_Ldap vendor module
		'ldap_native_timeout' => 3,
		'ldap_info_retries' => 3,
		'ldap_info_retry_delay' => 1,
		'ldap_update_name' => false,
		'ldap_update_email' => true,
		'environment' => 'dev',
		'audit_trail' => false,
		'watermark' => '',
		'watermark_admin' => 'You are logged in as admin. So this is OpenEyes Goldenrod Edition',
		'watermark_description' => '',
		'helpdesk_email' => 'helpdesk@example.com',
		'helpdesk_phone' => '12345678',
		'google_analytics_account' => '',
		'local_users' => array(),
		'log_events' => true,
		'urgent_booking_notify_hours' => 24,
		'urgent_booking_notify_email' => array(),
		'urgent_booking_notify_email_from' => 'OpenEyes <helpdesk@example.com>',
		'default_site_code' => '',
		'institution_code' => 'RP6',
		'erod_lead_time_weeks' => 3,
		// specifies which specialties are available in patient summary for diagnoses etc (use specialty codes)
		'specialty_codes' => array(),
		// specifies the order in which different specialties are laid out (use specialty codes)
		'specialty_sort' => array(),
		'hos_num_regex' => '/^([0-9]{1,9})$/',
		'pad_hos_num' => '%07s',
		'profile_user_can_edit' => true,
		'profile_user_can_change_password' => true,
		'menu_bar_items' => array(
			'home' => array(
			'title' => 'Home',
			'uri' => '',
			'position' => 1,
			),
			'logout' => array(
			'title' => 'Logout',
			'uri' => 'site/logout',
			'position' => 9999,
			),
		),
		'admin_menu' => array(
		),
		'enable_transactions' => true,
	),
);
