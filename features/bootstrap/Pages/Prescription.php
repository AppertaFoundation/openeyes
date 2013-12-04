<?php

class Prescription extends OpenEyesPage
{
    protected $path = "/site/OphDrPrescription/Default/create?patient_id={parentId}";

    protected $elements = array(
        'prescriptionCommonDrug' => array('xpath' => "//*[@id='common_drug_id']"),
        'prescriptionStandardSet' => array('xpath' => "//*[@id='drug_set_id']"),
        'prescriptionDoseItem0' => array('xpath' => "//*[@id='prescription_item_0_dose']"),
        'prescriptionRouteItem0' => array('xpath' => "//*[@id='prescription_item_0_route_id']"),
        'prescriptionEyeOptionItem0' => array('xpath' => "//*[@id='prescription_item_0_route_option_id']"),
        'prescriptionFrequencyItem0' => array('xpath' => "//*[@id='prescription_item_0_frequency_id']"),
        'prescriptionDurationItem0' => array('xpath' => "//*[@id='prescription_item_0_duration_id']"),
        'prescriptionComments' => array('xpath' => "//textarea[@id='Element_OphDrPrescription_Details_comments']"),
        'prescriptionSaveDraft' => array('xpath' => "//*[@id='et_save_draft']")
    );

    public function prescriptionDropdown ($drug)
    {
        $this->getElement('prescriptionCommonDrug')->selectOption($drug);
        $this->getSession()->wait(3000);
    }

    public function standardSet ($set)
    {
        $this->getElement('prescriptionStandardSet')->selectOption($set);
        $this->getSession()->wait(3000);
    }

    public function item0DoseDrops ($drops)
    {
        $this->getElement('prescriptionDoseItem0')->selectOption($drops);
    }

    public function item0Route ($route)
    {
        $this->getElement('prescriptionRouteItem0')->selectOption($route);
    }

    public function eyeOptionItem0 ($eyes)
    {
        $this->getElement('prescriptionEyeOptionItem0')->selectOption($eyes);
    }

    public function frequencyItem0 ($frequency)
    {
        $this->getElement('prescriptionFrequencyItem0')->selectOption($frequency);
    }

    public function durationItem1 ($duration)
    {
        $this->getElement('prescriptionDurationItem0')->setValue($duration);
        $this->getSession()->wait(3000);
    }

    public function comments ($comments)
    {
        $this->getElement('prescriptionComments')->setValue($comments);

    }

    public function savePrescription ()
    {
        $this->getElement('prescriptionSaveDraft')->click();
    }

}