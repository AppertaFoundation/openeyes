<?php

use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;

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
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->history();
    }

    /**
     * @Then /^I Add a Comorbiditiy of "([^"]*)"$/
     */
    public function iAddAComorbiditiyOf($com)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addComorbiditiy($com);
    }

    /**
     * @Then /^I choose to expand the "([^"]*)" section$/
     */
    public function iChooseToExpandTheSection($section)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->openExaminationSectionList($section);
        /*if($section=='visualFunction') {
            $examination->openVisualFunction();
        }
        elseif($section=='visualAcuity') {
            $examination->openVisualAcuity();
        }
        elseif($section=='nearVisualAcuity') {
            $examination->openNearVisualAcuity();
        }
        elseif($section=='anteriorSegment') {
            $examination->openAnteriorSegment();
        }
        elseif($section=='refraction'){
            $examination->openRefraction();
        }
        elseif($section=='intraocularPressure'){
            $examination->expandIntraocularPressure();
        }
        elseif($section=='dilation'){
            $examination->openDilation();
        }
        elseif($section=='visualFields'){
            $examination->expandVisualFields();
        }
        elseif($section=='gonioscopy'){
            $examination->expandGonioscopy();
        }
        elseif($section=='adnexalComorbidity'){
            $examination->expandAdnexalComorbidity();
        }
        elseif($section=='conclusion'){
            $examination->expandConclusion();
        }
        elseif($section=='pupillaryAbnormalities'){
            $examination->expandPupillaryAbnormalities();
        }
        elseif($section=='DRGrading'){
            $examination->expandDRGrading();
        }
        elseif($section=='opticDisc'){
            $examination->expandOpticDisc();
        }
        elseif($section=='posteriorPole'){
            $examination->expandPosteriorPole();
        }
        elseif($section=='diagnoses'){
            $examination->expandDiagnoses();
        }
        elseif($section=='investigation'){
            $examination->expandInvestigation();
        }
        elseif($section=='clinicalManagement'){
            $examination->expandClinicalManagement();
        }
        elseif($section=='cataractSurgicalManagement'){
            $examination->expandCataractSurgicalManagement();
        }
        elseif($section=='laserManagement'){
            $examination->expandLaserManagement();
        }
        elseif($section=='injectionManagement'){
            $examination->expandInjectionManagement();
        }
        elseif($section=='risks'){
            $examination->expandRisks();
        }
        elseif($section=='clinicalOutcome'){
            $examination->expandClinicalOutcome();
        }
        elseif($section=='overallManagement'){
            $examination->expandOverallManagement();
        }
        elseif($section=='currentManagement'){
            $examination->expandCurrentManagement();
        }
        elseif($section=='colourVision'){
            $examination->openColourVision();
        }
        elseif($section=='comorbidities'){
            $examination->openComorbidities();
        }
        elseif($section=='conclusion') {
            $examination->expandConclusion();
        }
        elseif($section=='opticDisc') {
            $examination->expandOpticDisc();
        }*/


    }


    /**
     * @Then /^I select a Segment of Tube patch and Material drop down of "([^"]*)"$/
     */
    public function iSelectASegmentAndMaterial($material)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectASegmentAndMaterial($material);
    }

    /**
     * @Given /^I choose a left eye diagnosis$/
     */
    public function iChooseALeftEyeDiagnosis()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diagnosesLeftEye();
    }

    /**
     * @Given /^I add Anterior Segment Description of "([^"]*)"$/
     */
    public function iAddAnteriorSegmentDescriptionOf($description)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->anteriorSegmentDescription($description);
    }

    /**
     * @Given /^I select a Visual Acuity of "([^"]*)"$/
     */
    public function iSelectAVisualAcuityOf($unit)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectVisualAcuity($unit);
    }

    /**
     * @Given /^I add Near Visual Acuity$/
     */
    public function iAddNearVisualAcuity()
    {
        /**
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->openVisualFunction();
        $examination->openNearVisualAcuity();
    }

    /**
     * @Given /^I add Allergy Element$/
     */
    public function iAddAllergyElement()
    {
        /**
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->openAllergies();
    }

    /**
     * @Then /^I Add Allergy "([^"]*)"$/
     */
    public function iAddAllergy($allergy)
    {
        /**
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addAllergyReading($allergy);
    }


    /**
     * @Then /^I select a "([^"]*)" Near Visual Acuity of "([^"]*)" using "([^"]*)"$/
     */
    public function iSelectANearVisualAcuityOfUsing ($side, $reading, $method){
        /**
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->ensureNVASide($side);
        $examination->addNVAReading($side, $reading, $method);
    }

    /**
     * @Given /^I select a Near Visual Acuity of "([^"]*)"$/
     */
    public function iSelectANearVisualAcuityOf($unit)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectNearVisualAcuity($unit);
    }

    /**
     * @Then /^I choose a left Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function SnellenMetreAndAReading($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectLeftVA($metre, $method);
    }

    /**
     * @Then /^I choose a right Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function RightVisualAcuitySnellenMetre($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectRightVA($metre, $method);
    }

    /**
     * @Then /^I choose a left Visual Acuity ETDRS Letters Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseALeftVisualAcuityEtdrsLettersSnellenMetreAndAReadingMethodOf($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectLeftVA($metre, $method);
    }

    /**
     * @Then /^I choose a right Visual Acuity ETDRS Letters Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseARightVisualAcuityEtdrsLettersSnellenMetreAndAReadingMethodOf($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectRightVA($metre, $method);
    }

    /**
     * @Then /^I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseALeftNearVisualAcuityEtdrsLettersSnellenMetreAndAReadingMethodOf($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectLeftNVA($metre, $method);
    }

    /**
     * @Then /^I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseARightNearVisualAcuityEtdrsLettersSnellenMetreAndAReadingMethodOf($metre, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectRightNVA($metre, $method);
    }

    /**
     * @Then /^I choose a left Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseALeftIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftIntracocular($pressure, $instrument);
    }

    /**
     * @Then /^I choose a right Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseARightIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightIntracocular($pressure, $instrument);
    }


    /**
     * @Then /^I choose left Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseLeftDilationOfAndDropsOf($dilation, $drops)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationLeft($dilation, $drops);
        $examination->dilationLeft($dilation, $drops);
    }

    /**
     * @Then /^I choose right Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseRightDilationOfAndDropsOf($dilation, $drops)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationRight($dilation, $drops);
    }

    /**
     * @Given /^I enter a left Dilation time of "([^"]*)"$/
     */
    public function iEnterALeftDilationTimeOf($time)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationLeftTime($time);
    }

    /**
     * @Given /^I enter a right Dilation time of "([^"]*)"$/
     */
    public function iEnterARightDilationTimeOf($time)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationRightTime($time);
    }

    /**
     * @Then /^I Confirm that the Dilation Invalid time error message is displayed$/
     */
    public function iConfirmThatTheDilationInvalidTimeErrorMessageIsDisplayed()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationTimeErrorValidation();

        $homepage = $this->getPage('HomePage');


        $homepage->open();
        sleep(5);
        $homepage->acceptAlert();
    }

    /**
     * @Then /^I choose to remove left Dilation treatment$/
     */
    public function iChooseToRemoveLeftDilation()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeLeftDilation();
    }

    /**
     * @Then /^I enter left Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function LeftRefractionDetails($sphere, $integer, $fraction)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftRefractionDetails($sphere, $integer, $fraction);
    }

    /**
     * @Given /^I enter left cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterLeftCylinderDetails($cylinder, $integer, $fraction)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCyclinderDetails($cylinder, $integer, $fraction);
    }

    /**
     * @Then /^I enter left Axis degrees of "([^"]*)"$/
     */
    public function iEnterLeftAxisDegreesOf($axis)
    {
        // HACK! I have entered the value twice in the code to stop the Axis from spinning
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftAxis($axis);
    }

    /**
     * @Given /^I enter a left type of "([^"]*)"$/
     */
    public function iEnterALeftTypeOf($type)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftType($type);
    }

    /**
     * @Then /^I select a Right Intended Treatment of "([^"]*)"$/
     */
    public function iSelectARightIntendedTreatmentOf($treatment)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightIntendedTreatment($treatment);
    }

    /**
     * @Then /^I select a Left Intended Treatment of "([^"]*)"$/
     */
    public function iSelectALeftIntendedTreatmentOf($treatment)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftIntendedTreatment($treatment);
    }

    /**
     * @Then /^I enter right Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightRefractionDetailsOfSphereIntegerFraction($sphere, $integer, $fraction)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->RightRefractionDetails($sphere, $integer, $fraction);
    }

    /**
     * @Given /^I enter right cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightCylinderDetailsOfOfCylinderIntegerFraction($cylinder, $integer, $fraction)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->RightCyclinderDetails($cylinder, $integer, $fraction);
    }

    /**
     * @Then /^I enter right Axis degrees of "([^"]*)"$/
     */
    public function iEnterRightAxisDegreesOf($axis)
    {
        // We need a Clear Field function here
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->RightAxis($axis);
        // We need to Press the tab key here
    }

    /**
     * @Given /^I enter a right type of "([^"]*)"$/
     */
    public function iEnterARightTypeOf($type)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->RightType($type);
    }


    /**
     * @Given /^I add a left Adnexal Comorbidity of "([^"]*)"$/
     */
    public function iAddALeftAdnexalComorbidityOf($left)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftAdnexal($left);
    }

    /**
     * @Given /^I add a right Adnexal Comorbidity of "([^"]*)"$/
     */
    public function iAddARightAdnexalComorbidityOf($right)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightAdnexal($right);
    }


    /**
     * @Given /^I add a left Abnormality of "([^"]*)"$/
     */
    public function iAddALeftAbnormalityOf($left)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftPupillaryAbnormality($left);
    }

    /**
     * @Given /^I add a right Abnormality of "([^"]*)"$/
     */
    public function iAddARightAbnormalityOf($right)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightPupillaryAbnormality($right);
    }


    /**
     * @Given /^I select Diagnosis of Cataract$/
     */
    public function iSelectDiagnosisOfCataract()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diagnosesOfCataract();
    }

    /**
     * @Given /^I choose a right eye diagnosis$/
     */
    public function iChooseARightEyeDiagnosis()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diagnosesRightEye();
    }

    /**
     * @Given /^I choose both eyes diagnosis$/
     */
    public function iChooseBothEyesDiagnosis()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diagnosesBothEyes();
    }

    /**
     * @Given /^I choose a principal diagnosis$/
     */
    public function principalDiagnosis()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->principalDiagnosis();
    }


    /**
     * @Given /^I add an Investigation of "([^"]*)"$/
     */
    public function iAddAnInvestigationOf($investigation)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addInvestigation($investigation);
    }

    /**
     * @Given /^I add Cataract Management Comments of "([^"]*)"$/
     */
    public function iAddCataractManagementCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->cataractManagementComments($comments);
    }

    /**
     * @Then /^I select First Eye$/
     */
    public function iSelectFirstEye()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectFirstEye();
    }

    /**
     * @Then /^I select Second Eye$/
     */
    public function iSelectSecondEye()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->selectSecondEye();
    }

    /**
     * @Given /^I choose City Road$/
     */
    public function iChooseCityRoad()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->cityRoad();
    }

    /**
     * @Given /^I choose At Satellite$/
     */
    public function iChooseAtSatellite()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->satellite();
    }

    /**
     * @Given /^I choose Straightforward case$/
     */
    public function iChooseStraightforwardCase()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->straightforward();
    }

    /**
     * @Then /^I select a post operative refractive target in dioptres of "([^"]*)"$/
     */
    public function iSelectAPostOperativeRefractiveTargetInDioptresOf($target)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->postOpRefractiveTarget($target);
    }

    /**
     * @Given /^the post operative target has been discussed with patient Yes$/
     */
    public function thePostOperativeTargetHasBeenDiscussedWithPatientYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->discussedWithPatientYes();
    }

    /**
     * @Given /^the post operative target has been discussed with patient No$/
     */
    public function thePostOperativeTargetHasBeenDiscussedWithPatientNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->discussedWithPatientNo();
    }

    /**
     * @Then /^I select a suitable for surgeon of "([^"]*)"$/
     */
    public function iSelectASuitableForSurgeonOf($surgeon)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->suitableForSurgeon($surgeon);
        // sleep(5);
    }

    /**
     * @Given /^I tick the Supervised checkbox$/
     */
    public function iTickTheSupervisedCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->supervisedCheckbox();
    }

    /**
     * @Then /^I select Previous Refractive Surgery Yes$/
     */
    public function iSelectPreviousRefractiveSurgeryYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->previousRefractiveSurgeryYes();
    }

    /**
     * @Then /^I select Previous Refractive Surgery No$/
     */
    public function iSelectPreviousRefractiveSurgeryNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->previousRefractiveSurgeryNo();
    }

    /**
     * @Given /^I select Vitrectomised Eye Yes$/
     */
    public function iSelectVitrectomisedEyeYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->vitrectomisedEyeYes();
    }

    /**
     * @Given /^I select Vitrectomised Eye No$/
     */
    public function iSelectVitrectomisedEyeNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->vitrectomisedEyeNo();
    }

    /**
     * @Given /^I choose a right laser choice of "([^"]*)"$/
     */
    public function iChooseARightLaserOf($laser)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->RightLaserStatusChoice($laser);
    }

    /**
     * @Given /^I choose a left laser choice of "([^"]*)"$/
     */
    public function iChooseALeftLaserOf($laser)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->LeftLaserStatusChoice($laser);
    }

    /**
     * @Given /^I choose a left laser type of "([^"]*)"$/
     */
    public function iChooseALeftLaserTypeOf($laser)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftLaser($laser);
    }

    /**
     * @Given /^I choose a right laser type of "([^"]*)"$/
     */
    public function iChooseARightLaserTypeOf($laser)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightLaser($laser);
    }

    /**
     * @Given /^I tick the No Treatment checkbox$/
     */
    public function iTickTheNoTreatmentCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->noTreatment();
    }

    /**
     * @Then /^I select a reason for No Treatment of "([^"]*)"$/
     */
    public function iSelectAReasonForNoTreatmentOf($treatment)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->noTreatmentReason($treatment);
    }

    /**
     * @Given /^I select a Right Diagnosis of Choroidal Retinal Neovascularisation$/
     */
    public function iSelectADiagnosisOfChoroidalRetinalNeovascularisation()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightChoroidalRetinal();
    }

    /**
     * @Then /^I select Right Secondary to "([^"]*)"$/
     */
    public function iSelectSecondaryTo($secondary)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightSecondaryTo($secondary);
    }

    /**
     * @Given /^I select a Left Diagnosis of Choroidal Retinal Neovascularisation$/
     */
    public function iSelectALeftDiagnosisOfChoroidalRetinalNeovascularisation()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftChoroidalRetinal();
    }

    /**
     * @Then /^I select Left Secondary to "([^"]*)"$/
     */
    public function iSelectLeftSecondaryTo($secondary)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftSecondaryTo($secondary);
    }

    /**
     * @Then /^I choose a Right CRT Increase <(\d+) of Yes$/
     */
    public function iChooseACrtIncreaseOfYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        sleep(5);
        $examination->rightCRTIncreaseLowerThanHundredYes();
    }

    /**
     * @Then /^I choose a Right CRT Increase <(\d+) of No$/
     */
    public function iChooseACrtIncreaseOfNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightCRTIncreaseLowerThanHundredNo();
    }

    /**
     * @Then /^I choose a Right CRT >=(\d+) of Yes$/
     */
    public function iChooseACrtOfYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightCRTIncreaseMoreThanHundredYes();
    }

    /**
     * @Then /^I choose a Right CRT >=(\d+) of No$/
     */
    public function iChooseACrtOfNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightCRTIncreaseMoreThanHundredNo();
    }

    /**
     * @Then /^I choose a Right Loss of (\d+) letters Yes$/
     */
    public function iChooseALossOfLettersYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightLossOfFiveLettersYes();
    }

    /**
     * @Then /^I choose a Right Loss of (\d+) letters No$/
     */
    public function iChooseALossOfLettersNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightLossOfFiveLettersNo();
    }

    /**
     * @Then /^I choose a Right Loss of (\d+) letters >(\d+) Yes$/
     */
    public function iChooseALossOfLettersYes2($arg1, $arg2)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightLossOfFiveLettersHigherThanFiveYes();
    }

    /**
     * @Then /^I choose a Right Loss of (\d+) letters >(\d+) No$/
     */
    public function iChooseALossOfLettersNo2($arg1, $arg2)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightLossOfFiveLettersHigherThanFiveNo();
    }

    /**
     * @Then /^I choose a Left CRT Increase <(\d+) of Yes$/
     */
    public function iChooseALeftCrtIncreaseOfYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCRTIncreaseLowerThanHundredYes();
    }

    /**
     * @Then /^I choose a Left CRT Increase <(\d+) of No$/
     */
    public function iChooseALeftCrtIncreaseOfNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCRTIncreaseLowerThanHundredNo();
    }

    /**
     * @Then /^I choose a Left CRT >=(\d+) of Yes$/
     */
    public function iChooseALeftCrtOfYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCRTIncreaseMoreThanHundredYes();
    }

    /**
     * @Then /^I choose a Left CRT >=(\d+) of No$/
     */
    public function iChooseALeftCrtOfNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCRTIncreaseMoreThanHundredNo();
    }

    /**
     * @Then /^I choose a Left Loss of (\d+) letters Yes$/
     */
    public function iChooseALeftLossOfLettersYes($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftLossOfFiveLettersYes();
    }

    /**
     * @Then /^I choose a Left Loss of (\d+) letters No$/
     */
    public function iChooseALeftLossOfLettersNo($arg1)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftLossOfFiveLettersNo();
    }

    /**
     * @Then /^I choose a Left Loss of (\d+) letters >(\d+) Yes$/
     */
    public function iChooseALeftLossOfLettersYes2($arg1, $arg2)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftLossOfFiveLettersHigherThanFiveYes();
    }

    /**
     * @Then /^I choose a Left Loss of (\d+) letters >(\d+) No$/
     */
    public function iChooseALeftLossOfLettersNo2($arg1, $arg2)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftLossOfFiveLettersHigherThanFiveNo();
    }

    /**
     * @Given /^I select a Right Diagnosis of Macular retinal oedema$/
     */
    public function iSelectARightDiagnosisOfMacularRetinalOedema()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightMacularRetinal();
    }

    /**
     * @Then /^I select Right Secondary of Venous retinal branch occlusion$/
     */
    public function iSelectRightSecondaryOfVenousRetinalBranchOcclusion()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightSecondaryVenousRetinalBranchOcclusion();
    }

    /**
     * @Given /^I select a Left Diagnosis of Macular retinal oedema$/
     */
    public function iSelectALeftDiagnosisOfMacularRetinalOedema()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftMacularRetinal();
    }

    /**
     * @Then /^I select Left Secondary of Diabetic macular oedema$/
     */
    public function iSelectLeftSecondaryOfDiabeticMacularOedema()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftSecondaryDiabeticMacularOedema();
    }

    /**
     * @Then /^I choose a Right Failed Laser of Yes$/
     */
    public function iChooseARightFailedLaserOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightFailedLaserYes();
    }

    /**
     * @Then /^I choose a Right Failed Laser of No$/
     */
    public function iChooseARightFailedLaserOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightFailedLaserNo();
    }

    /**
     * @Then /^I choose a Right Unsuitable Laser of Yes$/
     */
    public function iChooseARightUnsuitableLaserOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightUnsuitableForLaserYes();
    }

    /**
     * @Then /^I choose a Right Unsuitable Laser of No$/
     */
    public function iChooseARightUnsuitableLaserOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightUnsuitableForLaserNo();
    }

    /**
     * @Then /^I choose a Right Previous Ozurdex Yes$/
     */
    public function iChooseARightPreviousOzurdexYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightPreviousOzurdexYes();
    }

    /**
     * @Then /^I choose a Right Previous Ozurdex No$/
     */
    public function iChooseARightPreviousOzurdexNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightPreviousOzurdexNo();
    }

    /**
     * @Then /^I choose a Left CRT above Four Hundred of Yes$/
     */
    public function iChooseALeftCrtAboveFourHundredOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCrtIncreaseMoreThanFourHundredYes();
    }

    /**
     * @Then /^I choose a Left CRT above Four Hundred of No$/
     */
    public function iChooseALeftCrtAboveFourHundredOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftCrtIncreaseMoreThanFourHundredNo();
    }

    /**
     * @Then /^I choose a Left Foveal Structure Damage Yes$/
     */
    public function iChooseALeftFovealStructureDamageYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftFovealDamageYes();
    }

    /**
     * @Then /^I choose a Left Foveal Structure Damage No$/
     */
    public function iChooseALeftFovealStructureDamageNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftFovealDamageNo();
    }

    /**
     * @Then /^I choose a Left Failed Laser of Yes$/
     */
    public function iChooseALeftFailedLaserOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftFailedLaserYes();
    }

    /**
     * @Then /^I choose a Left Failed Laser of No$/
     */
    public function iChooseALeftFailedLaserOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftFailedLaserNo();
    }

    /**
     * @Then /^I choose a Left Unsuitable Laser of Yes$/
     */
    public function iChooseALeftUnsuitableLaserOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftUnsuitableForLaserYes();
    }

    /**
     * @Then /^I choose a Left Unsuitable Laser of No$/
     */
    public function iChooseALeftUnsuitableLaserOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftUnsuitableForLaserNo();
    }

    /**
     * @Then /^I choose a Left Previous Anti VEGF of Yes$/
     */
    public function iChooseALeftPreviousAntiVegfOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftPreviousAntiVEGFyes();
    }

    /**
     * @Then /^I choose a Left Previous Anti VEGF of No$/
     */
    public function iChooseALeftPreviousAntiVegfOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftPreviousAntiVEGFno();
    }

    /**
     * @Given /^I add comments to the Risk section of "([^"]*)"$/
     */
    public function iAddCommentsToTheRiskSectionOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->riskComments($comments);
    }


    /**
     * @Given /^I choose a Clinical Outcome Status of Discharge$/
     */
    public function iChooseAClinicalOutcomeStatusOfDischarge()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalOutcomeDischarge();
    }

    /**
     * @Given /^I choose a Clinical Outcome Status of Follow Up$/
     */
    public function iChooseAClinicalOutcomeStatusOfFollowUp()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalOutcomeFollowUp();
    }

    /**
     * @Then /^I choose a Follow Up quantity of "([^"]*)"$/
     */
    public function iChooseAFollowUpQuantityOf($quantity)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalFollowUpQuantity($quantity);
    }

    /**
     * @Given /^I choose a Follow Up period of "([^"]*)"$/
     */
    public function iChooseAFollowUpPeriodOf($period)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalFollowUpPeriod($period);
    }

    /**
     * @Given /^I tick the Patient Suitable for Community Patient Tariff$/
     */
    public function iTickThePatientSuitableForCommunityPatientTariff()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalSuitablePatient();
    }

    /**
     * @Then /^I choose a Role of "([^"]*)"$/
     */
    public function iChooseARoleOf($role)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalRole($role);
    }


    /**
     * @Given /^I choose a Conclusion option of "([^"]*)"$/
     */
    public function iChooseAConclusionOptionOf($option)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->conclusionOption($option);
    }

    /**
     * @Then /^I Save the Examination$/
     */
    public function iSaveTheExamination()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->saveEvent();
    }

    /**
     * @Then /^I Save the Examination and confirm it has been created successfully$/
     */
    public function iSaveTheExaminationAndConfirm()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->saveAndConfirm();
    }

    // VALIDATION TESTS

    /**
     * @Then /^a check is made that a right Axis degrees of "([^"]*)" was entered$/
     */
    public function aCheckIsMadeThatARightAxisDegreesOfWasEntered($axis)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightAxisCheck($axis);
    }

    /**
     * @Then /^a check is made that a left Axis degrees of "([^"]*)" was entered$/
     */
    public function aCheckIsMadeThatALeftAxisDegreesOfWasEntered($axis)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftAxisCheck($axis);
    }

    /**
     * @Then /^I select Add All optional elements$/
     */
    public function iSelectAddAllOptionalElements()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addAllElements();
    }

    /**
     * @Then /^I confirm that the Add All Validation error messages have been displayed$/
     */
    public function iConfirmThatTheAddAllValidationErrorMessagesHaveBeenDisplayed()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addAllElementsValidationCheck();

        $homepage = $this->getPage('HomePage');


        $homepage->open();
        sleep(5);
        $homepage->acceptAlert();
    }

    /**
     * @Then /^I select Close All elements$/
     */
    public function iSelectCloseAllElements()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeAllElements();
    }

    /**
     * @Then /^I confirm that the Remove All Validation error message is displayed$/
     */
    public function iConfirmThatTheRemoveAllValidationErrorMessageIsDisplayed()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeAllValidationCheck();

        $homepage = $this->getPage('HomePage');


        $homepage->open();
        sleep(5);
        $homepage->acceptAlert();
    }

    /**
     * @Then /^I Confirm that the History Validation error message is displayed$/
     */
    public function iConfirmThatHistoryErrorMessageIsDisplayed()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->historyValidationCheck();
    }


    /**
     * @Then /^I Confirm that the Conclusion Validation error message is displayed$/
     */
    public function iConfirmConlusionValidation()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->conclusionValidationCheck();

        $homepage = $this->getPage('HomePage');


        $homepage->open();
        sleep(5);
        $homepage->acceptAlert();
    }

    /**
     * @Then /^I Confirm to leave the page$/
     */
    public function iConfirmToLeaveThePage()
    {
        //$examination = $this->getPage ( 'Examination' );
        Sleep(5);
        $homepage = $this->getPage('Homepage');
        $homepage->ConfirmLeavePage();
    }


    /**
     * @Then /^I cancel the Examnination event$/
     */
    public function iCancelTheExamninationEvent()
    {
        $examination = $this->getPage('Examination');
        Sleep(5);
        $examination->CancelExaminationEvent();
    }

    /**
     * @Then /^I Confirm that the Dilation Validation error message is displayed$/
     */
    public function iConfirmDilationValidation()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->dilationValidationCheck();


        $homepage = $this->getPage('HomePage');


        $homepage->open();
        sleep(5);
        $homepage->acceptAlert();
    }

    /**
     * @Then /^I remove Refraction right side$/
     */
    public function iRemoveRefractionRightSide()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeRefractionRightSide();
    }

    /**
     * @Then /^I remove all comorbidities$/
     */
    public function removeComorbidties()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeAllComorbidities();
    }

    /**
     * @Given /^I choose to add a new left Visual Acuity reading of "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseToAddANewLeftVisualAcuityReadingOfAndAReadingMethodOf($reading, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addLeftVA($reading, $method);
    }

    /**
     * @Given /^I choose to add a new Right Visual Acuity reading of "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function iChooseToAddANewRightVisualAcuityReadingOfAndAReadingMethodOf($reading, $method)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->addRightVA($reading, $method);
    }

    /**
     * @Then /^I remove the initial Left Visual Acuity$/
     */
    public function iRemoveTheInitialLeftVisualAcuity()
    {
        $examination = $this->getPage('Examination');
        $examination->removeFirstLeftVA();
    }

    /**
     * @Then /^I remove the initial Right Visual Acuity$/
     */
    public function iRemoveTheInitialRightVisualAcuity()
    {
        $examination = $this->getPage('Examination');
        $examination->removeFirstRightVA();
    }

    /**
     * @Then /^I remove the newly added Left Visual Acuity$/
     */
    public function iRemoveTheNewlyAddedLeftVisualAcuity()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeSecondLeftVA();
    }

    /**
     * @Then /^I remove the newly added Right Visual Acuity$/
     */
    public function iRemoveTheNewlyAddedRightVisualAcuity()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->removeSecondRightVA();
    }

    /**
     * @Then /^I select a Diabetes type of Mellitus Type one$/
     */
    public function iSelectADiabetesTypeOfMellitusTypeone()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diabetesTypeOne();
    }

    /**
     * @Then /^I select a Diabetes type of Mellitus Type two$/
     */
    public function iSelectADiabetesTypeOfMellitusTypetwo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->diabetesTypeTwo();
    }

    /**
     * @Given /^I select a left Clinical Grading for Retinopathy of "([^"]*)"$/
     */
    public function iSelectALeftClinicalGradingForRetinopathyOf($grading)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftClinicalGradingRetino($grading);
    }

    /**
     * @Given /^I select a left NSC Retinopathy of "([^"]*)"$/
     */
    public function iSelectALeftNscRetinopathyOf($nsc)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftNSCRetino($nsc);
    }

    /**
     * @Given /^I select a left Retinopathy photocoagulation of Yes$/
     */
    public function iSelectALeftRetinopathyPhotocoagulationOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftRetinoPhotoYes();
    }

    /**
     * @Given /^I select a left Retinopathy photocoagulation of No$/
     */
    public function iSelectALeftRetinopathyPhotocoagulationOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftRetinoPhotoNo();
    }

    /**
     * @Given /^I select a left Clinical Grading for maculopathy of "([^"]*)"$/
     */
    public function iSelectALeftClinicalGradingForMaculopathyOf($grading)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftClinicalGradingMaculo($grading);
    }

    /**
     * @Given /^I select a left NSC maculopathy of "([^"]*)"$/
     */
    public function iSelectALeftNscMaculopathyOf($nsc)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftNSCMaculo($nsc);
    }

    /**
     * @Given /^I select a left Maculopathy photocoagulation of Yes$/
     */
    public function iSelectALeftMaculopathyPhotocoagulationOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftMaculoPhotoYes();
    }

    /**
     * @Given /^I select a left Maculopathy photocoagulation of No$/
     */
    public function iSelectALeftMaculopathyPhotocoagulationOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftMaculoPhotoNo();
    }

    /**
     * @Given /^I select a right Clinical Grading for Retinopathy of "([^"]*)"$/
     */
    public function iSelectARightClinicalGradingForRetinopathyOf($grading)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightClinicalGradingRetino($grading);
    }

    /**
     * @Given /^I select a right NSC Retinopathy of "([^"]*)"$/
     */
    public function iSelectARightNscRetinopathyOf($nsc)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightNSCRetino($nsc);
    }

    /**
     * @Given /^I select a right Retinopathy photocoagulation of Yes$/
     */
    public function iSelectARightRetinopathyPhotocoagulationOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightRetinoPhotoYes();
    }

    /**
     * @Given /^I select a right Retinopathy photocoagulation of No$/
     */
    public function iSelectARightRetinopathyPhotocoagulationOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightRetinoPhotoNo();
    }

    /**
     * @Given /^I select a right Clinical Grading for maculopathy of "([^"]*)"$/
     */
    public function iSelectARightClinicalGradingForMaculopathyOf($grading)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightClinicalGradingMaculo($grading);
    }

    /**
     * @Given /^I select a right NSC maculopathy of "([^"]*)"$/
     */
    public function iSelectARightNscMaculopathyOf($nsc)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightNSCMaculo($nsc);
    }

    /**
     * @Given /^I select a right Maculopathy photocoagulation of Yes$/
     */
    public function iSelectARightMaculopathyPhotocoagulationOfYes()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightMaculoPhotoYes();
    }

    /**
     * @Given /^I select a right Maculopathy photocoagulation of No$/
     */
    public function iSelectARightMaculopathyPhotocoagulationOfNo()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightMaculoPhotoNo();
    }

    /**
     * @Given /^I select Left Unable to assess checkbox$/
     */
    public function iSelectLeftUnableToAssessCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftUnableAssess();
    }

    /**
     * @Given /^I select Left Eye Missing checkbox$/
     */
    public function iSelectLeftEyeMissingCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftEyeMissing();
    }

    /**
     * @Given /^I select Right Unable to assess checkbox$/
     */
    public function iSelectRightUnableToAssessCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightUnableAssess();
    }

    /**
     * @Given /^I select Right Eye Missing checkbox$/
     */
    public function iSelectRightEyeMissingCheckbox()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightEyeMissing();
    }


    /**
     * @Given /^I choose a Clinical Interval of "([^"]*)"$/
     */
    public function iChooseAClinicalIntervalOf($interval)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clinicalInterval($interval);
    }

    /**
     * @Given /^I choose a Photo of "([^"]*)"$/
     */
    public function iChooseAPhotoOf($photo)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->photo($photo);
    }

    /**
     * @Given /^I choose a OCT of "([^"]*)"$/
     */
    public function iChooseAOctOf($oct)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->OCT($oct);
    }

    /**
     * @Given /^I choose a Visual Fields of "([^"]*)"$/
     */
    public function iChooseAVisualFieldsOf($visual)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->visualFields($visual);
    }

    /**
     * @Given /^I choose Overall Management Section Comments of "([^"]*)"$/
     */
    public function iChooseOverallManagementSectionCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->overallComments($comments);
    }

    /**
     * @Given /^I choose a Gonio of "([^"]*)"$/
     */
    public function iChooseAGonioOf($gonio)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->gonioDropdown($gonio);
    }

    /**
     * @Given /^I choose a Right Target IOP of "([^"]*)"$/
     */
    public function iChooseARightTargetIopOf($iop)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightIOP($iop);
    }

    /**
     * @Given /^I choose a Right Gonio of "([^"]*)"$/
     */
    public function iChooseARightGonioOf($gonio)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightGonio($gonio);
    }

    /**
     * @Given /^I choose a Left Target IOP of "([^"]*)"$/
     */
    public function iChooseALeftTargetIopOf($iop)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftIOP($iop);
    }

    /**
     * @Given /^I choose a Left Gonio of "([^"]*)"$/
     */
    public function iChooseALeftGonioOf($gonio)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftGonio($gonio);
    }


    /**
     * @Given /^I choose a Referral of Other Service$/
     */
    public function iChooseAReferralOfOtherService()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referralOther();
    }

    /**
     * @Given /^I choose a Referral of Refraction$/
     */
    public function iChooseAReferralOfRefraction()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referralRefraction();
    }

    /**
     * @Given /^I choose a Referral of LVA$/
     */
    public function iChooseAReferralOfLva()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referralLVA();
    }

    /**
     * @Given /^I choose a Referral of Orthopics$/
     */
    public function iChooseAReferralOfOrthopics()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referralOrthoptics();
    }

    /**
     * @Given /^I choose a Referral of CL clinic$/
     */
    public function iChooseAReferralOfClClinic()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referralCLClinic();
    }

    /**
     * @Then /^I choose Investigations of VF$/
     */
    public function iChooseInvestigationsOfVf()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsVF();
    }

    /**
     * @Then /^I choose Investigations of US$/
     */
    public function iChooseInvestigationsOfUs()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsUS();
    }

    /**
     * @Then /^I choose Investigations of Biometry$/
     */
    public function iChooseInvestigationsOfBiometry()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsBiometry();
    }

    /**
     * @Then /^I choose Investigations of OCT$/
     */
    public function iChooseInvestigationsOfOct()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsOCT();
    }

    /**
     * @Then /^I choose Investigations of HRT$/
     */
    public function iChooseInvestigationsOfHrt()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsHRT();
    }

    /**
     * @Then /^I choose Investigations of Disc Photos$/
     */
    public function iChooseInvestigationsOfDiscPhotos()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsDiscPhotos();
    }

    /**
     * @Then /^I choose Investigations of EDT$/
     */
    public function iChooseInvestigationsOfEdt()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->investigationsEDT();
    }

    /**
     * @Given /^I select a Left Glaucoma Status of "([^"]*)"$/
     */
    public function iSelectALeftGlaucomaStatusOf($status)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftGlaucomaStatus($status);
    }

    /**
     * @Given /^I select a Left Drop related problem of "([^"]*)"$/
     */
    public function iSelectALeftDropRelatedProblemOf($problem)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftDropProblems($problem);
    }

    /**
     * @Given /^I select a Left Drops of "([^"]*)"$/
     */
    public function iSelectALeftDropsOf($drops)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftDrops($drops);
    }

    /**
     * @Given /^I select a Left Surgery of "([^"]*)"$/
     */
    public function iSelectALeftSurgeryOf($surgery)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftSurgery($surgery);
    }

    /**
     * @Given /^I select a Right Glaucoma Status of "([^"]*)"$/
     */
    public function iSelectARightGlaucomaStatusOf($status)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightGlaucomaStatus($status);
    }

    /**
     * @Given /^I select a Right Drop related problem of "([^"]*)"$/
     */
    public function iSelectARightDropRelatedProblemOf($problem)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightDropProblems($problem);
    }

    /**
     * @Given /^I select a Right Drops of "([^"]*)"$/
     */
    public function iSelectARightDropsOf($drops)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightDrops($drops);
    }

    /**
     * @Given /^I select a Right Surgery of "([^"]*)"$/
     */
    public function iSelectARightSurgeryOf($surgery)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightSurgery($surgery);
    }

    /**
     * @Given /^I choose a Right Risks of "([^"]*)"$/
     */
    public function iChooseARightRisksOf($risks)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightRisks($risks);
    }

    /**
     * @Then /^I choose Right Injection Management Comments of "([^"]*)"$/
     */
    public function iChooseRightInjectionManagementCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightInjectionComments($comments);
    }

    /**
     * @Given /^I choose a Left Risks of "([^"]*)"$/
     */
    public function iChooseALeftRisksOf($risks)
    {
        /**
         *
         * @var Examination $examination
         */
        sleep(5);
        $examination = $this->getPage('Examination');
        $examination->leftRisks($risks);
    }

    /**
     * @Then /^I choose Left Injection Management Comments of "([^"]*)"$/
     */
    public function iChooseLeftInjectionManagementCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftInjectionComments($comments);
    }

    /**
     * @Then /^I select a Left RAPD$/
     */
    public function iSelectALeftRapd()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftRAPD();
    }

    /**
     * @Given /^I add Left RAPD comments of "([^"]*)"$/
     */
    public function iAddLeftRapdCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftRAPDComments($comments);
    }

    /**
     * @Then /^I select a Right RAPD$/
     */
    public function iSelectARightRapd()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightRAPD();
    }

    /**
     * @Given /^I add Right RAPD comments of "([^"]*)"$/
     */
    public function iAddRightRapdCommentsOf($comments)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightRAPDComments($comments);
    }


    /**
     * @Given /^I choose a Left Colour Vision of "([^"]*)"$/
     */
    public function iChooseALeftColourVisionOf($vision)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftColourVision($vision);
    }

    /**
     * @Given /^I choose A Left Colour Vision Value of "([^"]*)"$/
     */
    public function iChooseALeftColourVisionValueOf($value)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->leftColourVisionValue($value);
    }

    /**
     * @Given /^I choose a Right Colour Vision of "([^"]*)"$/
     */
    public function iChooseARightColourVisionOf($vision)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightColourVision($vision);
    }

    /**
     * @Given /^I choose A Right Colour Vision Value of "([^"]*)"$/
     */
    public function iChooseARightColourVisionValueOf($value)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->rightColourVisionValue($value);
    }

    /**
     * @Then /^I add the changes to left eye$/
     */
    public function iAddTheChangesToLeftEye()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->$this->iAddTheChangesToLeftEye();

        //throw new PendingException();
    }

    /**
     * @Then /^I click on Right Eye PCR Risk$/
     */
    public function iClickOnRightEyePCRRISK()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clickOnRightEyePCRRISK();
    }

    /**
     * @Then /^I click on Left Eye PCR Risk$/
     */
    public function iClickOnLeftEyePCRRISK()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clickOnLeftEyePCRRISK();
    }

    /**
     * @Then /^I should see reference link on PCR Right Eye block$/
     */
    public function iShouldSeeReferenceLinkOnPCRRightEyeBlock()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referenceLinkOnPCRRightEyeBlock();
    }

    /**
     * @Then /^I should see reference link on PCR Left Eye block$/
     */
    public function iShouldSeeReferenceLinkOnPCRLeftEyeBlock()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->referenceLinkOnPCRLeftEyeBlock();
    }

    /**
     * @Then /^I click on reference link on PCR Right Eye block$/
     */
    public function iClickOnReferenceLinkOnPCRRightEyeBlock()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clickOnReferenceLinkOnPCRRightEyeBlock();
    }

    /**
     * @Then /^I click on reference link on PCR Left Eye block$/
     */
    public function iClickOnReferenceLinkOnPCRLeftEyeBlock()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->clickOnReferenceLinkOnPCRLeftEyeBlock();
    }

    /**
     * @Then /^I should see the reference Page$/
     */
    public function iShouldSeeTheReferencePage()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->onPCRReferencePage();
    }

    /**
     * @Then /^I should have the default PCR values$/
     */
    public function iShouldHaveDefaultPcrValues()
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->checkPcrDefaultValues();
    }

    /**
     * @Then /^I set the "([^"]*)" PCR option "([^"]*)" to be "([^"]*)"$/
     */
    public function iSetThePcrOptionTo($side, $option, $value)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->setPcrValue($side, $option, $value);
    }

    /**
     * @Then /^I should have a calculated "([^"]*)" PCR value of "([^"]*)"$/
     */
    public function iShouldHaveCalculatedPcrValue($side, $value)
    {
        /**
         *
         * @var Examination $examination
         */
        $examination = $this->getPage('Examination');
        $examination->checkPcrCalculatedValue($side, $value);
    }

    /**
     * @Given /^I expand "([^"]*)" sidebar$/
     */
    public function iExpandHistorySidebar()
    {
        throw new PendingException();
    }
}
