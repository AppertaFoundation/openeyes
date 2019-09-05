<?php

use Behat\Behat\Exception\BehaviorException;

class OperationBooking extends OpenEyesPage
{
    protected $path = "/site/OphTrOperationbooking/Default/create?patient_id={parentId}";
    protected $elements = array(

        'saveOK' => array (
            'xpath' => "//*[@id='flash-success']"
        ),
        'diagnosisRightEye' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_2']"
        ),
        'diagnosisLeftEye' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_1']"
        ),
        'diagnosisBothEyes' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_3']"
        ),
        'operationDiagnosis' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Diagnosis_disorder_id']"
        ),
        'operationRightEye' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']"
        ),
        'operationBothEyes' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']"
        ),
        'operationLeftEye' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']"
        ),
        'operationProcedure' => array(
            'xpath' => "//*[@id='select_procedure_id_procs']"
        ),
        'consultantYes' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_consultant_required_1']"
        ),
        'consultantNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_consultant_required_0']"
        ),
        'otherdoctorNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_any_grade_of_doctor_0']"
        ),
        'preopassessmentNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_preassessment_booking_required_0']"
        ),
        'preopassessmentYes' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_preassessment_booking_required_1']"
        ),
        'AnaestheticTypeTopical' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_1']"
        ),
        'stopmedicationNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_stop_medication_0']"
        ),
        'admissiondiscussedYes' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_fast_track_discussed_with_patient_1']"
        ),
        'scheduleOptASAP' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_4']"
        ),
        'anaestheticTopical' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_AnaestheticType_Topical']"
        ),
        'anaestheticLa' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_AnaestheticType_LA']"
        ),
        'anaestheticLac' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_']"
        ),
        'anaestheticLas' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_4']"
        ),
        'anaestheticGa' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_AnaestheticType_GA']"
        ),
        'anaestheticSedation' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_AnaestheticType_Sedation']"
        ),
        'anaestheticNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_AnaestheticType_No_Anaesthetic']"
        ),
        'AnaestheticchoicePatientpreference' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_choice_id_1']"
        ),
        'postOpStatYes' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_1']"
        ),
        'postOpStatNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_required_id_1']"
        ),
        'OvernightStayNo' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_required_id_1']"
        ),
        'OvernightStayPreOp' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_required_id_2']"
        ),
        'OvernightStayPostOp' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_required_id_3']"
        ),
        'OvernightStayBoth' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_required_id_4']"
        ),
        'operationSiteID' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_site_id']"
        ),
        'priorityUrgent' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_priority_id_2']"
        ),
        'priorityRoutine' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_priority_id_1']"
        ),
        'decisionDate' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_decision_date_0']"
        ),
        'operationComments' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_comments']"
        ),
        'scheduleLater' => array(
            'xpath' => "//*[@id='et_save_and_schedule_later']"
        ),
        'scheduleNow' => array(
            //'xpath' => "//*[@id='et_save_and_schedule']"
            'xpath' => "//*[@id='et_save_and_schedule']"
        ),
        'duplicateProcedureOk' => array(
            'xpath' => "//*[@class='secondary small confirm ok']"
        ),
        'duplicateProcedureCancel' => array(
            'xpath' => "//*[@class='warning small confirm cancel']"
        ),
        'availableTheatreSlotDate' => array(
            'xpath' => "//*[@class='available']"
        ),
        'availableTheatreSlotDateOutsideRTT' => array(
            'xpath' => "//*[@class='available']"
            //'xpath' => "//*[@class='available outside_rtt']"
        ),
        'availableThreeWeeksTime' => array(
            'xpath' => "//*[@id='calendar']//*[contains(text(),'27')]"
        ),
        'nextMonth' => array(
            'css' => '#next_month > a'
        ),
        'availableTheatreSessionTime' => array(
            'xpath' => "//*[@class='timeBlock available bookable']"
        ),
        'noAnaesthetist' => array(
            'xpath' => "//*[@id='bookingSession1824']"
        ),
        'sessionComments' => array(
            'xpath' => "//*[@id='Session_comments']"
        ),
        'sessionOperationComments' => array(
            'xpath' => "//*[@name='Operation[comments]']"
        ),
        'sessionRTTComments' => array(
            'xpath' => "//*[@name='Operation[comments_rtt]']"
        ),
        'confirmSlot' => array(
            'xpath' => "//*[@id='confirm_slot']"
        ),
        'EmergencyList' => array(
            'xpath' => "//select[@id='firm_id']"
        ),
        'currentMonth' => array(
            'css' => "#current_month"
        ),
        'saveButton' => array(
            'xpath' => "//*[@id='et_save']"
        ),
        'chooseWard' => array(
            'xpath' => "//*[@id='Booking_ward_id']"
        ),
        'admissionTime' => array(
            'xpath' => "//*[@id='Booking_admission_time']"
        ),
        'consultantValidationError' => array(
            'xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Operation: The booked session does not have a consultant present, you must change the session or cancel the booking before making this change')]"
        ),
        'complexity_low' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_complexity_0']"
        ),
        'complexity_medium' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_complexity_5']"
        ),
        'complexity_high' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_complexity_10']"
        ),
        'schedule_option_am' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_2']"
        ),
        'schedule_option_pm' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_3']"
        ),
        'schedule_option_np' => array(
            'xpath' => "//input[@id='Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_4']"
        ),
        'special_equipment_details' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_special_equipment_details']"
        ),
        'collector_name' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_ContactDetails_collector_name']"
        ),
        'collector_number' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_ContactDetails_collector_contact_number']"
        ),
        'operation_rtt_comment' => array(
            'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_comments_rtt']"
        ),
        'add_procedure_btn' => array(
            'xpath' => "//*[@id='add-procedure-list-btn-procs']"
        ),
        'add_diagnosis_btn' => array(
            'xpath' => "//*[@id='add-operation-booking-diagnosis']"
        ),

    );

    public function diagnosisEyes($eye)
    {
        if ($eye === 'Right') {
            $this->getElement('diagnosisRightEye')->click();
        }
        if ($eye === 'Both') {
            $this->getElement('diagnosisBothEyes')->click();
        }
        if ($eye === 'Left') {
            $this->getElement('diagnosisLeftEye')->click();
        }
    }

    public function diagnosis($diagnosis)
    {
        $element = $this->getElement('operationDiagnosis');
        $this->scrollWindowToElement($element);
        $this->getElement('add_diagnosis_btn')->click();
        $this->addSelection($diagnosis);
    }

    public function operationEyes($opEyes)
    {
        if ($opEyes === 'Right') {
            $this->getElement('operationRightEye')->click();
        }
        if ($opEyes === 'Both') {
            $this->getElement('operationBothEyes')->click();
        }
        if ($opEyes === 'Left') {
            $this->getElement('operationLeftEye')->click();
        }
    }

    public function procedure($procedure)
    {
        $this->getSession()->wait(2000);
        $this->getElement('add_procedure_btn')->click();
        $this->addSelection($procedure);
        $this->getSession()->wait(2000);
    }

    public function addSelection($selection)
    {
        foreach ($this->findAll('xpath', '//*[@id="add-operation-booking-diagnosis"]') as $pop_up) {
            if ($pop_up->isVisible()) {
                $this->elements['pop_up_selection'] = array(
                    'css' => 'li[data-label=\'' . $selection . '\']'
                );
                $pop_up->find('xpath', $this->getElement('pop_up_selection')->getXpath())->click();
                $pop_up->find('css', '.add-icon-btn')->click();
            }
        }
    }


    public function consultantYes()
    {
        //$element = $this->getElement ( 'consultantYes' );
        //$this->scrollWindowToElement ( $element );
        //$element->click ();
        $this->getElement('consultantYes')->click();
    }

    public function consultantNo()
    {
        $this->getElement('consultantNo')->click();
    }

    public function otherdoctorNo()
    {
        $this->getElement('otherdoctorNo')->click();
    }

    public function preopassessmentNo()
    {
        $this->getElement('preopassessmentNo')->click();
    }

    public function preopassessmentYes()
    {
        $this->getElement('preopassessmentYes')->click();
    }

    public function AnaestheticTypeTopical()
    {
        $this->getElement('AnaestheticTypeTopical')->click();
    }

    public function AnaestheticchoicePatientpreference()
    {
        $this->getElement('AnaestheticchoicePatientpreference')->click();
    }

    public function stopmedicationNo()
    {
        $this->getElement('stopmedicationNo')->click();
    }

    public function admissiondiscussedYes()
    {
        $this->getElement('admissiondiscussedYes')->click();
    }

    public function scheduleOptASAP()
    {
        $this->getElement('scheduleOptASAP')->click();
    }

    public function selectAnaesthetic($type)
    {
        $element = null;
        if ($type === 'Topical') {
            $element = $this->getElement('anaestheticTopical');
        }
        if ($type === 'LA') {
            $element = $this->getElement('anaestheticLa');
        }
        if ($type === 'LAC') {
            $element = $this->getElement('anaestheticLac');
        }
        if ($type === 'LAS') {
            $element = $this->getElement('anaestheticLas');
        }
        if ($type === 'GA') {
            $element = $this->getElement('anaestheticGa');
        }
        if ($type === 'Sedation') {
            $element = $this->getElement('anaestheticSedation');
        }
        if ($type === 'No Anaesthetic') {
            $element = $this->getElement('anaestheticNo');
        }
        // $element->focus();
        //	$this->scrollWindowToElement ( $element );
        $this->getSession()->wait(2000);
        $element->click();
        $this->getSession()->wait(3000);
    }

    public function postOpStayYes()
    {
        $this->getElement('postOpStatYes')->click();
    }

    public function postOpStayNo()
    {
        $this->getElement('postOpStatNo')->click();
    }

    public function operationSiteID($site)
    {
        $this->getElement('operationSiteID')->selectOption($site);
    }

    public function priorityRoutine()
    {
        $element = $this->getElement('priorityRoutine');
        //$this->scrollWindowToElement ( $element );
        $element->click();
    }

    public function priorityUrgent()
    {
        $element = $this->getElement('priorityUrgent');
        //$this->scrollWindowToElement ( $element );
        $element->click();
    }

    public function decisionDate($date)
    {
        $this->getElement('decisionDate')->selectOption($date);
        $this->getSession()->wait(3000);
    }

    public function operationComments($comments)
    {
        $this->getElement('operationComments')->setValue($comments);
    }

    public function scheduleLater()
    {
        $this->getElement('scheduleLater')->click();
    }

    public function scheduleNow()
    {
        // $this->getElement('scheduleNow')->keyPress(2191);
        //$this->getSession ()->wait ( 5000 );
        $this->getElement('scheduleNow')->click();
        $this->getSession()->wait(8000, "window.$ && $('.event-title').html() == 'Schedule Operation' ");
    }

    public function duplicateProcedureOk()
    {
        $this->popupOk('duplicateProcedureOk');
    }

    public function EmergencyList()
    {
        $this->getElement('EmergencyList')->selectOption("EMG");
        // alert is not happening anymore so call is commented out
        // $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
        $this->getSession()->wait(15000, "window.$ && $('.alert-box.alert').last().html() == 'You are booking into the Emergency List.' ");
    }

    public function nextMonth()
    {
        $currMonthText = $this->getElement('currentMonth')->getText();
        $this->getElement('nextMonth')->click();
        $this->getSession()->wait(15000, "window.$ && $('#current_month').html().trim().length > 0 && $('#current_month').html().trim() != '" . $currMonthText . "' ");
    }

    public function availableSlotExactDay($day)
    {
        $slot = $this->find('xpath', "//*[@id='calendar']//*[number()='" . $day . "']");
        $this->scrollWindowToElement($slot);
        $slot->click();
        $this->getSession()->wait(15000, "window.$ && $('#calendar td.available.selected_date').html().trim() == '" . $day . "' ");
    }

    public function availableSlot()
    {
        $slots = $this->findAll('xpath', $this->getElement('availableTheatreSlotDate')->getXpath());
        foreach ($slots as $slot) {
            $this->scrollWindowToElement($slot);
            $slot->click();
            $this->getSession()->wait(10000, "window.$ && $('.sessionTimes').length > 0");
            $freeSession = $this->find('css', '.sessionTimes > a > .bookable');
            if ($freeSession) {
                return true;
            }
        }

        throw new \Exception ('No available theatre session found');
    }

    public function availableSlotOutsideRTT()
    {
        $slots = $this->findAll('xpath', $this->getElement('availableTheatreSlotDateOutsideRTT')->getXpath());
        foreach ($slots as $slot) {
            $slot->click();
            $this->getSession()->wait(10000, "window.$ && $('.sessionTimes').length > 0");
            $freeSession = $this->find('css', '.sessionTimes > a > .bookable');
            if ($freeSession) {
                return true;
            }
        }

        throw new \Exception ('No available theatre session Outside RTT found');
    }

    public function availableSessionTime()
    {
        $this->waitForElementDisplayBlock('.timeBlock.available.bookable');
        $element = $this->getElement('availableTheatreSessionTime');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->waitForElementDisplayBlock('Session_comments');
    }

    public function availableThreeWeeksTime()
    {
        $this->getElement('availableThreeWeeksTime')->click();
        $this->getElement('noAnaesthetist')->click();
    }

    public function sessionComments($sessionComments)
    {
        $this->getElement('sessionComments')->setValue($sessionComments);
    }

    public function sessionOperationComments($opComments)
    {
        $this->getElement('sessionOperationComments')->setValue($opComments);
    }

    public function enterRTTComments($RTTcomments)
    {
        $this->getElement('operation_rtt_comment')->setValue($RTTcomments);
    }

    public function confirmSlot()
    {
        $this->getElement('confirmSlot')->click();
    }

    public function save()
    {
        $this->getElement('saveButton')->click();
        $this->getSession()->wait(5000);
    }

    public function chooseWard($ward)
    {
        $this->waitForElementDisplayBlock('#Booking_ward_id');
        $this->getElement('chooseWard')->selectOption($ward);
    }

    public function admissionTime($time)
    {
        $this->getElement('admissionTime')->setValue($time);
    }

    public function consultantValidationError()
    {
        return ( bool )$this->find('xpath', $this->getElement('consultantValidationError')->getXpath());
    }

    public function consultantValidationCheck()
    {
        if (!$this->consultantValidationError()) {
            throw new BehaviorException ("CONSULTANT BOOKING VALIDATION ERROR!!!");
        }
    }

    public function selectOperationComplexity($complexity)
    {
        if (strpos(strtolower($complexity), 'high') !== false) {
            $this->getElement('complexity_high')->click();
        } elseif (strpos(strtolower($complexity), 'medium') !== false) {
            $this->getElement('complexity_medium')->click();
        } else {
            $this->getElement('complexity_low')->click();
        }
    }

    public function selectScheduleTime($schedule)
    {
        if (strpos(strtolower($schedule), 'am') !== false) {
            $this->getElement('schedule_option_am')->click();
        } elseif (strpos(strtolower($schedule), 'pm') !== false) {
            $this->getElement('schedule_option_pm')->click();
        } else {
            $this->getElement('schedule_option_np')->click();
        }
    }

    public function specialEquipment($required_or_not)
    {
        if ($required_or_not) {
            $this->elements['special_equipment'] = array(
                'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_special_equipment_1']"
            );
            $this->getElement('special_equipment')->click();
        } else {
            $this->elements['special_equipment'] = array(
                'xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_special_equipment_0']"
            );
            $this->getElement('special_equipment')->click();
        }
    }

    public function speicialEquipmentDetails($details)
    {
        $this->getElement('special_equipment_details')->setValue($details);
    }

    public function collecterName($name)
    {
        $this->getElement('collector_name')->setValue($name);
    }

    public function collecterNumber($number)
    {
        $this->getElement('collector_number')->setValue($number);
    }

    public function overnightRequiredOption($option)
    {
        if ($option === 'Both') {
            $this->getElement('OvernightStayBoth')->click();
        } elseif ($option === 'Pre-op') {
            $this->getElement('OvernightStayPreOp')->click();
        } elseif ($option === 'Post-op') {
            $this->getElement('OvernightStayPostOp')->click();
        } else {
            $this->getElement('OvernightStayNo')->click();
        }
    }

    /**
     * @throws BehaviorException when save fails
     */
    public function saveAndScheduleLater(){
        $this->getElement('scheduleLater')->click();
        $this->waitForElementDisplayBlock('saveOk');
        if (!$this->find('xpath', $this->getElement('saveOK')->getXpath())){
            throw new BehaviorException('Could not save Operation Booking');
        }
    }
}
