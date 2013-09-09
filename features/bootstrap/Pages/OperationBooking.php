<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class OperationBooking extends Page
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
        'scheduleNow' => array('xpath' => "//*[@id='et_schedulenow']"),
        'availableTheatreSlotDate' => array('xpath' => "//*[@class='available']"),
        'availableThreeWeeksTime' => array ('xpath' => "//*[@id='calendar']//*[contains(text(),'27')]"),
        'nextMonth' => array('xpath' => "//*[@id='next_month']"),
        'availableTheatreSessionTime' => array('xpath' => "//*[@class='timeBlock available bookable']"),
        'noAnaesthetist' => array ('xpath' => "//*[@id='bookingSession1824']"),
        'sessionComments' => array('xpath' => "//*[@id='Session_comments']"),
        'sessionOperationComments' => array('xpath' => "//*[@id='operation_comments']"),
        'confirmSlot' => array('xpath' => "//*[@id='confirm_slot']")
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
        if ($opEyes==='Both') {
            $this->getElement('operationBothEyes')->click();
    }
        if ($opEyes==='Left') {
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
        if ($type==='Topical') {
            $this->getElement('anaestheticTopical')->click();
        }
        if ($type==='LA') {
            $this->getElement('anaestheticLa')->click();
        }
        if ($type==='LAC') {
            $this->getElement('anaestheticLac')->click();
        }
        if ($type==='LAS') {
            $this->getElement('anaestheticLas')->click();
        }
        if ($type==='GA') {
            $this->getElement('anaestheticGa')->click();
        }
    }

    public function postOpStayYes ()
    {
        $this->getElement('postOpStatYes')->click();
    }

    public function postOpStayNo ()
    {
        $this->getElement('postOpStatNo')->click();
    }

    public function operationSiteID ($site)
    {
        $this->getElement('operationSiteID')->selectOption($site);
    }

    public function priorityRoutine ()
    {
        $this->getElement('priorityRoutine')->click();
    }

    public function priorityUrgent ()
    {
        $this->getElement('priorityUrgent')->click();
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
        $this->getElement('scheduleNow')->keyPress(2191);
        $this->getElement('scheduleNow')->click();
    }

    public function availableSlot ()
    {
        $slots = $this->findAll('xpath', $this->getElement('availableTheatreSlotDate')->getXpath());
        foreach ($slots as $slot) {
            $slot->click();
            $this->getSession()->wait(10000, "$('.sessionTimes').length > 0");
//            $freeSession = $this->getElement('availableTheatreSessionTime');
            $freeSession = $this->find('css', '.sessionTimes > a > .bookable');
            if ($freeSession) {
                return true;
            }
        }

        throw new \Exception('No available theatre session found');
    }

    public function availableSessionTime ()
    {
        $this->getElement('availableTheatreSessionTime')->click();
        $this->getSession()->wait(10000, "$('.active') == 0");
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

    public function confirmSlot ()
    {
        $this->getElement('confirmSlot')->click();
    }
}
