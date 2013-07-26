<?php

class PatientViewPage
{

    public static  $homeButton = "//*[@id='user_nav']//*[contains(text(), 'Home')]";
    public static  $theatreDiaries = "//*[@id='user_nav']//*[contains(text(), 'Theatre Diaries')]";
    public static  $partialBookingsWaitingList = "//*[@id='user_nav']//*[contains(text(), 'Partial bookings waiting list')]";
    public static  $logOut = "//*[@id='user_nav']//*[contains(text(), 'Logout')]";
    public static  $patientSummary = "//*[@id='patientID']//*[contains(text(), 'Patient Summary')]";
    public static  $changeFirmHeaderLink = "//*[@id='user_firm']//*[contains(text(), 'Change')]";
    public static  $userProfile = "//*[@id='user_id']/a";

    public static  $opthDiagnosis = "//button[@id='btn-add_new_ophthalmic_diagnosis']";
    public static  $opthDisorder = "//select[@id='diagnosisselection_ophthalmic_disorder_id']";
    public static  $opthRighteye = "//input[@name='diagnosis_eye']";
    public static  $opthLefteye = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]";
    public static  $opthBotheyes = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]";
    public static  $opthDay = "//select[@name='diagnosis_day']";
    public static  $opthMonth = "//select[@name='diagnosis_month']";
    public static  $opthYear = "//select[@name='diagnosis_year']";
    public static  $opthSaveButton = "//button[@type='submit']";
    public static  $sysDiagnosis = "//button[@id='btn-add_new_systemic_diagnosis']";
    public static  $sysDisorder = "//select[@id='diagnosisselection_systemic_disorder_id']";
    public static  $sysNoneSide = "(//input[@name='diagnosis_eye'])[4]";
    public static  $sysRightSide = "(//input[@name='diagnosis_eye'])[5]";
    public static  $sysBothSide = "(//input[@name='diagnosis_eye'])[6]";
    public static  $sysLeftSide = "(//input[@name='diagnosis_eye'])[7]";
    public static  $sysDay = "(//select[@name='diagnosis_day'])[2]";
    public static  $sysMonth = "(//select[@name='diagnosis_month'])[2]";
    public static  $sysYear = "(//select[@name='diagnosis_year'])[2]";
    public static  $sysSaveButton = "(//button[@type='submit'])[5]";
    public static  $selectAllergy = "//select[@id='allergy_id']";
    public static  $addAllergy = "//button[@id='btn_add_allergy']";
    public static  $cviEdit = "//button[@id='btn-edit_oph_info']";
    public static  $cviStatus = "//select[@id='patientophinfo_cvi_status_id']";
    public static  $cviDay = "(//select[@name='diagnosis_day'])[3]";
    public static  $cviMonth = "(//select[@name='diagnosis_month'])[3]";
    public static  $cviYear = "(//select[@name='diagnosis_year'])[3]";
    public static  $cviSave = "(//button[@type='submit'])[7]";
    public static  $addMedication = "//button[@id='btn-add_medication']";
    public static  $medicationSelect = "//select[@id='drug_id']";
    public static  $medicationRoute = "//select[@id='route_id']";
    public static  $medicationFrequency = "//select[@id='frequency_id']";
    public static  $medicationCalendar = "//input[@id='start_date']";
    public static  $medicationSave = "(//button[@type='submit'])[9]";
    public static  $removeDiagnosisLink = "//*[@class='small removediagnosis']//*[contains(text(), 'remove')]";
    public static  $removeDiagnosis = "//*[contains(text(), 'remove diagnosis')]";
    public static  $removeMedicationLink = "//*[@class='small removemedication']//*[contains(text(), 'remove')]";
    public static  $removeMedication = "//*[contains(text(), 'remove medication')]";
    public static  $removeAllergyLink = "//*[@class='small removeallergy']//*[contains(text(), 'remove')]";
    public static  $removeAllergy = "//*[@class='classy red venti btn_remove_allergy']//*[contains(text(), 'remove allergy')]";

//Pass Calendar Day from Example Table
    public static  function passDateFromTable ($dateFrom) {
        return "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'". $dateFrom ."')]";
}
}