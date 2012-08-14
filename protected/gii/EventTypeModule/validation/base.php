<?php
return array(
	'/^EventTypeModuleCode$/' => array(
		'EventTypeModuleCode' => array(
			array(
				'type' => 'required',
				'field_property' => 'moduleSuffix',
				'message' => 'Please enter an event name',
			),
		),
	),
	'/^elementName([0-9]+)$/' => array(
		'elementName{$element_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please enter an element name',
			),
			array(
				'type' => 'regex',
				'regex' => '/^[a-zA-Z\s]+$/',
				'message' => 'Element name must be letters and spaces only.',
			),
			array(
				'type' => 'exists',
				'exists_method' => 'elementExists',
				'message' => 'Element name is already in use',
			),
		),
	),
	'/^elementName([0-9]+)FieldName([0-9]+)$/' => array(
		'elementName{$element_num}FieldName{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please enter an element name',
			),
			array(
				'type' => 'regex',
				'regex' => '/^[a-z][a-z0-9_]+$/',
				'message' => 'Field name must be a-z, 0-9 and underscores only, and start with a letter.',
			),
		),
	),
	'/^elementName([0-9]+)FieldLabel([0-9]+)$/' => array(
		'elementName{$element_num}FieldLabel{$field_num}' => array(
			array(
				'type' => 'required',
				'message' => 'Please enter a field label',
			),
			array(
				'type' => 'regex',
				'regex' => '/^[a-zA-Z0-9\s]+$/',
				'message' => 'Field label must be letters, numbers and spaces only.',
			),
		),
	),
);
?>
