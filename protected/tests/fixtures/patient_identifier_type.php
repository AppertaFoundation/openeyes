<?php

return array(
    'ID' => array(
        'id' => 1,
        'usage_type' => 'LOCAL',
        'short_title' => 'ID',
        'long_title' => 'ID',
        'institution_id' => '1',
        'validate_regex' => '/^([0-9]{1,9})$/',
    ),
    'NHS' => array(
        'id' => 2,
        'usage_type' => 'GLOBAL',
        'short_title' => 'ID',
        'long_title' => 'National Health Service',
        'institution_id' => '1',
        'validate_regex' => '/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i',
    ),
);
