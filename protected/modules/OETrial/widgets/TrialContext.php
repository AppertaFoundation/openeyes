<?php
/**
 *
 */

class TrialContext extends CWidget
{
    protected $trial;
    protected $patient;

    public function renderBackLink(){
        echo 'go back - not implemented';
    }

    public function renderPatientTrialStatus(){
        echo 'patient trial status - not implemented';
    }

    /**
     * @param string $view
     * @param null $data
     * @param bool $return
     * @return string
     * @throws CException
     */
    public function render($view = 'Patient_trial_data', $data = null, $return = false)
    {
        if ($data === null){
            throw new CException('Missing trial and patient data');
        }
        if ($data['trial']){
            $this->trial = $data['trial'];
        } else {
            throw new CException('Missing trial data');
        }
        if ($data['patient']){
            $this->patient = $data['patient'];
        } else {
            throw new CException('Missing patient data');
        }

        return parent::render($view, $data, $return);
    }

    public function renderAddToTrial(){
        parent::render('Add_Remove_participant');
    }

    public function isPatientInTrial(){
        return null !== TrialPatient::getTrialPatient($this->patient, $this->trial->id);
    }
}