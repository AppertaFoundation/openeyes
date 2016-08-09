<?php

return array(
    'Textbox' => array(
        'textBoxSize{$element_num}Field{$field_num}' => array(
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
        'textBoxMaxLength{$element_num}Field{$field_num}' => array(
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
