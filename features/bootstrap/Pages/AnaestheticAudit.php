<?php

use \SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class AnaestheticAudit extends Page

{
    protected $path = "/site/OphOuAnaestheticsatisfactionaudit/Default/create?patient_id={patientId}";

    protected $elements = array(

      'anaesthetist' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist_anaesthetist_select']"),
      'pain' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_pain']"),
      'nausea' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_nausea']"),
      'vomitCheckBox' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_vomited']"),
      'respiratoryRate' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_respiratory_rate_id']"),
      'oxygenSaturation' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_oxygen_saturation_id']"),
      'systolicBloodPressure' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_systolic_id']"),
      'bodyTemp' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_body_temp_id']"),
      'heartRate' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_heart_rate_id']"),
      'consciousLevelAvpu' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_conscious_lvl_id']"),
      'comments' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_comments']"),
      'dischargeYes' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_ready_for_discharge_id_1']"),
      'dischargeNo' => array('xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_ready_for_discharge_id_2']"),
      'save' => array('xpath' => "//*[@id='et_save']"),
    );

    public function anaesthetist ($anaesthetist)
    {
        $this->getElement('anaesthetist')->selectOption($anaesthetist);
    }

    public function pain ($pain)
    {
        $this->getElement('pain')->selectOption($pain);
    }

    public function nausea ($nausea)
    {
        $this->getElement('nausea')->selectOption($nausea);
    }

    public function vomitCheckBoxYes ()
    {
        $this->getElement('vomitCheckBox')->check();
    }

    public function vomitCheckBoxNo ()
    {
        $this->getElement('vomitCheckBox')->uncheck();
    }

    public function respiratoryRate ($rate)
    {
        $this->getElement('respiratoryRate')->selectOption($rate);
    }

    public function oxygenSaturation ($saturation)
    {
        $this->getElement('oxygenSaturation')->selectOption($saturation);
    }

    public function systolicBlood ($systolic)
    {
        $this->getElement('systolicBloodPressure')->selectOption($systolic);
    }

    public function bodyTemp ($temp)
    {
        $this->getElement('bodyTemp')->selectOption($temp);
    }

    public function heartRate ($rate)
    {
        $this->getElement('heartRate')->selectOption($rate);
    }

    public function consciousLevel ($level)
    {
        $this->getElement('consciousLevelAvpu')->selectOption($level);
    }

    public function comments ($comments)
    {
        $this->getElement('comments')->setValue($comments);
    }

    public function dischargeYes ()
    {
        $this->getElement('dischargeYes')->click();
    }

    public function dischargeNo ()
    {
        $this->getElement('dischargeNo')->click();
    }

    public function saveEvent ()
    {
        $this->getElement('save')->click();
    }

}