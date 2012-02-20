<?php

/**
 * Merges with the various parameters set in the params.php file.
 */
return CMap::mergeArray(
	require(dirname(__FILE__).'/params.php'),
	array(
		'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
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
			'application.commands.shell.*',
			'application.controllers.*'
		),

		'modules' => array(
			/*
			// Gii tool
			'gii' => array(
				'class' => 'system.gii.GiiModule',
				'password' => '',
			),
			'admin',
			*/
		),

		// Application components
		'components' => array(
			'user' => array(
				// Enable cookie-based authentication
				'allowAutoLogin' => true,
				//'autoUpdateFlash' => false,
			),
			/*
			'fixture' => array(
				'class' => 'system.test.CDbFixtureManager',
			),
			*/
			'urlManager' => array(
				'urlFormat' => 'path',
				'showScriptName' => false,
				'rules' => array(
					'patient/results/error' => 'site/index',
					'patient/no-results' => 'site/index',
					'patient/no-results-pas' => 'site/index',
					'patient/results/<first_name:.*>/<last_name:.*>/<sort_by:\d+>/<sort_dir:\d+>/<page_num:\d+>'=>'patient/results',
					'patient/viewpas/<pas_key:\d+>' => 'patient/viewpas',
					'patient/viewhosnum/<hos_num:\d+>' => 'patient/viewhosnum',
					'patient/episodes/<id:\d+>/event/<event:\d+>' => 'patient/episodes',
					'patient/episodes/<id:\d+>/episode/<episode:\d+>' => 'patient/episodes',
					'transport/digest/<date:\d+>_<time:\d+>.csv'=>'transport/digest',
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
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
				'schemaCachingDuration' => 300,
			),
			'testdb' => array(
				'class' => 'CDbConnection',
				'connectionString' => 'mysql:host=localhost;dbname=openeyestest',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
			),
			'db_pas' => array(
				'class' => 'CDbConnection',
				'connectionString' => 'mysql:host=localhost;dbname=openeyes',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => '',
				'schemaCachingDuration' => 300,
				// Make default date format the same as MySQL (default is DD-MMM-YY)
				'initSQLs' => array(
					'ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'',
				),
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
					),
					// Development logging (application only)
					'debug' => array(
						'class' => 'CFileLogRoute',
						'levels' => 'trace, info, warning, error',
						'categories' => 'application.*',
						'logFile' => 'debug.log',
					),
					/*
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
					*/
				),
			),
			/*
			'session' => array(
				'class' => 'application.components.CDbHttpSession',
				'connectionID' => 'db',
				'sessionTableName' => 'user_session',
				'autoCreateSessionTable' => false
			),
			*/
		)
	)
);
