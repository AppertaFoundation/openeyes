<?php

$config = CMap::mergeArray(
	require(dirname(__FILE__).'/common.php'),
	array(
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
				'username' => 'root',
				'password' => '',
			),
			'db_pas' => array(
				'connectionString' => 'mysql:host=localhost;dbname=openeyestestpas',
				'username' => 'root',
				'password' => '',
			),
		),
		'params'=>array(
			'auth_source' => 'BASIC',
		),
	)
);

// Check for local test config 
$local_test = dirname(__FILE__).'/local/test.php';
if(file_exists($local_test)) {
	$config = CMap::mergeArray(
		$config,
		require($local_test)
	);
}

return $config;