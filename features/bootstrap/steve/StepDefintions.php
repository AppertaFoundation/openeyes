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
class FeatureContextSteve extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * @Given /^I am on the OpenEyes "([^"]*)" homepage$/
     */
    public function iAmOnTheOpeneyesHomepage($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select Site "([^"]*)"$/
     */
    public function iSelectSite($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter login credentials "([^"]*)" and "([^"]*)"$/
     */
    public function iEnterLoginCredentialsAnd($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I search for NHS number "([^"]*)"$/
     */
    public function iSearchForNhsNumber($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Add an Ophthalmic Diagnosis selection of "([^"]*)"$/
     */
    public function iAddAnOphthalmicDiagnosisSelectionOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select that it affects eye "([^"]*)"$/
     */
    public function iSelectThatItAffectsEye($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Opthalmic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iSelectAOpthalmicDiagnosisDateOfDayMonthYear($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I save the new Opthalmic Diagnosis$/
     */
    public function iSaveTheNewOpthalmicDiagnosis()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Add an Systemic Diagnosis selection of "([^"]*)"$/
     */
    public function iAddAnSystemicDiagnosisSelectionOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select that it affects side "([^"]*)"$/
     */
    public function iSelectThatItAffectsSide($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Systemic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iSelectASystemicDiagnosisDateOfDayMonthYear($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I save the new Systemic Diagnosis$/
     */
    public function iSaveTheNewSystemicDiagnosis()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I edit the CVI Status "([^"]*)" day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iEditTheCviStatusDayMonthYear($arg1, $arg2, $arg3, $arg4)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I Add Medication details medication "([^"]*)" route "([^"]*)" frequency "([^"]*)" date from "([^"]*)"$/
     */
    public function iAddMedicationDetailsMedicationRouteFrequencyDateFrom($arg1, $arg2, $arg3, $arg4)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Add Allergy "([^"]*)"$/
     */
    public function iAddAllergy($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I am on "([^"]*)"$/
     */
    public function iAmOn($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When /^I fill in "([^"]*)" with "([^"]*)"$/
     */
    public function iFillInWith($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I press "([^"]*)"$/
     */
    public function iPress($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see "([^"]*)"$/
     */
    public function iShouldSee($arg1)
    {
        throw new PendingException();
    }


}
