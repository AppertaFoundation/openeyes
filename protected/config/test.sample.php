<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'import'=>array(
			'application.vendors.*'
		),
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			'db'=>array(
				'class' => 'CDbConnection',
				'connectionString' => 'mysql:host=localhost;dbname=openeyestest;',
			),
		),
	)
);
