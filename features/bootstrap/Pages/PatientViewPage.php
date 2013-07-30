<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class PatientViewPage extends Page
{
    protected $path = "/site/patient/view";

    protected $elements = array(
        'homeButton' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Home')]"),
        'theatreDiaries' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Theatre Diaries')]"),
        'partialBookingsWaiting' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Partial bookings waiting list')]"),
        'logOut' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Logout')]"),
        'patientSummary' => array('xpath' => "//*[@id='patientID']//*[contains(text(), 'Patient Summary')]"),
        'userProfile' => array('xpath' => "//*[@id='user_id']/a"),
        'addOpthalmicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_ophthalmic_diagnosis']"),
        'selectOphthalmicDisorder' => array('xpath' => "//select[@id='diagnosisselection_ophthalmic_disorder_id']"),
        'rightEye' => array('xpath' => "//input[@name='diagnosis_eye']"),
        'leftEye' => array('xpath' => "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]"),
        'bothEyes' => array('xpath' => "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]"),
        'opthDay' => array('xpath' => "//select[@name='fuzzy_day']"),
        'opthMonth' => array('xpath' => "//select[@name='fuzzy_month']"),
        'opthYear' => array('xpath' => "//select[@name='fuzzy_year']"),
        'opthSaveButton' => array('xpath' => "//button[@type='submit']//*[contains(text(),'Save')]")



    );

    public function addOpthalmicDiagnosis ($diagnosis)
    {
        $this->getElement('addOpthalmicDiagnosis')->press();
        $this->getElement('selectOphthalmicDisorder')->selectOption($diagnosis);
    }

    public function selectEye ($eye)
    {
        $eyesArray = array(

            "Right" => 'rightEye',
            "Both" => 'leftEye',
            "Left" => 'bothEyes'
        );

        $this->getElement($eyesArray, $eye)->press();
    }

    public function addOpthalmicDiagnosisDate ($day, $month, $year)
    {
        $this->getElement('opthDay')->selectOption($day);
        $this->getElement('opthMonth')->selectOption($month);
        $this->getElement('opthYear')->selectOption($year);
    }
}

//    public static  $sysDiagnosis = "//button[@id='btn-add_new_systemic_diagnosis']";
//    public static  $sysDisorder = "//select[@id='diagnosisselection_systemic_disorder_id']";
//    public static  $sysNoneSide = "(//input[@name='diagnosis_eye'])[4]";
//    public static  $sysRightSide = "(//input[@name='diagnosis_eye'])[5]";
//    public static  $sysBothSide = "(//input[@name='diagnosis_eye'])[6]";
//    public static  $sysLeftSide = "(//input[@name='diagnosis_eye'])[7]";
//    public static  $sysDay = "(//select[@name='diagnosis_day'])[2]";
//    public static  $sysMonth = "(//select[@name='diagnosis_month'])[2]";
//    public static  $sysYear = "(//select[@name='diagnosis_year'])[2]";
//    public static  $sysSaveButton = "(//button[@type='submit'])[5]";
//    public static  $selectAllergy = "//select[@id='allergy_id']";
//    public static  $addAllergy = "//button[@id='btn_add_allergy']";
//    public static  $cviEdit = "//button[@id='btn-edit_oph_info']";
//    public static  $cviStatus = "//select[@id='patientophinfo_cvi_status_id']";
//    public static  $cviDay = "(//select[@name='diagnosis_day'])[3]";
//    public static  $cviMonth = "(//select[@name='diagnosis_month'])[3]";
//    public static  $cviYear = "(//select[@name='diagnosis_year'])[3]";
//    public static  $cviSave = "(//button[@type='submit'])[7]";
//    public static  $addMedication = "//button[@id='btn-add_medication']";
//    public static  $medicationSelect = "//select[@id='drug_id']";
//    public static  $medicationRoute = "//select[@id='route_id']";
//    public static  $medicationFrequency = "//select[@id='frequency_id']";
//    public static  $medicationCalendar = "//input[@id='start_date']";
//    public static  $medicationSave = "(//button[@type='submit'])[9]";
//    public static  $removeDiagnosisLink = "//*[@class='small removediagnosis']//*[contains(text(), 'remove')]";
//    public static  $removeDiagnosis = "//*[contains(text(), 'remove diagnosis')]";
//    public static  $removeMedicationLink = "//*[@class='small removemedication']//*[contains(text(), 'remove')]";
//    public static  $removeMedication = "//*[contains(text(), 'remove medication')]";
//    public static  $removeAllergyLink = "//*[@class='small removeallergy']//*[contains(text(), 'remove')]";
//    public static  $removeAllergy = "//*[@class='classy red venti btn_remove_allergy']//*[contains(text(), 'remove allergy')]";
//
////Pass Calendar Day from Example Table
//    public static  function passDateFromTable ($dateFrom) {
//        return "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'". $dateFrom ."')]";
//}
