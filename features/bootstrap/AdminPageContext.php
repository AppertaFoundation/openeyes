<?php
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;
class AdminPageContext extends PageObjectContext {
    public function __construct(array $parameters) {
    }

    /**
     * @Given /^I click on edit button in risks section$/
     */
    public function iClickOnEditButtonInRisksSection()
    {

    }

    /**
     * @When /^I select that patient has no risks$/
     */
    public function iSelectThatPatientHasNoRisks()
    {

    }

    /**
     * @Given /^I click on save button$/
     */
    public function iClickOnSaveButton()
    {

    }

    /**
     * @Then /^I should see the no risks information at risks section$/
     */
    public function iShouldSeeTheNoRisksInformationAtRisksSection()
    {


    }

    /**
     * @Then /^I select "([^"]*)" from the tabs on the admin page$/
     */
    public function iSelectTabFromAdminPage($tab)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->selectTab($tab);

    }

    /**
     * @Then /^I select the "([^"]*)"$/
     */
    public function iSelectSubTab($subTab)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->selectSubTab($subTab);

    }


    /**
     * @Then /^I look for the "([^"]*)" from the DICOM log and open process$/
     */
    //include processName
    public function iLookInLog($dicomFile)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->lookLog($dicomFile);

    }

    /**
     * @Then /^I choose the "([^"]*)" from DICOM file list$/
     */
    public function iChooseFromList($dicom)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->chooseFromList($dicom);

    }

    /**
     * @Then /^I click on submit$/
     */
    public function iClickOnSubmit()
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->clickSubmit();

    }

    /**
     * @Then /^I should see "([^"]*)" on the DICOM File Watcher page$/
     */
    public function iShouldSeeMessage($message)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->seeMessage($message);

    }

    /**
     * @Then /^I verify the "([^"]*)" with the "([^"]*)" and "([^"]*)"$/
     */
    public function iVerifyStatus($dicomFile,$processStatus,$processName)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->VerifyStatus($dicomFile,$processStatus,$processName);

    }

    /**
     * @Then /^I look for "([^"]*)","([^"]*)" and "([^"]*)" in machine details$/
     */
    public function iLookMachineDetails($make,$model,$softwareVersion)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->machineDetails($make,$model,$softwareVersion);

    }

    /**
     * @Then /^I enter "([^"]*)" with the "([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)" and "([^"]*)" in the search fields$/
     */
    public function iEnterSearch($DICOMFile,$stationID,$location,$patientNumber,$status,$type,$studyInstanceId)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->enterSearch($DICOMFile,$stationID,$location,$patientNumber,$status,$type,$studyInstanceId);

    }

    /**
     * @Then /^Then I select "([^"]*)", "([^"]*)" and "([^"]*)"$/
     */
    public function iEnterSearchDate($dateType,$startDate,$endDate)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->enterSearchDate($dateType,$startDate,$endDate);

    }

    /**
     * @Then /^I click search$/
     */
    public function iClickSearch()
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->clickLogSearch();

    }

    /**
     * @Then /^I search for "([^"]*)" in debug data$/
     */
    public function iSearchDebugData($dicomValue)
    {
        /**
         *
         * @var AdminPage $adminPage
         */
        $adminPage= $this->getPage('AdminPage');
        $adminPage->searchDebugData($dicomValue);

    }
}