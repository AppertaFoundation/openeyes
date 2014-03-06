<?php

//use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class PatientViewNewDiagnosis extends OpenEyesPage
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
        'opthRightEye' => array('xpath' => "//*[@class='diagnosis_eye row field-row']//*[@value='2']"),
        'opthLeftEye' => array('xpath' => "//*[@class='diagnosis_eye row field-row']//*[@value='1']"),
        'opthBothEyes' => array('xpath' => "//*[@class='diagnosis_eye row field-row']//*[@value='3']"),
        'opthDay' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_day']"),
        'opthMonth' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_month']"),
        'opthYear' => array('xpath' => "//*[@id='add-ophthalmic-diagnosis']//select[@name='fuzzy_year']"),
        'opthSaveButton' => array('xpath' => "//form[@id='add-ophthalmic-diagnosis']//*[contains(text(),'Save')]"),
        'addSystemicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_systemic_diagnosis']"),
        'selectSystemicDiagnosis' => array('xpath' => "//*[@id='DiagnosisSelection_systemic_disorder_id']"),
        'sysDay' => array('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_day']"),
        'sysMonth'=> array('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_month']"),
        'sysYear' => array ('xpath' => "//*[@id='add-systemic-diagnosis']//select[@name='fuzzy_year']"),
        'sysNoEyes' => array('xpath' => "//*[@id='add_new_systemic_diagnosis']//*[@class='diagnosis_eye row field-row']//*[@value='']"),
        'sysRightEye' => array('xpath' => "//*[@id='add_new_systemic_diagnosis']//*[@class='diagnosis_eye row field-row']//*[@value=2]"),
        'sysBothEyes' => array('xpath' => "//*[@id='add_new_systemic_diagnosis']//*[@class='diagnosis_eye row field-row']//*[@value=3]"),
        'sysLeftEye' => array('xpath' => "//*[@id='add_new_systemic_diagnosis']//*[@class='diagnosis_eye row field-row']//*[@value=1]"),
        'sysSaveButton' => array('xpath' => "//*[@class='secondary small btn_save_systemic_diagnosis']"),
        'addPreviousOperation' => array('xpath' => "//*[@id='btn-add_previous_operation']"),
        'commonOperation' => array('xpath' => "//select[@id='common_previous_operation']"),
        'operationDay' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_day']"),
        'operationMonth' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_month']"),
        'operationYear' => array('xpath' => "//*[@id='add-previous_operation']//select[@name='fuzzy_year']"),
        'operationNoEyes' => array('xpath' => "//*[@id='add-previous_operation']//*[@class='row field-row']//*[@value='']"),
        'operationRightEye' => array('xpath' => "//*[@id='add-previous_operation']//*[@class='row field-row']//*[@value=2]"),
        'operationBothEyes' => array('xpath' => "//*[@id='add-previous_operation']//*[@class='row field-row']//*[@value=3]"),
        'operationLeftEye' => array('xpath' => "//*[@id='add-previous_operation']//*[@class='row field-row']//*[@value=1]"),
        'operationSaveButton' => array('xpath' => "//*[@class='secondary small btn_save_previous_operation']"),
        'editCVIstatusButton' => array('xpath'=> "//*[@id='btn-edit_oph_info']"),
        'cviStatus' => array('xpath' => "//select[@id='PatientOphInfo_cvi_status_id']"),
        'CVIDay' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_day']"),
        'CVIMonth' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_month']"),
        'CVIYear' => array('xpath' => "//*[@id='edit-oph_info']//select[@name='fuzzy_year']"),
        'saveCVI' => array('xpath' => "//*[@class='secondary small btn_save_oph_info']"),
        'addMedicationButton' => array('xpath' => "//button[@id='btn-add_medication']"),
        'selectMedication' => array('xpath' => "//select[@id='drug_id']"),
        'selectRoute' => array('xpath' => "//select[@id='route_id']"),
        'selectFrequency' => array('xpath' => "//select[@id='frequency_id']"),
        'openMedicationDate' => array('xpath' => "//*[@class='hasDatepicker']"),
        'hopefullFIX' => array('xpath' => "//form[@id='add-medication']/div[8]"),
        'selectDateFrom' => array('xpath' => "//*[@id='ui-datepicker-div']//*[contains(text(),'10')]"),
        'saveMedication' => array('xpath' => "//*[@class='secondary small btn_save_medication']"),
        'addAllergyButton' => array('xpath' => "//*[@id='btn-add_allergy']"),
        'selectAllergy' => array('xpath' => "//select[@id='allergy_id']"),
        'noAllergyTickbox' => array('xpath' => "//*[@id='no_allergies']"),
        'saveAllergy' => array('xpath' => "//*[@class='secondary small btn_save_allergy']"),
        'addFamilyHistoryButton' => array('xpath' => "//*[@id='btn-add_family_history']"),
        'selectRelativeID' => array('xpath' => "//*[@id='relative_id']"),
        'selectFamilySide' => array('xpath' => "//*[@id='side_id']"),
        'selectFamilyCondition' => array('xpath' => "//*[@id='condition_id']"),
        'enterFamilyComments' => array('xpath' => "//*[@id='comments']"),
        'saveFamilyHistory' => array('xpath' => "//*[@class='secondary small btn_save_family_history']"),
        'createNewEpisodeAddEvent' => array('xpath' => "//*[@class='box patient-info episode-links']//*[contains(text(),'Create episode / add event')]"),
        'addEpisodeButton' => array('xpath' => "//*[@id='add-episode']"),
        'addEpisode' => array('xpath' => "//*[@class='secondary small add-episode']//*[@class='icon-button-small-plus-sign']"),
        'confirmCreateEpisode' => array('xpath' => "//*[@id='add-new-episode-form']//*[contains(text(),'Create new episode')]"),
        'latestEvent' => array('xpath' => "//*[@class='box patient-info episode-links']//*[contains(text(),'Latest Event')]"),
        'removeAllergyButton' => array('xpath' => "//*[@id='currentAllergies']//*[contains(text(),'Remove')]"),
        'removeConfirmButton' => array('xpath' => "//*[@id='delete_allergy']/div[2]//*[contains(text(),'Remove allergy')]"),
        'removeOpthalmicDiagnosisLink' => array('xpath' => "//*[@class='removeDiagnosis']"),
        'removeOpthalmicDiagnosisConfirm' => array('xpath' => "//*[@id='delete_diagnosis']//*[contains(text(),'Remove diagnosis')]"),
        'removeOperation' => array('xpath' => "//*[@class='removeOperation']"),
        'removeOperationConfirmButton' => array('xpath' => "//*[@id='delete_operation']//*[contains(text(),'Remove operation')]"),
        'removeMedication' => array('xpath' => "//*[@class='removeMedication']"),
        'removeMedicationConfirmButton' => array('xpath' => "//*[contains(text(),'Remove medication')]"),
        'datePicker' => array('xpath' => "//*[@class='ui-datepicker-title']")
        );

    public function addOpthalmicDiagnosis ($diagnosis)
    {
        $element = $this->getElement('addOpthalmicDiagnosis');
        $this->scrollWindowToElement($element);
        $element->press();
        $this->getElement('selectOphthalmicDisorder')->selectOption($diagnosis);
    }

    public function selectEye ($eye)
    {
        if ($eye===('Right')) {
        $this->getElement('opthRightEye')->check();
        }
        if ($eye===('Both'))  {
            $this->getElement('opthBothEyes')->check();
        }
        if ($eye===('Left'))  {
            $this->getElement('opthLeftEye')->check();
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
        $element = $this->getElement('opthSaveButton');
        $this->scrollWindowToElement($element);
        $element->click();
	    $this->waitForElementDisplayNone('#add_new_ophthalmic_diagnosis' );
      //$this->getSession()->wait(10000,"$('#add_new_ophthalmic_diagnosis').css('display') == 'none'");
    }

    public function addSystemicDiagnosis ($diagnosis)
    {
        //the waits make sure the action is completed before going forward
		$element =$this->getElement('addSystemicDiagnosis');
        $this->scrollWindowToElement($element);
        $element->press();
		$this->waitForElementDisplayBlock('#add_new_systemic_diagnosis');
		//$this->getSession()->wait(2000,"$('#add_new_systemic_diagnosis').css('display') == 'block'");
        $this->getElement('selectSystemicDiagnosis')->selectOption($diagnosis);
		$this->getSession()->wait(2000,"$('#DiagnosisSelection_systemic_disorder_id').val() == '" . $diagnosis . "'");
    }

    public function selectSystemicSide ($side)
    {
        $el = null;
		if ($side===("None")) {
        	$el = $this->getElement('sysNoEyes');
			$el->check();
        }
        if ($side===("Right")) {
			$el = $this->getElement('sysRightEye');
			$el->check();
        }
        if ($side===("Both")) {
			$el = $this->getElement('sysBothEyes');
			$el->check();
        }
        if ($side===("Left")) {
			$el = $this->getElement('sysLeftEye');
			$el->check();
        }
        $this->getSession()->wait(3000, "$(\"#add-systemic-diagnosis [name='diagnosis_eye']:checked\").val() == " .   $el->getValue());

    }

    public function saveSystemicDiagnosis ()
    {
        $element =$this->getElement('sysSaveButton');
        $this->scrollWindowToElement($element);
        $element->press();
        $this->waitForElementDisplayNone('#add_new_systemic_diagnosis' );
        //$this->getSession()->wait(10000,"$('#add_new_systemic_diagnosis').css('display') == 'none'");
    }

    public function previousOperation ($operation)
    {
        $element =$this->getElement('addPreviousOperation');
        $this->scrollWindowToElement($element);
        $element->press();
        $this->getElement('commonOperation')->selectOption($operation);
        $this->getSession()->wait(1000,false);
    }

    public function operationSide ($side)
    {
        if ($side===("None")) {
            $this->getElement('operationNoEyes')->check();
        }
        if ($side===("Right")) {
            $this->getElement('operationRightEye')->check();
        }
        if ($side===("Both")) {
            $this->getElement('operationBothEyes')->check();
        }
        if ($side===("Left")) {
            $this->getElement('operationLeftEye')->check();
        }
    }

    public function savePreviousOperation ()
    {
      $element =$this->getElement('operationSaveButton');
      $this->scrollWindowToElement($element);
      $element->press();
      $this->waitForElementDisplayNone('#add_previous_operation' );
      //$this->getSession()->wait(15000, "window.$ && $('#add_previous_operation').css('display') == 'none'");
    }

    public function medicationDetails ($medication, $route, $frequency, $datefrom)
    {
        $element = $this->getElement('addMedicationButton');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('selectMedication')->selectOption($medication);
        $this->getElement('selectRoute')->selectOption($route);
        $this->getElement('selectFrequency')->selectOption($frequency);
        $element = $this->getElement('openMedicationDate');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->waitForElementDisplayNone('#ui-datepicker-title');
//        $this->getSession()->wait(8000);
        $this->getElement('selectDateFrom')->click($datefrom);
        $this->getElement('saveMedication')->click();
        $this->waitForElementDisplayNone('#add_medication');
    }

    public function editCVIstatus ($status)
    {
        $this->getElement('editCVIstatusButton')->click();
        $this->getElement('cviStatus')->selectOption($status);
        $this->getSession()->wait(3000);
    }

    public function saveCVIstatus ()
    {
        $this->getElement('saveCVI')->click();
		$this->waitForElementDisplayNone('#edit_oph_info');
        //$this->getSession()->wait(10000, "$('#edit_oph_info').css('display') == 'none'");
    }

    protected function doesRemoveAllergyExist ()
    {
        return (bool) $this->find('xpath', $this->getElement('removeAllergyButton')->getXpath());
    }

    public function removeAllergy ()
    {
        if ($this->doesRemoveAllergyExist())
        {
        $element =$this->getElement('removeAllergyButton');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('removeConfirmButton')->click();
        }
    }

    public function addAllergy ($allergy)
    {
        $element = $this->getElement('addAllergyButton');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getSession()->wait(1000);
        $this->getElement('selectAllergy')->selectOption($allergy);
        $this->getElement('saveAllergy')->click();
			$this->waitForElementDisplayNone('#add_allergy');
        //$this->getSession()->wait(10000,"$('#add_allergy').css('display') == 'none'");
    }



    public function noAllergyTickbox ()
    {
        $this->getElement('addAllergyButton')->click();
        $this->getSession()->wait(1000);
        $this->getElement('noAllergyTickbox')->check();
        $this->getElement('saveAllergy')->click();
			$this->waitForElementDisplayNone('#add_allergy');
        //$this->getSession()->wait(10000,"$('#add_allergy').css('display') == 'none'");
    }

    public function addFamilyHistory ($relative, $side, $condition, $comments)
    {
        $element =$this->getElement('addFamilyHistoryButton');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('selectRelativeID')->selectOption($relative);
        $this->getElement('selectFamilySide')->selectOption($side);
        $this->getElement('selectFamilyCondition')->selectOption($condition);
        $this->getElement('enterFamilyComments')->setValue($comments);
        $element =$this->getElement('saveFamilyHistory');
        $this->scrollWindowToElement($element);
        $element->click();
		$this->waitForElementDisplayNone('#add_family_history');
        //$this->getSession()->wait(10000,"$('#add_family_history').css('display') == 'none'");
    }

    public function addEpisodeAndEvent()
    {
        $this->getSession()->wait(5000, 'window.$ && $.active == 10');

        if ($this->episodesAndEventsAreNotPresent()) {
            $this->createNewEpisodeAndEvent();
        } else {
            $this->selectLatestEvent();
        }
        $this->getSession()->wait(5000);
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

    public function addEpisodePreviousFirmCreated ()
    {
        $this->getElement('addEpisode')->click();
        $this->getSession()->wait(3000,false);
        $this->getElement('confirmCreateEpisode')->click();
    }

    public function selectLatestEvent ()
    {
        $this->getElement('latestEvent')->click();
		//make sure the Episodes and Events page is shown after clicking latest event link
		$this->waitForTitle('Episodes and events');
        //$this->getSession()->wait(15000, "$('h1.badge').html() ==  'Episodes and events' ");
    }

    protected function episodesAndEventsAreNotPresent()
    {
        return $this->find('xpath', $this->getElement('createNewEpisodeAddEvent')->getXpath());
    }

    public function removeAndConfirm ()
    {
        $this->waitForElementDisplayNone('#add_new_ophthalmic_diagnosis');
        $element = $this->getElement('removeOpthalmicDiagnosisLink');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('removeOpthalmicDiagnosisConfirm')->click();
        $this->getSession()->wait(5000, 'window.$ && $.active == 0');
    }

    public function removeOperation ()
    {
        $element = $this->getElement('removeOperation');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('removeOperationConfirmButton')->click();
        $this->getSession()->wait(5000, 'window.$ && $.active == 0');
    }

    public function removeMedication ()
    {
        $element = $this->getElement('removeMedication');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getElement('removeMedicationConfirmButton')->click();
        $this->getSession()->wait(5000, 'window.$ && $.active == 0');
    }
}
