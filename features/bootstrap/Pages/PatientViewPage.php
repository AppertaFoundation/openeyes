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
        'day' => array('xpath' => "//select[@name='fuzzy_day']"),
        'month' => array('xpath' => "//select[@name='fuzzy_month']"),
        'year' => array('xpath' => "//select[@name='fuzzy_year']"),
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
        'cviStatus' => array('xpath' => "//button[@id='btn-edit_oph_info']"),
        'saveCVI' => array('xpath' => "//*[@class='classy green mini btn_save_oph_info']//*[contains(text(),'Save')]"),
        'addMedicationButton' => array('xpath' => "//button[@id='btn-add_medication']"),
        'selectMedication' => array('xpath' => "//select[@id='drug_id']"),
        'selectRoute' => array('xpath' => "//select[@id='route_id']"),
        'selectFrequency' => array('xpath' => "//select[@id='frequency_id']"),
        'selectDateFrom' => array('xpath' => "//input[@id='start_date']"),
        'saveMedication' => array('xpath' => "//*[@class='classy green mini btn_save_medication']//*[contains(text(),'Save')]"),
        'addAllergyButton' => array('xpath' => "//button[@id='btn_add_allergy']"),
        'selectAllergy' => array('xpath' => "//select[@id='allergy_id']"),
        'addFamilyHistoryButton' => array('xpath' => "//*[@id='btn-add_family_history']"),
        'selectRelativeID' => array('xpath' => "//*[@id='relative_id']"),
        'selectFamilySide' => array('xpath' => "//*[@id='side_id']"),
        'selectFamilyCondition' => array('xpath' => "//*[@id='condition_id']"),
        'enterFamilyComments' => array('xpath' => "//*[@id='comments']"),
        'saveFamilyHistory' => array('xpath' => "//*[@class='classy green mini btn_save_family_history']//*[contains(text(),'Save')]")
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
        $this->getElement('day')->selectOption($day);
        $this->getElement('month')->selectOption($month);
        $this->getElement('year')->selectOption($year);
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

    public function medicationDetails ($medication, $route, $frequency, $datefrom)
    {
        $this->getElement('addMedicationButton')->click();
        $this->getElement('selectMedication')->selectOption($medication);
        $this->getElement('selectRoute')->selectOption($route);
        $this->getElement('selectFrequency')->selectOption($frequency);
        $this->getElement('selectDateFrom')->selectOption($datefrom);
        $this->getElement('saveMedication')->click();
    }

    public function editCVIstatus ($status)
    {
        $this->getElement('editCVIstatusButton')->click();
        $this->getElement('cviStatus')->selectOption($status);
    }

    public function saveCVIstatus ()
    {
        $this->getElement('saveCVI')->click();
    }

    public function addAllergy ($allergy)
    {
        $this->getElement('addAllergyButton')->click();
        $this->getElement('selectAllergy')->selectOption($allergy);
    }

    public function addFamilyHistory ($relative, $side, $condition, $comments)
    {
        $this->getElement('addFamilyHistoryButton')->click();
        $this->getElement('selectRelativeID')->selectOption($relative);
        $this->getElement('selectFamilySide')->selectOption($side);
        $this->getElement('selectFamilyCondition')->selectOption($condition);
        $this->getElement('enterFamilyComments')->selectOption($comments);
        $this->getElement('saveFamilyHistory')->click();


    }

}


////Pass Calendar Day from Example Table
//    public static  function passDateFromTable ($dateFrom) {
//        return "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'". $dateFrom ."')]";
//}
