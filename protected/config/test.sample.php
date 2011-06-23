<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'import'=>array(
			'application.modules.admin.controllers.*',
                        'application.models.*',
                        'application.models.elements.*',
                        'application.components.*',
                        'application.services.*',
                        'application.controllers.*',
                        'application.modules.*',
                        'application.commands.shell.*'
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
                'params'=>array(
                        // Currently test BASIC only.
                        'auth_source' => 'BASIC', // Options are BASIC or LDAP.
                        'use_pas' => 0,
			'pseudonymise_patient_details' => 'yes'
                ),
	)
);
