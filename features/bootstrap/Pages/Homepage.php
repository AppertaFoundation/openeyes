<?php
use Behat\Behat\Exception\BehaviorException;
class Homepage extends OpenEyesPage {
	protected $path = '/';
	protected $elements = array (
			'siteID' => array (
					'xpath' => "//*[@id='SiteAndFirmForm_site_id']"
			),
			'navList' => array(
			    'xpath' => "//*[@id='js-nav-shortcuts']"
            ),
			'firmDropdown' => array (
					'xpath' => "//*[@id='SiteAndFirmForm_firm_id']"
			),
			'confirmSiteAndFirmButton' => array (
					'xpath' => "//*[@id='site-and-firm-form']//*[@value='Confirm change']"
			),
			'mainSearch' => array (
					'xpath' => "//input[@id='query']"
			),
			'searchSubmit' => array (
					'xpath' => "//button[@type='submit']"
			),
			'changeFirmHeaderLink' => array (
					'xpath' => "//*[@id='change-firm']"
			),
			'invalidLogin' => array (
					'xpath' => "//*[contains(text(),'Invalid login.')]"
			),
			'patientInfoBox' => array (
					'xpath' => "//*[@class='box patient-info js-toggle-container']"
			),
			'allEpisodesBox' => array (
					'xpath' => "//*[@class='box patient-info episodes']"
			),
			'latestEventBox' => array (
					'xpath' => "//*[@class='box patient-info episode-links']"
			),
			'associatedDataBoxes' => array (
					'xpath' => "//*[@class='box patient-info associated-data js-toggle-container']"
			),
			'addOpthalmicDiagnosis' => array (
					'xpath' => "//button[@id='btn-add_new_ophthalmic_diagnosis']"
			),
			'addSystemicDiagnosis' => array (
					'xpath' => "//button[@id='btn-add_new_systemic_diagnosis']"
			),
			'addPreviousOperation' => array (
					'xpath' => "//*[@id='btn-add_previous_operation']"
			),
			'addMedicationButton' => array (
					'xpath' => "//button[@id='btn-add_medication']"
			),
			'editCVIstatusButton' => array (
					'xpath' => "//*[@id='btn-edit_oph_info']"
			),
			'addAllergyButton' => array (
					'xpath' => "//*[@id='btn-add_allergy']"
			),
			'addFamilyHistoryButton' => array (
					'xpath' => "//*[@id='btn-add_family_history']"
			),
			'editEvent' => array (
					'xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'Edit')]"
			),
			'deleteEvent' => array (
					'xpath' => "//*[@class=' delete event-action button button-icon small']//*[@class='icon-button-small-trash-can']"
			),
			'printButton' => array (
					'xpath' => "//*[@id='et_print']"
			),
			'addNewEpisodeButton' => array (
					'xpath' => "//*[@id='episodes_sidebar']//*[contains(text(),'Add episode')]"
			),
			'addEventButtons' => array (
					'xpath' => "//*[@class='button secondary tiny add-event addEvent enabled']"
			),
			'prescriptionDisabled' => array (
					'xpath' => "//*[@title='You do not have permission to add Prescription']"
			),
		'closeSiteAndFirmPopup' => array(
			'xpath' => "//*[@class='ui-dialog-titlebar-close ui-corner-all']//*[contains(text(),'close')]"
		),
		'moreTab' => array(
			'xpath' => "//*[@data-dropdown='menu-item-more-sub']"
		),
		'adminOption' => array(
			'xpath' => "//*[contains(text(),'Admin')]"
		),
		'auditOption' => array(
			'xpath' => "//*[contains(text(),'Audit')]"
		),
		'reportsOption' => array(
			'xpath' => "//*[contains(text(),'Reports')]"
		)
	);

	public function selectNav($navStr){
	    $navList = $this->getElement('navList');
	    $navList->mouseOver();
        $navList->clickLink($navStr);
    }

	public function selectSiteID($siteAddress) {
		$mysite = 'SiteAndFirmForm_site_id';
		$this->elements['siteID'] = array (
			'xpath' => "//*[@id='{$mysite}']"
		);
        $this->waitForElementDisplayBlock('siteID');
		$this->getElement ( 'siteID' )->selectOption ( $siteAddress );
	}
	public function selectFirm($firm) {
		$this->getElement ( 'firmDropdown' )->selectOption ( $firm );
	}
	public function confirmSelection() {
		$this->getElement ( 'confirmSiteAndFirmButton' )->press ();
	}
	public function changeFirm() {
		$this->getElement ( 'changeFirmHeaderLink' )->press ();
	}
	public function searchHospitalNumber($hospital) {
		$this->getElement ( 'mainSearch' )->setValue ( $hospital );
	}
	public function searchPatientName($last, $first) {
		$this->getElement ( 'mainSearch' )->setValue ( $last . ' ' . $first );
	}
	public function searchNhsNumber($nhs) {
		$this->getElement ( 'mainSearch' )->setValue ( $nhs );
	}
	public function searchSubmit() {
		$this->getElement ( 'searchSubmit' )->press ();
		// make sure the patient page is shown after a search
//		$this->waitForTitle ( 'Patient summary' );
		// $this->getSession()->wait(15000, "window.$ && $('h1.badge').html() == 'Patient summary' ");
	}
	public function followLink($link) {
		$this->clickLink ( $link );
	}

