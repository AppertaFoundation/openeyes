<?php

return array(
    'step1' => array(
        'id' => 1,
        'pathway_id' => 1,
        'step_type_id' => 5,
        'status' => PathwayStep::STEP_COMPLETED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'Bio',
        'long_name' => 'Biometry',
    ),
    'step2' => array(
        'id' => 2,
        'pathway_id' => 1,
        'step_type_id' => 7,
        'status' => PathwayStep::STEP_STARTED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'Exam',
        'long_name' => 'Examination',
    ),
    'step3' => array(
        'id' => 3,
        'pathway_id' => 1,
        'step_type_id' => 10,
        'status' => PathwayStep::STEP_REQUESTED,
        'todo_order' => 1,
        'short_name' => 'Rx',
        'long_name' => 'Prescription',
    ),
    'step4' => array(
        'id' => 4,
        'pathway_id' => 2,
        'step_type_id' => 5,
        'status' => PathwayStep::STEP_COMPLETED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'bio',
        'long_name' => 'Biometry',
    ),
    'step5' => array(
        'id' => 5,
        'pathway_id' => 3,
        'step_type_id' => 5,
        'status' => PathwayStep::STEP_COMPLETED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'bio',
        'long_name' => 'Biometry',
    ),
    'step6' => array(
        'id' => 6,
        'pathway_id' => 2,
        'step_type_id' => 11,
        'status' => PathwayStep::STEP_REQUESTED,
        'todo_order' => 1,
        'short_name' => 'Rx',
        'long_name' => 'Prescription',
    ),
    'step7' => array(
        'id' => 7,
        'pathway_id' => 4,
        'step_type_id' => 5,
        'status' => PathwayStep::STEP_COMPLETED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'bio',
        'long_name' => 'Biometry',
    ),
    'step8' => array(
        'id' => 8,
        'pathway_id' => 5,
        'step_type_id' => 5,
        'status' => PathwayStep::STEP_COMPLETED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'bio',
        'long_name' => 'Biometry',
    ),
    'checkInStep1' => array(
        'id' => 9,
        'pathway_id' => 6,
        'step_type_id' => 13,
        'status' => PathwayStep::STEP_REQUESTED,
        'todo_order' => 1,
        'queue_order' => 1,
        'short_name' => 'checkin',
        'long_name' => 'Check in',
    ),
);
