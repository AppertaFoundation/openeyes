<?php

return array(
    'trial_patient_1' => array(
        'external_trial_identifier' => 'abc',
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'patient_id' => $this->getRecord('patient', 'patient1')->id,
        'patient_status' => TrialPatient::STATUS_SHORTLISTED,
        'treatment_type' => TrialPatient::TREATMENT_TYPE_UNKNOWN,
    ),
    'trial_patient_2' => array(
        'external_trial_identifier' => 'def',
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'patient_id' => $this->getRecord('patient', 'patient3')->id,
        'patient_status' => TrialPatient::STATUS_SHORTLISTED,
        'treatment_type' => TrialPatient::TREATMENT_TYPE_UNKNOWN,
    ),

    'trial_patient_3' => array(
        'external_trial_identifier' => 'dvorak',
        'trial_id' => $this->getRecord('trial', 'trial2')->id,
        'patient_id' => $this->getRecord('patient', 'patient3')->id,
        'patient_status' => TrialPatient::STATUS_ACCEPTED,
        'treatment_type' => TrialPatient::TREATMENT_TYPE_UNKNOWN,
    ),

    'trial_patient_4' => array(
        'external_trial_identifier' => 'qwerty',
        'trial_id' => $this->getRecord('trial', 'trial3')->id,
        'patient_id' => $this->getRecord('patient', 'patient4')->id,
        'patient_status' => TrialPatient::STATUS_ACCEPTED,
        'treatment_type' => TrialPatient::TREATMENT_TYPE_INTERVENTION,
    ),


    'trial_patient_non_intervention_1' => array(
        'external_trial_identifier' => 'wasd',
        'trial_id' => $this->getRecord('trial', 'non_intervention_trial_1')->id,
        'patient_id' => $this->getRecord('patient', 'patient1')->id,
        'patient_status' => TrialPatient::STATUS_SHORTLISTED,
        'treatment_type' => TrialPatient::TREATMENT_TYPE_UNKNOWN,
    ),
);
