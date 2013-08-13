<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class OperationBooking extends Page
{
    protected $elements = array(
        'diagnosisRightEye' => array('xpath' => "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_2']"),
        'diagnosisLeftEye' => array('xpath' => "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_1']"),
        'diagnosisBothEyes' => array('xpath' => "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_3']"),
        'operationDiagnosis' => array('xpath' => "//select[@id='element_ophtroperationbooking_diagnosis_disorder_id']"),
        'operationProcedure' => array('xpath' => "//*[@id='select_procedure_id_procs']"),
        'operationRightEye' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']"),
        'operationBothEyes' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']"),
        'operationLeftEye' => array('xpath' => "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']"),
        'consultantYes' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_consultant_required_1']"),
        'consultantNo' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_consultant_required_0']"),
        'anaestheticTopical' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_1']"),
        'anaestheticLac' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_2']"),
        'anaestheticLa' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_3']"),
        'anaestheticLas' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_4']"),
        'anaestheticGa' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_5']"),
        'postOpStayYes' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']"),
        'postOpStayNo' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']"),
        'operationSite' => array('xpath' => "//select[@id='element_ophtroperationbooking_operation_site_id']"),
        'routineOperation' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_priority_id_1']"),
        'urgentOperation' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_priority_id_2']"),
        'decisionOpen' => array('xpath' => "//input[@id='element_ophtroperationbooking_operation_decision_date_0']"),
        'addComments' => array('xpath' => "//textarea[@id='element_ophtroperationbooking_operation_comments']"),
        'scheduleLater' => array('xpath' => "//button[@id='et_schedulelater']"),
        'scheduleAndSaveNow' => array('xpath' => "//button[@id='et_schedulenow']"),
        'scheduleNowButton' => array('xpath' => "//a[@id='btn_schedule-now']/span"),
        'theatreSessionDate' => array('xpath' => "//*[@class='available']"),
        'theatreSessionTime' => array('xpath' => "//*[@class='timeBlock available bookable']"),
        'sessionComments' => array('xpath' => "//textarea[@id='session_comments']"),
        'operationComments' => array('xpath' => "//textarea[@id='operation_comments']"),
        'confirmSlot' => array('xpath' => "//button[@id='confirm_slot']"),
    );

}