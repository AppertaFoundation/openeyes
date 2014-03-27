<?php
use Behat\Behat\Exception\BehaviorException;
class Homepage extends OpenEyesPage
{
    protected $path = '/';

    protected $elements = array(
        'siteID' => array('xpath' => "//*[@id='SiteAndFirmForm_site_id']"),
        'firmDropdown' => array('xpath' => "//*[@id='SiteAndFirmForm_firm_id']"),
        'confirmSiteAndFirmButton' => array('xpath' => "//*[@id='site-and-firm-form']//*[@value='Confirm']"),
        'mainSearch' => array('xpath' => "//input[@id='query']"),
        'searchSubmit' => array('xpath' => "//button[@type='submit']"),
        'changeFirmHeaderLink' => array('xpath' => "//*[@id='user_firm']//*[contains(text(), 'Change')]"),
        'invalidLogin' => array('xpath' => "//*[contains(text(),'Invalid login.')]"),
        'patientInfoBox' => array('xpath' => "//*[@class='box patient-info js-toggle-container']"),
        'allEpisodesBox' => array('xpath' => "//*[@class='box patient-info episodes']"),
        'latestEventBox' => array('xpath' => "//*[@class='box patient-info episode-links']"),
        'associatedDataBoxes' => array('xpath' => "//*[@class='box patient-info associated-data js-toggle-container']"),
        'addOpthalmicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_ophthalmic_diagnosis']"),
        'addSystemicDiagnosis' => array('xpath' => "//button[@id='btn-add_new_systemic_diagnosis']"),
        'addPreviousOperation' => array('xpath' => "//*[@id='btn-add_previous_operation']"),
        'addMedicationButton' => array('xpath' => "//button[@id='btn-add_medication']"),
        'editCVIstatusButton' => array('xpath'=> "//*[@id='btn-edit_oph_info']"),
        'addAllergyButton' => array('xpath' => "//*[@id='btn-add_allergy']"),
        'addFamilyHistoryButton' => array('xpath' => "//*[@id='btn-add_family_history']"),
        'editEvent' => array('xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'Edit')]"),
        'deleteEvent' => array('xpath' => "//*[@class=' delete event-action button button-icon small']//*[@class='icon-button-small-trash-can']"),
        'printButton' => array('xpath' => "//*[@id='et_print']"),
        'addNewEpisodeButton' => array('xpath' => "//*[@id='episodes_sidebar']//*[contains(text(),'Add episode')]"),
        'addEventButtons' => array('xpath' => "//*[@class='button secondary tiny add-event addEvent enabled']"),
        'prescriptionDisabled' => array('xpath' => "//*[@title='You do not have permission to add Prescription']"),
    );

    public function selectSiteID($siteAddress)
    {
        $this->getElement('siteID')->selectOption($siteAddress);
    }

    public function selectFirm ($firm)
    {
        $this->getElement('firmDropdown')->selectOption($firm);
    }

    public function confirmSelection()
    {
        $this->getElement('confirmSiteAndFirmButton')->press();
    }

    public function changeFirm ()
    {
        $this->getElement('changeFirmHeaderLink')->press();
    }

    public function searchHospitalNumber ($hospital)
    {
        $this->getElement('mainSearch')->setValue($hospital);
    }

    public function searchPatientName ($last, $first)
    {
        $this->getElement('mainSearch')->setValue($last . ' ' . $first);
    }

    public function searchNhsNumber ($nhs)
    {
        $this->getElement('mainSearch')->setValue($nhs);
    }

    public function searchSubmit ()
    {
      $this->getElement('searchSubmit')->press();
			//make sure the patient page is shown after a search
			$this->waitForTitle('Patient summary');
			//$this->getSession()->wait(15000, "window.$ && $('h1.badge').html() ==  'Patient summary' ");
    }

    public function followLink($link)
    {
        $this->clickLink($link);
    }

    public function invalidLoginMessage ()
{
    return (bool) $this->find('xpath', $this->getElement('invalidLogin')->getXpath());
}

