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

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

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
        $this->selectOption(Login::$siteId,$siteAddress);
    }

    /**
     * @Given /^I enter login credentials "([^"]*)" and "([^"]*)"$/
     */
    public function iEnterLoginCredentialsAnd($user, $password)
    {
       PatientViewPage::$opthDiagnosis;
       $this->fillField(Login::$login, $user );
       $this->fillField(Login::$pass, $password);
    }

    /**
     * @Then /^I search for hospital number "([^"]*)"$/
     */
    public function iSearchForHospitalNumber($hospital)
    {
        $this->fillField(Login::$mainSearch, $hospital);
        $this->clickLink(Login::$searchSubmit);
    }

    /**
     * @Then /^I search for patient name last name "([^"]*)" and first name"([^"]*)"$/
     */
    public function iSearchPatientName ($first, $last)
    {
        $this->fillField(Login::$mainSearch, $first );
        $this->fillField(Login::$mainSearch, $last);
        $this->clickLink(Login::$searchSubmit);
    }

    /**
     * @Then /^I search for NHS number "([^"]*)"$/
     */
    public function iSearchForNhsNumber($nhs)
    {
       $this->fillField(Login::$mainSearch, $nhs);
       $this->clickLink(Login::$searchSubmit);
    }

    /**
     * @Then /^I search a firm of "([^"]*)"$/
     */
    public function iselectAFirm($firm)
    {
        $this->clickLink(Login::$firmDropdown,$firm);
    }

        /**
     * @Then /^I Add an Ophthalmic Diagnosis selection of "([^"]*)"$/
     */
    public function OphthalmicDiagnosisSelection($diagnosis)
    {
        $this->pressButton(PatientViewPage::$opthDiagnosis);
        $this->selectOption(PatientViewPage::$opthDisorder, $diagnosis);
        $this->removeDiagnosis++; //
    }

    /**
     * @Given /^I select that it affects eye "([^"]*)"$/
     */
    public function iSelectThatItAffectsEye($eye)
    {
        if ($eye=="Right") {
            $this->clickLink(PatientViewPage::$opthRighteye);
        }
        if ($eye=="Both") {
            $this->clickLink(PatientViewPage::$opthBotheyes);
        }
        if ($eye=="Left") {
            $this->clickLink(PatientViewPage::$opthLefteye);
        }
    }

    /**
     * @Given /^I select a Opthalmic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function OpthalmicDiagnosis($day, $month, $year)
    {
        $this->selectOption(PatientViewPage::$opthDay, $day);
        $this->selectOption(PatientViewPage::$opthMonth, $month);
        $this->selectOption(PatientViewPage::$opthYear, $year);
     }

    /**
     * @Then /^I save the new Opthalmic Diagnosis$/
     */
    public function iSaveTheNewOpthalmicDiagnosis()
    {
        $this->pressButton(PatientViewPage::$opthSaveButton);
    }

    /**
     * @Then /^I Add an Systemic Diagnosis selection of "([^"]*)"$/
     */
    public function SystemicDiagnosisSelection($systemic)
    {
        $this->pressButton(PatientViewPage::$sysDiagnosis);
        $this->selectOption(PatientViewPage::$sysDisorder, $systemic);
        $this->removeDiagnosis++;
    }

    /**
     * @Given /^I select that it affects side "([^"]*)"$/
     */
    public function AffectsSide($side)
    {
        if ($side=("None")) {
            $this->clickLink(PatientViewPage::$sysNoneSide);
        }
        if ($side=("Right")) {
            $this->clickLink(PatientViewPage::$sysRightSide);
        }
        if ($side=("Both")) {
            $this->clickLink(PatientViewPage::$sysBothSide);
        }
        if ($side=("Left")) {
            $this->clickLink(PatientViewPage::$sysLeftSide);
        }
    }

    /**
     * @Given /^I select a Systemic Diagnosis date of day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function iSelectASystemicDiagnosis($day, $month, $year)
    {
        $this->selectOption(PatientViewPage::$sysDay, $day);
        $this->selectOption(PatientViewPage::$sysMonth, $month);
        $this->selectOption(PatientViewPage::$sysYear, $year);
    }

    /**
     * @Then /^I save the new Systemic Diagnosis$/
     */
    public function iSaveTheNewSystemicDiagnosis()
    {
        $this->pressButton(PatientViewPage::$sysSaveButton);
    }

    /**
     * @Then /^I edit the CVI Status "([^"]*)" day "([^"]*)" month "([^"]*)" year "([^"]*)"$/
     */
    public function CviStatus($status, $day, $month, $year)
    {
        $this->clickLink(PatientViewPage::$cviEdit);
        $this->selectOption(PatientViewPage::$cviStatus, $status);
        $this->selectOption(PatientViewPage::$cviDay, $day);
        $this->selectOption(PatientViewPage::$cviMonth, $month);
        $this->selectOption(PatientViewPage::$cviYear, $year);
        $this->clickLink(PatientViewPage::$cviSave);
    }

    /**
     * @Given /^I Add Medication details medication "([^"]*)" route "([^"]*)" frequency "([^"]*)" date from "([^"]*)"$/
     */
    public function iAddMedicationDetails($medication, $route, $frequency, $datefrom)
    {
        $this->clickLink(PatientViewPage::$addMedication);
        $this->selectOption(PatientViewPage::$medicationSelect, $medication);
        $this->waitForActionToFinish();
        $this->selectOption(PatientViewPage::$medicationRoute, $route);
        $this->selectOption(PatientViewPage::$medicationFrequency, $frequency);
        $this->clickLink(PatientViewPage::$medicationCalendar);
        $this->clickLink(PatientViewPage::passDateFromTable($datefrom));
        $this->clickLink(PatientViewPage::$medicationSave);
        $this->waitForActionToFinish();
        $this->removeMedication++;
    }

    /**
     * @Then /^I Add Allergy "([^"]*)"$/
     */
    public function iAddAllergy($allergy)
    {
        $this->selectOption(PatientViewPage::$selectAllergy, $allergy);
        $this->clickLink(PatientViewPage::$addAllergy);
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
        $this->clickLink(PatientViewPage::$removeDiagnosisLink);
        $this->waitForActionToFinish();
        $this->clickLink(PatientViewPage::$removeDiagnosis);
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
            $this->clickLink(PatientViewPage::$removeMedicationLink);
            $this->waitForActionToFinish();
            $this->clickLink(PatientViewPage::$removeMedication);
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
            $this->clickLink(PatientViewPage::$removeAllergyLink);
            $this->waitForActionToFinish();
            $this->clickLink(PatientViewPage::$removeAllergy);
            $this->waitForActionToFinish();
            $this->removeAllergy--;
        }
    }

    /**
     * @Then /^I select Create or View Episodes and Events$/
     */
    public function iSelectCreateOrViewEpisodesAndEvents()
    {
        $this->clickLink(AddingNewEvent::$createViewEpisodeEvent);
    }

    /**
     * @Given /^I add a New Event "([^"]*)"$/
     */
    public function iAddANewEvent($event)
    {
       $this->clickLink(AddingNewEvent::$addNewEvent);

       if ($event=="Satisfaction") {
          $this->clickLink(AddingNewEvent::$anaestheticSatisfaction);
       }
       if ($event=="Consent") {
          $this->clickLink(AddingNewEvent::$consentForm);
       }
       if ($event=="Correspondence") {
          $this->clickLink(AddingNewEvent::$correspondence);
       }
       if ($event=="Examination") {
          $this->clickLink(AddingNewEvent::$examination);
       }
       if ($event=="OpBooking") {
          $this->clickLink(AddingNewEvent::$operationBooking);
       }
       if ($event=="OpNote") {
          $this->clickLink(AddingNewEvent::$operationNote);
       }
       if ($event=="Phasing") {
          $this->clickLink(AddingNewEvent::$phasing);
       }
       if ($event=="Prescription") {
          $this->clickLink(AddingNewEvent::$prescription);
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
          $this->selectOption(OperationBooking::$operationDiagnosis, $diagnosis);
    }

    /**
     * @Then /^I select Operation Eyes of "([^"]*)"$/
     */
    public function iSelectOperationEyesOf($opEyes)
    {
        if ($opEyes=="Right") {
            $this->clickLink(OperationBooking::$operationRightEye);
        }
        if ($opEyes=="Both") {
            $this->clickLink(OperationBooking::$operationBothEyes);
        }
        if ($opEyes=="Left") {
            $this->clickLink(OperationBooking::$operationLeftEye);
        }
    }

    /**
     * @Given /^I select a Procedure of "([^"]*)"$/
     */
    public function iSelectAProcedureOf($procedure)
    {
        $this->selectOption(OperationBooking::$operationProcedure, $procedure);
    }

    /**
     * @Then /^I select Yes to Consultant required$/
     */
    public function iSelectYesToConsultantRequired()
    {
        $this->clickLink(OperationBooking::$consultantYes);
    }

    /**
     * @Then /^I select No to Consultant required$/
     */
    public function iSelectNoToConsultantRequired()
    {
        $this->clickLink(OperationBooking::$consultantNo);
    }

    /**
     * @Given /^I select a Anaesthetic type "([^"]*)"$/
     */
    public function iSelectAAnaestheticType($type)
    {
        if ($type=="Topical") {
            $this->clickLink(OperationBooking::$anaestheticTopical);
        }
        if ($type=="LA") {
            $this->clickLink(OperationBooking::$anaestheticLa);
        }
        if ($type=="LAC") {
            $this->clickLink(OperationBooking::$anaestheticLac);
        }
        if ($type=="LAS") {
            $this->clickLink(OperationBooking::$anaestheticLas);
        }
        if ($type=="GA") {
            $this->clickLink(OperationBooking::$anaestheticGa);
        }
    }

    /**
     * @Then /^I select Yes to a Post Operative Stay$/
     */
    public function iSelectYesToAPostOperativeStay()
    {
        $this->clickLink(OperationBooking::$postOpStayYes);
    }

    /**
     * @Then /^I select No to a Post Operative Stay$/
     */
    public function iSelectNoToAPostOperativeStay()
    {
        $this->clickLink(OperationBooking::$postOpStayNo);
    }

    /**
     * @Given /^I select a Operation Site of "([^"]*)"$/
     */
    public function iSelectAOperationSiteOf($site)
    {
        $this->selectOption(OperationBooking::$operationSite, $site);
    }

    /**
     * @Then /^I select a Priority of Routine$/
     */
    public function iSelectAPriorityOfRoutine()
    {
        $this->clickLink(OperationBooking::$routineOperation);
    }

    /**
     * @Then /^I select a Priority of Urgent$/
     */
    public function iSelectAPriorityOfUrgent()
    {
        $this->clickLink(OperationBooking::$urgentOperation);
    }

    /**
     * @Given /^I select a decision date of "([^"]*)"$/
     */
    public function iSelectADecisionDateOf($datefrom)
    {
        $this->clickLink(OperationBooking::$decisionOpen);
        $this->clickLink(PatientViewPage::passDateFromTable($datefrom));
    }

    /**
     * @Then /^I add comments of "([^"]*)"$/
     */
    public function iAddCommentsOf($comments)
    {
        $this->fillField(OperationBooking::$addComments, $comments);
    }

    /**
     * @Then /^I select Save and Schedule later$/
     */
    public function iSelectSaveAndScheduleLater()
    {
        $this->clickLink(OperationBooking::$scheduleLater);
    }

    /**
     * @Then /^I select Save and Schedule now$/
     */
    public function iSelectSaveAndScheduleNow()
    {
        $this->clickLink(OperationBooking::$scheduleAndSaveNow);
    }

    /**
     * @Given /^I select an Available theatre slot date$/
     */
    public function iSelectAnAvailableTheatreSlotDate()
    {
        $this->clickLink(OperationBooking::$theatreSessionDate);
    }

    /**
     * @Given /^I select an Available session time$/
     */
    public function iSelectAnAvailableSessionTime()
    {
        $this->clickLink(OperationBooking::$theatreSessionTime);
    }

    /**
     * @Then /^I add Session comments of "([^"]*)"$/
     */
    public function iAddSessionCommentsOf($sessionComments)
    {
        //As this field has existing text we need a function to Clear Field
        $this->fillField(OperationBooking::$sessionComments, $sessionComments);
    }

    /**
     * @Given /^I add Operation comments of "([^"]*)"$/
     */
    public function iAddOperationCommentsOf($opComments)
    {
        $this->fillField(OperationBooking::$operationComments, $opComments);
    }

    /**
     * @Then /^I confirm the operation slot$/
     */
    public function iConfirmTheOperationSlot()
    {
        $this->clickLink(OperationBooking::$confirmSlot);
    }

    /**
     * @Then /^I select an Anaesthetist "([^"]*)"$/
     */
    public function iSelectAnAnaesthetist($select)
    {
        $this->selectOption(AnaestheticAudit::$anaesthetist,$select);
    }

    /**
     * @And /^I select Satisfaction levels of Pain "([^"]*)" Nausea "([^"]*)"$/
     */
    public function iSelectSatisfactionLevelsOfPainNausea($pain, $nausea)
    {
        //Need to clear these two text fields
        $this->fillField(AnaestheticAudit::$nausea,$nausea);
        $this->fillField(AnaestheticAudit::$pain, $pain);
    }

    /**
     * @Given /^I tick the Vomited checkbox$/
     * @And /^I tick the Vomited checkbox$/
     */
    public function iTickTheVomitedCheckbox()
    {
        $this->checkOption(AnaestheticAudit::$vomitCheckbox);
    }

    /**
     * @And /^I untick the Vomited checkbox$/
     */
    public function iUntickTheVomitedCheckbox()
    {
        $this->uncheckOption(AnaestheticAudit::$vomitCheckbox);
    }

    /**
     * @Then /^I select Vital Signs of Respiratory Rate "([^"]*)" Oxygen Saturation "([^"]*)" Systolic Blood Pressure "([^"]*)"$/
     */
    public function iSelectVitalSigns($rate, $oxygen, $pressure)
    {
        $this->selectOption(AnaestheticAudit::$respirotaryRate, $rate);
        $this->selectOption(AnaestheticAudit::$oxygenSaturation, $oxygen);
        $this->selectOption(AnaestheticAudit::$systolicBloodPressure, $pressure);
    }

    /**
     * @Then /^I select Vital Signs of Body Temperature "([^"]*)" and Heart Rate "([^"]*)" Conscious Level AVPU "([^"]*)"$/
     */
    public function iSelectVitalSignsTemp($temp, $rate, $avpu)
    {
        $this->selectOption(AnaestheticAudit::$bodyTemp, $temp);
        $this->selectOption(AnaestheticAudit::$heartRate, $rate);
        $this->selectOption(AnaestheticAudit::$consciousLevelAvpu, $avpu);
    }

    /**
     * @Then /^I enter Comments "([^"]*)"$/
     */
    public function iEnterComments($comments)
    {
        $this->fillField(AnaestheticAudit::$comments, $comments);
    }

    /**
     * @And /^I select the Yes option for Ready to Discharge$/
     */
    public function iSelectTheYesOptionForReadyToDischarge()
    {
        $this->clickLink(AnaestheticAudit::$dischargeYes);
    }

    /**
     * @And /^I select the No option for Read to Discharge$/
     */
    public function iSelectTheNoOptionForReadToDischarge()
    {
       $this->clickLink(AnaestheticAudit::$dischargeNo);
    }

    /**
     * @Then /^I Save the Event$/
     */
    public function iSaveTheEvent()
    {
       $this->clickLink(Examination::$saveExamination);
    }

    /**
     * @Then /^I Cancel the Event$/
     */
    public function iCancelTheEvent()
    {
       $this->clickLink(AnaestheticAudit::$cancelEvent);
    }

    /**
     * @Then /^I select a Common Drug "([^"]*)"$/
     */
    public function iSelectACommonDrug($drug)
    {
       $this->selectOption(Prescription::$prescriptionDropDown, $drug);
    }

    /**
     * @Given /^I select a Standard Set of "([^"]*)"$/
     */
    public function iSelectAStandardSetOf($set)
    {
       $this->selectOption(Prescription::$prescriptionStandardSet, $set);
    }

    /**
     * @Then /^I enter a Dose of "([^"]*)" drops$/
     */
    public function iEnterADoseOfDrops($drops)
    {
       //Clear field required here
       $this->fillField(Prescription::$prescriptionDose, $drops);
    }

    /**
     * @Given /^I enter a route of "([^"]*)"$/
     */
    public function iEnterARouteOf($route)
    {
       $this->selectOption(Prescription::$prescriptionRoute, $route);
    }

    /**
     * @Then /^I enter a eyes option "([^"]*)"$/
     */
    public function iEnterAEyesOption($eyes)
    {
       $this->selectOption(Prescription::$prescriptionOptions, $eyes);
    }

    /**
     * @Given /^I enter a frequency of "([^"]*)"$/
     */
    public function iEnterAFrequencyOf($frequency)
    {
       $this->selectOption(Prescription::$prescriptionFrequency, $frequency);
    }

    /**
     * @Then /^I enter a duration of "([^"]*)"$/
     */
    public function iEnterADurationOf($duration)
    {
       $this->selectOption(Prescription::$prescriptionDuration, $duration);
    }

    /**
     * @Given /^I add Prescription comments of "([^"]*)"$/
     */
    public function iAddPrescriptionCommentsOf($comments)
    {
       $this->selectOption(Prescription::$prescriptionComments, $comments);
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function RightEyeIntraocular($righteye)
    {
       $this->selectOption(Phasing::$phasingInstrumentRight, $righteye);
    }

    /**
     * @Given /^I choose right eye Dilation of "([^"]*)"$/
     */
    public function iChooseRightEyeDilationOf($dilation)
    {
        $this->clickLink(Phasing::$phasingDilationRight);
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureReadingOf($righteye)
    {
        $this->fillField(Phasing::$phasingPressureLeft, $righteye);
    }

    /**
     * @Given /^I add right eye comments of "([^"]*)"$/
     */
    public function iAddRightEyeCommentsOf($comments)
    {
        $this->fillField(Phasing::$phasingCommentsRight, $comments);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureInstrumentOf($lefteye)
    {
        $this->selectOption(Phasing::$phasingInstrumentLeft,$lefteye);
    }

    /**
     * @Given /^I choose left eye Dilation of "([^"]*)"$/
     */
    public function iChooseLeftEyeDilationOf($dilation)
    {
        $this->clickLink(Phasing::$phasingDilationLeft);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureReadingOf($lefteye)
    {
       $this->fillField(Phasing::$phasingPressureRight, $lefteye);
    }

    /**
     * @Given /^I add left eye comments of "([^"]*)"$/
     */
    public function iAddLeftEyeCommentsOf($comments)
    {
        $this->fillField(Phasing::$phasingCommentsLeft, $comments);
    }

    /**
     * @Then /^I Save the Phasing Event$/
     */
    public function iSaveThePhasingEvent()
    {
        $this->clickLink(Examination::$saveExamination);
    }

    /**
     * @Then /^I select a History of Blurred Vision, Mild Severity, Onset (\d+) Week, Left Eye, (\d+) Week$/
     */
    public function iSelectAHistoryOfBlurredVision($notused, $orthisone)
    {
        $this->clickLink(Examination::$history);
        $this->clickLink(Examination::$severity);
        $this->clickLink(Examination::$onset);
        $this->clickLink(Examination::$eye);
        $this->clickLink(Examination::$duration);
    }

    /**
     * @Given /^I choose to expand the Comorbidities section$/
     */
    public function iChooseToExpandTheComorbiditiesSection()
    {
        $this->clickLink(Examination::$openComorbidities);
    }

    /**
     * @Then /^I Add a Comorbiditiy of "([^"]*)"$/
     */
    public function iAddAComorbiditiyOf($com)
    {
        $this->selectOption(Examination::$addComorbidities, $com);
    }

    /**
     * @Then /^I choose to expand the Visual Acuity section$/
     */
    public function iChooseToExpandTheVisualAcuitySection()
    {
        $this->clickLink(Examination::$openVisualAcuity);
    }

    /**
     * @Then /^I choose a left Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function SnellenMetreAndAReading($metre, $method)
    {
        $this->clickLink(Examination::$openLeftVa);
        $this->selectOption(Examination::$snellenLeft, $metre);
        $this->selectOption(Examination::$readingLeft, $method);
    }

    /**
     * @Then /^I choose a right Visual Acuity Snellen Metre "([^"]*)" and a reading method of "([^"]*)"$/
     */
    public function RightVisualAcuitySnellenMetre($metre, $method)
    {
        $this->clickLink(Examination::$openRightVa);
        $this->selectOption(Examination::$snellenRight, $metre);
        $this->selectOption(Examination::$readingRight, $method);
    }

    /**
     * @Then /^I choose to expand the Intraocular Pressure section$/
     */
    public function iChooseToExpandTheIntraocularPressureSection()
    {
        $this->clickLink(Examination::$openIntraocularPressure);
    }

    /**
     * @Then /^I choose a left Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseALeftIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        $this->selectOption(Examination::$intraocularRight, $pressure);
        $this->selectOption(Examination::$instrumentRight, $instrument);
    }

    /**
     * @Then /^I choose a right Intraocular Pressure of "([^"]*)" and Instrument "([^"]*)"$/
     */
    public function iChooseARightIntraocularPressureOfAndInstrument($pressure, $instrument)
    {
        $this->selectOption(Examination::$intraocularLeft, $pressure);
        $this->selectOption(Examination::$instrumentLeft, $instrument);
    }

    /**
     * @Then /^I choose to expand the Dilation section$/
     */
    public function iChooseToExpandTheDilationSection()
    {
        $this->clickLink(Examination::$openDilation);
    }

    /**
     * @Then /^I choose left Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseLeftDilationOfAndDropsOf($dilation, $drops)
    {
        $this->selectOption(Examination::$dilationLeft, $dilation);
        $this->selectOption(Examination::$dropsLeft, $drops);
    }

    /**
     * @Then /^I choose right Dilation of "([^"]*)" and drops of "([^"]*)"$/
     */
    public function iChooseRightDilationOfAndDropsOf($dilation, $drops)
    {
        $this->selectOption(Examination::$dilationRight, $dilation);
        $this->selectOption(Examination::$dropsRight, $drops);
    }

    /**
     * @Then /^I choose to expand the Refraction section$/
     */
    public function iChooseToExpandTheRefractionSection()
    {
        $this->clickLink(Examination::$expandRefraction);
    }

    /**
     * @Then /^I enter left Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function LeftRefractionDetails($sphere, $integer, $fraction)
    {
        $this->selectOption(Examination::$sphereRight, $sphere);
        $this->selectOption(Examination::$sphereRightInt, $integer);
        $this->selectOption(Examination::$sphereRightFraction, $fraction);
    }

    /**
     * @Given /^I enter left cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterLeftCylinderDetails($cylinder, $integer, $fraction)
    {
        $this->selectOption(Examination::$cylinderLeft, $cylinder);
        $this->selectOption(Examination::$cylinderLeftInt, $integer);
        $this->selectOption(Examination::$cylinderLeftFraction, $fraction);
    }

    /**
     * @Then /^I enter left Axis degrees of "([^"]*)"$/
     */
    public function iEnterLeftAxisDegreesOf($axis)
    {
        //We need a Clear Field function here
        $this->fillField(Examination::$sphereLeftAxis, $axis);
        //We need to Press the tab key here
    }

    /**
     * @Given /^I enter a left type of "([^"]*)"$/
     */
    public function iEnterALeftTypeOf($type)
    {
        $this->selectOption(Examination::$sphereLeftType, $type);
    }

    /**
     * @Then /^I enter right Refraction details of Sphere "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightRefractionDetailsOfSphereIntegerFraction($sphere, $integer, $fraction)
    {
        $this->selectOption(Examination::$sphereRight, $sphere);
        $this->selectOption(Examination::$sphereRightInt, $integer);
        $this->selectOption(Examination::$sphereRightFraction, $fraction);
    }

    /**
     * @Given /^I enter right cylinder details of of Cylinder "([^"]*)" integer "([^"]*)" fraction "([^"]*)"$/
     */
    public function iEnterRightCylinderDetailsOfOfCylinderIntegerFraction($cylinder, $integer, $fraction)
    {
        $this->selectOption(Examination::$cylinderRight, $cylinder);
        $this->selectOption(Examination::$cylinderRightInt, $integer);
        $this->selectOption(Examination::$cylinderRightFraction, $fraction);
    }

    /**
     * @Then /^I enter right Axis degrees of "([^"]*)"$/
     */
    public function iEnterRightAxisDegreesOf($axis)
    {
        //We need a Clear Field function here
        $this->fillField(Examination::$sphereRightAxis, $axis);
        //We need to Press the tab key here
    }

    /**
     * @Given /^I enter a right type of "([^"]*)"$/
     */
    public function iEnterARightTypeOf($type)
    {
        $this->selectOption(Examination::$sphereRightType, $type);
    }

    /**
     * @Then /^I choose to expand the Gonioscopy section$/
     */
    public function iChooseToExpandTheGonioscopySection()
    {
        $this->clickLink(Examination::$expandGonioscopy);
    }

    /**
     * @Then /^I choose to expand the Adnexal Comorbidity section$/
     */
    public function iChooseToExpandTheAdnexalComorbiditySection()
    {
        $this->clickLink(Examination::$expandaAdnexalComorbidity);
    }

    /**
     * @Then /^I choose to expand the Anterior Segment section$/
     */
    public function iChooseToExpandTheAnteriorSegmentSection()
    {
        $this->clickLink(Examination::$expandAnteriorSegment);
    }

    /**
     * @Then /^I choose to expand the Pupillary Abnormalities section$/
     */
    public function iChooseToExpandThePupillaryAbnormalitiesSection()
    {
        $this->clickLink(Examination::$expandPupillaryAbnormalities);
    }

    /**
     * @Then /^I choose to expand the Optic Disc section$/
     */
    public function iChooseToExpandTheOpticDiscSection()
    {
        $this->clickLink(Examination::$expandOpticDisc);
    }

    /**
     * @Then /^I choose to expand the Posterior Pole section$/
     */
    public function iChooseToExpandThePosteriorPoleSection()
    {
        $this->clickLink(Examination::$expandPosteriorPole);
    }

    /**
     * @Then /^I choose to expand the Diagnoses section$/
     */
    public function iChooseToExpandTheDiagnosesSection()
    {
        $this->clickLink(Examination::$expandDiagnoses);
    }

    /**
     * @Then /^I choose to expand the Investigation section$/
     */
    public function iChooseToExpandTheInvestigationSection()
    {
        $this->clickLink(Examination::$expandInvestigation);
    }

    /**
     * @Then /^I choose to expand the Clinical Management section$/
     */
    public function iChooseToExpandTheClinicalManagementSection()
    {
        $this->clickLink(Examination::$expandClinicalManagement);
    }

    /**
     * @Then /^I choose to expand the Risks section$/
     */
    public function iChooseToExpandTheRisksSection()
    {
        $this->clickLink(Examination::$expandRisks);
    }

    /**
     * @Then /^I choose to expand the Clinic Outcome section$/
     */
    public function iChooseToExpandTheClinicOutcomeSection()
    {
        $this->clickLink(Examination::$expandClinicOutcome);
    }

    /**
     * @Then /^I choose to expand the Conclusion section$/
     */
    public function iChooseToExpandTheConclusionSection()
    {
        $this->clickLink(Examination::$expandConclusion);
    }

    /**
     * @Then /^I Save the Examination$/
     */
    public function iSaveTheExamination()
    {
        $this->clickLink(Examination::$saveExamination);
    }

    /**
     * @Then /^I Cancel the Examination$/
     */
    public function iCancelTheExamination()
    {
        $this->clickLink(AnaestheticAudit::$cancelExam);
    }

    /**
     * @Then /^I select Site ID "([^"]*)"$/
     */
    public function iSelectSiteId($site)
    {
        $this->selectOption(Correspondence::$siteDropdown, $site);
    }

    /**
     * @Given /^I select Address Target "([^"]*)"$/
     */
    public function iSelectAddressTarget($address)
    {
       $this->selectOption(Correspondence::$addressTarget, $address);
    }

    /**
     * @Then /^I choose a Macro of "([^"]*)"$/
     */
    public function iChooseAMacroOf($macro)
    {
       $this->selectOption(Correspondence::$macro, $macro);
    }

    /**
     * @Given /^I select Clinic Date "([^"]*)"$/
     */
    public function iSelectClinicDate($date)
    {
        $this->clickLink(Correspondence::$letterDate);
        $this->clickLink(PatientViewPage::passDateFromTable($datefrom));
    }

    /**
     * @Then /^I choose an Introduction of "([^"]*)"$/
     */
    public function iChooseAnIntroductionOf($intro)
    {
        $this->selectOption(Correspondence::$introduction, $intro);
    }

    /**
     * @Given /^I choose a Diagnosis of "([^"]*)"$/
     */
    public function iChooseADiagnosisOf($diagnosis)
    {
        $this->selectOption(Correspondence::$diagnosis, $diagnosis);
    }

    /**
     * @Then /^I choose a Management of "([^"]*)"$/
     */
    public function iChooseAManagementOf($management)
    {
        $this->selectOption(Correspondence::$management, $management);
    }

    /**
     * @Given /^I choose Drugs "([^"]*)"$/
     */
    public function iChooseDrugs($drugs)
    {
        $this->selectOption(Correspondence::$drugs, $drugs);
    }

    /**
     * @Then /^I choose Outcome "([^"]*)"$/
     */
    public function iChooseOutcome($outcome)
    {
        $this->selectOption(Correspondence::$outcome, $outcome);
    }

    /**
     * @Given /^I choose CC Target "([^"]*)"$/
     */
    public function iChooseCcTarget($cc)
    {
        $this->selectOption(Correspondence::$letterCc, $cc);
    }

    /**
     * @Given /^I add a New Enclosure$/
     */
    public function iAddANewEnclosure()
    {
        $this->clickLink(Correspondence::$addEnclosure);
    }

    /**
     * @Then /^I choose to close the browser$/
     */
    public function iChooseToCloseTheBrowser()
    {
       //To be coded - need Mink function
    }

}


