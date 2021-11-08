<?php
return array(
    'pathway1' => array(
        'id' => 1,
        'worklist_patient_id' => 1,
        'pathway_type_id' => 1,
        'status' => Pathway::STATUS_LATER,
        'short_name' => 'default',
        'long_name' => 'Default pathway',
    ),
    'pathway2' => array(
        'id' => 2,
        'worklist_patient_id' => 2,
        'pathway_type_id' => 1,
        'status' => Pathway::STATUS_ACTIVE,
        'short_name' => 'emergency',
        'long_name' => 'Emergency pathway',
    ),
    'pathway3' => array(
        'id' => 3,
        'worklist_patient_id' => 3,
        'pathway_type_id' => 1,
        'status' => Pathway::STATUS_DELAYED,
        'short_name' => 'urgent',
        'long_name' => 'Urgent pathway',
    ),
    'pathway4' => array(
        'id' => 4,
        'worklist_patient_id' => 3,
        'pathway_type_id' => 1,
        'status' => Pathway::STATUS_DISCHARGED,
        'short_name' => 'complete',
        'long_name' => 'Complete pathway',
    ),
    'pathway5' => array(
        'id' => 5,
        'worklist_patient_id' => 3,
        'pathway_type_id' => 1,
        'status' => Pathway::STATUS_DONE,
        'short_name' => 'done',
        'long_name' => 'Closed pathway',
    ),
);
