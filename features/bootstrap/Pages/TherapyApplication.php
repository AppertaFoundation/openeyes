<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class TherapyApplication extends Page
{
    protected $path = "/site/OphCoTherapyapplication/Default/create?patient_id={parentId}";

    protected $elements = array(
        #Diagnosis
        'addRightSide' => array('xpath' => "//*[@id='clinical-create']/div[3]/div/div[1]/div[2]/a"),
        'leftDiagnosis' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis1_id']"),
        'rightDiagnosis' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis1_id']"),
        'rightSecondaryTo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis2_id']"),
        'leftSecondaryTo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis2_id']"),

        #Patient Suitability
        'rightTreatment' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_treatment_id']"),
        'leftTreatment' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_treatment_id']"),
        'hopefullFIXRight' => array('xpath' => "//form[@id='clinical-create']/div[4]/div/div[2]/div/div[2]"),
        'hopefullFIXLeft'=> array('xpath' => "//form[@id='clinical-create']/div[4]/div/div/div/div[2]"),
        'rightAngiogramDate' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_angiogram_baseline_date_0']"),
        'leftAngiogramDate' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_angiogram_baseline_date_0']"),
        'calendarDate' => array('xpath' => "//*[@id='ui-datepicker-div']/table/tbody//*[contains(text(),'1')]"),

        #Relative ContraIndications
        'cerebrovascularYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_1']"),
        'cerebrovascularNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_0']"),
        'ischaemicYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_1']"),
        'ischaemicNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_0']"),
        'myocardialYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_1']"),
        'myocardialNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_0']"),

        'consultant' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_consultant_id']"),
    );


    public function addRightSide ()
    {
        $this->getSession()->wait(3000);
        $this->getElement('addRightSide')->click();
    }

    public function rightSideDiagnosis ($diagnosis)
    {
        $this->getElement('rightDiagnosis')->selectOption($diagnosis);
    }

    public function leftSideDiagnosis ($diagnosis)
    {
        $this->getElement('leftDiagnosis')->selectOption($diagnosis);
    }

    public function rightSecondaryTo ($secondary)
    {
        $this->getElement('rightSecondaryTo')->selectOption($secondary);
    }

    public function leftSecondaryTo ($secondary)
    {
        $this->getElement('leftSecondaryTo')->selectOption($secondary);
    }

    public function rightTreatment ($treatment)
    {
        $this->getElement('rightTreatment')->selectOption($treatment);
    }

    public function rightDate ($date)
    {
        $this->getElement('hopefullFIXRight')->click();
//        $this->getSession()->wait(5000);
        $this->getElement('rightAngiogramDate')->click();
        $this->getSession()->wait(5000);
        $this->getElement('calendarDate')->click();
//        $this->getElement('calendarDate')->selectOption($date);

    }

    public function leftTreatment ($treatment)
    {
        $this->getElement('leftTreatment')->selectOption($treatment);
    }

    public function leftDate ($date)
    {
        $this->getElement('hopefullFIXLeft')->click();
//        $this->getSession()->wait(5000);
        $this->getElement('leftAngiogramDate')->click();
        $this->getSession()->wait(5000);
        $this->getElement('calendarDate')->click();
//        $this->getElement('calendarDate')->selectOption($date);
    }

    public function cerebYes ()
    {
        $this->getElement('cerebrovascularYes')->click();
    }

    public function cerebNo ()
    {
        $this->getElement('cerebrovascularNo')->click();
    }

    public function ischaemicYes ()
    {
        $this->getElement('ischaemicYes')->click();
    }

    public function ischaemicNo ()
    {
        $this->getElement('ischaemicNo')->click();
    }

    public function myocardialYes ()
    {
        $this->getElement('myocardialYes')->click();
    }

    public function myocardialNo ()
    {
        $this->getElement('myocardialNo')->click();
    }

    public function consultantSelect ($consultant)
    {
        $this->getElement('consultant')->selectOption($consultant);
    }

    public function saveTherapy ()
    {

    }


}



