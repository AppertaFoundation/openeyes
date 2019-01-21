<?php

class PatientDiagnosesAndMedicationsWidget extends CWidget
{
    /**
     * @var $patient Patient
     */
    public $patient;


    public function init()
    {

    }

    public function getData()
    {
        return $this->patient;
    }

    public function run()
    {
        $this->render('patientDiagnosesAndMedicationsWidget');
    }
}
