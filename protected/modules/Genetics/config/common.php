<?php

return array(
	'import' => array(
		'application.modules.Genetics.models.*',
		'application.modules.Genetics.components.*',
	),
	'params' => array(
		'menu_bar_items' => array(
			'pedigrees' => array(
				'title' => 'Pedigrees',
				'uri' => 'Genetics/default/index',
				'position' => 40,
				'restricted' => array('TaskViewPedigreeData'),
			),
		),
		'module_partials' => array(
			'patient_summary_column1' => array(
				'Genetics' => array(
					'_patient_genetics',
				),
			),
		),
		'advanced_search' => array(
			'Genetics' => array(
				'Advanced Patient Search' => 'geneticPatients',
			),
		),
	),
	'components' => array(
	'event' => array(
		'observers' => array(
			'patient_add_diagnosis' => array(
				array(
					'class' => 'DiagnosisObserver',
					'method' => 'patientAddDiagnosis',
				),
			),
			'patient_remove_diagnosis' => array(
				array(
					'class' => 'DiagnosisObserver',
					'method' => 'patientRemoveDiagnosis',
				),
			),
		),
	),
),
);