    public function isInvalidLoginShown ()
    {
        if ($this->invalidLoginMessage()){
            print "Invalid Login message displayed OK";
        }

        else {
            throw new BehaviorException("WARNING!!! Invalid Login is NOT displayed WARNING!!!");
        }
    }

    public function modulesUnavailableCheck ()
    {
        return (bool) $this->find('xpath', $this->getElement('allEpisodesBox')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('latestEventBox')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('associatedDataBoxes')->getXpath());
    }

    public function levelOneAccess ()
    {
        if ($this->modulesUnavailableCheck()){
            throw new BehaviorException ("WARNING!!! Level 1 RBAC access is NOT functioning correctly WARNING!!!");
        }

        else {
            print "Level 1 RBAC access working OK";
        }
    }

    public function modulesAvailableCheck ()
    {
        return (bool) $this->find('xpath', $this->getElement('patientInfoBox')->getXpath());
    }

    public function modulesCorrect ()
    {
        if ($this->modulesAvailableCheck()){
            print "Patient Information modules are being displayed correctly";
        }

        else {

            throw new BehaviorException ("WARNING!!! Patient Information modules are NOT being displayed correctly WARNING!!!");
        }
    }

    public function modulesUnavailableLevelTwoCheck ()
    {
        return (bool) $this->find('xpath', $this->getElement('addOpthalmicDiagnosis')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addSystemicDiagnosis')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addPreviousOperation')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addMedicationButton')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('editCVIstatusButton')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addAllergyButton')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addFamilyHistoryButton')->getXpath());
    }

    public function levelTwoAccess ()
    {
        if ($this->modulesUnavailableLevelTwoCheck()){
            throw new BehaviorException ("WARNING!!! Level 2 RBAC access is NOT functioning correctly WARNING!!!");
        }

        else {
            print "Level 2 RBAC access working OK";
        }
    }

    public function modulesUnavailableLevelTwoAdditionalChecks ()
    {
        return (bool) $this->find('xpath', $this->getElement('editEvent')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('deleteEvent')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addNewEpisodeButton')->getXpath()) ||
        (bool) $this->find('xpath', $this->getElement('addEventButtons')->getXpath()) ;
    }

    public function levelTwoAccessAdditionalChecks ()
    {
        if ($this->modulesUnavailableLevelTwoAdditionalChecks()){
            throw new BehaviorException ("WARNING!!! Level 2 RBAC access is NOT functioning correctly (edit, print, delete, episode or event) WARNING!!!");
        }

        else {
            print "Level 2 RBAC access working OK";
        }
    }

    public function hasPrintingBeenDisabled ()
    {
        return (bool) $this->find('xpath', $this->getElement('printButton')->getXpath());
    }

    public function printDisabled ()
    {
        if ($this->hasPrintingBeenDisabled()){
            throw new BehaviorException ("WARNING!!! Level 2 RBAC Printing access IS visible WARNING!!!");
        }

        else {
            print "Level 2 RBAC Printing access is disabled OK";
        }
    }

    public function printingAccessCheck ()
    {
        return (bool) $this->find('xpath', $this->getElement('printButton')->getXpath());
    }

    public function printAccessCheck ()
    {
        if ($this->printingAccessCheck()){
            print "Level 3 RBAC Printing access working OK";
        }

        else {
            throw new BehaviorException ("WARNING!!! Level 3 RBAC Printing access NOT working WARNING!!!");
        }
    }

    protected function hasPrescriptionBeenDisabled ()
    {
        return (bool) $this->find('xpath', $this->getElement('prescriptionDisabled')->getXpath());
    }

    public function levelFourAccess ()
    {
        if($this->hasPrescriptionBeenDisabled()){
            print "Level 4 RBAC Prescription has been disabled OK";
    }

        else {
            throw new BehaviorException ("WARNING!!! Level 4 RBAC Prescription IS enabled WARNING!!!");
    }
    }
}