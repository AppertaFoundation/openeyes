<?php
return array(
    'worklist_patient1' => array(
        'id' => 1,
        'worklist_id' => 3,
        'when' => date_format(new Datetime(), 'Y-m-d 09:00:00'),
        'patient_id' => 1,
    ),
    'worklist_patient2' => array(
        'id' => 2,
        'worklist_id' => 4,
        'when' => date_format(new Datetime(), 'Y-m-d 09:00:00'),
        'patient_id' => 2,
    ),
    'worklist_patient3' => array(
        'id' => 3,
        'worklist_id' => 5,
        'when' => date_format(new Datetime("+7 days"), 'Y-m-d 09:00:00'),
        'patient_id' => 3,
    ),
);
