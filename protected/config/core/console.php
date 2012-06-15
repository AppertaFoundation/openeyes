<?php 

return array(
		'name'=>'OpenEyes Console',
		'commandMap' => array(
				'migrate' => array(
						'class' => 'system.cli.commands.MigrateCommand',
						'migrationPath' => 'application.migrations',
						'migrationTable' => 'tbl_migration',
						'connectionID' => 'db'
				),
		),
);
