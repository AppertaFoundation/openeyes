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
     * @Given /^I add diagnosis parameter for diagnosed with "([^"]*)" by "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddDiagnosisParameterForDiagnosedWith($diagnosis, $firm)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addDiagnosis(true,$diagnosis,$firm,false);
    }
    /**
     * @Given /^I add diagnosis parameter for diagnosed not with "([^"]*)" by "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddDiagnosisParameterForDiagnosedNotWith($diagnosis, $firm)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addDiagnosis(false,$diagnosis,$firm,false);
    }
    /**
     * @Given /^I add medication parameter for has taken "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddMedicationParameterForHasTaken($medication)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addMedication(true,$medication);
    }
    /**
     * @Given /^I add medication parameter for has not taken "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddMedicationParameterForHasNotTaken($medication)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addMedication(false,$medication);
    }
    /**
     * @Given /^I add allergy parameter for is allergic to "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddAllergyParameterForIsAllergicTo($allergy)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addAllergy(true,$allergy);
    }
    /**
     * @Given /^I add allergy parameter for is not allergic to "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddAllergyParameterForIsNotAllergicTo($allergy)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addAllergy(false,$allergy);
    }
    /**
     * @Given /^I add family history parameter for side "([^"]*)" relative "([^"]*)" operation "([^"]*)" condition "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddFamilyHistoryParameter($side,$relative,$operation,$condition)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addFamilyHistory($side,$relative,$operation,$condition);
    }
    /**
     * @Given /^I add patient name parameter for name "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddPatientNameParameterForName($name)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addPatientName($name);
    }
    /**
     * @Given /^I add patient number parameter for number "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddPatientNumberParameterForNumber($number)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addPatientNUmber($number);
    }
    /**
     * @Given /^I add previous procedure parameter for has had a  "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddPreviousProcedureParameterForHasHadA($procedure)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addPreviousProcedure(true,$procedure);
    }
    /**
     * @Given /^I add previous procedure parameter for has not had a  "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     */
    public function iAddPreviousProcedureParameterForHasNotHadA($procedure)
    {

        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->addPreviousProcedure(false,$procedure);
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

    /**
     * @Then /^I should have specific result with NHS "([^"]*)"$/
     *
     * @var caseSearch CaseSearch
     *
     * @return bool wether or not results exist
     */
    public function iShouldHaveSpecificResultWithNHS($nhs)
    {
        $caseSearch = $this->getPage('CaseSearch');
        $caseSearch->specificResultExist($nhs);
    }
}

