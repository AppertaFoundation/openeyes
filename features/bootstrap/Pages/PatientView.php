<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class PatientView extends Page
{
    protected $path = "/site/patient/view/";

    protected $elements = array(
        'homeButton' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Home')]"),
        'theatreDiaries' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Theatre Diaries')]"),
        'partialBookingsWaiting' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Partial bookings waiting list')]"),
        'logOut' => array('xpath' => "//*[@id='user_nav']//*[contains(text(), 'Logout')]"),
        'patientSummary' => array('xpath' => "//*[@id='patientID']//*[contains(text(), 'Patient Summary')]"),
        'userProfile' => array('xpath' => "//*[@id='user_id']/a"),
        'addOpthalmicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_ophthalmic_diagnosis']"),
        'selectOphthalmicDisorder' => array('xpath' => "//*[@id='DiagnosisSelection_ophthalmic_disorder_id']"),
        'opthRightEye' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']/div[4]/input[1]"),
        'opthLeftEye' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']/div[4]/input[3]"),
        'opthBothEyes' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']/div[4]/input[2]"),
        'opthDay' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_day']"),
        'opthMonth' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_month']"),
        'opthYear' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_year']"),
        'opthSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_ophthalmic_diagnosis']//*[contains(text(),'Save')]"),
        'addSystemicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_systemic_diagnosis']"),
        'selectSystemicDiagnosis' => array('xpath' => "//*[@id='DiagnosisSelection_systemic_disorder_id']"),
        'sysDay' => array('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_day']"),
        'sysMonth'=> array('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_month']"),
        'sysYear' => array ('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_year']"),
        'sysNoEyes' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[1]"),
        'sysRightEye' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[2]"),
        'sysBothEyes' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[3]"),
        'sysLeftEye' => array('xpath' => "//*[@id='add-systemic-diagnosis']/div[4]/input[4]"),
        'sysSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_systemic_diagnosis']//*[contains(text(),'Save')]"),
        'addPreviousOperation' => array('xpath' => "//*[@id='btn-add_previous_operation']"),
        'commonOperation' => array('xpath' => "//select[@id='common_previous_operation']"),
        'operationDay' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_day']"),
        'operationMonth' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_month']"),
        'operationYear' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_year']"),
        'operationNoEyes' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[1]"),
        'operationRightEye' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[2]"),
        'operationBothEyes' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[3]"),
        'operationLeftEye' => array('xpath' => "//*[@id='add-previous_operation']/div[4]/input[4]"),
        'operationSaveButton' => array('xpath' => "//*[@class='classy green mini btn_save_previous_operation']//*[contains(text(),'Save')]"),
        'editCVIstatusButton' => array('xpath'=> "//button[@id='btn-edit_oph_info']"),
        'cviStatus' => array('xpath' => "//button[@id='btn-edit_oph_info']"),
        'CVIDay' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_day']"),
        'CVIMonth' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_month']"),
        'CVIYear' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_year']"),
        'saveCVI' => array('xpath' => "//*[@class='classy green mini btn_save_oph_info']//*[contains(text(),'Save')]"),
        'addMedicationButton' => array('xpath' => "//button[@id='btn-add_medication']"),
        'selectMedication' => array('xpath' => "//select[@id='drug_id']"),
        'selectRoute' => array('xpath' => "//select[@id='route_id']"),
        'selectFrequency' => array('xpath' => "//select[@id='frequency_id']"),
        'openMedicationDate' => array('xpath' => "//*[@id='start_date']"),
        'selectDateFrom' => array('xpath' => "//*[@id='ui-datepicker-div']/table/tbody/tr[2]/td[4]/a"),
        'saveMedication' => array('xpath' => "//*[@class='classy green mini btn_save_medication']//*[contains(text(),'Save')]"),
        'addAllergyButton' => array('xpath' => "//*[@id='btn-add_allergy']"),
        'selectAllergy' => array('xpath' => "//select[@id='allergy_id']"),
        'saveAllergy' => array('xpath' => "//*[@id='add-allergy']/div[3]/button[1]//*[contains(text(),'Save')]"),
        'addFamilyHistoryButton' => array('xpath' => "//*[@id='btn-add_family_history']"),
        'selectRelativeID' => array('xpath' => "//*[@id='relative_id']"),
        'selectFamilySide' => array('xpath' => "//*[@id='side_id']"),
        'selectFamilyCondition' => array('xpath' => "//*[@id='condition_id']"),
        'enterFamilyComments' => array('xpath' => "//*[@id='comments']"),
        'saveFamilyHistory' => array('xpath' => "//*[@class='classy green mini btn_save_family_history']//*[contains(text(),'Save')]"),
        'createNewEpisodeAddEvent' => array('xpath' => "//*[@id='content']/div/div[2]//*[contains(text(),'Create episode / add event')]"),
        'addEpisodeButton' => array('xpath' => "//*[@id='event_display']/div[3]//*[contains(text(),'Add episode')]"),
        'confirmCreateEpisode' => array('xpath' => "//*[@id='add-new-episode-form']/div[2]/div[2]//*[contains(text(),'Create new episode')]"),
        'latestEvent' => array('xpath' => "//*[@id='content']/div/div[2]/p//*[contains(text(),'Latest Event')]"),
        'removeAllergyButton' => array('xpath' => "//*[@id='patient_allergies']//*[contains(text(),'Remove')]"),
        'removeConfirmButton' => array('xpath' => "//*[@id='delete_allergy']/div[2]//*[contains(text(),'Remove allergy')]")

        );

    public function addOpthalmicDiagnosis ($diagnosis)
    {
        $this->getElement('addOpthalmicDiagnosis')->press();
        $this->getElement('selectOphthalmicDisorder')->selectOption($diagnosis);
    }

    public function selectEye ($eye)
    {
        if ($eye===('Right')) {
        $this->getElement('opthRightEye')->press();
        }
        if ($eye===('Both'))  {
            $this->getElement('opthBothEyes')->press();
        }
        if ($eye===('Left'))  {
            $this->getElement('opthLeftEye')->press();
        }
    }

    public function addOpthalmicDate ($day, $month, $year)
    {
        $this->getElement('opthDay')->selectOption($day);
        $this->getElement('opthMonth')->selectOption($month);
        $this->getElement('opthYear')->selectOption($year);
    }

    public function addSystemicDate ($day, $month, $year)
    {
        $this->getElement('sysDay')->selectOption($day);
        $this->getElement('sysMonth')->selectOption($month);
        $this->getElement('sysYear')->selectOption($year);
    }

    public function addOperationDate ($day, $month, $year)
    {
        $this->getElement('operationDay')->selectOption($day);
        $this->getElement('operationMonth')->selectOption($month);
        $this->getElement('operationYear')->selectOption($year);
    }

    public function addCVIDate ($day, $month, $year)
    {
        $this->getElement('CVIDay')->selectOption($day);
        $this->getElement('CVIMonth')->selectOption($month);
        $this->getElement('CVIYear')->selectOption($year);
    }

    public function saveOpthalmicDiagnosis ()
    {
        $this->getElement('opthSaveButton')->press();
        $this->getSession()->wait(1000,false);
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
        $this->getSession()->wait(1000,false);
    }

    public function previousOperation ($operation)
    {
        $this->getElement('addPreviousOperation')->press();
        $this->getElement('commonOperation')->selectOption($operation);
        $this->getSession()->wait(1000,false);
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
        $this->getSession()->wait(1000,false);
    }

    public function medicationDetails ($medication, $route, $frequency, $datefrom)
    {
        $this->getElement('addMedicationButton')->click();
        $this->getElement('selectMedication')->selectOption($medication);
        $this->getElement('selectRoute')->selectOption($route);
        $this->getElement('selectFrequency')->selectOption($frequency);
        $this->getSession()->wait(3000,false);
        $this->getElement('openMedicationDate')->click();
        $this->getSession()->wait(3000,false);
        $this->getElement('selectDateFrom')->click($datefrom);
        $this->getSession()->wait(3000,false);
        $this->getElement('saveMedication')->click();
        $this->getSession()->wait(1000,false);
    }

    public function editCVIstatus ($status)
    {
        $this->getElement('editCVIstatusButton')->click();
        $this->getElement('cviStatus')->selectOption($status);
    }

    public function saveCVIstatus ()
    {
        $this->getElement('saveCVI')->click();
        $this->getSession()->wait(1000,false);
    }

    protected function doesRemoveAllergyExist ()
    {
        return (bool) $this->find('xpath', $this->getElement('removeAllergyButton')->getXpath());
    }

    public function removeAllergy ()
    {
        if ($this->doesRemoveAllergyExist())
        {
        $this->getElement('removeAllergyButton')->click();
        $this->getElement('removeConfirmButton')->click();
        $this->getSession()->wait(3000,false);
        }
    }

    public function addAllergy ($allergy)
    {
        $this->getElement('addAllergyButton')->click();
        $this->getSession()->wait(1000,false);
        $this->getElement('selectAllergy')->selectOption($allergy);
        $this->getElement('saveAllergy')->click();
        $this->getSession()->wait(1000,false);
    }

    public function addFamilyHistory ($relative, $side, $condition, $comments)
    {
        $this->getElement('addFamilyHistoryButton')->click();
        $this->getElement('selectRelativeID')->selectOption($relative);
        $this->getElement('selectFamilySide')->selectOption($side);
        $this->getElement('selectFamilyCondition')->selectOption($condition);
        $this->getElement('enterFamilyComments')->setValue($comments);
        $this->getElement('saveFamilyHistory')->click();
        $this->getSession()->wait(1000,false);
    }

    public function addEpisodeAndEvent()
    {
        $this->getSession()->wait(5000, '$.active == 10');

        if ($this->episodesAndEventsAreNotPresent()) {
            $this->createNewEpisodeAndEvent();
        } else {
            $this->selectLatestEvent();
        }
    }

    public function createNewEpisodeAndEvent ()
    {
        $this->getElement('createNewEpisodeAddEvent')->click();
    }

    public function addEpisode ()
    {
        $this->getElement('addEpisodeButton')->click();
        $this->getSession()->wait(3000,false);
        $this->getElement('confirmCreateEpisode')->click();
        $this->getSession()->wait(3000,false);
    }

    public function selectLatestEvent ()
    {
        $this->getElement('latestEvent')->click();
    }

    protected function episodesAndEventsAreNotPresent()
    {
        return $this->find('xpath', $this->getElement('createNewEpisodeAddEvent')->getXpath());
    }

}
