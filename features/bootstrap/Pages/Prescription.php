<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Prescription extends Page
{
    protected $elements = array(
        'prescriptionDropDown' => array('xpath' => "//*[@id='common_drug_id']"),
        'prescriptionStandardSet' => array('xpath' => "//*[@id='drug_set_id']"),
        'prescriptionDose' => array('xpath' => "//*[@id='prescription_item_0_dose']"),
        'prescriptionRoute' => array('xpath' => "//*[@id='prescription_item_0_route_id']"),
        'prescriptionOptions' => array('xpath' => "//*[@id='prescription_item_0_route_option_id']"),
        'prescriptionFrequency' => array('xpath' => "//*[@id='prescription_item_0_frequency_id']"),
        'prescriptionDuration' => array('xpath' => "//*[@id='prescription_item_0_duration_id']"),
        'prescriptionComments' => array('xpath' => "//textarea[@id='element_ophdrprescription_details_comments']"),
    );

}