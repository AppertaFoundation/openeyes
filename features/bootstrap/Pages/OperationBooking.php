<?php
class OperationBooking extends OpenEyesPage
{
    protected $path = "/site/OphTrOperationbooking/Default/create?patient_id={parentId}";

    protected $elements = array(

        'diagnosisRightEye' => array('xpath'=>"//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_2']"),
        'diagnosisLeftEye' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_1']"),
        'diagnosisBothEyes' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Diagnosis_eye_id_3']"),
        'operationDiagnosis' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Diagnosis_disorder_id']"),
        'operationRightEye' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']"),
        'operationBothEyes' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']"),
        'operationLeftEye' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']"),
        'operationProcedure' => array('xpath' => "//*[@id='select_procedure_id_procs']"),
        'consultantYes' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_consultant_required_1']"),
        'consultantNo' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_consultant_required_0']"),
        'anaestheticTopical' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_1']"),
        'anaestheticLa' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_3']"),
        'anaestheticLac' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_2']"),
        'anaestheticLas' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_4']"),
        'anaestheticGa' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_anaesthetic_type_id_5']"),
        'postOpStatYes' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_1']"),
        'postOpStatNo' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_overnight_stay_0']"),
        'operationSiteID' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_site_id']"),
        'priorityUrgent' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_priority_id_2']"),
        'priorityRoutine' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_priority_id_1']"),
        'decisionDate' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_decision_date_0']"),
        'operationComments' => array('xpath' => "//*[@id='Element_OphTrOperationbooking_Operation_comments']"),
        'scheduleLater' => array('xpath' => "//*[@id='et_schedulelater']"),
        'scheduleNow' => array('xpath' => "//*[@id='et_save_and_schedule']"),
        'availableTheatreSlotDate' => array('xpath' => "//*[@class='available']"),
        'availableTheatreSlotDateOutsideRTT' => array('xpath' => "//*[@class='available outside_rtt']"),
        'availableThreeWeeksTime' => array ('xpath' => "//*[@id='calendar']//*[contains(text(),'27')]"),
        'nextMonth' => array('css' => '#next_month > a'),
        'availableTheatreSessionTime' => array('xpath' => "//*[@class='timeBlock available bookable']"),
        'noAnaesthetist' => array ('xpath' => "//*[@id='bookingSession1824']"),
        'sessionComments' => array('xpath' => "//*[@id='Session_comments']"),
        'sessionOperationComments' => array('xpath' => "//*[@name='Operation[comments]']"),
        'sessionRTTComments' => array('xpath' => "//*[@name='Operation[comments_rtt]']"),
        'confirmSlot' => array('xpath' => "//*[@id='confirm_slot']"),
        'EmergencyList' => array ('xpath' => "//select[@id='firm_id']"),
	    'currentMonth' => array('css' => "#current_month"),
        'saveButton' => array('xpath' => "//*[@id='et_save']"),
        'chooseWard' => array('xpath' => "//*[@id='Booking_ward_id']"),
        'admissionTime' => array('xpath' => "//*[@id='Booking_admission_time']"),


    );

    public function diagnosisEyes ($eye)
    {
        if ($eye==='Right') {
        $this->getElement('diagnosisRightEye')->click();
    }
        if ($eye==='Both') {
            $this->getElement('diagnosisBothEyes')->click();
    }
        if ($eye==='Left') {
            $this->getElement('diagnosisLeftEye')->click();
    }
    }

    public function diagnosis ($diagnosis)
    {
        $this->getElement('operationDiagnosis')->setValue($diagnosis);
    }

    public function operationEyes ($opEyes)
    {
        if ($opEyes==='Right') {
            $this->getElement('operationRightEye')->click();
    }
        if ($opEyes==='Both')  {
            $this->getElement('operationBothEyes')->click();
    }
        if ($opEyes==='Left')  {
            $this->getElement('operationLeftEye')->click();
    }
}
    public function procedure ($procedure)
    {
        $this->getElement('operationProcedure')->setValue($procedure);
    }

    public function consultantYes ()
    {
        $this->getElement('consultantYes')->click();
    }

    public function consultantNo ()
    {
        $this->getElement('consultantNo')->click();
    }

