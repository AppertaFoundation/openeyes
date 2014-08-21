<?php

return array(
	'import' => array(
		'application.modules.genetics.models.*',
		'application.modules.genetics.components.*',
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
					'class' => 'DiagnosesObserver',
					'method' => 'patientRemoveDiagnosis',
				),
			),
		),
	),
),
);
