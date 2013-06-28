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


class FeatureContext extends MinkContext implements YiiAwareContextInterface
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

    /**
     * @BeforeStep
     * @AfterStep
     */
    public function waitForActionToFinish()
    {
        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
            try {
                $this->getSession()->wait(5000, "$.active == 0");
            } catch (\Exception $e) {}
        }
    }

    /**
     * @Given /^I am on the OpenEyes "([^"]*)" homepage$/
     */
    public function iAmOnTheOpeneyesHomepage($environment)
    {
        if (isset($this->environment[$environment])) {
            $this->visit($this->environment[$environment]);
        } else {
            throw new \Exception("Environment $environment doesn't exists");
        }

        //Clear cookies function required here

    }

    /**
     * @Given /^I select Site "([^"]*)"$/
     */
    public function iSelectSite($siteAddress)
    {
        $this->selectOption(OpenEyesPageObjects::$siteId,$siteAddress);

    }

    /**
     * @Given /^I enter login credentials "([^"]*)" and "([^"]*)"$/
     */
    public function iEnterLoginCredentialsAnd($user, $password)
    {
       DiagnosisPatient::$opthDiagnosis;
       $this->fillField(OpenEyesPageObjects::$login, $user );
       $this->fillField(OpenEyesPageObjects::$pass, $password);
    }

    /**
     * @Then /^I search for hospital number "([^"]*)"$/
     */
    public function iSearchForHospitalNumber($hospital)
    {
        $this->fillField(OpenEyesPageObjects::$mainSearch, $hospital);
        $this->clickLink(OpenEyesPageObjects::$searchSubmit);
    }

    /**
     * @Then /^I search for patient name last name "([^"]*)" and first name "([^"]*)"$/
     */
    public function iSearchForPatientNameLastNameAndFirstName ($first, $last)
    {
        $this->fillField(OpenEyesPageObjects::$mainSearch, $first );
        $this->fillField(OpenEyesPageObjects::$mainSearch, $last);
        $this->clickLink(OpenEyesPageObjects::$searchSubmit);
    }

    /**
     * @Then /^I search for NHS number "([^"]*)"$/
     */
    public function iSearchForNhsNumber($nhs)
    {
       $this->fillField(OpenEyesPageObjects::$mainSearch, $nhs);
       $this->clickLink(OpenEyesPageObjects::$searchSubmit);
    }

    /**
     * @Then /^I search a firm of "([^"]*)"$/
     */
    public function iselectAFirm($firm)
    {
        $this->clickLink(OpenEyesPageObjects::$firmDropdown,$firm);
    }

        /**
     * @Then /^I Add an Ophthalmic Diagnosis selection of "([^"]*)"$/
     */
    public function OphthalmicDiagnosisSelection($diagnosis)
    {
        $this->pressButton(OpenEyesPageObjects::$opthDiagnosis);
        $this->selectOption(OpenEyesPageObjects::$opthDisorder, $diagnosis);
        $this->removeDiagnosis++; //
    }

    /**
     * @Given /^I select that it affects eye "([^"]*)"$/
     */
    public function iSelectThatItAffectsEye($eye)
    {
        if ($eye=="Right") {
            $this->clickLink(OpenEyesPageObjects::$opthRighteye);
        }
        if ($eye=="Both") {
            $this->clickLink(OpenEyesPageObjects::$opthBotheyes);
        }
        if ($eye=="Left") {
            $this->clickLink(OpenEyesPageObjects::$opthLefteye);
        }
    }

    /**
     * @Given /^I select a Opthalmic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function OpthalmicDiagnosis($day, $month, $year)
    {
        $this->selectOption(OpenEyesPageObjects::$opthDay, $day);
        $this->selectOption(OpenEyesPageObjects::$opthMonth, $month);
        $this->selectOption(OpenEyesPageObjects::$opthYear, $year);
     }

    /**
     * @Then /^I save the new Opthalmic Diagnosis$/
     */
    public function iSaveTheNewOpthalmicDiagnosis()
    {
        $this->pressButton(OpenEyesPageObjects::$opthSaveButton);
    }

    /**
     * @Then /^I Add an Systemic Diagnosis selection of "([^"]*)"$/
     */
    public function SystemicDiagnosisSelection($systemic)
    {
        $this->pressButton(OpenEyesPageObjects::$sysDiagnosis);
        $this->selectOption(OpenEyesPageObjects::$sysDisorder, $systemic);
        $this->removeDiagnosis++;
    }

    /**
     * @Given /^I select that it affects side "([^"]*)"$/
     */
    public function AffectsSide($side)
    {
        if ($side=("None")) {
            $this->clickLink(OpenEyesPageObjects::$sysNoneSide);
        }
        if ($side=("Right")) {
            $this->clickLink(OpenEyesPageObjects::$sysRightSide);
        }
        if ($side=("Both")) {
            $this->clickLink(OpenEyesPageObjects::$sysBothSide);
        }
        if ($side=("Left")) {
            $this->clickLink(OpenEyesPageObjects::$sysLeftSide);
        }
    }

    /**
     * @Given /^I select a Systemic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iSelectASystemicDiagnosis($day, $month, $year)
    {
        $this->selectOption(OpenEyesPageObjects::$sysDay, $day);
        $this->selectOption(OpenEyesPageObjects::$sysMonth, $month);
        $this->selectOption(OpenEyesPageObjects::$sysYear, $year);
    }

    /**
     * @Then /^I save the new Systemic Diagnosis$/
     */
    public function iSaveTheNewSystemicDiagnosis()
    {
        $this->pressButton(OpenEyesPageObjects::$sysSaveButton);
    }

    /**
     * @Then /^I edit the CVI Status "([^"]*)" day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function CviStatus($status, $day, $month, $year)
    {
        $this->clickLink(OpenEyesPageObjects::$cviEdit);
        $this->selectOption(OpenEyesPageObjects::$cviStatus, $status);
        $this->selectOption(OpenEyesPageObjects::$cviDay, $day);
        $this->selectOption(OpenEyesPageObjects::$cviMonth, $month);
        $this->selectOption(OpenEyesPageObjects::$cviYear, $year);
        $this->clickLink(OpenEyesPageObjects::$cviSave);

    }

    /**
     * @Given /^I Add Medication details medication "([^"]*)" route "([^"]*)" frequency "([^"]*)" date from "([^"]*)"$/
     */
    public function iAddMedicationDetails($medication, $route, $frequency, $datefrom)
    {

        $this->clickLink(OpenEyesPageObjects::$addMedication);
        $this->selectOption(OpenEyesPageObjects::$medicationSelect, $medication);
        $this->waitForActionToFinish();
        $this->selectOption(OpenEyesPageObjects::$medicationRoute, $route);
        $this->selectOption(OpenEyesPageObjects::$medicationFrequency, $frequency);
        $this->clickLink(OpenEyesPageObjects::$medicationCalendar);
        $this->clickLink(OpenEyesPageObjects::passDateFromTable($datefrom));
        $this->clickLink(OpenEyesPageObjects::$medicationsave);
        $this->waitForActionToFinish();
        $this->removeMedication++;
    }

    /**
     * @Then /^I Add Allergy "([^"]*)"$/
     */
    public function iAddAllergy($allergy)
    {
        $this->selectOption(OpenEyesPageObjects::$selectAllergy, $allergy);
        $this->clickLink(OpenEyesPageObjects::$addAllergy);
        $this->waitForActionToFinish();
        $this->removeAllergy++;
    }

    /**
     * @Then /^I remove diagnosis test data$/
     */
     public function I_remove_diagnosis_test_data ()
     {

        echo "". $this->removeDiagnosis ." number of Diagnosis test data items to be removed";

        while ($this->removeDiagnosis) {
        $this->clickLink(OpenEyesPageObjects::$removediagnosislink);
        $this->waitForActionToFinish();
        $this->clickLink(OpenEyesPageObjects::$removediagnosis);
        $this->waitForActionToFinish();
        $this->removeDiagnosis--;
        }
     }

    /**
     * @Then /^I remove medication test data$/
     */
    public function I_remove_medication_test_data ()
    {

        echo "". $this->removeMedication ." number of Medication test data items to be removed";

        while ($this->removeMedication) {
            $this->clickLink(OpenEyesPageObjects::$removemedicationlink);
            $this->waitForActionToFinish();
            $this->clickLink(OpenEyesPageObjects::$removemedication);
            $this->waitForActionToFinish();
            $this->removeMedication--;
        }
    }

    /**
     * @Then /^I remove allergy test data$/
     */
    public function I_remove_allergy_test_data ()
    {

        echo "". $this->removeAllergy ." number of Allergy test data items to be removed";

        while ($this->removeAllergy) {
            $this->clickLink(OpenEyesPageObjects::$removeallergylink);
            $this->waitForActionToFinish();
            $this->clickLink(OpenEyesPageObjects::$removeallergy);
            $this->waitForActionToFinish();
            $this->removeAllergy--;
        }
    }

    /**
     * @Then /^I select Create or View Episodes and Events$/
     */
    public function iSelectCreateOrViewEpisodesAndEvents()
    {
        $this->clickLink(OpenEyesPageObjects::$createviewepisodeevent);
    }

    /**
     * @Given /^I add a New Event "([^"]*)"$/
     */
    public function iAddANewEvent($event)
    {
       $this->clickLink(OpenEyesPageObjects::$addnewevent);

       if ($event=="Satisfaction") {
          $this->clickLink(OpenEyesPageObjects::$anaestheticsatisfaction);
       }
       if ($event=="Consent") {
          $this->clickLink(OpenEyesPageObjects::$consentform);
       }
       if ($event=="Correspondence") {
          $this->clickLink(OpenEyesPageObjects::$correspondence);
       }
       if ($event=="Examination") {
          $this->clickLink(OpenEyesPageObjects::$examination);
       }
       if ($event=="OpBooking") {
          $this->clickLink(OpenEyesPageObjects::$operationbooking);
       }
       if ($event=="OpNote") {
          $this->clickLink(OpenEyesPageObjects::$operationnote);
       }
       if ($event=="Phasing") {
          $this->clickLink(OpenEyesPageObjects::$phasing);
       }
       if ($event=="Prescription") {
          $this->clickLink(OpenEyesPageObjects::$prescription);
       }
    }

    /**
     * @Then /^I select Diagnosis Eyes of "([^"]*)"$/
     */
    public function iSelectDiagnosisEyesOf($eye)
    {
        if ($eye=="Right") {
            $this->clickLink(OpenEyesPageObjects::$diagnosisrighteye);
        }
        if ($eye=="Both") {
            $this->clickLink(OpenEyesPageObjects::$diagnosisbotheyes);
        }
        if ($eye=="Left") {
            $this->clickLink(OpenEyesPageObjects::$diagnosislefteye);
        }
    }

    /**
     * @Given /^I select a Diagnosis of "([^"]*)"$/
     */
    public function iSelectADiagnosisOf($diagnosis)
    {
          $this->selectOption(OpenEyesPageObjects::$operationdiagnosis, $diagnosis);
    }

    /**
     * @Then /^I select Operation Eyes of "([^"]*)"$/
     */
    public function iSelectOperationEyesOf($opEyes)
    {
        if ($opEyes=="Right") {
            $this->clickLink(OpenEyesPageObjects::$operationRightEye);
        }
        if ($opEyes=="Both") {
            $this->clickLink(OpenEyesPageObjects::$operationBothEyes);
        }
        if ($opEyes=="Left") {
            $this->clickLink(OpenEyesPageObjects::$operationLeftEye);
        }
    }

    /**
     * @Given /^I select a Procedure of "([^"]*)"$/
     */
    public function iSelectAProcedureOf($procedure)
    {
        $this->selectOption(OpenEyesPageObjects::$operationprocedure, $procedure);
    }

    /**
     * @Then /^I select Yes to Consultant required$/
     */
    public function iSelectYesToConsultantRequired()
    {
        $this->clickLink(OpenEyesPageObjects::$consultantyes);
    }

    /**
     * @Then /^I select No to Consultant required$/
     */
    public function iSelectNoToConsultantRequired()
    {
        $this->clickLink(OpenEyesPageObjects::$consultantno);
    }

    /**
     * @Given /^I select a Anaesthetic type "([^"]*)"$/
     */
    public function iSelectAAnaestheticType($type)
    {
        if ($type=="Topical") {
            $this->clickLink(OpenEyesPageObjects::$anaesthetictopical);
        }
        if ($type=="LA") {
            $this->clickLink(OpenEyesPageObjects::$anaestheticla);
        }
        if ($type=="LAC") {
            $this->clickLink(OpenEyesPageObjects::$anaestheticlac);
        }
        if ($type=="LAS") {
            $this->clickLink(OpenEyesPageObjects::$anaestheticlas);
        }
        if ($type=="GA") {
            $this->clickLink(OpenEyesPageObjects::$anaestheticga);
        }
    }

    /**
     * @Then /^I select Yes to a Post Operative Stay$/
     */
    public function iSelectYesToAPostOperativeStay()
    {
        $this->clickLink(OpenEyesPageObjects::$postopstayyes);
    }

    /**
     * @Then /^I select No to a Post Operative Stay$/
     */
    public function iSelectNoToAPostOperativeStay()
    {
        $this->clickLink(OpenEyesPageObjects::$postopstayno);
    }

    /**
     * @Given /^I select a Operation Site of "([^"]*)"$/
     */
    public function iSelectAOperationSiteOf($site)
    {
        $this->selectOption(OpenEyesPageObjects::$operationsite, $site);
    }

    /**
     * @Then /^I select a Priority of Routine$/
     */
    public function iSelectAPriorityOfRoutine()
    {
        $this->clickLink(OpenEyesPageObjects::$routineoperation);
    }

    /**
     * @Then /^I select a Priority of Urgent$/
     */
    public function iSelectAPriorityOfUrgent()
    {
        $this->clickLink(OpenEyesPageObjects::$urgentoperation);
    }

    /**
     * @Given /^I select a decision date of "([^"]*)"$/
     */
    public function iSelectADecisionDateOf($datefrom)
    {
        $this->clickLink(OpenEyesPageObjects::$decisionopen);
        $this->clickLink(OpenEyesPageObjects::passDateFromTable($datefrom));
    }

    /**
     * @Then /^I add comments of "([^"]*)"$/
     */
    public function iAddCommentsOf($comments)
    {
        $this->fillField(OpenEyesPageObjects::$addcomments, $comments);
    }

    /**
     * @Then /^I select Save and Schedule later$/
     */
    public function iSelectSaveAndScheduleLater()
    {
        $this->clickLink(OpenEyesPageObjects::$schedulelater);
    }

    /**
     * @Then /^I select Save and Schedule now$/
     */
    public function iSelectSaveAndScheduleNow()
    {
        $this->clickLink(OpenEyesPageObjects::$scheduleandsavenow);
    }

    /**
     * @Given /^I select an Available theatre slot date$/
     */
    public function iSelectAnAvailableTheatreSlotDate()
    {
        $this->clickLink(OpenEyesPageObjects::$theatresessiondate);
    }

    /**
     * @Given /^I select an Available session time$/
     */
    public function iSelectAnAvailableSessionTime()
    {
        $this->clickLink(OpenEyesPageObjects::$theatresessiontime);
    }

    /**
     * @Then /^I add Session comments of "([^"]*)"$/
     */
    public function iAddSessionCommentsOf($sessionComments)
    {
        //As this field has existing text we need a function to Clear Field
        $this->fillField(OpenEyesPageObjects::$sessioncomments, $sessionComments);
    }

    /**
     * @Given /^I add Operation comments of "([^"]*)"$/
     */
    public function iAddOperationCommentsOf($opComments)
    {
        $this->fillField(OpenEyesPageObjects::$operationcomments, $opComments);
    }

    /**
     * @Then /^I confirm the operation slot$/
     */
    public function iConfirmTheOperationSlot()
    {
        $this->clickLink(OpenEyesPageObjects::$confirmslot);
    }

    /**
     * @Then /^I select an Anaesthetist "([^"]*)"$/
     */
    public function iSelectAnAnaesthetist($select)
    {
        $this->selectOption(OpenEyesPageObjects::$anaesthetist,$select);
    }

    /**
     * @Given /^I select Satisfaction levels of Pain "([^"]*)" Nausea "([^"]*)"$/
     * @And /^I select Satisfaction levels of Pain "([^"]*)" Nausea "([^"]*)"$/
     */
    public function iSelectSatisfactionLevelsOfPainNausea($pain, $nausea)
    {
        //Need to clear these two text fields
        $this->fillField(OpenEyesPageObjects::$nausea,$nausea);
        $this->fillField(OpenEyesPageObjects::$pain, $pain);
    }

    /**
     * @Given /^I tick the Vomited checkbox$/
     * @And /^I tick the Vomited checkbox$/
     */
    public function iTickTheVomitedCheckbox()
    {
        $this->checkOption(OpenEyesPageObjects::$vomitcheckbox);
    }

    /**
     * @And /^I untick the Vomited checkbox$/
     */
    public function iUntickTheVomitedCheckbox()
    {
        $this->uncheckOption(OpenEyesPageObjects::$vomitcheckbox);
    }

    /**
     * @Then /^I select Vital Signs of Respiratory Rate "([^"]*)" Oxygen Saturation "([^"]*)" Systolic Blood Pressure "([^"]*)"$/
     */
    public function iSelectVitalSigns($rate, $oxygen, $pressure)
    {
        $this->selectOption(OpenEyesPageObjects::$respirotaryrate, $rate);
        $this->selectOption(OpenEyesPageObjects::$oxygensaturation, $oxygen);
        $this->selectOption(OpenEyesPageObjects::$systolicbloodpressure, $pressure);
    }

    /**
     * @Then /^I select Vital Signs of Body Temperature "([^"]*)" and Heart Rate "([^"]*)" Conscious Level AVPU "([^"]*)"$/
     */
    public function iSelectVitalSignsTemp($temp, $rate, $avpu)
    {
        $this->selectOption(OpenEyesPageObjects::$bodytemp, $temp);
        $this->selectOption(OpenEyesPageObjects::$heartrate, $rate);
        $this->selectOption(OpenEyesPageObjects::$consciouslevelavpu, $avpu);
    }

    /**
     * @Then /^I enter Comments "([^"]*)"$/
     */
    public function iEnterComments($comments)
    {
        $this->fillField(OpenEyesPageObjects::$comments, comments);
    }

    /**
     * @And /^I select the Yes option for Ready to Discharge$/
     */
    public function iSelectTheYesOptionForReadyToDischarge()
    {
        $this->clickLink(OpenEyesPageObjects::$dischargeyes);
    }

    /**
     * @Given /^I select the No option for Read to Discharge$/
     * @And /^I select the No option for Read to Discharge$/
     */
    public function iSelectTheNoOptionForReadToDischarge()
    {
       $this->clickLink(OpenEyesPageObjects::$dischargeno);
    }

    /**
     * @Then /^I Save the Event$/
     */
    public function iSaveTheEvent()
    {
       $this->clickLink(OpenEyesPageObjects::$saveexamination);
    }

    /**
     * @Then /^I Cancel the Event$/
     */
    public function iCancelTheEvent()
    {
       $this->clickLink(OpenEyesPageObjects::$cancelevent);
    }

    /**
     * @Then /^I select a Common Drug "([^"]*)"$/
     */
    public function iSelectACommonDrug($drug)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptiondropdown, $drug);
    }

    /**
     * @Given /^I select a Standard Set of "([^"]*)"$/
     */
    public function iSelectAStandardSetOf($set)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptionstandardset, $set);
    }

    /**
     * @Then /^I enter a Dose of "([^"]*)" drops$/
     */
    public function iEnterADoseOfDrops($drops)
    {
       //Clear field required here
       $this->fillField(OpenEyesPageObjects::$prescriptiondose, $drops);
    }

    /**
     * @Given /^I enter a route of "([^"]*)"$/
     */
    public function iEnterARouteOf($route)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptionroute, $route);
    }

    /**
     * @Then /^I enter a eyes option "([^"]*)"$/
     */
    public function iEnterAEyesOption($eyes)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptionoptions, $eyes);
    }

    /**
     * @Given /^I enter a frequency of "([^"]*)"$/
     */
    public function iEnterAFrequencyOf($frequency)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptionfrequency, $frequency);
    }

    /**
     * @Then /^I enter a duration of "([^"]*)"$/
     */
    public function iEnterADurationOf($duration)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptionduration, $duration);
    }

    /**
     * @Given /^I add Prescription comments of "([^"]*)"$/
     */
    public function iAddPrescriptionCommentsOf($comments)
    {
       $this->selectOption(OpenEyesPageObjects::$prescriptioncomments, $comments);
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function RightEyeIntraocular($righteye)
    {
       $this->selectOption(OpenEyesPageObjects::$phasinginstrumentright, $righteye);
    }

    /**
     * @Given /^I choose right eye Dilation of "([^"]*)"$/
     */
    public function iChooseRightEyeDilationOf($dilation)
    {
        $this->clickLink(OpenEyesPageObjects::$phasingdilationright);
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureReadingOf($righteye)
    {
        $this->fillField(OpenEyesPageObjects::$phasingpressureleft, $righteye);
    }

    /**
     * @Given /^I add right eye comments of "([^"]*)"$/
     */
    public function iAddRightEyeCommentsOf($comments)
    {
        $this->fillField(OpenEyesPageObjects::$phasingcommentsright, $comments);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureInstrumentOf($lefteye)
    {
        $this->selectOption(OpenEyesPageObjects::$phasinginstrumentleft,$lefteye);
    }

    /**
     * @Given /^I choose left eye Dilation of "([^"]*)"$/
     */
    public function iChooseLeftEyeDilationOf($dilation)
    {
        $this->clickLink(OpenEyesPageObjects::$dilationleft);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureReadingOf($lefteye)
    {
       $this->fillField(OpenEyesPageObjects::$phasingpressureright, $lefteye);
    }

    /**
     * @Given /^I add left eye comments of "([^"]*)"$/
     */
    public function iAddLeftEyeCommentsOf($comments)
    {
        $this->fillField(OpenEyesPageObjects::$phasingcommentsleft, $comments);
    }

    /**
     * @Then /^I Save the Phasing Event$/
     */
    public function iSaveThePhasingEvent()
    {
        $this->clickLink(OpenEyesPageObjects::$saveexamination);
    }

    /**
     * @Then /^I select a History of Blurred Vision, Mild Severity, Onset (\d+) Week, Left Eye, (\d+) Week$/
     */
    public function iSelectAHistoryOfBlurredVision($notused, $orthisone)
    {
        $this->clickLink(OpenEyesPageObjects::$history);
        $this->clickLink(OpenEyesPageObjects::$severity);
        $this->clickLink(OpenEyesPageObjects::$onset);
        $this->clickLink(OpenEyesPageObjects::$eye);
        $this->clickLink(OpenEyesPageObjects::$duration);
    }

    /**
     * @Given /^I choose to expand the Comorbidities section$/
     */
    public function iChooseToExpandTheComorbiditiesSection()
    {
        $this->clickLink(OpenEyesPageObjects::$opencomorbidities);
    }

    /**
     * @Then /^I Add a Comorbiditiy of "([^"]*)"$/
     */
    public function iAddAComorbiditiyOf($com)
    {
        $this->selectOption(OpenEyesPageObjects::$addcomorbidities, $com);
    }

    /**
     * @Then /^I choose to expand the Visual Acuity section$/
     */
    public function iChooseToExpandTheVisualAcuitySection()
    {
        $this->clickLink(OpenEyesPageObjects::$openVisualAcuity);
    }

    /**
     * @Then /^I choose a left Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function SnellenMetreAndAReading($metre, $method)
    {
        $this->clickLink(OpenEyesPageObjects::$openleftva);
        $this->selectOption(OpenEyesPageObjects::$snellenleft, $metre);
        $this->selectOption(OpenEyesPageObjects::$readingleft, $method);
    }

    /**
     * @Then /^I choose a right Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function RightVisualAcuitySnellenMetre($metre, $method)
    {
        $this->clickLink(OpenEyesPageObjects::$openrightva);
        $this->selectOption(OpenEyesPageObjects::$snellenright, $metre);
        $this->selectOption(OpenEyesPageObjects::$readingright, $method);
    }

    /**
     * @Then /^I choose to expand the Intraocular Pressure section$/
     */
    public function iChooseToExpandTheIntraocularPressureSection()
    {
        $this->clickLink(OpenEyesPageObjects::$openIntraocularPressure);
    }

    /**
     * @Then /^I choose a left Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseALeftIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        $this->selectOption(OpenEyesPageObjects::$intraocularright, $pressure);
        $this->selectOption(OpenEyesPageObjects::$instrumentright, $instrument);
    }

    /**
     * @Then /^I choose a right Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseARightIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        $this->selectOption(OpenEyesPageObjects::$intraocularleft, $pressure);
        $this->selectOption(OpenEyesPageObjects::$instrumentleft, $instrument);
    }

    /**
     * @Then /^I choose to expand the Dilation section$/
     */
    public function iChooseToExpandTheDilationSection()
    {
        $this->clickLink(OpenEyesPageObjects::$openDilation);
    }

    /**
     * @Then /^I choose left Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseLeftDilationOfAndDropsOf($dilation, $drops)
    {
        $this->selectOption(OpenEyesPageObjects::$dilationleft, $dilation);
        $this->selectOption(OpenEyesPageObjects::$dropsleft, $drops);
    }

    /**
     * @Then /^I choose right Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseRightDilationOfAndDropsOf($dilation, $drops)
    {
        $this->selectOption(OpenEyesPageObjects::$dilationright, $dilation);
        $this->selectOption(OpenEyesPageObjects::$dropsright, $drops);
    }

    /**
     * @Then /^I choose to expand the Refraction section$/
     */
    public function iChooseToExpandTheRefractionSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandrefraction);
    }

    /**
     * @Then /^I enter left Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function LeftRefractionDetails($sphere, $integer, $fraction)
    {
        $this->selectOption(OpenEyesPageObjects::$sphereright, $sphere);
        $this->selectOption(OpenEyesPageObjects::$sphererightint, $integer);
        $this->selectOption(OpenEyesPageObjects::$sphererightfraction, $fraction);
    }

    /**
     * @Given /^I enter left cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterLeftCylinderDetails($cylinder, $integer, $fraction)
    {
        $this->selectOption(OpenEyesPageObjects::$cylinderleft, $cylinder);
        $this->selectOption(OpenEyesPageObjects::$cylinderleftint, $integer);
        $this->selectOption(OpenEyesPageObjects::$cylinderleftfraction, $fraction);
    }

    /**
     * @Then /^I enter left Axis degrees of "([^"]*)"$/
     */
    public function iEnterLeftAxisDegreesOf($axis)
    {
        //We need a Clear Field function here
        $this->fillField(OpenEyesPageObjects::$sphereleftaxis, $axis);
        //We need to Press the tab key here
    }

    /**
     * @Given /^I enter a left type of "([^"]*)"$/
     */
    public function iEnterALeftTypeOf($type)
    {
        $this->selectOption(OpenEyesPageObjects::$spherelefttype, $type);
    }

    /**
     * @Then /^I enter right Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightRefractionDetailsOfSphereIntegerFraction($sphere, $integer, $fraction)
    {
        $this->selectOption(OpenEyesPageObjects::$sphereright, $sphere);
        $this->selectOption(OpenEyesPageObjects::$sphererightint, $integer);
        $this->selectOption(OpenEyesPageObjects::$sphererightfraction, $fraction);
    }

    /**
     * @Given /^I enter right cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightCylinderDetailsOfOfCylinderIntegerFraction($cylinder, $integer, $fraction)
    {
        $this->selectOption(OpenEyesPageObjects::$cylinderright, $cylinder);
        $this->selectOption(OpenEyesPageObjects::$cylinderrightint, $integer);
        $this->selectOption(OpenEyesPageObjects::$cylinderrightfraction, $fraction);
    }

    /**
     * @Then /^I enter right Axis degrees of "([^"]*)"$/
     */
    public function iEnterRightAxisDegreesOf($axis)
    {
        //We need a Clear Field function here
        $this->fillField(OpenEyesPageObjects::$sphererightaxis, $axis);
        //We need to Press the tab key here
    }

    /**
     * @Given /^I enter a right type of "([^"]*)"$/
     */
    public function iEnterARightTypeOf($type)
    {
        $this->selectOption(OpenEyesPageObjects::$sphererighttype, $type);
    }

    /**
     * @Then /^I choose to expand the Gonioscopy section$/
     */
    public function iChooseToExpandTheGonioscopySection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandgonioscopy);
    }

    /**
     * @Then /^I choose to expand the Adnexal Comorbidity section$/
     */
    public function iChooseToExpandTheAdnexalComorbiditySection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandadnexalcomorbidity);
    }

    /**
     * @Then /^I choose to expand the Anterior Segment section$/
     */
    public function iChooseToExpandTheAnteriorSegmentSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandanteriorsegment);
    }

    /**
     * @Then /^I choose to expand the Pupillary Abnormalities section$/
     */
    public function iChooseToExpandThePupillaryAbnormalitiesSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandpupillaryabnormalities);
    }

    /**
     * @Then /^I choose to expand the Optic Disc section$/
     */
    public function iChooseToExpandTheOpticDiscSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandopticdisc);
    }

    /**
     * @Then /^I choose to expand the Posterior Pole section$/
     */
    public function iChooseToExpandThePosteriorPoleSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandposteriorpole);
    }

    /**
     * @Then /^I choose to expand the Diagnoses section$/
     */
    public function iChooseToExpandTheDiagnosesSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expanddiagnoses);
    }

    /**
     * @Then /^I choose to expand the Investigation section$/
     */
    public function iChooseToExpandTheInvestigationSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandinvestigation);
    }

    /**
     * @Then /^I choose to expand the Clinical Management section$/
     */
    public function iChooseToExpandTheClinicalManagementSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandclinicalmanagement);
    }

    /**
     * @Then /^I choose to expand the Risks section$/
     */
    public function iChooseToExpandTheRisksSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandrisks);
    }

    /**
     * @Then /^I choose to expand the Clinic Outcome section$/
     */
    public function iChooseToExpandTheClinicOutcomeSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandclinicoutcome);
    }

    /**
     * @Then /^I choose to expand the Conclusion section$/
     */
    public function iChooseToExpandTheConclusionSection()
    {
        $this->clickLink(OpenEyesPageObjects::$expandconclusion);
    }

    /**
     * @Then /^I Save the Examination$/
     */
    public function iSaveTheExamination()
    {
        $this->clickLink(OpenEyesPageObjects::$saveexamination);
    }

    /**
     * @Then /^I Cancel the Examination$/
     */
    public function iCancelTheExamination()
    {
        $this->clickLink(OpenEyesPageObjects::$cancelexam);
    }

    /**
     * @Then /^I select Site ID "([^"]*)"$/
     */
    public function iSelectSiteId($site)
    {
        $this->selectOption(OpenEyesPageObjects::$sitedropdown, $site);
    }

    /**
     * @Given /^I select Address Target "([^"]*)"$/
     */
    public function iSelectAddressTarget($address)
    {
       $this->selectOption(OpenEyesPageObjects::$addresstarget, address);
    }

    /**
     * @Then /^I choose a Macro of "([^"]*)"$/
     */
    public function iChooseAMacroOf($macro)
    {
       $this->selectOption(OpenEyesPageObjects::$macro, macro);
    }

    /**
     * @Given /^I select Clinic Date "([^"]*)"$/
     */
    public function iSelectClinicDate($date)
    {
        $this->clickLink(OpenEyesPageObjects::$letterdate);
        $this->clickLink(OpenEyesPageObjects::passDateFromTable(datefrom));
    }

    /**
     * @Then /^I choose an Introduction of "([^"]*)"$/
     */
    public function iChooseAnIntroductionOf($intro)
    {
        $this->selectOption(OpenEyesPageObjects::$introduction, $intro);
    }

    /**
     * @Given /^I choose a Diagnosis of "([^"]*)"$/
     */
    public function iChooseADiagnosisOf($diagnosis)
    {
        $this->selectOption(OpenEyesPageObjects::$diagnosis, $diagnosis);
    }

    /**
     * @Then /^I choose a Management of "([^"]*)"$/
     */
    public function iChooseAManagementOf($management)
    {
        $this->selectOption(OpenEyesPageObjects::$management, $management);
    }

    /**
     * @Given /^I choose Drugs "([^"]*)"$/
     */
    public function iChooseDrugs($drugs)
    {
        $this->selectOption(OpenEyesPageObjects::$drugs, drugs);
    }

    /**
     * @Then /^I choose Outcome "([^"]*)"$/
     */
    public function iChooseOutcome($outcome)
    {
        $this->selectOption(OpenEyesPageObjects::$outcome, $outcome);
    }

    /**
     * @Given /^I choose CC Target "([^"]*)"$/
     */
    public function iChooseCcTarget($cc)
    {
        $this->selectOption(OpenEyesPageObjects::$lettercc, cc);
    }

    /**
     * @Given /^I add a New Enclosure$/
     */
    public function iAddANewEnclosure()
    {
        $this->clickLink(OpenEyesPageObjects::$addenclosure);
    }


    /**
     * @Then /^I choose to close the browser$/
     */
    public function iChooseToCloseTheBrowser()
    {

    }
}
