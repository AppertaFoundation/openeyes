<?php

return array(
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=openeyes',
			'username' => 'root',
			'password' => '',
		),
		'db_pas' => array(
			'connectionString' => 'oci:dbname=remotename:1521/database',
			'emulatePrepare' => false,
			'username' => 'root',
			'password' => '',
			// Make oracle default date format the same as MySQL (default is DD-MMM-YY)
			'initSQLs' => array(
				'ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'',
			),
		),
		/*
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
	'params'=>array(
		'use_pas' => true,
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
		'bad_gps' => array(),
		'local_users' => array('admin','username'),
		//'log_events' => true,
		//'urgent_booking_notify_hours' => 24,
		'urgent_booking_notify_email' => array(
			'alerts@example.com',
		),
		'urgent_booking_notify_email_from' => 'OpenEyes <helpdesk@example.com>'
	),
);
