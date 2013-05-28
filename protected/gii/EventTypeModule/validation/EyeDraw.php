<?php
return array(
	'EyeDraw' => array(
		'eyedrawToolbarDoodle{$element_num}Field{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please select at least one toolbar doodle',
			),
		),
		'eyedrawDefaultDoodle{$element_num}Field{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please select at least one default doodle',
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
