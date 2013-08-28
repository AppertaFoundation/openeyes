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

class PatientViewContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I select Add First New Episode and Confirm$/
     */
    public function iSelectAddFirstNewEpisodeAndConfirm()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addEpisodeAndEvent();
        $patientView->addEpisode();
    }

    /**
     * @Then /^I select Create or View Episodes and Events$/
     */
    public function CreateOrViewEpisodesAndEvents()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addEpisodeAndEvent();
    }

    /**
     * @Then /^I select the Latest Event$/
     */
    public function iSelectTheLatestEvent()
    {
        /**
         * @var PatientView $patientview
         */
        $patientview= $this->getPage('PatientView');
        $patientview->addEpisodeAndEvent();
    }

    /**
     * @Then /^I Add an Ophthalmic Diagnosis selection of "([^"]*)"$/
     */
    public function addOpthalmicDiagnosis ($diagnosis)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addOpthalmicDiagnosis($diagnosis);
    }

    /**
     * @Given /^I select that it affects eye "([^"]*)"$/
     */
    public function SelectThatItAffectsEye($eye)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->selectEye($eye);
    }

    /**
     * @Given /^I select a Opthalmic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function OpthalmicDiagnosis($day, $month, $year)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addOpthalmicDate($day, $month, $year);
    }

    /**
     * @Then /^I save the new Opthalmic Diagnosis$/
     */
    public function SaveTheNewOpthalmicDiagnosis()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->saveOpthalmicDiagnosis();
    }

    /**
     * @Then /^I Add an Systemic Diagnosis selection of "([^"]*)"$/
     */
    public function SystemicDiagnosisSelection($diagnosis)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addSystemicDiagnosis($diagnosis);
    }

    /**
     * @Given /^I select that it affects Systemic side "([^"]*)"$/
     */
    public function systemicSide($side)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->selectSystemicSide($side);
    }

    /**
     * @Given /^I select a Systemic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function SystemicDiagnosisDate($day, $month, $year)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addSystemicDate($day, $month, $year);
    }

    /**
     * @Then /^I save the new Systemic Diagnosis$/
     */
    public function SaveTheNewSystemicDiagnosis()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->saveSystemicDiagnosis();
    }

    /**
     * @Then /^I Add a Previous Operation of "([^"]*)"$/
     */
    public function iAddAPreviousOperationOf($operation)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->previousOperation($operation);
    }

    /**
     * @Given /^I select that it affects Operation side "([^"]*)"$/
     */
    public function SelectThatItAffectsOperationSide($operation)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->operationSide($operation);
    }

    /**
     * @Given /^I select a Previous Operation date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function PreviousOperationDate($day, $month, $year)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addOpthalmicDate($day, $month, $year);
    }

    /**
     * @Then /^I save the new Previous Operation$/
     */
    public function iSaveTheNewPreviousOperation()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->savePreviousOperation();
    }

    /**
     * @Given /^I Add Medication details medication "([^"]*)" route "([^"]*)" frequency "([^"]*)" date from "([^"]*)" and Save$/
     */
    public function iAddMedicationDetails($medication, $route, $frequency, $dateFrom)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->medicationDetails($medication, $route, $frequency, $dateFrom);
    }

    /**
     * @Then /^I edit the CVI Status "([^"]*)"$/
     */
    public function iEditTheCviStatus($status)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->editCVIstatus($status);
    }

    /**
     * @Given /^I select a CVI Status date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iSelectACviStatusDateOfDayMonthYear($day, $month, $year)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addCVIDate($day, $month, $year);
    }

    /**
     * @Then /^I save the new CVI status$/
     */
    public function iSaveTheNewCviStatus()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->saveCVIstatus();
    }
    /**
     * @Then /^I Remove existing Allergy$/
     */
    public function removeAllergy()
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->removeAllergy();
    }

    /**
     * @Then /^I Add Allergy "([^"]*)" and Save$/
     */
    public function iAddAllergy($allergy)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addAllergy($allergy);

    }

    /**
     * @Given /^I Add a Family History of relative "([^"]*)" side "([^"]*)" condition "([^"]*)" and comments "([^"]*)" and Save$/
     */
    public function FamilyHistory($relative, $side, $condition, $comments)
    {
        /**
         * @var PatientView $patientView
         */
        $patientView = $this->getPage('PatientView');
        $patientView->addFamilyHistory($relative, $side, $condition, $comments);
    }
}