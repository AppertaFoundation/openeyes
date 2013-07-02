<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class AnaestheticAudit extends Page
{
    public static $anaesthetist= "//select[@id='element_ophouanaestheticsatisfactionaudit_anaesthetist_anaesthetist_select']";
    public static $pain = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_pain']";
    public static $nausea = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_nausea']";
    public static $vomitCheckbox = "//div[@id='div_element_ophouanaestheticsatisfactionaudit_satisfaction_vomited']/div[2]/input[2]"; //id element didnt work for this checkbox
    public static $respirotaryRate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_respiratory_rate_id']";
    public static $oxygenSaturation = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_oxygen_saturation_id']";
    public static $systolicBloodPressure = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_systolic_id']";
    public static $bodyTemp = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_body_temp_id']";
    public static $heartRate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_heart_rate_id']";
    public static $consciousLevelAvpu = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_conscious_lvl_id']";
    public static $comments = "//textarea[@id='element_ophouanaestheticsatisfactionaudit_notes_comments']";
    public static $dischargeYes = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_1']";
    public static $dischargeNo = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_2']";
    public static $saveEvent = "//button[@id='et_save_draft']";
    public static $cancelEvent = "//*[@id='clinical-create']//*[contains(text(), 'cancel')]";
    public static $cancelExam = "//*[@id='clinical-create']/div[1]/div/ul/li[2]/a/span";
}