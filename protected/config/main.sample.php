<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(
	require(dirname(__FILE__).'/params.php'),
	array(
		'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name'=>'Open Eyes',

		// preloading 'log' component
		'preload'=>array('log'),

		// autoloading model and component classes
		'import'=>array(
			'application.models.*',
			'application.components.*',
			'application.services.*'
		),

		'modules'=>array(
			// uncomment the following to enable the Gii tool
			/*
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'Enter Your Password Here',
				'generatorPaths'=>array('ext.mpgii'), // use multi-activerecord
			),
			*/
			'admin',
		),

		// application components
		'components'=>array(
			'user'=>array(
				// enable cookie-based authentication
				'allowAutoLogin'=>true,
			),
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			// uncomment the following to enable URLs in path-format
			/*
			'urlManager'=>array(
				'urlFormat'=>'path',
				'rules'=>array(
					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				),
			),
			*/
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=openeyes',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
			),
			'db_pas'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=openeyes',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
			),
			'authManager'=>array(
				'class' => 'CDbAuthManager',
				'connectionID' => 'db',
			),
			'errorHandler'=>array(
				// use 'site/error' action to display errors
				'errorAction'=>'site/error',
			),
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning',
					),
					// uncomment the following to show log messages on web pages
					/*
					array(
						'class'=>'CWebLogRoute',
					),
					*/
				),
			),
		)
	)
);