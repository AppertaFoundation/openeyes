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
						),
				),
		),
		'params'=>array(
				'bad_gps' => array(),
		),
);