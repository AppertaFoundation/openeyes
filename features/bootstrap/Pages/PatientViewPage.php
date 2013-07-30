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
        'opthRightEye' => array('xpath' => "//input[@name='diagnosis_eye']"),
        'opthLeftEye' => array('xpath' => "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]"),
        'opthBothEyes' => array('xpath' => "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]"),
        'Day' => array('xpath' => "//select[@name='fuzzy_day']"),
        'Month' => array('xpath' => "//select[@name='fuzzy_month']"),
        'Year' => array('xpath' => "//select[@name='fuzzy_year']"),
        'opthSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_ophthalmic_diagnosis']//*[contains(text(),'Save')]"),
        'addSystemicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_systemic_diagnosis']"),
        'selectSystemicDiagnosis' => array('xpath' => "//select[@id='diagnosisselection_systemic_disorder_id']"),
        'sysNoEyes' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[1]"),
        'sysRightEye' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[2]"),
        'sysBothEyes' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[3]"),
        'sysLeftEye' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[4]"),
        'sysSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_systemic_diagnosis']//*[contains(text(),'Save')]"),
        'commonOperation' => array('xpath' => "//select[@id='common_previous_operation']"),
        'operationNoEyes' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[1]"),
        'operationRightEye' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[2]"),
        'operationBothEyes' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[3]"),
        'operationLeftEye' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[4]"),
        'operationSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_previous_operation']//*[contains(text(),'Save')]"),
        'editCVIstatusButton' => array('xpath'=> "//button[@id='btn-edit_oph_info']"),
        'CVIstatus' => array('xpath' => "//button[@id='btn-edit_oph_info']"),
        'saveCVI' => array('xpath' => "//*[@class='classy green mini btn_save_oph_info']//*[contains(text(),'Save')]")
    );

    public function addOpthalmicDiagnosis ($diagnosis)
    {
        $this->getElement('addOpthalmicDiagnosis')->press();
        $this->getElement('selectOphthalmicDisorder')->selectOption($diagnosis);
    }

    public function selectEye ($eye)
    {
        $eyesArray = array(
            "Right" => 'opthRightEye',
            "Both" => 'opthLeftEye',
            "Left" => 'opthBothEyes'
        );

       // $this->getElement($eyesArray,[$eye])->press();
    }

    public function addDate ($day, $month, $year)
    {
        $this->getElement('Day')->selectOption($day);
        $this->getElement('Month')->selectOption($month);
        $this->getElement('Year')->selectOption($year);
    }

    public function saveOpthalmicDiagnosis ()
    {
        $this->getElement('opthSaveButton')->press();
    }

    public function addSystemicDiagnosis ($diagnosis)
    {
        $this->getElement('addSystemicDiagnosis')->press();
        $this->getElement('selectSystemicDiagnosis')->selectOption($diagnosis);
    }

    public function selectSystemicSide ($side)
    {
        if ($side===("None")) {
        $this->getElement('sysNoEyes')->click();
        }
        if ($side===("Right")) {
            $this->getElement('sysRightEye')->click();
        }
        if ($side===("Both")) {
            $this->getElement('sysBothEyes')->click();
        }
        if ($side===("Left")) {
            $this->getElement('sysLeftEye')->click();
        }
    }

    public function saveSystemicDiagnosis ()
    {
        $this->getElement('sysSaveButton')->press();
    }

    public function commonOperation ($operation)
    {
        $this->getElement('commonOperation')->selectOption($operation);
    }

    public function operationSide ($side)
    {
        if ($side===("None")) {
            $this->getElement('operationNoEyes')->click();
        }
        if ($side===("Right")) {
            $this->getElement('operationRightEye')->click();
        }
        if ($side===("Both")) {
            $this->getElement('operationBothEyes')->click();
        }
        if ($side===("Left")) {
            $this->getElement('operationLeftEye')->click();
        }
    }

    public function savePreviousOperation ()
    {
        $this->getElement('operationSaveButton')->press();
    }

    public function editCVIstatus ($status)
    {
        $this->getElement('editCVIstatusButton')->click();
        $this->getElement('CVIstatus')->selectOption($status);
    }

    public function saveCVIstatus ()
    {
        $this->getElement('saveCVI')->click();
    }
}


//    public static  $selectAllergy = "//select[@id='allergy_id']";
//    public static  $addAllergy = "//button[@id='btn_add_allergy']";


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
