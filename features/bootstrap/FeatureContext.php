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

    protected $environment = array(
        'master' => 'http://admin:openeyesdevel@master.test.openeyes.org.uk',
        'develop' => 'http://admin:openeyesdevel@develop.test.openeyes.org.uk'
    );

    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii;
    }

    public function __construct(array $parameters)
    {
        $this->useContext('LoginContext', new LoginContext($parameters));
        $this->useContext('HomepageContext', new HomepageContext($parameters));
        $this->useContext('WaitingListContext', new WaitingListContext($parameters));
        $this->useContext('AddingNewEventContext', new AddingNewEventContext($parameters));
        $this->useContext('PatientViewContext', new PatientViewContext($parameters));
        $this->useContext('OperationBookingContext', new OperationBookingContext($parameters));
        $this->useContext('AnaestheticAuditContext', new AnaestheticAuditContext($parameters));
        $this->useContext('ExaminationContext', new ExaminationContext($parameters));
        $this->useContext('LaserContext', new LaserContext($parameters));
        $this->useContext('PrescriptionContext', new PrescriptionContext($parameters));

    }

    /**
     * @Given /^I am on the OpenEyes "([^"]*)" homepage$/
     */
    public function iAmOnTheOpeneyesHomepage($environment)
    {
        /**
         * @var Login $loginPage
         */
        if (isset($this->environment[$environment])) {
            $this->getPage('HomePage')->open();
            ;

        } else {
            throw new \Exception("Environment $environment doesn't exist");
        }
    }

    /**
     * @And /^I Select Add a New Episode and Confirm$/
     */
    public function addNewEpisode ()
    {
        /**
         * @var AddingNewEvent $addNewEvent
         */
        $addNewEvent = $this->getPage('AddingNewEvent');
        $addNewEvent->addNewEpisode();
    }


