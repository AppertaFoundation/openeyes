<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Prescription extends Page
{
    public  $prescriptionDropDown = "//*[@id='common_drug_id']";
    public  $prescriptionStandardSet = "//*[@id='drug_set_id']";
    public  $prescriptionDose = "//*[@id='prescription_item_0_dose']";
    public  $prescriptionRoute = "//*[@id='prescription_item_0_route_id']";
    public  $prescriptionOptions = "//*[@id='prescription_item_0_route_option_id']";
    public  $prescriptionFrequency = "//*[@id='prescription_item_0_frequency_id']";
    public  $prescriptionDuration = "//*[@id='prescription_item_0_duration_id']";
    public  $prescriptionComments = "//textarea[@id='element_ophdrprescription_details_comments']";
}