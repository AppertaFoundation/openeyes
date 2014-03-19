<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class CorrespondenceContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I select Site ID "([^"]*)"$/
     */
    public function iSelectSiteId($site)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->siteDropdown($site);
    }

    /**
     * @Given /^I select Address Target "([^"]*)"$/
     */
    public function iSelectAddressTarget($address)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->addressTarget($address);
    }

    /**
     * @Then /^I choose a Macro of "([^"]*)"$/
     */
    public function iChooseAMacroOf($macro)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->macro($macro);
    }

    /**
     * @Given /^I select Clinic Date "([^"]*)"$/
     */
    public function iSelectClinicDate($date)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->clinicDate($date);
    }

    /**
     * @Then /^I choose an Introduction of "([^"]*)"$/
     */
    public function iChooseAnIntroductionOf($intro)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->introduction($intro);
    }

    /**
     * @Given /^I add Findings of "([^"]*)"$/
     */
    public function iAddFindingsOf($findings)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->findings($findings);
    }

    /**
     * @Given /^I choose a Diagnosis of "([^"]*)"$/
     */
    public function iChooseADiagnosisOf($diagnosis)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->diagnosis($diagnosis);
    }

    /**
     * @Then /^I choose a Management of "([^"]*)"$/
     */
    public function iChooseAManagementOf($management)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->management($management);
    }

    /**
     * @Given /^I choose Drugs "([^"]*)"$/
     */
    public function iChooseDrugs($drugs)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->drugs($drugs);
    }

    /**
     * @Then /^I choose Outcome "([^"]*)"$/
     */
    public function iChooseOutcome($outcome)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->outcome($outcome);
    }

    /**
     * @Given /^I choose CC Target "([^"]*)"$/
     */
    public function iChooseCcTarget($cc)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->CC($cc);
    }

    /**
     * @Given /^I add a New Enclosure of "([^"]*)"$/
     */
    public function iAddANewEnclosure($enclosure)
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->enclosure($enclosure);
    }

    /**
     * @Then /^I Save the Correspondence Draft and confirm it has been created successfully$/
     */
    public function iSaveTheCorrespondenceDraftAndConfirm()
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->saveCorrespondenceAndConfirm();
    }

    /**
     * @Then /^I Save the Correspondence Draft$/
     */
    public function iSaveTheCorrespondenceDraft()
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->saveDraft();
    }

    /**
     * @Then /^I Confirm that the Mandatory Correspondence fields validation error messages are displayed$/
     */
    public function iConfirmThatTheMandatoryFieldsValidationErrorMessagesAreDisplayed()
    {
        /**
         * @var correspondence $Correspondence
         */
        $Correspondence  = $this->getPage('Correspondence');
        $Correspondence->correspondenceMandatoryFieldsErrorValidation();
    }
}