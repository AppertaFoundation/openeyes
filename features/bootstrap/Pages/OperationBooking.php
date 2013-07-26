<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class OperationBooking extends Page
{
    public static  $diagnosisRightEye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_2']";
    public static  $diagnosisLeftEye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_1']";
    public static  $diagnosisBothEyes = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_3']";
    public static  $operationDiagnosis = "//select[@id='element_ophtroperationbooking_diagnosis_disorder_id']";
    public static  $operationProcedure = "//*[@id='select_procedure_id_procs']";
    public static  $operationRightEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']";
    public static  $operationBothEyes = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']";
    public static  $operationLeftEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']";
    public static  $consultantYes = "//input[@id='element_ophtroperationbooking_operation_consultant_required_1']";
    public static  $consultantNo = "//input[@id='element_ophtroperationbooking_operation_consultant_required_0']";
    public static  $anaestheticTopical= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_1']";
    public static  $anaestheticLac= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_2']";
    public static  $anaestheticLa= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_3']";
    public static  $anaestheticLas= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_4']";
    public static  $anaestheticGa= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_5']";
    public static  $postOpStayYes = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
    public static  $postOpStayNo = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
    public static  $operationSite = "//select[@id='element_ophtroperationbooking_operation_site_id']";
    public static  $routineOperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_1']";
    public static  $urgentOperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_2']";
    public static  $decisionOpen = "//input[@id='element_ophtroperationbooking_operation_decision_date_0']";
    public static  $addComments = "//textarea[@id='element_ophtroperationbooking_operation_comments']";
    public static  $scheduleLater = "//button[@id='et_schedulelater']";
    public static  $scheduleAndSaveNow = "//button[@id='et_schedulenow']";
    public static  $scheduleNowButton = "//a[@id='btn_schedule-now']/span";
    public static  $theatreSessionDate = "//*[@class='available']";
    public static  $theatreSessionTime = "//*[@class='timeBlock available bookable']";
    public static  $sessionComments = "//textarea[@id='session_comments']";
    public static  $operationComments = "//textarea[@id='operation_comments']";
    public static  $confirmSlot = "//button[@id='confirm_slot']";
}