<?php
/**
 *
 */

class TrialContext extends CWidget
{
    public $trial;
    public $patient;

    /**
     * Renders the status of a patient in trials
     * @param array|null $data
     * @throws CException
     */
    public function renderPatientTrialStatus($data = null)
    {
        $this->ensureTrialAndPatientSet($data);
        parent::render('Patient_trial_data');
    }

    /**
     * Show default items
     * @param string $view
     * @param array|null $data
     * @param bool $return
     * @return string
     * @throws CException
     */
    public function render($view = 'Patient_trial_data', $data = null, $return = false)
    {
        $this->ensureTrialAndPatientSet($data);
        return parent::render($view, $data, $return);
    }

    /***
     * Renders the link to Add/Remove the patient to/from the trial
     * @param array|null $data array containing patient/trial to set
     * @throws CException
     */
    public function renderAddToTrial($data = null)
    {
        $this->ensureTrialAndPatientSet($data);
        parent::render('Add_Remove_participant');
    }

    /***
     * @return bool Whether the patient belongs to the trial
     */
    public function isPatientInTrial()
    {
        return null !== TrialPatient::getTrialPatient($this->patient, $this->trial->id);
    }

    /***
     * @param array|null $data array containing patient and trail
     * @throws CException When either trial or patient remained unset
     */
    private function ensureTrialAndPatientSet($data = null){
        $trial_data = $this->ensureVarSet('trial', $data);
        $patient_data = $this->ensureVarSet('patient', $data);
        $this->throwContextDataNotFound($trial_data, $patient_data);
    }

    /***
     * @param string $var name of class variable to set
     * @param array|null $data array containing the new value
     * @return bool Whether the variable was set
     */
    private function ensureVarSet($var, $data = null){
        if ($data && $data[$var]) {
            $this->$var = $data[$var];
            return true;
        } else {
            return isset($this->$var);
        }
    }

    /**
     * Throw appropriate exceptions when patient and trial data is missing
     * @param $trial_data
     * @param $patient_data
     * @throws CException
     */
    private function throwContextDataNotFound($trial_data, $patient_data)
    {
        if (!$trial_data && !$patient_data) {
            throw new CException('Missing trial and patient data');
        } elseif (!$trial_data) {
            throw new CException('Missing trial data');
        } elseif (!$patient_data) {
            throw new CException('Missing patient data');
        }
    }
}