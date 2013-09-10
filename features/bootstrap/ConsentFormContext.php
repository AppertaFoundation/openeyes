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

class ConsentFormContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }


    /**
     * @Then /^I select Add Consent Form$/
     */
    public function iSelectAddConsentForm()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->createConsentForm();
    }

    /**
     * @Given /^I choose Type "([^"]*)"$/
     */
    public function iChooseType($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I choose Procedure eye of "([^"]*)"$/
     */
    public function iChooseProcedureEyeOf($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I choose an Anaesthetic type of "([^"]*)"$/
     */
    public function iChooseAnAnaestheticTypeOf($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I add a common procedure of "([^"]*)"$/
     */
    public function iAddACommonProcedureOf($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I choose Permissions for images "([^"]*)"$/
     */
    public function iChoosePermissionsForImages($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I select the Information leaflet checkbox$/
     */
    public function iSelectTheInformationLeafletCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I select the Anasethetic leflet checkbox$/
     */
    public function iSelectTheAnasetheticLefletCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I select a Witness Required checkbox$/
     */
    public function iSelectAWitnessRequiredCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I enter a Witness Name of "([^"]*)"$/
     */
    public function iEnterAWitnessNameOf($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I select a Interpreter required checkbox$/
     */
    public function iSelectAInterpreterRequiredCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Given /^I enter a Interpreter name of "([^"]*)"$/
     */
    public function iEnterAInterpreterNameOf($arg1)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I select a supplementary consent form checkbox$/
     */
    public function iSelectASupplementaryConsentFormCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }

    /**
     * @Then /^I save the Consent Form$/
     */
    public function iSaveTheConsentForm()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
    }



}
