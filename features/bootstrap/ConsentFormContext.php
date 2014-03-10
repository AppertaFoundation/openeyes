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
     * @Then /^I select Unbooked Procedures$/
     */
    public function iSelectUnbookedProcedures()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->unbookedProcedure();
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
    public function iChooseType($type)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->chooseType($type);
    }

    /**
     * @Then /^I choose Procedure eye of "([^"]*)"$/
     */
    public function iChooseProcedureEyeOf($eye)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->procedureEye($eye);
    }

    /**
     * @Given /^I choose a Procedure of "([^"]*)"$/
     */
    public function iChooseAProcedureOf($type)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->procedureType($type);
    }


    /**
     * @Given /^I choose an Anaesthetic type of LA$/
     */
    public function iChooseAnAnaestheticTypeOf()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->anaestheticTypeLA();
    }

    /**
     * @Given /^I choose an Anaesthetic type of LAC$/
     */
    public function iChooseAnAnaestheticTypeOfLAC()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->anaestheticTypeLAC();
    }

    /**
     * @Given /^I add a common procedure of "([^"]*)"$/
     */
    public function iAddACommonProcedureOf($common)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->commonProcedure($common);
    }

    /**
     * @Then /^I choose Permissions for images No$/
     */
    public function iChoosePermissionsForImages()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->permissionImagesNo();
    }

    /**
     * @Then /^I choose Permissions for images Yes$/
     */
    public function iChoosePermissionsForImagesYes()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->permissionImagesNo();
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
        $consentForm->informationLeaflet();
    }

    /**
     * @Given /^I select the Anaesthetic leaflet checkbox$/
     */
    public function iSelectTheAnasetheticLefletCheckbox()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->anaestheticLeaflet();
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
        $consentForm->witnessRequired();
    }

    /**
     * @Given /^I enter a Witness Name of "([^"]*)"$/
     */
    public function iEnterAWitnessNameOf($witness)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->witnessName($witness);
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
        $consentForm->interpreterRequired();
    }

    /**
     * @Given /^I enter a Interpreter name of "([^"]*)"$/
     */
    public function iEnterAInterpreterNameOf($name)
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->interpreterName($name);
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
        $consentForm->supplementaryConsent();
    }

    /**
     * @Then /^I save the Consent Form Draft$/
     */
    public function iSaveTheConsentFormDraft()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->saveConsentFormDraft();
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
        $consentForm->saveConsentForm();
    }

    /**
     * @Then /^I save the Consent Form and confirm it has been created successfully$/
     */
    public function iSaveTheConsentFormAndConfirm()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->saveConsentAndConfirm();
    }
}
