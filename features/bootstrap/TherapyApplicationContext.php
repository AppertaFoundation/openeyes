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
    public function iSelectARightSideDiagnosisOf($diagnosis)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightSideDiagnosis($diagnosis);
    }

    /**
     * @Given /^I select a Left Side Diagnosis of "([^"]*)"$/
     */
    public function iSelectALeftSideDiagnosisOf($diagnosis)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftSideDiagnosis($diagnosis);
    }

    /**
     * @Then /^I select a Right Secondary To of "([^"]*)"$/
     */
    public function iSelectARightSecondaryToOf($secondary)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightSecondaryTo($secondary);
    }

    /**
     * @Then /^I select a Left Secondary To of "([^"]*)"$/
     */
    public function iSelectALeftSecondaryToOf($secondary)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftSecondaryTo($secondary);
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
        $TherapyApplication->cerebYes();
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
        $TherapyApplication->cerebNo();
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
        $TherapyApplication->ischaemicYes();
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
        $TherapyApplication->ischaemicNo();
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
        $TherapyApplication->myocardialYes();
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
        $TherapyApplication->myocardialNo();
    }

    /**
     * @Then /^I select a Right Treatment of "([^"]*)"$/
     */
    public function iSelectARightTreatmentOf($treatment)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightTreatment($treatment);
    }

    /**
     * @Given /^I select a Right Angiogram Baseline Date of "([^"]*)"$/
     */
    public function iSelectARightAngiogramBaselineDateOf($date)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightDate($date);
    }

    /**
     * @Then /^I select a Left Treatment of "([^"]*)"$/
     */
    public function iSelectALeftTreatmentOf($treatment)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftTreatment($treatment);
    }

    /**
     * @Given /^I select a Left Angiogram Baseline Date of "([^"]*)"$/
     */
    public function iSelectALeftAngiogramBaselineDateOf($date)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftDate($date);
    }

    /**
     * @Given /^I select a Consultant of "([^"]*)"$/
     */
    public function iSelectAConsultantOf($consultant)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->consultantSelect($consultant);
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
        $TherapyApplication->saveTherapy();
    }

}