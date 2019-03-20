<?php


class Appointment extends BaseCWidget
{

    public $patient;
    public $past_worklist_patient;
    public $worklist_patients;
    public $pro_theme = '';

    public function init()
    {
        parent::init();

        $criteria_past_worklist_patient = new CDbCriteria();
        $criteria_past_worklist_patient->addCondition('t.when < NOW()');
        $criteria_past_worklist_patient->order = 't.when asc';

        $this->past_worklist_patient = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria_past_worklist_patient
        );

        $criteria_worklist_patients = new CDbCriteria();
        $criteria_worklist_patients->addCondition('t.when >= NOW()');
        $criteria_worklist_patients->order = 't.when asc';

        $this->worklist_patients = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria_worklist_patients
        );
    }

    public function render($view, $data = null, $return = false)
    {
        if (is_array($data)) {
            $data = array_merge($data, get_object_vars($this));
        } else {
            $data = get_object_vars($this);
        }

        parent::render($view, $data, $return);
    }

    public function run()
    {
        $this->render(get_class($this));
    }
}