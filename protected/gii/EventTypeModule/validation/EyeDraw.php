<?php
return array(
	'EyeDraw' => array(
		'eyedrawClass{$element_num}Field{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please select an eyedraw type',
			),
		),
		'eyedrawSize{$element_num}Field{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please enter a size (in pixels)',
			),
			array(
				'type' => 'integer_positive',
				'message' => 'Size must be specified as a number of pixels',
			),
		),
	),
);
?>
