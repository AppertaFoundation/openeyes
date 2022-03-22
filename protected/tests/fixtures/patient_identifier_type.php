<?php

return array(
    'ID' => array(
        'id' => 1,
        'usage_type' => 'LOCAL',
        'short_title' => 'ID',
        'long_title' => 'ID',
        'institution_id' => '1',
        'validate_regex' => '/^([0-9]{1,9})$/',
        'unique_row_string' => 'LOCAL-1-0',
    ),
    'NHS' => array(
        'id' => 2,
        'usage_type' => 'GLOBAL',
        'short_title' => 'ID',
        'long_title' => 'National Health Service',
        'institution_id' => '1',
        'validate_regex' => '/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i',
        'unique_row_string' => 'GLOBAL-181-0',
    ),
);