    public function selectAnaesthetic ($type)
    {
		$el = null;
		if ($type==='Topical') {
            $el = $this->getElement('anaestheticTopical');
        }
        if ($type==='LA') {
			$el = $this->getElement('anaestheticLa');
        }
        if ($type==='LAC') {
			$el = $this->getElement('anaestheticLac');
        }
        if ($type==='LAS') {
			$el = $this->getElement('anaestheticLas');
        }
        if ($type==='GA') {
			$el = $this->getElement('anaestheticGa');
        }
		$el->focus();
        $this->scrollWindowToElement($el);
		$el->click();
		$this->getSession()->wait(3000, "window.$ && $(\"#Element_OphTrOperationbooking_Operation_anaesthetic_type_id [name='Element_OphTrOperationbooking_Operation[anaesthetic_type_id]']:checked\").val() == " .   $el->getValue());
    }

    public function postOpStayYes ()
    {
        $this->getElement('postOpStatYes')->check();
    }

    public function postOpStayNo ()
    {
        $this->getElement('postOpStatNo')->check();
    }

    public function operationSiteID ($site)
    {
        $this->getElement('operationSiteID')->selectOption($site);
    }

    public function priorityRoutine ()
    {
        $element = $this->getElement('priorityRoutine');
        $this->scrollWindowToElement($element);
        $element->click();
    }

    public function priorityUrgent ()
    {
        $element = $this->getElement('priorityUrgent');
        $this->scrollWindowToElement($element);
        $element->check();

    }

    public function decisionDate ($date)
    {
        $this->getElement('decisionDate')->selectOption($date);
    }

    public function operationComments ($comments)
    {
        $this->getElement('operationComments')->setValue($comments);
    }

    public function scheduleLater ()
    {
        $this->getElement('scheduleLater')->click();
    }

    public function scheduleNow ()
    {
        //$this->getElement('scheduleNow')->keyPress(2191);
        $this->getElement('scheduleNow')->click();
        $this->getSession()->wait(15000,"window.$ && $('.event-title').html() == 'Schedule Operation' ");
    }

    public function EmergencyList ()
    {
        $this->getElement('EmergencyList')->selectOption("EMG");
		//alert is not happening anymore so call is commented out
        //$this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
        $this->getSession()->wait(15000, "window.$ && $('.alert-box.alert').last().html() == 'You are booking into the Emergency List.' ");
    }

    public function nextMonth ()
    {
			$currMonthText = $this->getElement('currentMonth')->getText();
			$this->getElement('nextMonth')->click();
			$this->getSession()->wait(15000, "window.$ && $('#current_month').html().trim().length > 0 && $('#current_month').html().trim() != '" . $currMonthText . "' ");
    }

    public function availableSlotExactDay ($day)
    {
		$slot = $this->find('xpath' , "//*[@id='calendar']//*[number()='" . $day ."']");
		$slot->click();
		$this->getSession()->wait(15000, "window.$ && $('#calendar td.available.selected_date').html().trim() == '" . $day . "' ");
    }

    public function availableSlot ()
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

        throw new \Exception('No available theatre session found');
    }

    public function availableSlotOutsideRTT ()
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

        throw new \Exception('No available theatre session Outside RTT found');
    }

    public function availableSessionTime ()
    {
        $this->waitForElementDisplayBlock('.timeBlock.available.bookable');
		$element = $this->getElement('availableTheatreSessionTime');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->getSession()->wait(10000);
    }

    public function availableThreeWeeksTime ()
    {
//        $this->getElement('nextMonth')->click();
        $this->getElement('availableThreeWeeksTime')->click();
        $this->getElement('noAnaesthetist')->click();
    }

    public function sessionComments ($sessionComments)
    {
        $this->getElement('sessionComments')->setValue($sessionComments);
    }

    public function sessionOperationComments ($opComments)
    {
        $this->getElement('sessionOperationComments')->setValue($opComments);
    }

    public function sessionRTTComments ($RTTcomments)
    {
        $this->getElement('sessionRTTComments')->setValue($RTTcomments);
    }

    public function confirmSlot ()
    {
        $this->getElement('confirmSlot')->click();
    }

    public function save ()
    {
        $this->getElement('saveButton')->click();
    }

    public function chooseWard ($ward)
    {
        $this->waitForElementDisplayBlock('#Booking_ward_id');
		$this->getElement('chooseWard')->selectOption($ward);
    }

    public function admissionTime ($time)
    {
        $this->getElement('admissionTime')->setValue($time);
    }

}
