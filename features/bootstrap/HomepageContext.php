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

class HomepageContext extends PageObjectContext
{
    private $patient;

    public function __construct(array $parameters)
    {

    }

    /**
     * @Given /^I select Site "([^"]*)"$/
     */
    public function iSelectSite($siteAddress)
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->selectSiteID($siteAddress);
    }

    /**
     * @Given /^I select a firm of "([^"]*)"$/
     */
    public function iselectAFirm($firm)
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->selectFirm($firm);
        $homepage->confirmSelection();
    }

    /**
     * @Then /^I select Change Firm$/
     */
    public function changeFirm ()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->changeFirm();
    }

    /**
     * @Then /^I search for hospital number "([^"]*)"$/
     */

    public function SearchForHospitalNumber($hospital)
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->searchHospitalNumber($hospital);
        $homepage->searchSubmit();
    }

    /**
     * @Then /^I search for patient name last name "([^"]*)" and first name "([^"]*)"$/
     */
    public function SearchPatientName ($last, $first)
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->searchPatientName($last, $first);
        $homepage->searchSubmit();
    }

    /**
     * @Then /^I search for NHS number "([^"]*)"$/
     */
    public function SearchForNhsNumber($nhs)
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->searchNhsNumber($nhs);
        $homepage->searchSubmit();
    }

    /**
     * @When /^I follow "([^"]*)"$/
     */
    public function iFollow($link)
    {
        $hp = $this->getPage('Homepage');
//        $hp->open();
        $hp->getSession()->wait(5000, "window.$ && $.active == 0");
        $hp->clickLink($link);
    }

    /**
     * @Given /^I confirm that an Invalid Login error message is displayed$/
     */
    public function iConfirmThatAnInvalidLoginErrorMessageIsDisplayed()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->isInvalidLoginShown();
    }

    /**
     * @Then /^a check is made to confirm the user has correct level one access$/
     */
    public function aCheckIsMadeToConfirmTheUserHasCorrectLevelOneAccess()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->levelOneAccess();
    }

    /**
     * @Then /^a check is made to confirm the user has correct level two access$/
     */
    public function aCheckIsMadeToConfirmTheUserHasCorrectLevelTwoAccess()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->levelTwoAccess();
    }

    /**
     * @Then /^a check is made to confirm that Patient details information is displayed$/
     */
    public function aCheckIsMadeToConfirmThatPatientDetailsInformationIsDisplayed()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->modulesCorrect();
    }

    /**
     * @Then /^additional checks are made for correct level two access$/
     */
    public function additionalChecksAreMadForCorrectLevelTwoAccess()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->levelTwoAccessAdditionalChecks();
    }

    /**
     * @Given /^a check to see printing has been enabled$/
     */
    public function aCheckToSeePrintingHasBeenEnabled()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->printAccessCheck();
    }

    /**
     * @Given /^a check to see printing has been disabled$/
     */
    public function aCheckToSeePrintingHasBeenDisabled()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->printDisabled();
    }

    /**
     * @Then /^a check is made to confirm the user has correct level four access$/
     */
    public function aCheckIsMadeToConfirmTheUserHasCorrectLevelFourAccess()
    {
        /**
         * @var Homepage $homepage
         */
        $homepage = $this->getPage('Homepage');
        $homepage->levelFourAccess();
    }



}