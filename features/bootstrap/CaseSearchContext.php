<?php


use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;

class CaseSearchContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {
    }

    /**
     * @Given /^I add patient age parameter for ages "([^"]*)" to "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddPatientAgeParameterForAgesTo($lowerAge, $upperAge)
    {
        $lowerAge = $lowerAge == "null" ? null : $lowerAge;
        $upperAge = $upperAge == "null" ? null : $upperAge;

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addAgeParam($lowerAge, $upperAge);
    }

    /**
     * @Then /^I search$/
     *
     * @var caseSearch CaseSearch
     */
    public function iSearch()
    {
        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->Search();
    }

    /**
     * @Then /^I should have results$/
     *
     * @var caseSearch CaseSearch
     *
     * @return bool wether or not results exist
     */
    public function iShouldHaveResults()
    {
        $caseSearch = $this->getPage('CaseSearch');
        return $caseSearch->resultsExist();
    }


}

