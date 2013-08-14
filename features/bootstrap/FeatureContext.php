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

class FeatureContext extends PageObjectContext implements YiiAwareContextInterface
{
    private    $yii;
    protected  $loop = 0;
    protected  $removeDiagnosis = 0;
    protected  $removeMedication = 0;
    protected  $removeAllergy = 0;

    protected $environment = array(
        'master' => 'http://admin:openeyesdevel@master.test.openeyes.org.uk',
        'develop' => 'http://admin:openeyesdevel@develop.test.openeyes.org.uk'
    );

    public function __construct(array $parameters)
    {
        $this->useContext('LoginContext', new LoginContext($parameters));
        $this->useContext('HomepageContext', new HomepageContext($parameters));
        $this->useContext('WaitingListContext', new WaitingListContext($parameters));
        $this->useContext('PatientViewContext', new PatientViewContext($parameters));
        $this->useContext('AddingNewEventContext', new AddingNewEventContext($parameters));
        $this->useContext('OperationBookingContext', new OperationBookingContext($parameters));
        $this->useContext('AnaestheticAuditContext', new AnaestheticAuditContext($parameters));
        $this->useContext('ExaminationContext', new ExaminationContext($parameters));
        $this->useContext('LaserContext', new LaserContext($parameters));
    }

    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii;
    }

    /**
     * @BeforeScenario @javascript
     */
    public function maximizeBrowserWindow()
    {
        $this->getSession()->resizeWindow(1280, 800);
    }

