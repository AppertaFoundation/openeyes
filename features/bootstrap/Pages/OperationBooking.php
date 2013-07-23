<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class OperationBooking extends Page
{
    public  $diagnosisRightEye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_2']";
    public  $diagnosisLeftEye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_1']";
    public  $diagnosisBothEyes = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_3']";
    public  $operationDiagnosis = "//select[@id='element_ophtroperationbooking_diagnosis_disorder_id']";
    public  $operationProcedure = "//*[@id='select_procedure_id_procs']";
    public  $operationRightEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']";
    public  $operationBothEyes = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']";
    public  $operationLeftEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']";
    public  $consultantYes = "//input[@id='element_ophtroperationbooking_operation_consultant_required_1']";
    public  $consultantNo = "//input[@id='element_ophtroperationbooking_operation_consultant_required_0']";
    public  $anaestheticTopical= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_1']";
    public  $anaestheticLac= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_2']";
    public  $anaestheticLa= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_3']";
    public  $anaestheticLas= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_4']";
    public  $anaestheticGa= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_5']";
    public  $postOpStayYes = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
    public  $postOpStayNo = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
    public  $operationSite = "//select[@id='element_ophtroperationbooking_operation_site_id']";
    public  $routineOperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_1']";
    public  $urgentOperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_2']";
    public  $decisionOpen = "//input[@id='element_ophtroperationbooking_operation_decision_date_0']";
    public  $addComments = "//textarea[@id='element_ophtroperationbooking_operation_comments']";
    public  $scheduleLater = "//button[@id='et_schedulelater']";
    public  $scheduleAndSaveNow = "//button[@id='et_schedulenow']";
    public  $scheduleNowButton = "//a[@id='btn_schedule-now']/span";
    public  $theatreSessionDate = "//*[@class='available']";
    public  $theatreSessionTime = "//*[@class='timeblock available bookable']";
    public  $sessionComments = "//textarea[@id='session_comments']";
    public  $operationComments = "//textarea[@id='operation_comments']";
    public  $confirmSlot = "//button[@id='confirm_slot']";
}