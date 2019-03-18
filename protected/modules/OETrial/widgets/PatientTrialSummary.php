<?php
/**
 *
 */

class PatientTrialSummary extends CWidget
{
    public $patient;

    public function run()
    {
        parent::render('patient_trial_summary');
    }
}