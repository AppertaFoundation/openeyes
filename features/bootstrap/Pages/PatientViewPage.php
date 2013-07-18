<?php

class PatientViewPage
{

    public  $homeButton = "//*[@id='user_nav']//*[contains(text(), 'Home')]";
    public  $theatreDiaries = "//*[@id='user_nav']//*[contains(text(), 'Theatre Diaries')]";
    public  $partialBookingsWaitingList = "//*[@id='user_nav']//*[contains(text(), 'Partial bookings waiting list')]";
    public  $logOut = "//*[@id='user_nav']//*[contains(text(), 'Logout')]";
    public  $patientSummary = "//*[@id='patientID']//*[contains(text(), 'Patient Summary')]";
    public  $changeFirmHeaderLink = "//*[@id='user_firm']//*[contains(text(), 'Change')]";
    public  $userProfile = "//*[@id='user_id']/a";

    public  $opthDiagnosis = "//button[@id='btn-add_new_ophthalmic_diagnosis']";
    public  $opthDisorder = "//select[@id='diagnosisselection_ophthalmic_disorder_id']";
    public  $opthRighteye = "//input[@name='diagnosis_eye']";
    public  $opthLefteye = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]";
    public  $opthBotheyes = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]";
    public  $opthDay = "//select[@name='diagnosis_day']";
    public  $opthMonth = "//select[@name='diagnosis_month']";
    public  $opthYear = "//select[@name='diagnosis_year']";
    public  $opthSaveButton = "//button[@type='submit']";
    public  $sysDiagnosis = "//button[@id='btn-add_new_systemic_diagnosis']";
    public  $sysDisorder = "//select[@id='diagnosisselection_systemic_disorder_id']";
    public  $sysNoneSide = "(//input[@name='diagnosis_eye'])[4]";
    public  $sysRightSide = "(//input[@name='diagnosis_eye'])[5]";
    public  $sysBothSide = "(//input[@name='diagnosis_eye'])[6]";
    public  $sysLeftSide = "(//input[@name='diagnosis_eye'])[7]";
    public  $sysDay = "(//select[@name='diagnosis_day'])[2]";
    public  $sysMonth = "(//select[@name='diagnosis_month'])[2]";
    public  $sysYear = "(//select[@name='diagnosis_year'])[2]";
    public  $sysSaveButton = "(//button[@type='submit'])[5]";
    public  $selectAllergy = "//select[@id='allergy_id']";
    public  $addAllergy = "//button[@id='btn_add_allergy']";
    public  $cviEdit = "//button[@id='btn-edit_oph_info']";
    public  $cviStatus = "//select[@id='patientophinfo_cvi_status_id']";
    public  $cviDay = "(//select[@name='diagnosis_day'])[3]";
    public  $cviMonth = "(//select[@name='diagnosis_month'])[3]";
    public  $cviYear = "(//select[@name='diagnosis_year'])[3]";
    public  $cviSave = "(//button[@type='submit'])[7]";
    public  $addMedication = "//button[@id='btn-add_medication']";
    public  $medicationSelect = "//select[@id='drug_id']";
    public  $medicationRoute = "//select[@id='route_id']";
    public  $medicationFrequency = "//select[@id='frequency_id']";
    public  $medicationCalendar = "//input[@id='start_date']";
    public  $medicationSave = "(//button[@type='submit'])[9]";
    public  $removeDiagnosisLink = "//*[@class='small removediagnosis']//*[contains(text(), 'remove')]";
    public  $removeDiagnosis = "//*[contains(text(), 'remove diagnosis')]";
    public  $removeMedicationLink = "//*[@class='small removemedication']//*[contains(text(), 'remove')]";
    public  $removeMedication = "//*[contains(text(), 'remove medication')]";
    public  $removeAllergyLink = "//*[@class='small removeallergy']//*[contains(text(), 'remove')]";
    public  $removeAllergy = "//*[@class='classy red venti btn_remove_allergy']//*[contains(text(), 'remove allergy')]";

//Pass Calendar Day from Example Table
    public  function passDateFromTable ($dateFrom) {
        return "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'". $dateFrom ."')]";
}
}