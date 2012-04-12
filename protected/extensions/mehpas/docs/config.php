<?php
return array(
		'import' => array(
				'ext.mehpas.*',
				'ext.mehpas.components.*',
				'ext.mehpas.models.*',
		),
		'components' => array(
				'event' => array(
						'observers' => array(
								'patient_search_criteria' => array(
										'search_pas' => array(
												'class' => 'PasObserver',
												'method' => 'searchPas',
										),
								),
								'patient_after_find' => array(
										'update_from_pas' => array(
												'class' => 'PasObserver',
												'method' => 'updatePatientFromPas',
										),
								),
								'gp_after_find' => array(
										'update_from_pas' => array(
												'class' => 'PasObserver',
												'method' => 'updateGpFromPas',
										),
								),
								/* Referral code is currently broken
								'episode_after_create' => array(
										'fetch_pas_referral' => array(
												'class' => 'PasObserver',
												'method' => 'fetchReferralFromPas',
										),
								),
								*/
						),
				),
				'db_pas' => array(
						'class' => 'CDbConnection',
						'connectionString' => 'oci:dbname=remotename:1521/database',
						'emulatePrepare' => false,
						'username' => 'root',
						'password' => '',
						'schemaCachingDuration' => 300,
						// Make oracle default date format the same as MySQL (default is DD-MMM-YY)
						'initSQLs' => array(
								'ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'',
						),
				),
		),
		'params'=>array(
				'mehpas_enabled' => true,
				'mehpas_cache_time' => 300,
				'mehpas_bad_gps' => array(),
		),
);

