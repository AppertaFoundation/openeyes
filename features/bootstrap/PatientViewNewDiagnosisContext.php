<?php
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
class PatientViewContext extends PageObjectContext {
	public function __construct(array $parameters) {
	}
	
	/**
	 * @Then /^I select Add First New Episode and Confirm$/
	 */
	public function iSelectAddFirstNewEpisodeAndConfirm() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addEpisodeAndEvent ();
		$patientView->addEpisode ();
	}
	
	/**
	 * @Given /^I select Add Episode from the sidebar$/
	 */
	public function iSelectAddEpisode() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addEpisodePreviousFirmCreated ();
	}
	
	/**
	 * @Then /^I select Create or View Episodes and Events$/
	 */
	public function CreateOrViewEpisodesAndEvents() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addEpisodeAndEvent ();
	}
	
	/**
	 * @Then /^I select the Latest Event$/
	 */
	public function iSelectTheLatestEvent() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientview
		 */
		$patientview = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientview->selectLatestEvent ();
		// $patientview->addEpisodeAndEvent();
	}
	
	/**
	 * @Then /^I Add an Ophthalmic Diagnosis selection of "([^"]*)"$/
	 */
	public function addOpthalmicDiagnosis($diagnosis) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addOpthalmicDiagnosis ( $diagnosis );
	}
	
	/**
	 * @Given /^I select that it affects eye "([^"]*)"$/
	 */
	public function SelectThatItAffectsEye($eye) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->selectEye ( $eye );
	}
	
	/**
	 * @Given /^I select a Opthalmic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
	 */
	public function OpthalmicDiagnosis($day, $month, $year) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		sleep(5);
		$patientView->addOpthalmicDate ( $day, $month, $year );
	}
	
	/**
	 * @Then /^I save the new Opthalmic Diagnosis$/
	 */
	public function SaveTheNewOpthalmicDiagnosis() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->saveOpthalmicDiagnosis ();
	}
	
	/**
	 * @Then /^I Add an Systemic Diagnosis selection of "([^"]*)"$/
	 */
	public function SystemicDiagnosisSelection($diagnosis) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addSystemicDiagnosis ( $diagnosis );
	}
	
	/**
	 * @Given /^I select that it affects Systemic side "([^"]*)"$/
	 */
	public function systemicSide($side) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->selectSystemicSide ( $side );
	}
	
	/**
	 * @Given /^I select a Systemic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
	 */
	public function SystemicDiagnosisDate($day, $month, $year) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addSystemicDate ( $day, $month, $year );
	}
	
	/**
	 * @Then /^I save the new Systemic Diagnosis$/
	 */
	public function SaveTheNewSystemicDiagnosis() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->saveSystemicDiagnosis ();
	}
	
	/**
	 * @Then /^I Add a Previous Operation of "([^"]*)"$/
	 */
	public function iAddAPreviousOperationOf($operation) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->previousOperation ( $operation );
	}
	
	/**
	 * @Given /^I select that it affects Operation side "([^"]*)"$/
	 */
	public function SelectThatItAffectsOperationSide($operation) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->operationSide ( $operation );
	}
	
	/**
	 * @Given /^I select a Previous Operation date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
	 */
	public function PreviousOperationDate($day, $month, $year) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		sleep(5);
		$patientView->addOperationDate ( $day, $month, $year );
	}
	
	/**
	 * @Then /^I save the new Previous Operation$/
	 */
	public function iSaveTheNewPreviousOperation() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->savePreviousOperation ();
	}
	
	/**
	 * @Given /^I Add Medication details medication "([^"]*)" route "([^"]*)" frequency "([^"]*)" date from "([^"]*)" and Save$/
	 */
	public function iAddMedicationDetails($medication, $route, $frequency, $dateFrom) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->medicationDetails ( $medication, $route, $frequency, $dateFrom );
	}
	
	/**
	 * @Then /^I edit the CVI Status "([^"]*)"$/
	 */
	public function iEditTheCviStatus($status) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->editCVIstatus ( $status );
	}
	
	/**
	 * @Given /^I select a CVI Status date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
	 */
	public function iSelectACviStatusDateOfDayMonthYear($day, $month, $year) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addCVIDate ( $day, $month, $year );
	}
	
	/**
	 * @Then /^I save the new CVI status$/
	 */
	public function iSaveTheNewCviStatus() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->saveCVIstatus ();
	}
	/**
	 * @Then /^I Remove existing Allergy$/
	 */
	public function removeAllergy() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->removeAllergy ();
	}
	
	/**
	 * @Then /^I Add Allergy "([^"]*)" and Save$/
	 */
	public function iAddAllergy($allergy) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addAllergy ( $allergy );
	}
	
	/**
	 * @Then /^I confirm the patient has no allergies and Save$/
	 */
	public function iConfirmThePatientHasNoAllergies() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->noAllergyTickbox ();
	}
	
	/**
	 * @Given /^I Add a Family History of relative "([^"]*)" side "([^"]*)" condition "([^"]*)" and comments "([^"]*)" and Save$/
	 */
	public function FamilyHistory($relative, $side, $condition, $comments) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addFamilyHistory ( $relative, $side, $condition, $comments );
	}
	
	/**
	 * @Then /^I remove the Opthalmic Diagnosis$/
	 */
	public function iRemoveTheOpthalmicDiagnosis() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->removeAndConfirm ();
	}
	
	/**
	 * @Then /^I remove the Systemic Diagnosis$/
	 */
	public function iRemoveTheSystemicDiagnosis() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->removeAndConfirm ();
	}
	
	/**
	 * @Then /^I remove the Previous Operation$/
	 */
	public function iRemoveThePreviousOperation() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->removeOperation ();
	}
	
	/**
	 * @Then /^I remove the Medication$/
	 */
	public function iRemoveMedication() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->removeMedication ();
	}
	
	/**
	 * @Then /^I expand Social History$/
	 */
	public function iExpandSocialHistory() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->addSocialHistory ();
	}
	
	/**
	 * @Given /^I add an Occupation of "([^"]*)"$/
	 */
	public function iAddAnOccupationOf($occupation) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->occupationType ( $occupation );
	}
	
	/**
	 * @Given /^I add an Occupation Other type of "([^"]*)"$/
	 */
	public function iAddAnOccupationOtherTypeOf($other) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->occupationOtherType ( $other );
	}
	
	/**
	 * @Then /^I add a Driving status of "([^"]*)"$/
	 */
	public function iAddADrivingStatusOf($status) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->drivingStatus ( $status );
	}
	
	/**
	 * @Given /^I add a Smoking status of "([^"]*)"$/
	 */
	public function iAddASmokingStatusOf($status) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->smokingStatus ( $status );
	}
	
	/**
	 * @Given /^I add an Accommodation status of "([^"]*)"$/
	 */
	public function iAddAnAccommodationStatusOf($status) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->accommodationStatus ( $status );
	}
	
	/**
	 * @Then /^I add Social Comments of "([^"]*)"$/
	 */
	public function iAddSocialCommentsOf($comments) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->socialComments ( $comments );
	}
	
	/**
	 * @Given /^I select a Carer status of "([^"]*)"$/
	 */
	public function iSelectACarerStatusOf($carer) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->carerStatus ( $carer );
	}
	
	/**
	 * @Given /^I select a Substance Misuse status of "([^"]*)"$/
	 */
	public function iSelectASubstanceMisuseStatusOf($substance) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->substanceMisuse ( $substance );
	}
	
	/**
	 * @Then /^I set an Alcohol intake of "([^"]*)" units a week$/
	 */
	public function iSetAnAlcoholIntakeOfUnitsAWeek($units) {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->alcoholIntake ( $units );
	}
	
	/**
	 * @Then /^I Save the Social History$/
	 */
	public function iSaveTheSocialHistory() {
		/**
		 *
		 * @var PatientViewNewDiagnosis $patientView
		 */
		$patientView = $this->getPage ( 'PatientViewNewDiagnosis' );
		$patientView->saveSocialHistory ();
	}
}