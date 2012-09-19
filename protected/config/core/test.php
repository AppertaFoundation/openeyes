<?php

return 	array(
		'name'=>'OpenEyes Test',
		'import'=>array(
				'application.modules.admin.controllers.*',
		),
		'components'=>array(
				'fixture'=>array(
						'class'=>'system.test.CDbFixtureManager',
				),
				'db'=>array(
						'connectionString' => 'mysql:host=localhost;dbname=openeyestest',
						'username' => 'oe',
						'password' => '_OE_TESTDB_PASSWORD_',
				),
		),
);
