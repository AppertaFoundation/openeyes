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
     * @Then /^I remove the Diagnosis right eye$/
     */
    public function iRemoveRightEye()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->removeRightEye();
    }

    /**
     * @Then /^I add the Diagnosis right eye$/
     */
    public function iAddRightEye()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->addRightEye();
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
     * @Then /^I select Right Cerebrovascular accident Yes$/
     */
    public function iSelectCerebrovascularAccidentYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightCerebYes();
    }

    /**
     * @Then /^I select Right Cerebrovascular accident No$/
     */
    public function iSelectCerebrovascularAccidentNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightCerebNo();
    }

    /**
     * @Then /^I select Right Ischaemic attack Yes$/
     */
    public function iSelectIschaemicAttackYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightIschaemicYes();
    }

    /**
     * @Then /^I select Right Ischaemic attack No$/
     */
    public function iSelectIschaemicAttackNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightIschaemicNo();
    }

    /**
     * @Then /^I select Right Myocardial infarction Yes$/
     */
    public function iSelectMyocardialInfarctionYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightMyocardialYes();
    }

    /**
     * @Then /^I select Right Myocardial infarction No$/
     */
    public function iSelectMyocardialInfarctionNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightMyocardialNo();
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
     * @Given /^I select a Right Consultant of "([^"]*)"$/
     */
    public function iSelectAConsultantOf($consultant)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightConsultantSelect($consultant);
    }

    /**
     * @Then /^I select a Right Standard Intervention Exists of Yes$/
     */
    public function iSelectAStandardInterventionExistsOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardExistsYes();
    }

    /**
     * @Then /^I select a Right Standard Intervention Exists of No$/
     */
    public function iSelectAStandardInterventionExistsOfNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardExistsNo();
    }

    /**
     * @Given /^I choose a Right Standard Intervention of "([^"]*)"$/
     */
    public function iChooseAStandardInterventionOf($standard)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardIntervention($standard);
    }

    /**
     * @Given /^I select a Right Standard Intervention Previous of Yes$/
     */
    public function iSelectAStandardInterventionPreviousOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardPreviousYes();
    }

    /**
     * @Given /^I select a Right Standard Intervention Previous of No$/
     */
    public function iSelectAStandardInterventionPreviousOfNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardPreviousNo();
    }

    /**
     * @Then /^I select Right In addition to the standard \(Additional\)$/
     */
    public function iSelectInAdditionToTheStandardAdditional()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardAdditional();
    }

    /**
     * @Then /^I select Right Instead of the standard \(Deviation\)$/
     */
    public function iSelectInsteadOfTheStandardDeviation()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightStandardDeviation();

    }

    /**
     * @Given /^I add Right details of additional of "([^"]*)"$/
     */
    public function iAddDetailsOfAdditionalOf($details)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightAdditionalOrDeviationComments($details);
    }

    /**
     * @Given /^I add Right details of deviation of "([^"]*)"$/
     */
    public function iAddDetailsOfDeviationOf($details)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightAdditionalOrDeviationComments($details);
    }

    /**
     * @Then /^I choose a Right reason for not using standard intervention of "([^"]*)"$/
     */
    public function iChooseAReasonForNotUsingStandardInterventionOf($option)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightNotUsingStandardIntervention($option);
    }

    /**
     * @Then /^I add Right How is the patient different to others of "([^"]*)"$/
     */
    public function iAddHowIsThePatientDifferentToOthersOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientSignificantDifferent($comments);
    }

    /**
     * @Given /^I add Right How is the patient likely to gain benefit "([^"]*)"$/
     */
    public function iAddHowIsThePatientLikelyToGainBenefit($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientMoreBenefit($comments);
    }

    /**
     * @Then /^I select Right Patient Factors Yes$/
     */
    public function iSelectPatientFactorsYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientFactorsYes();
    }

    /**
     * @Then /^I select Right Patient Factors No$/
     */
    public function iSelectPatientFactorsNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientFactorsNo();
    }

    /**
     * @Then /^I add Right Patient Factor Details of "([^"]*)"$/
     */
    public function iAddPatientFactorDetailsOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientFactorDetails($comments);
    }

    /**
     * @Given /^I add Right Patient Expectations of "([^"]*)"$/
     */
    public function iAddPatientExpectationsOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightPatientExpectations($comments);
    }

    /**
     * @Then /^I add Right Anticipated Start Date of "([^"]*)"$/
     */
    public function iAddAnticipatedStartDateOf($date)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->RightAnticipatedStartDate($date);
    }

    /**
     * @Then /^I select a Left Standard Intervention Exists of Yes$/
     */
    public function iSelectALeftStandardInterventionExistsOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftStandardExistsYes();
    }

    /**
     * @Given /^I choose a Left Standard Intervention of "([^"]*)"$/
     */
    public function iChooseALeftStandardInterventionOf($standard)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftStandardIntervention($standard);
    }

    /**
     * @Given /^I select a Left Standard Intervention Previous of Yes$/
     */
    public function iSelectALeftStandardInterventionPreviousOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftStandardPreviousYes();
    }

    /**
     * @Then /^I select Left Instead of the standard \(Deviation\)$/
     */
    public function iSelectLeftInsteadOfTheStandardDeviation()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftStandardDeviation();
    }

    /**
     * @Given /^I add Left details of deviation of "([^"]*)"$/
     */
    public function iAddLeftDetailsOfDeviationOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftAdditionalOrDeviationComments($comments);
    }

    /**
     * @Then /^I choose a Left reason for not using standard intervention of "([^"]*)"$/
     */
    public function iChooseALeftReasonForNotUsingStandardInterventionOf($option)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftNotUsingStandardIntervention($option);
    }

    /**
     * @Then /^I add Left How is the patient different to others of "([^"]*)"$/
     */
    public function iAddLeftHowIsThePatientDifferentToOthersOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftPatientSignificantDifferent($comments);
    }

    /**
     * @Given /^I add Left How is the patient likely to gain benefit "([^"]*)"$/
     */
    public function iAddLeftHowIsThePatientLikelyToGainBenefit($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftPatientMoreBenefit($comments);
    }

    /**
     * @Then /^I select Left Patient Factors Yes$/
     */
    public function iSelectLeftPatientFactorsYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftPatientFactorsYes();
    }

    /**
     * @Then /^I add Left Patient Factor Details of "([^"]*)"$/
     */
    public function iAddLeftPatientFactorDetailsOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftPatientFactorDetails($comments);
    }

    /**
     * @Given /^I add Left Patient Expectations of "([^"]*)"$/
     */
    public function iAddLeftPatientExpectationsOf($comments)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftPatientExpectations($comments);
    }

    /**
     * @Then /^I add Left Anticipated Start Date of "([^"]*)"$/
     */
    public function iAddLeftAnticipatedStartDateOf($date)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->LeftAnticipatedStartDate($date);
    }

    /**
     * @Then /^I select Patient Venous Occlusion of "([^"]*)"$/
     */
    public function iSelectPatientVenousOcclusionOfYes($option)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->patientVenousYes($option);
    }

    /**
     * @Given /^I select CRVO of "([^"]*)"$/
     */
    public function iSelectCrvoOfYes($option)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->CRVOyes($option);
    }

    /**
     * @Then /^I Save the Therapy Application and confirm it has been created successfully$/
     */
    public function iSaveTheTherapyApplication()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->saveTherapyAndConfirm();
    }

    /**
     * @Given /^I select a Right Patient has CNV of No$/
     */
    public function iSelectARightPatientHasCnvOfNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightPatientCnvNO();
    }

    /**
     * @Then /^I select a Right Patient has Macular Oedema of Yes$/
     */
    public function iSelectARightPatientHasMacularOedemaOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightMacularOdemaYes();
    }

    /**
     * @Given /^I select a Right Patient has Diabetic Macular Oedema of Yes$/
     */
    public function iSelectARightPatientHasDiabeticMacularOedemaOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightDiabeticMacularOdemaYes();
    }

    /**
     * @Then /^I select a Right CRT>=(\d+) of Yes$/
     */
    public function iSelectARightCrtOfYes($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->rightCRT400Yes();
    }

    /**
     * @Given /^I select a Left Patient has CNV of No$/
     */
    public function iSelectALeftPatientHasCnvOfNo()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftPatientCnvNO();
    }

    /**
     * @Then /^I select a Left Patient has Macular Oedema of Yes$/
     */
    public function iSelectALeftPatientHasMacularOedemaOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftMacularOdemaYes();
    }

    /**
     * @Given /^I select a Left Patient has Diabetic Macular Oedema of Yes$/
     */
    public function iSelectALeftPatientHasDiabeticMacularOedemaOfYes()
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftDiabeticMacularOdemaYes();
    }

    /**
     * @Then /^I select a Left CRT>=(\d+) of Yes$/
     */
    public function iSelectALeftCrtOfYes($arg1)
    {
        /**
         * @var TherapyApplication $TherapyApplication
         */
        $TherapyApplication = $this->getPage("TherapyApplication");
        $TherapyApplication->leftCRT400Yes();
    }



}