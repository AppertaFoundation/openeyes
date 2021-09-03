<?php
/**
 *
 */

class PatientTrialSummary extends CWidget
{
    public $patient;

    /**
     * @throws CException
     */
    public function run()
    {
        parent::render('patient_trial_summary');
    }
}
