<?php


class Appointment extends BaseCWidget
{

    public $patient;
    public $worklists_past_patient;
    public $worklists_patient;
    public $pro_theme = '';

    public function init()
    {
        parent::init();

        $criteria_worklist_past_patient = new CDbCriteria();
        $criteria_worklist_past_patient->addCondition('t.when < NOW()');
        $criteria_worklist_past_patient->order = 't.when asc';

        $this->worklists_past_patient = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria_worklist_past_patient
        );

        $criteria_worklists_patient = new CDbCriteria();
        $criteria_worklists_patient->addCondition('t.when >= NOW()');
        $criteria_worklists_patient->order = 't.when asc';

        $this->worklists_patient = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria_worklists_patient
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