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

class ExaminationContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I select a History of Blurred Vision, Mild Severity, Onset (\d+) Week, Left Eye, (\d+) Week$/
     */
    public function iSelectAHistoryOfBlurredVision()
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->history();
    }

    /**
     * @Given /^I choose to expand the Comorbidities section$/
     */
    public function iChooseToExpandTheComorbiditiesSection()
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->openComorbidities();
    }

    /**
     * @Then /^I Add a Comorbiditiy of "([^"]*)"$/
     */
    public function iAddAComorbiditiyOf($com)
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->addComorbiditiy($com);
    }

    /**
     * @Then /^I choose to expand the Visual Acuity section$/
     */
    public function iChooseToExpandTheVisualAcuitySection()
    {
        /**
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->getSession()->wait(5000, '$.active == 0');
        $examination->openVisualAcuity();
    }

    /**
     * @Given /^I select a Visual Acuity of "([^"]*)"$/
     */
    public function iSelectAVisualAcuityOf($unit)
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->selectVisualAcuity($unit);
    }

    /**
     * @Then /^I choose a left Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function SnellenMetreAndAReading($metre, $method)
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->leftVisualAcuity($metre, $method);
    }

    /**
     * @Then /^I choose a right Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function RightVisualAcuitySnellenMetre($metre, $method)
    {
        /**
         * @var Examination $examination
         */
        $examination= $this->getPage('Examination');
        $examination->rightVisualAcuity($metre, $method);
    }

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
}