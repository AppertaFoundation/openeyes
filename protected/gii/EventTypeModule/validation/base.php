<?php

return array(
    '/^EventTypeModuleCode$/' => array(
        'EventTypeModuleCode' => array(
            array(
                'type' => 'required',
                'field_property' => 'moduleSuffix',
                'message' => 'Please enter an event name',
            ),
            array(
                'type' => 'length',
                'field_property' => 'moduleShortSuffix',
                'max' => 20,
                'regstrip' => '/\s+/',
                'message' => 'Event name cannot be more than 20 characters (not including spaces)',
            ),
            array(
                'type' => 'required',
                'field_property' => 'moduleShortSuffix',
                'message' => 'Please enter an event short name',
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
    '/^elementShortName([0-9]+)$/' => array(
            'elementShortName{$element_num}' => array(
                    array(
                            'type' => 'required',
                            'message' => 'Please enter an element short name',
                    ),
                    array(
                            'type' => 'regex',
                            'regex' => '/^[a-zA-Z_]+$/',
                            'message' => 'Element short name must be letters and underscores only.',
                    ),
                    array(
                            'type' => 'exists',
                            'exists_method' => 'elementShortNameExists',
                            'message' => 'Element short name is already in use',
                    ),
                    array(
                            'type' => 'length',
                            'max' => 11,
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
            // need to limit the length of the field name for fields which will generate another table
            array(
                'type' => 'length',
                'max' => 11,
                'regstrip' => '/_id$/',
                'condition' => array(
                    'field' => 'elementType{$element_num}FieldType{$field_num}',
                    'value_list' => array('Multi select'),
                ),
                'message' => 'field name cannot be longer than 11 characters for this field type (excluding _id postfix)',

            ),
            array(
                    'type' => 'length',
                    'max' => 20,
                    'regstrip' => '/_id$/',
                    'condition' => array(
                            'field' => 'elementType{$element_num}FieldType{$field_num}',
                            'value_list' => array('Dropdown list', 'Radio buttons', 'Textarea with dropdown'),
                    ),
                    'message' => 'field name cannot be longer than 20 characters for this field type (excluding _id postfix)',

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
