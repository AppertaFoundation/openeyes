<?php

return array(
    'Integer' => array(
        'integerMinValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'integer',
            ),
        ),
        'integerMaxValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'integer',
            ),
        ),
        'integerDefaultValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'integer',
            ),
            array(
                'type' => 'compare',
                'operator' => 'greater_equal',
                'compare_field' => 'integerMinValue{$element_num}Field{$field_num}',
                'message' => 'Default value must be greater or equal to minimum value',
            ),
            array(
                'type' => 'compare',
                'operator' => 'lower_equal',
                'compare_field' => 'integerMaxValue{$element_num}Field{$field_num}',
                'message' => 'Default value must be lower or equal to maximum value',
            ),
        ),
        'integerSize{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'message' => 'Please enter a field size',
            ),
            array(
                'type' => 'integer',
            ),
            array(
                'type' => 'compare',
                'operator' => 'greater_equal',
                'compare_value' => 1,
            ),
        ),
        'integerMaxLength{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'integer',
            ),
            array(
                'type' => 'compare',
                'operator' => 'greater_equal',
                'compare_value' => 1,
            ),
        ),
    ),
);
