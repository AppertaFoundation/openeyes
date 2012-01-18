<?php

/**
 * Merges with the various parameters set in the params.php file.
 */
return CMap::mergeArray(
	require(dirname(__FILE__).'/params.php'),
	array(
		'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name'=>'OpenEyes',

		// preloading 'log' component
		'preload'=>array('log'),

		// autoloading model and component classes
		'import'=>array(
			'application.models.*',
			'application.components.*',
			'application.components.summaryWidgets.*',
			'application.controllers.*',
			'application.models.elements.*',
			'application.services.*',
			'application.modules.*'
		),

		'modules'=>array(
			// uncomment the following to enable the Gii tool
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'',
			),
			'admin',
		),

		// application components
		'components'=>array(
			'user'=>array(
				// enable cookie-based authentication
				'allowAutoLogin'=>true,
				'autoUpdateFlash'=>false,
			),
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			// uncomment the following to enable URLs in path-format
			'urlManager'=>array(
				'urlFormat'=>'path',
				'showScriptName'=>false,
				'rules'=>array(
					'patient/results/error'=>'site/index',
					'patient/no-results'=>'site/index',
					'patient/results/<hos_num:\d+>/<first_name:.*>/<last_name:.*>/<nhs_num:[\d-]+>/<gender:.*>/<dob_day:\d+>/<dob_month:\d+>/<dob_year:\d+>/<page_num:\d+>'=>'patient/results',
					'patient/viewhosnum/<hos_num:.+>'=>'patient/viewhosnum',
					'patient/episodes/<id:\d+>/event/<event:\d+>'=>'patient/episodes',
					'patient/episodes/<id:\d+>/episode/<episode:\d+>'=>'patient/episodes',
					'' => 'site/index', // default action
					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>/<hospital_num:\d+>'=>'patient/results',
				),
			),
			// uncomment the following line to use a sqlite database
			/*
			'db'=>array(
				'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
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
				// Make default date format the same as MySQL (default is DD-MMM-YY)
				'initSQLs'=> array(
					'ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'',
				),
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
																				array(
																								'class'=>'CFileLogRoute',
																								'levels'=>'user',
																								'logfile'=>'userActivity.log',
																								'filter' => array(
																												'class' => 'CLogFilter',
																												'prefixSession' => False,
																												'prefixUser' => true,
																												'logUser' => true,
																												'logVars' => array('_GET','_POST'),
																								),
																				),
					// uncomment the following to show log messages on web pages
					/*
					array(
						'class'=>'CWebLogRoute',
					),
					*/
				),
			),
			'widgetFactory' => array(
				'class'=>'CWidgetFactory',
				'widgets' => array(
					'CJuiAccordion' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiButton' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
//					'CJuiDatePicker' => array(
//						'themeUrl'=>'/css/jqueryui',
//						'theme'=>'theme'
//					),
					'CJuiDialog' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiDraggable' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiDroppable' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiInputWidget' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiProgressBar' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiResizable' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiSelectable' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiSlider' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiSliderInput' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiSortable' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiTabs' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
					'CJuiWidget' => array(
						'themeUrl'=>'/css/jqueryui',
						'theme'=>'theme'
					),
				),
			),
			'session' => array(
				'class' => 'application.components.CDbHttpSession',
				'connectionID' => 'db',
				'sessionTableName' => 'user_session',
				'autoCreateSessionTable' => false
			)
		)
	)
);
