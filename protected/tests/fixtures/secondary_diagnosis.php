<?php

return array(
    array(
        'disorder_id' => 1,
        'eye_id' => 1,
        'patient_id' => 2,
        'date' => '2004',
    ),
    array(
        'disorder_id' => 2,
        'eye_id' => 2,
        'patient_id' => 2,
        'date' => '2006',
    ),
    array(
        'disorder_id' => 3,
        'eye_id' => 3,
        'patient_id' => 2,
        'date' => '2005',
    ),
    array(
        'disorder_id' => 5,
        'patient_id' => 2,
        'date' => '2008',
    ),
    array(
        'disorder_id' => 6,
        'patient_id' => 2,
        'date' => '2007',
    ),
    array(
        'disorder_id' => 1,
        'patient_id' => 1,
        'eye_id' => 1,
        'date' => date('Y-m-d', strtotime('-10 days')),
    ),
    'secondaryDiagnoses7' => array(
        'disorder_id' => 2,
        'patient_id' => 1,
        'eye_id' => 2,
        'date' => date('Y-m-d', strtotime('-12 days')),
    ),
    'secondaryDiagnoses8' => array(
        'disorder_id' => 3,
        'patient_id' => 1,
        'eye_id' => 3,
        'date' => date('Y-m-d', strtotime('-22 days')),
    ),
);
