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

class CorrespondenceContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

//    /**
//     * @Then /^I select Site ID "([^"]*)"$/
//     */
//    public function iSelectSiteId($site)
//    {
//        $this->selectOption(Correspondence::$siteDropdown, $site);
//    }
//
//    /**
//     * @Given /^I select Address Target "([^"]*)"$/
//     */
//    public function iSelectAddressTarget($address)
//    {
//       $this->selectOption(Correspondence::$addressTarget, $address);
//    }
//
//    /**
//     * @Then /^I choose a Macro of "([^"]*)"$/
//     */
//    public function iChooseAMacroOf($macro)
//    {
//       $this->selectOption(Correspondence::$macro, $macro);
//    }
//
//    /**
//     * @Then /^I choose an Introduction of "([^"]*)"$/
//     */
//    public function iChooseAnIntroductionOf($intro)
//    {
//        $this->selectOption(Correspondence::$introduction, $intro);
//    }
//
//    /**
//     * @Given /^I choose a Diagnosis of "([^"]*)"$/
//     */
//    public function iChooseADiagnosisOf($diagnosis)
//    {
//        $this->selectOption(Correspondence::$diagnosis, $diagnosis);
//    }
//
//    /**
//     * @Then /^I choose a Management of "([^"]*)"$/
//     */
//    public function iChooseAManagementOf($management)
//    {
//        $this->selectOption(Correspondence::$management, $management);
//    }
//
//    /**
//     * @Given /^I choose Drugs "([^"]*)"$/
//     */
//    public function iChooseDrugs($drugs)
//    {
//        $this->selectOption(Correspondence::$drugs, $drugs);
//    }
//
//    /**
//     * @Then /^I choose Outcome "([^"]*)"$/
//     */
//    public function iChooseOutcome($outcome)
//    {
//        $this->selectOption(Correspondence::$outcome, $outcome);
//    }
//
//    /**
//     * @Given /^I choose CC Target "([^"]*)"$/
//     */
//    public function iChooseCcTarget($cc)
//    {
//        $this->selectOption(Correspondence::$letterCc, $cc);
//    }
//
//    /**
//     * @Given /^I add a New Enclosure$/
//     */
//    public function iAddANewEnclosure()
//    {
//        $this->clickLink(Correspondence::$addEnclosure);
//    }
}