<?php

return array(
    'Dropdown list' => array(
        'dropDownMethod{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'message' => 'Please select a dropdown method',
            ),
        ),
        'dropDownFieldSQLTable{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'condition' => array(
                    'field' => 'dropDownMethod{$element_num}Field{$field_num}',
                    'value' => '1',
                ),
                'message' => 'Please select a table',
            ),
            array(
                'type' => 'required',
                'condition' => array(
                    'field' => 'dropDownMethod{$element_num}Field{$field_num}',
                    'value' => '1',
                ),
                'message' => 'Please select a field',
            ),
        ),
    ),
);