//    /**
//     * @BeforeStep
//     * @AfterStep
//     */
//    public function waitForActionToFinish()
//    {
//        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
//            try {
//                $this->getSession()->wait(5000, "$.active === 0");
//            } catch (\Exception $e) {}
//        }
//    }

    /**
     * @Given /^I am on the OpenEyes "([^"]*)" homepage$/
     */
    public function iAmOnTheOpeneyesHomepage($environment)
    {
        if (isset($this->environment[$environment])) {
            $this->getPage('Homepage')->open();
        } else {
            throw new \Exception("Environment $environment doesn't exist");
        }
        //Clear cookies function required here
    }

    /**
     * @Then /^I select Add First New Episode and Confirm$/
     */
    public function iSelectAddFirstNewEpisodeAndConfirm()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I Add a New Episode and Confirm$/
     */
    public function iAddANewEpisodeAndConfirm()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Site ID "([^"]*)"$/
     */
    public function iSelectSiteId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select Address Target "([^"]*)"$/
     */
    public function iSelectAddressTarget($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a Macro of "([^"]*)"$/
     */
    public function iChooseAMacroOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select Clinic Date "([^"]*)"$/
     */
    public function iSelectClinicDate($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose an Introduction of "([^"]*)"$/
     */
    public function iChooseAnIntroductionOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose a Diagnosis of "([^"]*)"$/
     */
    public function iChooseADiagnosisOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a Management of "([^"]*)"$/
     */
    public function iChooseAManagementOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Drugs "([^"]*)"$/
     */
    public function iChooseDrugs($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Outcome "([^"]*)"$/
     */
    public function iChooseOutcome($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose CC Target "([^"]*)"$/
     */
    public function iChooseCcTarget($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I add a New Enclosure$/
     */
    public function iAddANewEnclosure()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Cancel the Event$/
     */
    public function iCancelTheEvent()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to close the browser$/
     */
    public function iChooseToCloseTheBrowser()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Add a Comorbiditiy of "([^"]*)"$/
     */
    public function iAddAComorbiditiyOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Visual Acuity section$/
     */
    public function iChooseToExpandTheVisualAcuitySection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a left Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseALeftVisualAcuitySnellenMetreAndAReadingMethodOf($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a right Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseARightVisualAcuitySnellenMetreAndAReadingMethodOf($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Intraocular Pressure section$/
     */
    public function iChooseToExpandTheIntraocularPressureSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a left Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseALeftIntraocularPressureOfAndInstrument($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a right Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseARightIntraocularPressureOfAndInstrument($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Dilation section$/
     */
    public function iChooseToExpandTheDilationSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose left Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseLeftDilationOfAndDropsOf($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose right Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseRightDilationOfAndDropsOf($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Refraction section$/
     */
    public function iChooseToExpandTheRefractionSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter left Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterLeftRefractionDetailsOfSphereIntegerFraction($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter left cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterLeftCylinderDetailsOfOfCylinderIntegerFraction($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter left Axis degrees of "([^"]*)"$/
     */
    public function iEnterLeftAxisDegreesOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a left type of "([^"]*)"$/
     */
    public function iEnterALeftTypeOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter right Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightRefractionDetailsOfSphereIntegerFraction($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter right cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightCylinderDetailsOfOfCylinderIntegerFraction($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter right Axis degrees of "([^"]*)"$/
     */
    public function iEnterRightAxisDegreesOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a right type of "([^"]*)"$/
     */
    public function iEnterARightTypeOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Gonioscopy section$/
     */
    public function iChooseToExpandTheGonioscopySection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Adnexal Comorbidity section$/
     */
    public function iChooseToExpandTheAdnexalComorbiditySection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Anterior Segment section$/
     */
    public function iChooseToExpandTheAnteriorSegmentSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Pupillary Abnormalities section$/
     */
    public function iChooseToExpandThePupillaryAbnormalitiesSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Optic Disc section$/
     */
    public function iChooseToExpandTheOpticDiscSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Posterior Pole section$/
     */
    public function iChooseToExpandThePosteriorPoleSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Diagnoses section$/
     */
    public function iChooseToExpandTheDiagnosesSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Investigation section$/
     */
    public function iChooseToExpandTheInvestigationSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Clinical Management section$/
     */
    public function iChooseToExpandTheClinicalManagementSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Risks section$/
     */
    public function iChooseToExpandTheRisksSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Clinic Outcome section$/
     */
    public function iChooseToExpandTheClinicOutcomeSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose to expand the Conclusion section$/
     */
    public function iChooseToExpandTheConclusionSection()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Save the Examination$/
     */
    public function iSaveTheExamination()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Type of Topical$/
     */
    public function iChooseRightAnaestheticTypeOfTopical()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Type of LA$/
     */
    public function iChooseRightAnaestheticTypeOfLa()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Retrobulbar$/
     */
    public function iChooseRightAnaestheticDeliveryOfRetrobulbar()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Peribulbar$/
     */
    public function iChooseRightAnaestheticDeliveryOfPeribulbar()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Subtenons$/
     */
    public function iChooseRightAnaestheticDeliveryOfSubtenons()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Subconjunctival$/
     */
    public function iChooseRightAnaestheticDeliveryOfSubconjunctival()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Topical$/
     */
    public function iChooseRightAnaestheticDeliveryOfTopical()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of TopicalandIntracameral$/
     */
    public function iChooseRightAnaestheticDeliveryOfTopicalandintracameral()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Other$/
     */
    public function iChooseRightAnaestheticDeliveryOfOther()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Right Anaesthetic Agent "([^"]*)"$/
     */
    public function iChooseRightAnaestheticAgent($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Type of Topical$/
     */
    public function iChooseLeftAnaestheticTypeOfTopical()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Type of LA$/
     */
    public function iChooseLeftAnaestheticTypeOfLa()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Retrobulbar$/
     */
    public function iChooseLeftAnaestheticDeliveryOfRetrobulbar()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Peribulbar$/
     */
    public function iChooseLeftAnaestheticDeliveryOfPeribulbar()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Subtenons$/
     */
    public function iChooseLeftAnaestheticDeliveryOfSubtenons()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Subconjunctival$/
     */
    public function iChooseLeftAnaestheticDeliveryOfSubconjunctival()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Topical$/
     */
    public function iChooseLeftAnaestheticDeliveryOfTopical()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of TopicalandIntracameral$/
     */
    public function iChooseLeftAnaestheticDeliveryOfTopicalandintracameral()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Other$/
     */
    public function iChooseLeftAnaestheticDeliveryOfOther()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Left Anaesthetic Agent "([^"]*)"$/
     */
    public function iChooseLeftAnaestheticAgent($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Pre Injection Antiseptic "([^"]*)"$/
     */
    public function iChooseRightPreInjectionAntiseptic($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Pre Injection Skin Cleanser "([^"]*)"$/
     */
    public function iChooseRightPreInjectionSkinCleanser($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I tick the Right Pre Injection IOP Lowering Drops checkbox$/
     */
    public function iTickTheRightPreInjectionIopLoweringDropsCheckbox()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Drug "([^"]*)"$/
     */
    public function iChooseRightDrug($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter "([^"]*)" number of Right injections$/
     */
    public function iEnterNumberOfRightInjections($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter Right batch number "([^"]*)"$/
     */
    public function iEnterRightBatchNumber($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a Right batch expiry date of "([^"]*)"$/
     */
    public function iEnterARightBatchExpiryDateOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Injection Given By "([^"]*)"$/
     */
    public function iChooseRightInjectionGivenBy($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a Right Injection time of "([^"]*)"$/
     */
    public function iEnterARightInjectionTimeOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Pre Injection Antiseptic "([^"]*)"$/
     */
    public function iChooseLeftPreInjectionAntiseptic($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Pre Injection Skin Cleanser "([^"]*)"$/
     */
    public function iChooseLeftPreInjectionSkinCleanser($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I tick the Left Pre Injection IOP Lowering Drops checkbox$/
     */
    public function iTickTheLeftPreInjectionIopLoweringDropsCheckbox()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Drug "([^"]*)"$/
     */
    public function iChooseLeftDrug($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter "([^"]*)" number of Left injections$/
     */
    public function iEnterNumberOfLeftInjections($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter Left batch number "([^"]*)"$/
     */
    public function iEnterLeftBatchNumber($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a Left batch expiry date of "([^"]*)"$/
     */
    public function iEnterALeftBatchExpiryDateOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Injection Given By "([^"]*)"$/
     */
    public function iChooseLeftInjectionGivenBy($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a Left Injection time of "([^"]*)"$/
     */
    public function iEnterALeftInjectionTimeOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose A Right Lens Status of "([^"]*)"$/
     */
    public function iChooseARightLensStatusOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Right Counting Fingers Checked Yes$/
     */
    public function iChooseRightCountingFingersCheckedYes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Right Counting Fingers Checked No$/
     */
    public function iChooseRightCountingFingersCheckedNo()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Right IOP Needs to be Checked Yes$/
     */
    public function iChooseRightIopNeedsToBeCheckedYes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Right IOP Needs to be Checked No$/
     */
    public function iChooseRightIopNeedsToBeCheckedNo()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Right Post Injection Drops$/
     */
    public function iChooseRightPostInjectionDrops()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose A Left Lens Status of "([^"]*)"$/
     */
    public function iChooseALeftLensStatusOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Left Counting Fingers Checked Yes$/
     */
    public function iChooseLeftCountingFingersCheckedYes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Left Counting Fingers Checked No$/
     */
    public function iChooseLeftCountingFingersCheckedNo()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Left IOP Needs to be Checked Yes$/
     */
    public function iChooseLeftIopNeedsToBeCheckedYes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose Left IOP Needs to be Checked No$/
     */
    public function iChooseLeftIopNeedsToBeCheckedNo()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose Left Post Injection Drops$/
     */
    public function iChooseLeftPostInjectionDrops()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select Right Complications "([^"]*)"$/
     */
    public function iSelectRightComplications($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select Left Complications "([^"]*)"$/
     */
    public function iSelectLeftComplications($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureInstrumentOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose right eye Dilation of "([^"]*)"$/
     */
    public function iChooseRightEyeDilationOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureReadingOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I add right eye comments of "([^"]*)"$/
     */
    public function iAddRightEyeCommentsOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureInstrumentOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I choose left eye Dilation of "([^"]*)"$/
     */
    public function iChooseLeftEyeDilationOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureReadingOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I add left eye comments of "([^"]*)"$/
     */
    public function iAddLeftEyeCommentsOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I Save the Phasing Event$/
     */
    public function iSaveThePhasingEvent()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select a Common Drug "([^"]*)"$/
     */
    public function iSelectACommonDrug($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Standard Set of "([^"]*)"$/
     */
    public function iSelectAStandardSetOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter a Dose of "([^"]*)" drops$/
     */
    public function iEnterADoseOfDrops($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a route of "([^"]*)"$/
     */
    public function iEnterARouteOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter a eyes option "([^"]*)"$/
     */
    public function iEnterAEyesOption($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter a frequency of "([^"]*)"$/
     */
    public function iEnterAFrequencyOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I enter a duration of "([^"]*)"$/
     */
    public function iEnterADurationOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I add Prescription comments of "([^"]*)"$/
     */
    public function iAddPrescriptionCommentsOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I add Right Side$/
     */
    public function iAddRightSide()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Right Side Diagnosis of "([^"]*)"$/
     */
    public function iSelectARightSideDiagnosisOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Left Side Diagnosis of "([^"]*)"$/
     */
    public function iSelectALeftSideDiagnosisOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select a Right Secondary To of "([^"]*)"$/
     */
    public function iSelectARightSecondaryToOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select a Left Secondary To of "([^"]*)"$/
     */
    public function iSelectALeftSecondaryToOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Cerebrovascular accident Yes$/
     */
    public function iSelectCerebrovascularAccidentYes()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Cerebrovascular accident No$/
     */
    public function iSelectCerebrovascularAccidentNo()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Ischaemic attack Yes$/
     */
    public function iSelectIschaemicAttackYes()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Ischaemic attack No$/
     */
    public function iSelectIschaemicAttackNo()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Myocardial infarction Yes$/
     */
    public function iSelectMyocardialInfarctionYes()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select Myocardial infarction No$/
     */
    public function iSelectMyocardialInfarctionNo()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a Consultant of "([^"]*)"$/
     */
    public function iSelectAConsultantOf($arg1)
    {
        throw new PendingException();
    }
}
