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

class TherapyApplicationContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I add Right Side$/
     */
    public function iAddRightSide()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->addRightSide();
    }

    /**
     * @Given /^I select a Right Side Diagnosis of "([^"]*)"$/
     */
    public function iSelectARightSideDiagnosisOf($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Given /^I select a Left Side Diagnosis of "([^"]*)"$/
     */
    public function iSelectALeftSideDiagnosisOf($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select a Right Secondary To of "([^"]*)"$/
     */
    public function iSelectARightSecondaryToOf($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select a Left Secondary To of "([^"]*)"$/
     */
    public function iSelectALeftSecondaryToOf($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Cerebrovascular accident Yes$/
     */
    public function iSelectCerebrovascularAccidentYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Cerebrovascular accident No$/
     */
    public function iSelectCerebrovascularAccidentNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Ischaemic attack Yes$/
     */
    public function iSelectIschaemicAttackYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Ischaemic attack No$/
     */
    public function iSelectIschaemicAttackNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Myocardial infarction Yes$/
     */
    public function iSelectMyocardialInfarctionYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I select Myocardial infarction No$/
     */
    public function iSelectMyocardialInfarctionNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Given /^I select a Consultant of "([^"]*)"$/
     */
    public function iSelectAConsultantOf($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

    /**
     * @Then /^I Save the Therapy Application$/
     */
    public function iSaveTheTherapyApplication()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
    }

}