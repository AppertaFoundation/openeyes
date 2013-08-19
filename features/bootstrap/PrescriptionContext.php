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

//    /**
//     * @Then /^I select a Common Drug "([^"]*)"$/
//     */
//    public function iSelectACommonDrug($drug)
//    {
//       $this->selectOption(Prescription::$prescriptionDropDown, $drug);
//    }
//
//    /**
//     * @Given /^I select a Standard Set of "([^"]*)"$/
//     */
//    public function iSelectAStandardSetOf($set)
//    {
//       $this->selectOption(Prescription::$prescriptionStandardSet, $set);
//    }
//
//    /**
//     * @Then /^I enter a Dose of "([^"]*)" drops$/
//     */
//    public function iEnterADoseOfDrops($drops)
//    {
//       //Clear field required here
//       $this->fillField(Prescription::$prescriptionDose, $drops);
//    }
//
//    /**
//     * @Given /^I enter a route of "([^"]*)"$/
//     */
//    public function iEnterARouteOf($route)
//    {
//       $this->selectOption(Prescription::$prescriptionRoute, $route);
//    }
//
//    /**
//     * @Then /^I enter a eyes option "([^"]*)"$/
//     */
//    public function iEnterAEyesOption($eyes)
//    {
//       $this->selectOption(Prescription::$prescriptionOptions, $eyes);
//    }
//
//    /**
//     * @Given /^I enter a frequency of "([^"]*)"$/
//     */
//    public function iEnterAFrequencyOf($frequency)
//    {
//       $this->selectOption(Prescription::$prescriptionFrequency, $frequency);
//    }
//
//    /**
//     * @Then /^I enter a duration of "([^"]*)"$/
//     */
//    public function iEnterADurationOf($duration)
//    {
//       $this->selectOption(Prescription::$prescriptionDuration, $duration);
//    }
//
//    /**
//     * @Given /^I add Prescription comments of "([^"]*)"$/
//     */
//    public function iAddPrescriptionCommentsOf($comments)
//    {
//       $this->selectOption(Prescription::$prescriptionComments, $comments);
//    }

}