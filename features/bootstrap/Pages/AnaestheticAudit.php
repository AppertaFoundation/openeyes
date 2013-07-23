<?php


class AnaestheticAudit
{
    public  $anaesthetist= "//select[@id='element_ophouanaestheticsatisfactionaudit_anaesthetist_anaesthetist_select']";
    public  $pain = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_pain']";
    public  $nausea = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_nausea']";
    public  $vomitCheckbox = "//div[@id='div_element_ophouanaestheticsatisfactionaudit_satisfaction_vomited']/div[2]/input[2]"; //id element didnt work for this checkbox
    public  $respirotaryRate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_respiratory_rate_id']";
    public  $oxygenSaturation = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_oxygen_saturation_id']";
    public  $systolicBloodPressure = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_systolic_id']";
    public  $bodyTemp = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_body_temp_id']";
    public  $heartRate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_heart_rate_id']";
    public  $consciousLevelAvpu = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_conscious_lvl_id']";
    public  $comments = "//textarea[@id='element_ophouanaestheticsatisfactionaudit_notes_comments']";
    public  $dischargeYes = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_1']";
    public  $dischargeNo = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_2']";
    public  $saveEvent = "//button[@id='et_save_draft']";
    public  $cancelEvent = "//*[@id='clinical-create']//*[contains(text(), 'cancel')]";
    public  $cancelExam = "//*[@id='clinical-create']/div[1]/div/ul/li[2]/a/span";
}