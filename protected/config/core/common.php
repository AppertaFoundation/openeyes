<?php

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
				'admin',
		),

		// Application components
		'components' => array(
				'event' => array(
						'class' => 'OEEventManager',
						'observers' => array(),
				),
				'user' => array(
						// Enable cookie-based authentication
						'allowAutoLogin' => true,
				),
				'urlManager' => array(
						'urlFormat' => 'path',
						'showScriptName' => false,
						'rules' => array(
								'patient/results/error' => 'site/index',
								'patient/no-results' => 'site/index',
								'patient/no-results-pas' => 'site/index',
								'patient/no-results-address'=>'site/index',
								'patient/results/<first_name:.*>/<last_name:.*>/<nhs_num:\w+>/<gender:\w+>/<sort_by:\d+>/<sort_dir:\d+>/<page_num:\d+>'=>'patient/results',
								'patient/viewpas/<pas_key:\d+>' => 'patient/viewpas',
								'patient/viewhosnum/<hos_num:\d+>' => 'patient/viewhosnum',
								'transport/digest/<date:\d+>_<time:\d+>.csv'=>'transport/digest',
				'transport/<page:\d+>' => 'transport/index',
				'transport/<page:\d+>/<date_from:.*>/<date_to:.*>/<include_bookings:.*>/<include_reschedules:.*>/<include_cancellations:.*>' => 'transport/index',
								'<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
								'' => 'site/index', // default action
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
						'class' => 'CDbAuthManager',
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
						'class' => 'application.components.CDbHttpSession',
						'connectionID' => 'db',
						'sessionTableName' => 'user_session',
						'autoCreateSessionTable' => false
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
				'erod_lead_time_weeks' => 3,
		)
);
