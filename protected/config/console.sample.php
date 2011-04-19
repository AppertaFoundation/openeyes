<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	'commandMap' => array(
		'migrate' => array(
			'class' => 'system.cli.commands.MigrateCommand',
			'migrationPath' => 'application.migrations',
			'migrationTable' => 'tbl_migration',
			'connectionID' => 'db'
		),
	),
));