<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class AnaestheticAudit extends Page
{
    protected $elements = array(
        'anaesthetist' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_anaesthetist_anaesthetist_select']"),
        'pain' => array('xpath' => "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_pain']"),
        'nausea' => array('xpath' => "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_nausea']"),
        'vomitCheckbox' => array('xpath' => "//div[@id='div_element_ophouanaestheticsatisfactionaudit_satisfaction_vomited']/div[2]/input[2]"), //id element didnt work for this checkbox
        'respirotaryRate' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_respiratory_rate_id']"),
        'oxygenSaturation' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_oxygen_saturation_id']"),
        'systolicBloodPressure' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_systolic_id']"),
        'bodyTemp' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_body_temp_id']"),
        'heartRate' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_heart_rate_id']"),
        'consciousLevelAvpu' => array('xpath' => "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_conscious_lvl_id']"),
        'comments' => array('xpath' => "//textarea[@id='element_ophouanaestheticsatisfactionaudit_notes_comments']"),
        'dischargeYes' => array('xpath' => "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_1']"),
        'dischargeNo' => array('xpath' => "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_2']"),
        'saveEvent' => array('xpath' => "//button[@id='et_save_draft']"),
        'cancelEvent' => array('xpath' => "//*[@id='clinical-create']//*[contains(text(), 'cancel')]"),
        'cancelExam' => array('xpath' => "//*[@id='clinical-create']/div[1]/div/ul/li[2]/a/span"),
    );
}