<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Prescription extends Page
{
    public static $prescriptionDropDown = "//*[@id='common_drug_id']";
    public static $prescriptionStandardSet = "//*[@id='drug_set_id']";
    public static $prescriptionDose = "//*[@id='prescription_item_0_dose']";
    public static $prescriptionRoute = "//*[@id='prescription_item_0_route_id']";
    public static $prescriptionOptions = "//*[@id='prescription_item_0_route_option_id']";
    public static $prescriptionFrequency = "//*[@id='prescription_item_0_frequency_id']";
    public static $prescriptionDuration = "//*[@id='prescription_item_0_duration_id']";
    public static $prescriptionComments = "//textarea[@id='element_ophdrprescription_details_comments']";
}