	public function ConfirmLeavePage() {
		//$this->driver.Keyboard.PressKey(Keys.Enter);

		//$this->mink->getSession()->getDriver();
		$this->getSession()->getDriver();
		$alert = $driver.SwitchTo.Alert();
		$alert.Accept();
	}
	public function invalidLoginMessage() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'invalidLogin' )->getXpath () );
	}
	public function isInvalidLoginShown() {
		if (!$this->invalidLoginMessage ()) {
			throw new BehaviorException ( "WARNING!!! Invalid Login is NOT displayed WARNING!!!" );
		}
	}
	public function modulesUnavailableCheck() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'allEpisodesBox' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'latestEventBox' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'associatedDataBoxes' )->getXpath () );
	}
	public function levelOneAccess() {
		if ($this->modulesUnavailableCheck ()) {
			throw new BehaviorException ( "WARNING!!! Level 1 RBAC access is NOT functioning correctly WARNING!!!" );
		}
	}
	public function modulesAvailableCheck() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'patientInfoBox' )->getXpath () );
	}
	public function modulesCorrect() {
		if (!$this->modulesAvailableCheck ()) {
			throw new BehaviorException ( "WARNING!!! Patient Information modules are NOT being displayed correctly WARNING!!!" );
		}
	}
	public function modulesUnavailableLevelTwoCheck() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'addOpthalmicDiagnosis' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addSystemicDiagnosis' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addPreviousOperation' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addMedicationButton' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'editCVIstatusButton' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addAllergyButton' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addFamilyHistoryButton' )->getXpath () );
	}
	public function levelTwoAccess() {
		if ($this->modulesUnavailableLevelTwoCheck ()) {
			throw new BehaviorException ( "WARNING!!! Level 2 RBAC access is NOT functioning correctly WARNING!!!" );
		}
	}
	public function modulesUnavailableLevelTwoAdditionalChecks() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'editEvent' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'deleteEvent' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addNewEpisodeButton' )->getXpath () ) || ( bool ) $this->find ( 'xpath', $this->getElement ( 'addEventButtons' )->getXpath () );
	}
	public function levelTwoAccessAdditionalChecks() {
		if ($this->modulesUnavailableLevelTwoAdditionalChecks ()) {
			throw new BehaviorException ( "WARNING!!! Level 2 RBAC access is NOT functioning correctly (edit, print, delete, episode or event) WARNING!!!" );
		}
	}
	public function hasPrintingBeenDisabled() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'printButton' )->getXpath () );
	}
	public function printDisabled() {
		if ($this->hasPrintingBeenDisabled ()) {
			throw new BehaviorException ( "WARNING!!! Level 2 RBAC Printing access IS visible WARNING!!!" );
		}
	}
	public function printingAccessCheck() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'printButton' )->getXpath () );
	}
	public function printAccessCheck() {
		if (!$this->printingAccessCheck ()) {
			throw new BehaviorException ( "WARNING!!! Level 3 RBAC Printing access NOT working WARNING!!!" );
		}
	}
	protected function hasPrescriptionBeenDisabled() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'prescriptionDisabled' )->getXpath () );
	}
	public function levelFourAccess() {
		if (!$this->hasPrescriptionBeenDisabled ()) {
			throw new BehaviorException ( "WARNING!!! Level 4 RBAC Prescription IS enabled WARNING!!!" );
		}
	}

	public function closePopup(){
     sleep(3);
		$this->getElement ( 'closeSiteAndFirmPopup' )->click();
	}

	public function selectTab($pageTab){
     $this->waitForElementDisplayBlock('moreTab');
		$this->getElement('moreTab')->mouseOver();
		if($pageTab=='Admin'){
			$this->getElement('adminOption')->click();
		}
		elseif($pageTab=='Audit'){
			$this->getElement('auditOption')->click();
		}
		elseif($pageTab=='Reports'){
			$this->getElement('reportsOption')->click();
		}
		else{
			throw new BehaviorException ( "Invalid Option Selected!!" );
		}

	}

	public function openUrl($url){
		$this->getDriver()->visit($url);
	}

	public function homepageAlert($alert){
		$this->elements['homepageAlert'] = array (
			'xpath' => "//*[//*[@class='messages patient']//*[contains(text(),'$alert')]]"
		);
		if(!$this->getElement('homepageAlert')->isVisible()){
			throw new BehaviorException("Alert not displayed correctly!");
		}
	}

	public function clickOnTab($tab){
		$this->elements['tabID'] = array (
			'xpath' => "//*[@class='inline-list navigation user right']//*[contains(text(),'$tab')]"
		);
		$this->getElement('tabID')->click();
	}

}
