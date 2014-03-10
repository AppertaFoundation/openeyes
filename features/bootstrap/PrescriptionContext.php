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