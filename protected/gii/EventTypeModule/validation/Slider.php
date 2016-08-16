<?php

return array(
    'Slider' => array(
        'sliderMinValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'message' => 'Please enter a minimum value',
            ),
        ),
        'sliderMinValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'number',
            ),
        ),
        'sliderMaxValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'message' => 'Please enter a maximum value',
            ),
            array(
                'type' => 'number',
            ),
        ),
        'sliderDefaultValue{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'number',
            ),
        ),
        'sliderStepping{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'required',
                'message' => 'Please enter a stepping value',
            ),
            array(
                'type' => 'number_positive',
            ),
        ),
        'sliderForceDP{$element_num}Field{$field_num}' => array(
            array(
                'type' => 'integer_positive',
            ),
        ),
    ),
);
