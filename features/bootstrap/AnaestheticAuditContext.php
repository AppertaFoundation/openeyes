<?php
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
class AnaestheticAuditContext extends PageObjectContext {

	/**
	 * @Then /^I select an Anaesthetist "([^"]*)"$/
	 */
	public function iSelectAnAnaesthetist($anaesthetist) {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->anaesthetist ( $anaesthetist );
	}
	
	/**
	 * @Given /^I select Satisfaction levels of Pain "([^"]*)" Nausea "([^"]*)"$/
	 */
	public function iSelectSatisfactionLevelsOfPainNausea($pain, $nausea) {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->pain ( $pain );
		$asa->nausea ( $nausea );
	}
	
	/**
	 * @Given /^I tick the Vomited checkbox$/
	 */
	public function iTickTheVomitedCheckbox() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->vomitCheckBoxNo ();
	}
	
	/**
	 * @Given /^I untick the Vomited checkbox$/
	 */
	public function iUntickTheVomitedCheckbox() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->vomitCheckBoxNo ();
	}
	
	/**
	 * @Then /^I select Vital Signs of Respiratory Rate "([^"]*)" Oxygen Saturation "([^"]*)" Systolic Blood Pressure "([^"]*)"$/
	 */
	public function iSelectVitalSigns($rate, $oxygen, $pressure) {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->respiratoryRate ( $rate );
		$asa->oxygenSaturation ( $oxygen );
		$asa->systolicBlood ( $pressure );
	}
	
	/**
	 * @Then /^I select Vital Signs of Body Temperature "([^"]*)" and Heart Rate "([^"]*)" Conscious Level AVPU "([^"]*)"$/
	 */
	public function iSelectVitalSignsTemp($temp, $rate, $level) {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->bodyTemp ( $temp );
		$asa->heartRate ( $rate );
		$asa->consciousLevel ( $level );
	}
	
	/**
	 * @Then /^I enter Comments "([^"]*)"$/
	 */
	public function iEnterComments($comments) {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->comments ( $comments );
	}
	
	/**
	 * @Given/^I select the Yes option for Ready to Discharge$/
	 */
	public function iSelectTheYesOptionForReadyToDischarge() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->dischargeYes ();
	}
	
	/**
	 * @Given /^I select the No option for Ready to Discharge$/
	 */
	public function iSelectTheNoOptionForReadToDischarge() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->dischargeNo ();
	}
	
	/**
	 * @Then /^I Save the Event$/
	 */
	public function iSaveTheEvent() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->saveEvent ();
	}
	
	/**
	 * @Then /^I Save the Event and confirm it has been created successfully$/
	 */
	public function iSaveTheEventAndConfirm() {
		/**
		 *
		 * @var Examination $exam
		 */
		$exam = $this->getPage ( 'Examination' );
		$exam->saveAndConfirm();
	}
	
	/**
	 * @Given /^I edit the Last Event$/
	 */
	public function iEditTheLastEvent() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->editEvent ();
	}
	
	/**
	 * @Given /^I delete the Last Event$/
	 */
	public function iDeleteTheLastEvent() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->deleteEvent ();
	}
	
	/**
	 * @Then /^I confirm that the ASA Validation error messages have been displayed$/
	 */
	public function iConfirmThatTheAsaValidationErrorMessagesHaveBeenDisplayed() {
		/**
		 *
		 * @var AnaestheticAudit $asa
		 */
		$asa = $this->getPage ( 'AnaestheticAudit' );
		$asa->validationErrorCheck ();
	}
}