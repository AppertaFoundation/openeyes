<?php

$config = CMap::mergeArray(
	require(dirname(__FILE__).'/common.php'),
	array(
		'name'=>'OpenEyes Console',
		'commandMap' => array(
			'migrate' => array(
				'class' => 'system.cli.commands.MigrateCommand',
				'migrationPath' => 'application.migrations',
				'migrationTable' => 'tbl_migration',
				'connectionID' => 'db'
			),
		),
	)
);

// Check for local console config
$local_console = dirname(__FILE__).'/local/console.php';
if(file_exists($local_console)) {
	$config = CMap::mergeArray(
		$config,
		require($local_console)
	);
}

return $config;
