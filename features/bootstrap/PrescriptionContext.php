<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\YiiExtension\Context\YiiAwareContextInterface;
use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class PrescriptionContext extends PageObjectContext
{
    public function __construct(array $paramters)
    {

    }

    /**
     * @Then /^I choose to filter by type "([^"]*)"$/
     */
    public function iChooseToFilterByType($filter)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->filterBy($filter);
    }

    /**
     * @Given /^I select the No preservative checkbox$/
     */
    public function iSelectTheNoPreservativeCheckbox()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->noPreservativeCheckbox();
    }

    /**
     * @Then /^I select a Common Drug "([^"]*)"$/
     */
    public function iSelectACommonDrug($drug)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->prescriptionDropdown($drug);

    }

    /**
     * @Given /^I select a Standard Set of "([^"]*)"$/
     */
    public function iSelectAStandardSetOf($set)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->standardSet($set);
    }

    /**
     * @Then /^I enter a Dose of "([^"]*)" drops$/
     */
    public function iEnterADoseOfDrops($drops)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->item0DoseDrops($drops);
    }

    /**
     * @Given /^I enter a route of "([^"]*)"$/
     */
    public function iEnterARouteOf($route)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->item0Route($route);
    }

    /**
     * @Then /^I enter a eyes option "([^"]*)"$/
     */
    public function iEnterAEyesOption($eyes)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->eyeOptionItem0($eyes);
    }

    /**
     * @Given /^I enter a frequency of "([^"]*)"$/
     */
    public function iEnterAFrequencyOf($frequency)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->frequencyItem0($frequency);
    }

    /**
     * @Then /^I enter a duration of "([^"]*)"$/
     */
    public function iEnterADurationOf($duration)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->durationItem1($duration);
    }

    /**
     * @Then /^I add a Taper$/
     */
    public function iAddATaper()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->addTaper();
    }

    /**
     * @Given /^I enter a first Taper does of "([^"]*)"$/
     */
    public function iEnterAFirstTaperDoesOf($taper)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->firstTaperDose($taper);
    }

    /**
     * @Then /^I enter a first Taper frequency of "([^"]*)"$/
     */
    public function iEnterAFirstTaperFrequencyOf($frequency)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->firstTaperFrequency($frequency);
    }

    /**
     * @Given /^I enter a first Taper duration of "([^"]*)"$/
     */
    public function iEnterAFirstTaperDurationOf($duration)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->firstTaperDuration($duration);
    }

    /**
     * @Given /^I enter a second Taper dose of "([^"]*)"$/
     */
    public function iEnterASecondTaperDoseOf($taper)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->secondTaperDose($taper);
    }

    /**
     * @Then /^I enter a second Taper frequency of "([^"]*)"$/
     */
    public function iEnterASecondTaperFrequencyOf($frequency)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->secondTaperFrequency($frequency);
    }

    /**
     * @Given /^I enter a second Taper duration of "([^"]*)"$/
     */
    public function iEnterASecondTaperDurationOf($duration)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->secondTaperDuration($duration);
    }

    /**
     * @Then /^I remove the last Taper$/
     */
    public function iRemoveTheLastTaper()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->removeThirdTaper();
    }

    /**
     * @Given /^I add Prescription comments of "([^"]*)"$/
     */
    public function iAddPrescriptionCommentsOf($comments)
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->comments($comments);
    }

    /**
     * @Given /^I confirm the prescription validation error has been displayed$/
     */
    public function iConfirmThePrescriptionValidationErrorHasBeenDisplayed()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->confirmPrescriptionValidationError();
    }

    /**
     * @Then /^I Save the Prescription Draft$/
     */
    public function iSaveThePrescription()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->savePrescription();
    }

    /**
     * @Then /^I Save the Prescription Draft and confirm it has been created successfully$/
     */
    public function iSaveThePrescriptionDraft()
    {
        /**
         * @var Prescription $prescription
         */
        $prescription= $this->getPage('Prescription');
        $prescription->savePrescriptionAndConfirm();
    }


}