//
//    /**
//     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
//     */
//    public function RightEyeIntraocular($righteye)
//    {
//       $this->selectOption(Phasing::$phasingInstrumentRight, $righteye);
//    }
//
//    /**
//     * @Given /^I choose right eye Dilation of "([^"]*)"$/
//     */
//    public function iChooseRightEyeDilationOf($dilation)
//    {
//        $this->clickLink(Phasing::$phasingDilationRight);
//    }
//
//    /**
//     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
//     */
//    public function iChooseARightEyeIntraocularPressureReadingOf($righteye)
//    {
//        $this->fillField(Phasing::$phasingPressureLeft, $righteye);
//    }
//
//    /**
//     * @Given /^I add right eye comments of "([^"]*)"$/
//     */
//    public function iAddRightEyeCommentsOf($comments)
//    {
//        $this->fillField(Phasing::$phasingCommentsRight, $comments);
//    }
//
//    /**
//     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
//     */
//    public function iChooseALeftEyeIntraocularPressureInstrumentOf($lefteye)
//    {
//        $this->selectOption(Phasing::$phasingInstrumentLeft,$lefteye);
//    }
//
//    /**
//     * @Given /^I choose left eye Dilation of "([^"]*)"$/
//     */
//    public function iChooseLeftEyeDilationOf($dilation)
//    {
//        $this->clickLink(Phasing::$phasingDilationLeft);
//    }
//
//    /**
//     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
//     */
//    public function iChooseALeftEyeIntraocularPressureReadingOf($lefteye)
//    {
//       $this->fillField(Phasing::$phasingPressureRight, $lefteye);
//    }
//
//    /**
//     * @Given /^I add left eye comments of "([^"]*)"$/
//     */
//    public function iAddLeftEyeCommentsOf($comments)
//    {
//        $this->fillField(Phasing::$phasingCommentsLeft, $comments);
//    }
//
//    /**
//     * @Then /^I Save the Phasing Event$/
//     */
//    public function iSaveThePhasingEvent()
//    {
//        $this->clickLink(Examination::$saveExamination);
//    }
//
//    /**
//     * @Then /^I choose to expand the Intraocular Pressure section$/
//     */
//    public function iChooseToExpandTheIntraocularPressureSection()
//    {
//        $this->clickLink(Examination::$openIntraocularPressure);
//    }
//
//    /**
//     * @Then /^I choose a left Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
//     */
//    public function iChooseALeftIntraocularPressureOfAndInstrument($pressure, $instrument)
//    {
//        $this->selectOption(Examination::$intraocularRight, $pressure);
//        $this->selectOption(Examination::$instrumentRight, $instrument);
//    }
//
//    /**
//     * @Then /^I choose a right Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
//     */
//    public function iChooseARightIntraocularPressureOfAndInstrument($pressure, $instrument)
//    {
//        $this->selectOption(Examination::$intraocularLeft, $pressure);
//        $this->selectOption(Examination::$instrumentLeft, $instrument);
//    }
//
//    /**
//     * @Then /^I choose to expand the Dilation section$/
//     */
//    public function iChooseToExpandTheDilationSection()
//    {
//        $this->clickLink(Examination::$openDilation);
//    }
//
//    /**
//     * @Then /^I choose left Dilation of "([^"]*)" and drops of "([^"]*)"$/
//     */
//    public function iChooseLeftDilationOfAndDropsOf($dilation, $drops)
//    {
//        $this->selectOption(Examination::$dilationLeft, $dilation);
//        $this->selectOption(Examination::$dropsLeft, $drops);
//    }
//
//    /**
//     * @Then /^I choose right Dilation of "([^"]*)" and drops of "([^"]*)"$/
//     */
//    public function iChooseRightDilationOfAndDropsOf($dilation, $drops)
//    {
//        $this->selectOption(Examination::$dilationRight, $dilation);
//        $this->selectOption(Examination::$dropsRight, $drops);
//    }
//
//    /**
//     * @Then /^I choose to expand the Refraction section$/
//     */
//    public function iChooseToExpandTheRefractionSection()
//    {
//        $this->clickLink(Examination::$expandRefraction);
//    }
//
//    /**
//     * @Then /^I enter left Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
//     */
//    public function LeftRefractionDetails($sphere, $integer, $fraction)
//    {
//        $this->selectOption(Examination::$sphereRight, $sphere);
//        $this->selectOption(Examination::$sphereRightInt, $integer);
//        $this->selectOption(Examination::$sphereRightFraction, $fraction);
//    }
//
//    /**
//     * @Given /^I enter left cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
//     */
//    public function iEnterLeftCylinderDetails($cylinder, $integer, $fraction)
//    {
//        $this->selectOption(Examination::$cylinderLeft, $cylinder);
//        $this->selectOption(Examination::$cylinderLeftInt, $integer);
//        $this->selectOption(Examination::$cylinderLeftFraction, $fraction);
//    }
//
//    /**
//     * @Then /^I enter left Axis degrees of "([^"]*)"$/
//     */
//    public function iEnterLeftAxisDegreesOf($axis)
//    {
//        //We need a Clear Field function here
//        $this->fillField(Examination::$sphereLeftAxis, $axis);
//        //We need to Press the tab key here
//    }
//
//    /**
//     * @Given /^I enter a left type of "([^"]*)"$/
//     */
//    public function iEnterALeftTypeOf($type)
//    {
//        $this->selectOption(Examination::$sphereLeftType, $type);
//    }
//
//    /**
//     * @Then /^I enter right Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
//     */
//    public function iEnterRightRefractionDetailsOfSphereIntegerFraction($sphere, $integer, $fraction)
//    {
//        $this->selectOption(Examination::$sphereRight, $sphere);
//        $this->selectOption(Examination::$sphereRightInt, $integer);
//        $this->selectOption(Examination::$sphereRightFraction, $fraction);
//    }
//
//    /**
//     * @Given /^I enter right cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
//     */
//    public function iEnterRightCylinderDetailsOfOfCylinderIntegerFraction($cylinder, $integer, $fraction)
//    {
//        $this->selectOption(Examination::$cylinderRight, $cylinder);
//        $this->selectOption(Examination::$cylinderRightInt, $integer);
//        $this->selectOption(Examination::$cylinderRightFraction, $fraction);
//    }
//
//    /**
//     * @Then /^I enter right Axis degrees of "([^"]*)"$/
//     */
//    public function iEnterRightAxisDegreesOf($axis)
//    {
//        //We need a Clear Field function here
//        $this->fillField(Examination::$sphereRightAxis, $axis);
//        //We need to Press the tab key here
//    }
//
//    /**
//     * @Given /^I enter a right type of "([^"]*)"$/
//     */
//    public function iEnterARightTypeOf($type)
//    {
//        $this->selectOption(Examination::$sphereRightType, $type);
//    }
//
//    /**
//     * @Then /^I choose to expand the Gonioscopy section$/
//     */
//    public function iChooseToExpandTheGonioscopySection()
//    {
//        $this->clickLink(Examination::$expandGonioscopy);
//    }
//
//    /**
//     * @Then /^I choose to expand the Adnexal Comorbidity section$/
//     */
//    public function iChooseToExpandTheAdnexalComorbiditySection()
//    {
//        $this->clickLink(Examination::$expandaAdnexalComorbidity);
//    }
//
//    /**
//     * @Then /^I choose to expand the Anterior Segment section$/
//     */
//    public function iChooseToExpandTheAnteriorSegmentSection()
//    {
//        $this->clickLink(Examination::$expandAnteriorSegment);
//    }
//
//    /**
//     * @Then /^I choose to expand the Pupillary Abnormalities section$/
//     */
//    public function iChooseToExpandThePupillaryAbnormalitiesSection()
//    {
//        $this->clickLink(Examination::$expandPupillaryAbnormalities);
//    }
//
//    /**
//     * @Then /^I choose to expand the Optic Disc section$/
//     */
//    public function iChooseToExpandTheOpticDiscSection()
//    {
//        $this->clickLink(Examination::$expandOpticDisc);
//    }
//
//    /**
//     * @Then /^I choose to expand the Posterior Pole section$/
//     */
//    public function iChooseToExpandThePosteriorPoleSection()
//    {
//        $this->clickLink(Examination::$expandPosteriorPole);
//    }
//
//    /**
//     * @Then /^I choose to expand the Diagnoses section$/
//     */
//    public function iChooseToExpandTheDiagnosesSection()
//    {
//        $this->clickLink(Examination::$expandDiagnoses);
//    }
//
//    /**
//     * @Then /^I choose to expand the Investigation section$/
//     */
//    public function iChooseToExpandTheInvestigationSection()
//    {
//        $this->clickLink(Examination::$expandInvestigation);
//    }
//
//    /**
//     * @Then /^I choose to expand the Clinical Management section$/
//     */
//    public function iChooseToExpandTheClinicalManagementSection()
//    {
//        $this->clickLink(Examination::$expandClinicalManagement);
//    }
//
//    /**
//     * @Then /^I choose to expand the Risks section$/
//     */
//    public function iChooseToExpandTheRisksSection()
//    {
//        $this->clickLink(Examination::$expandRisks);
//    }
//
//    /**
//     * @Then /^I choose to expand the Clinic Outcome section$/
//     */
//    public function iChooseToExpandTheClinicOutcomeSection()
//    {
//        $this->clickLink(Examination::$expandClinicOutcome);
//    }
//
//    /**
//     * @Then /^I choose to expand the Conclusion section$/
//     */
//    public function iChooseToExpandTheConclusionSection()
//    {
//        $this->clickLink(Examination::$expandConclusion);
//    }
//
//    /**
//     * @Then /^I Save the Examination$/
//     */
//    public function iSaveTheExamination()
//    {
//        $this->clickLink(Examination::$saveExamination);
//    }
//
//    /**
//     * @Then /^I Cancel the Examination$/
//     */
//    public function iCancelTheExamination()
//    {
//        $this->clickLink(AnaestheticAudit::$cancelExam);
//    }
//
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
//     * @Given /^I select Clinic Date "([^"]*)"$/
//     */
//    public function iSelectClinicDate($dateFrom)
//    {
//        $this->clickLink(Correspondence::$letterDate);
//        $this->clickLink(PatientViewPage::passDateFromTable($dateFrom));
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
//
//    /**
//     * @Then /^I choose Right Anaesthetic Type of Topical$/
//     */
//    public function RightAnaestheticTopical()
//    {
//        $this->clickLink(Intravitreal::$rightAnaestheticTopical);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Type of LA$/
//     */
//    public function RightAnaestheticLa()
//    {
//        $this->clickLink(Intravitreal::$rightAnaestheticLA);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Retrobulbar$/
//     */
//    public function RightAnaestheticRetrobulbar()
//    {
//        $this->clickLink(Intravitreal::$rightDeliveryRetrobulbar);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Peribulbar$/
//     */
//    public function RightAnaestheticPeribulbar()
//    {
//        $this->clickLink(Intravitreal::$rightDeliveryPeribulbar);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Subtenons$/
//     */
//    public function RightAnaestheticSubtenons()
//    {
//        $this->clickLink(Intravitreal::$rightDeliverySubtenons);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Subconjunctival$/
//     */
//    public function RightAnaestheticSubconjunctival()
//    {
//        $this->clickLink(Intravitreal::$rightDeliverySubconjunctival);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Topical$/
//     */
//    public function RightAnaestheticDeliveryTopical()
//    {
//        $this->clickLink(Intravitreal::$rightDeliveryTopical);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of TopicalandIntracameral$/
//     */
//    public function RightAnaestheticDeliveryTopicalandIntracameral()
//    {
//        $this->clickLink(Intravitreal::$rightDeliveryTopicalIntracameral);
//    }
//
//    /**
//     * @Then /^I choose Right Anaesthetic Delivery of Other$/
//     */
//    public function RightAnaestheticDeliveryOfOther()
//    {
//        $this->clickLink(Intravitreal::$rightDeliveryOther);
//    }
//
//    /**
//     * @Given /^I choose Right Anaesthetic Agent "([^"]*)"$/
//     */
//    public function RightAnaestheticAgent($agent)
//    {
//       $this->selectOption(Intravitreal::$rightAnaestheticAgent, $agent);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Type of Topical$/
//     */
//    public function LeftAnaestheticTypeOfTopical()
//    {
//       $this->clickLink(Intravitreal::$leftAnaestheticTopical);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Type of LA$/
//     */
//    public function LeftAnaestheticTypeOfLa()
//    {
//       $this->clickLink(Intravitreal::$leftAnaestheticLA);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Retrobulbar$/
//     */
//    public function LeftAnaestheticDeliveryOfRetrobulbar()
//    {
//       $this->clickLink(Intravitreal::$leftDeliveryRetrobulbar);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Peribulbar$/
//     */
//    public function LeftAnaestheticDeliveryOfPeribulbar()
//    {
//      $this->clickLink(Intravitreal::$leftDeliveryPeribulbar);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Subtenons$/
//     */
//    public function LeftAnaestheticDeliveryOfSubtenons()
//    {
//      $this->clickLink(Intravitreal::$leftDeliverySubtenons);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Subconjunctival$/
//     */
//    public function LeftAnaestheticDeliveryOfSubconjunctival()
//    {
//      $this->clickLink(Intravitreal::$leftDeliverySubconjunctival);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Topical$/
//     */
//    public function LeftAnaestheticDeliveryOfTopical()
//    {
//      $this->clickLink(Intravitreal::$leftDeliveryTopical);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of TopicalandIntracameral$/
//     */
//    public function LeftAnaestheticDeliveryOfTopicalandintracameral()
//    {
//      $this->clickLink(Intravitreal::$leftDeliveryTopicalIntracameral);
//    }
//
//    /**
//     * @Then /^I choose Left Anaesthetic Delivery of Other$/
//     */
//    public function LeftAnaestheticDeliveryOfOther()
//    {
//      $this->clickLink(Intravitreal::$leftDeliveryOther);
//    }
//
//    /**
//     * @Given /^I choose Left Anaesthetic Agent "([^"]*)"$/
//     */
//    public function LeftAnaestheticAgent($agent)
//    {
//      $this->selectOption(Intravitreal::$leftAnaestheticAgent, $agent);
//    }
//
//    /**
//     * @Then /^I choose Right Pre Injection Antiseptic "([^"]*)"$/
//     */
//    public function RightPreInjectionAntiseptic($antiseptic)
//    {
//      $this->selectOption(Intravitreal::$rightPreInjectionAntiseptic, $antiseptic);
//    }
//
//    /**
//     * @Then /^I choose Right Pre Injection Skin Cleanser "([^"]*)"$/
//     */
//    public function RightPreInjectionSkinCleanser($skin)
//    {
//      $this->selectOption(Intravitreal::$rightPreInjectionSkinCleanser, skin);
//    }
//
//    /**
//     * @Given /^I tick the Right Pre Injection IOP Lowering Drops checkbox$/
//     */
//    public function TickRightPreInjectionIopLoweringDropsCheckbox()
//    {
//      $this->checkOption(Intravitreal::$rightPerInjectionIOPDrops);
//    }
//
//    /**
//     * @Then /^I choose Right Drug "([^"]*)"$/
//     */
//    public function iChooseRightDrug($drug)
//    {
//      $this->selectOption(Intravitreal::$rightDrug, $drug);
//    }
//
//    /**
//     * @Given /^I enter "([^"]*)" number of Right injections$/
//     */
//    public function NumberOfRightInjections($injections)
//    {
//      $this->fillField(Intravitreal::$rightNumberOfInjections, $injections);
//    }
//
//    /**
//     * @Then /^I enter Right batch number "([^"]*)"$/
//     */
//    public function RightBatchNumber($batch)
//    {
//      $this->fillField(Intravitreal::$rightBatchNumber, $batch);
//    }
//
//    /**
//     * @Given /^I enter a Right batch expiry date of "([^"]*)"$/
//     */
//    public function RightBatchExpiryDateOf($dateFrom)
//    {
//       $this->clickLink(Intravitreal::$rightBatchExpiryDate);
//       $this->clickLink(PatientViewPage::passDateFromTable($dateFrom));
//    }
//
//    /**
//     * @Then /^I choose Right Injection Given By "([^"]*)"$/
//     */
//    public function RightInjectionGivenBy($injection)
//    {
//       $this->selectOption(Intravitreal::$rightInjectionGivenBy, $injection);
//    }
//
//    /**
//     * @Given /^I enter a Right Injection time of "([^"]*)"$/
//     */
//    public function RightInjectionTimeOf($time)
//    {
//       $this->fillField(Intravitreal::$rightInjectionTime, $time);
//    }
//
//    /**
//     * @Then /^I choose Left Pre Injection Antiseptic "([^"]*)"$/
//     */
//    public function LeftPreInjectionAntiseptic($antispetic)
//    {
//       $this->selectOption(Intravitreal::$leftPreInjectionAntiseptic, $antispetic);
//    }
//
//    /**
//     * @Then /^I choose Left Pre Injection Skin Cleanser "([^"]*)"$/
//     */
//    public function LeftPreInjectionSkinCleanser($skin)
//    {
//       $this->selectOption(Intravitreal::$leftPreInjectionSkinCleanser, $skin);
//    }
//
//    /**
//     * @Given /^I tick the Left Pre Injection IOP Lowering Drops checkbox$/
//     */
//    public function LeftPreInjectionIopLoweringDropsCheckbox()
//    {
//       $this->checkOption(Intravitreal::$leftPerInjectionIOPDrops);
//    }
//
//    /**
//     * @Then /^I choose Left Drug "([^"]*)"$/
//     */
//    public function iChooseLeftDrug($drug)
//    {
//       $this->fillField(Intravitreal::$leftDrug, $drug);
//    }
//
//    /**
//     * @Given /^I enter "([^"]*)" number of Left injections$/
//     */
//    public function NumberOfLeftInjections($injections)
//    {
//       $this->fillField(Intravitreal::$leftNumberOfInjections, $injections);
//    }
//
//    /**
//     * @Then /^I enter Left batch number "([^"]*)"$/
//     */
//    public function iLeftBatchNumber($batch)
//    {
//       $this->fillField(Intravitreal::$leftBatchNumber, $batch);
//    }
//
//    /**
//     * @Given /^I enter a Left batch expiry date of "([^"]*)"$/
//     */
//    public function LeftBatchExpiryDateOf($dateFrom)
//    {
//       $this->clickLink(Intravitreal::$leftBatchExpiryDate);
//       $this->clickLink(PatientViewPage::passDateFromTable($dateFrom));
//    }
//
//    /**
//     * @Then /^I choose Left Injection Given By "([^"]*)"$/
//     */
//    public function LeftInjectionGivenBy($injection)
//    {
//       $this->selectOption(Intravitreal::$leftInjectionGivenBy, $injection);
//    }
//
//    /**
//     * @Given /^I enter a Left Injection time of "([^"]*)"$/
//     */
//    public function iEnterALeftInjectionTimeOf($time)
//    {
//       $this->fillField(Intravitreal::$leftInjectionTime, $time);
//    }
//
//    /**
//     * @Then /^I choose A Right Lens Status of "([^"]*)"$/
//     */
//    public function RightLensStatusOf($lens)
//    {
//       $this->selectOption(Intravitreal::$rightLensStatus, $lens);
//    }
//
//    /**
//     * @Given /^I choose Right Counting Fingers Checked Yes$/
//     */
//    public function RightCountingFingersCheckedYes()
//    {
//       $this->clickLink(Intravitreal::$rightCountingFingersYes);
//    }
//
//    /**
//     * @Given /^I choose Right Counting Fingers Checked No$/
//     */
//    public function RightCountingFingersCheckedNo()
//    {
//       $this->clickLink(Intravitreal::$rightCountingFingersNo);
//    }
//
//    /**
//     * @Given /^I choose Right IOP Needs to be Checked Yes$/
//     */
//    public function RightIopNeedsToBeCheckedYes()
//    {
//       $this->clickLink(Intravitreal::$rightIOPCheckYes);
//    }
//
//    /**
//     * @Given /^I choose Right IOP Needs to be Checked No$/
//     */
//    public function RightIopNeedsToBeCheckedNo()
//    {
//       $this->clickLink(Intravitreal::$rightIOPCheckNo);
//    }
//
//    /**
//     * @Then /^I choose Right Post Injection Drops$/
//     */
//    public function RightPostInjectionDrops()
//    {
//       $this->checkOption(Intravitreal::$rightPostInjectionIOPDrops);
//    }
//
//    /**
//     * @Then /^I choose A Left Lens Status of "([^"]*)"$/
//     */
//    public function LeftLensStatusOf($lens)
//    {
//      $this->selectOption(Intravitreal::$leftLensStatus, $lens);
//    }
//
//    /**
//     * @Given /^I choose Left Counting Fingers Checked Yes$/
//     */
//    public function LeftCountingFingersCheckedYes()
//    {
//      $this->clickLink(Intravitreal::$leftCountingFingersYes);
//    }
//
//    /**
//     * @Given /^I choose Left Counting Fingers Checked No$/
//     */
//    public function LeftCountingFingersCheckedNo()
//    {
//
//       $this->clickLink(Intravitreal::$leftCountingFingersNo);
//    }
//
//    /**
//     * @Given /^I choose Left IOP Needs to be Checked Yes$/
//     */
//    public function LeftIopNeedsToBeCheckedYes()
//    {
//       $this->clickLink(Intravitreal::$leftIOPCheckYes);
//    }
//
//    /**
//     * @Given /^I choose Left IOP Needs to be Checked No$/
//     */
//    public function LeftIopNeedsToBeCheckedNo()
//    {
//       $this->clickLink(Intravitreal::$leftIOPCheckNo);
//    }
//
//    /**
//     * @Given /^I select Right Complications "([^"]*)"$/
//     */
//    public function RightComplications($complication)
//    {
//       $this->selectOption(Intravitreal::$rightComplicationsDropdown, $complication);
//    }
//
//    /**
//     * @Given /^I select Left Complications "([^"]*)"$/
//     */
//    public function LeftComplications($complication)
//    {
//        $this->selectOption(Intravitreal::$leftComplicationsDropdown, $complication
//        );
//    }
//
//    /**
//     * @Then /^I select Add First New Episode and Confirm$/
//     */
//    public function iSelectAddFirstNewEpisodeAndConfirm()
//    {
//
//    }
//
//    /**
//     * @Then /^I add Right Side$/
//     */
//    public function iAddRightSide()
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Right Side Diagnosis of "([^"]*)"$/
//     */
//    public function iSelectARightSideDiagnosisOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Left Side Diagnosis of "([^"]*)"$/
//     */
//    public function iSelectALeftSideDiagnosisOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select a Right Secondary To of "([^"]*)"$/
//     */
//    public function iSelectARightSecondaryToOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select a Left Secondary To of "([^"]*)"$/
//     */
//    public function iSelectALeftSecondaryToOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select Cerebrovascular accident Yes$/
//     */
//    public function iSelectCerebrovascularAccidentYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Cerebrovascular accident No$/
//     */
//    public function iSelectCerebrovascularAccidentNo()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Ischaemic attack Yes$/
//     */
//    public function iSelectIschaemicAttackYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Ischaemic attack No$/
//     */
//    public function iSelectIschaemicAttackNo()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Myocardial infarction Yes$/
//     */
//    public function iSelectMyocardialInfarctionYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Myocardial infarction No$/
//     */
//    public function iSelectMyocardialInfarctionNo()
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Consultant of "([^"]*)"$/
//     */
//    public function iSelectAConsultantOf($arg1)
//    {
//
//    }
//

//
//    /**
//     * @Then /^I search for patient name last name "([^"]*)" and first name "([^"]*)"$/
//     */
//    public function iSearchForPatientNameLastNameAndFirstName($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^I Add a New Episode and Confirm$/
//     */
//    public function iAddANewEpisodeAndConfirm()
//    {
//        throw new PendingException();
//    }
//

//    /**
//     * @Given /^I select the No option for Read to Discharge$/
//     */
//    public function iSelectTheNoOptionForReadToDischarge2()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^I choose Left Post Injection Drops$/
//     */
//    public function iChooseLeftPostInjectionDrops()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^I select an existing "([^"]*)" Episode$/
//     */
//    public function iSelectAnExistingEpisode($arg1)
//    {
//        throw new PendingException();
//    }


}
