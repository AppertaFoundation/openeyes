<?php

return array(
    'trial_patient_1' => array(
        'external_trial_identifier' => 'abc',
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'patient_id' => $this->getRecord('patient', 'patient1')->id,
        'status_id' => $this->getRecord('trial_patient_status', 'trial_patient_status_shortlisted')->id,
        'treatment_type_id' => $this->getRecord('treatment_type', 'treatment_type_unknown')->id,
    ),
    'trial_patient_2' => array(
        'external_trial_identifier' => 'def',
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'patient_id' => $this->getRecord('patient', 'patient3')->id,
        'status_id' => $this->getRecord('trial_patient_status', 'trial_patient_status_shortlisted')->id,
        'treatment_type_id' => $this->getRecord('treatment_type', 'treatment_type_unknown')->id,
    ),

    'trial_patient_3' => array(
        'external_trial_identifier' => 'dvorak',
        'trial_id' => $this->getRecord('trial', 'trial2')->id,
        'patient_id' => $this->getRecord('patient', 'patient3')->id,
        'status_id' => $this->getRecord('trial_patient_status', 'trial_patient_status_accepted')->id,
        'treatment_type_id' => $this->getRecord('treatment_type', 'treatment_type_unknown')->id,
    ),

    'trial_patient_4' => array(
        'external_trial_identifier' => 'qwerty',
        'trial_id' => $this->getRecord('trial', 'trial3')->id,
        'patient_id' => $this->getRecord('patient', 'patient4')->id,
        'status_id' => $this->getRecord('trial_patient_status', 'trial_patient_status_accepted')->id,
        'treatment_type_id' => $this->getRecord('treatment_type', 'treatment_type_intervention')->id,
    ),


    'trial_patient_non_intervention_1' => array(
        'external_trial_identifier' => 'wasd',
        'trial_id' => $this->getRecord('trial', 'non_intervention_trial_1')->id,
        'patient_id' => $this->getRecord('patient', 'patient1')->id,
        'status_id' => $this->getRecord('trial_patient_status', 'trial_patient_status_shortlisted')->id,
        'treatment_type_id' => $this->getRecord('treatment_type', 'treatment_type_unknown')->id,
    ),
);
