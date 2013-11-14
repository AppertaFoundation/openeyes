<?php
return array(
	'params' => array(
		'menu_bar_items' => array(
			'pedigrees' => array(
				'title' => 'Pedigrees',
				'uri' => 'Genetics/default/index',
				'position' => 40,
			),
		),
		'module_partials' => array(
			'patient_summary_column1' => array(
				'Genetics' => array(
					'_patient_pedigree',
				),
			),
		),
	)
);
