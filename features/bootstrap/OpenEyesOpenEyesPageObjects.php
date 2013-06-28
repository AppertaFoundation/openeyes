<?php
/**
 * created by jetbrains phpstorm.
 * user: admin
 * date: 17/06/2013
 * time: 08:49
 * to change this template use file | settings | file templates.
 */

class OpenEyesPageObjects {

//login
public static $login = "//input[@id='loginform_username']";
public static $pass = "//input[@id='loginform_password']";
public static $siteId = "//select[@id='loginform_siteid']";
public static $loginButton = "//button[@id='login_button']";
public static $mainSearch = "//input[@id='query']";
public static $searchSubmit = "//button[@type='submit']";
public static $firmDropdown = "//*[@id='selected_firm_id']";

//diagnosis/patient view page
public static $opthDiagnosis = "//button[@id='btn-add_new_ophthalmic_diagnosis']";
public static $opthDisorder = "//select[@id='diagnosisselection_ophthalmic_disorder_id']";
public static $opthRighteye = "//input[@name='diagnosis_eye']";
public static $opthLefteye = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]";
public static $opthBotheyes = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]";
public static $opthDay = "//select[@name='diagnosis_day']";
public static $opthMonth = "//select[@name='diagnosis_month']";
public static $opthYear = "//select[@name='diagnosis_year']";
public static $opthSaveButton = "//button[@type='submit']";
public static $sysDiagnosis = "//button[@id='btn-add_new_systemic_diagnosis']";
public static $sysDisorder = "//select[@id='diagnosisselection_systemic_disorder_id']";
public static $sysNoneSide = "(//input[@name='diagnosis_eye'])[4]";
public static $sysRightSide = "(//input[@name='diagnosis_eye'])[5]";
public static $sysBothSide = "(//input[@name='diagnosis_eye'])[6]";
public static $sysLeftSide = "(//input[@name='diagnosis_eye'])[7]";
public static $sysDay = "(//select[@name='diagnosis_day'])[2]";
public static $sysMonth = "(//select[@name='diagnosis_month'])[2]";
public static $sysYear = "(//select[@name='diagnosis_year'])[2]";
public static $sysSaveButton = "(//button[@type='submit'])[5]";
public static $selectAllergy = "//select[@id='allergy_id']";
public static $addAllergy = "//button[@id='btn_add_allergy']";
public static $cviEdit = "//button[@id='btn-edit_oph_info']";
public static $cviStatus = "//select[@id='patientophinfo_cvi_status_id']";
public static $cviDay = "(//select[@name='diagnosis_day'])[3]";
public static $cviMonth = "(//select[@name='diagnosis_month'])[3]";
public static $cviYear = "(//select[@name='diagnosis_year'])[3]";
public static $cviSave = "(//button[@type='submit'])[7]";
public static $addMedication = "//button[@id='btn-add_medication']";
public static $medicationSelect = "//select[@id='drug_id']";
public static $medicationRoute = "//select[@id='route_id']";
public static $medicationFrequency = "//select[@id='frequency_id']";
public static $medicationCalendar = "//input[@id='start_date']";

//Pass Calendar Day from Example Table
public static function passDateFromTable ($dateFrom) {
return "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'". $dateFrom ."')]";
}

public static $medicationsave = "(//button[@type='submit'])[9]";
public static $removediagnosislink = "//*[@class='small removediagnosis']//*[contains(text(), 'remove')]";
public static $removediagnosis = "//*[contains(text(), 'remove diagnosis')]";
public static $removemedicationlink = "//*[@class='small removemedication']//*[contains(text(), 'remove')]";
public static $removemedication = "//*[contains(text(), 'remove medication')]";
public static $removeallergylink = "//*[@class='small removeallergy']//*[contains(text(), 'remove')]";
public static $removeallergy = "//*[@class='classy red venti btn_remove_allergy']//*[contains(text(), 'remove allergy')]";

//episodes & events page
public static $createviewepisodeevent = "//*[@id='content']//*[contains(text(), 'create or view episodes and events')]";
public static $addnewevent = "//button[@id='addnewevent']";

//adding new event
public static $anaestheticsatisfaction = "//*[@id='add-event-select-type']//*[contains(text(), 'anaesthetic satisfaction audit')]";
public static $consentform = "//*[@id='add-event-select-type']//*[contains(text(), 'consent form')]";
public static $correspondence = "//*[@id='add-event-select-type']//*[contains(text(), 'correspondence')]";
public static $examination = "//*[@id='add-event-select-type']//*[contains(text(), 'examination')]";
public static $operationbooking = "//*[@id='add-event-select-type']//*[contains(text(), 'operation booking')]";
public static $operationnote = "//*[@id='add-event-select-type']//*[contains(text(), 'operation note')]";
public static $phasing = "//*[@id='add-event-select-type']//*[contains(text(), 'phasing')]";
public static $prescription = "//*[@id='add-event-select-type']//*[contains(text(), 'prescription')]";

//anaesthetic satisfaction audit
//anaesthesist
public static $anaesthetist= "//select[@id='element_ophouanaestheticsatisfactionaudit_anaesthetist_anaesthetist_select']";
//satisfaction
public static $pain = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_pain']";
public static $nausea = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_nausea']";
public static $vomitcheckbox = "//div[@id='div_element_ophouanaestheticsatisfactionaudit_satisfaction_vomited']/div[2]/input[2]"; //id element didnt work for this checkbox
//vital signs
public static $respirotaryrate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_respiratory_rate_id']";
public static $oxygensaturation = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_oxygen_saturation_id']";
public static $systolicbloodpressure = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_systolic_id']";
public static $bodytemp = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_body_temp_id']";
public static $heartrate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_heart_rate_id']";
public static $consciouslevelavpu = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_conscious_lvl_id']";
//notes
public static $comments = "//textarea[@id='element_ophouanaestheticsatisfactionaudit_notes_comments']";
public static $dischargeyes = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_1']";
public static $dischargeno = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_2']";

public static $saveevent = "//button[@id='et_save_draft']";
public static $cancelevent = "//*[@id='clinical-create']//*[contains(text(), 'cancel')]";
public static $cancelexam = "//*[@id='clinical-create']/div[1]/div/ul/li[2]/a/span";

//correspondence
public static $sitedropdown = "//select[@id='elementletter_site_id']";
public static $addresstarget = "//select[@id='address_target']";
public static $letteraddress = "//textarea[@id='elementletter_address']";
public static $letterdate = "//input[@id='elementletter_date_0']";
public static $clinicdate = "//input[@id='elementletter_clinic_date_0']";
public static $macro = "//select[@id='macro']";
public static $letterintro = "//textarea[@id='elementletter_introduction']";
public static $letterref = "//textarea[@id='elementletter_re']";
public static $introduction = "//select[@id='introduction']";
public static $diagnosis = "//select[@id='diagnosis']";
public static $management = "//select[@id='management']";
public static $drugs = "//select[@id='drugs']";
public static $outcome = "//select[@id='outcome']";
public static $letterfooter = "//textarea[@id='elementletter_footer']";
public static $lettercc = "//select[@id='cc']";
public static $letterelement = "//textarea[@id='elementletter_cc']";
public static $addenclosure = "//button[@type='button']//*[contains(text(), 'add')]";

//examination
public static $history = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='blurred vision, ']";
public static $severity = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='mild, ']";
public static $onset = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[3]//*[@value='gradual onset, ']";
public static $eye = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[4]//*[@value='left eye, ']";
public static $duration = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[5]//*[@value='1 week, ']";
public static $opencomorbidities = "//div[@id='active_elements']/div/div[4]/div/h5";
public static $addcomorbidities = "//div[@id='comorbidities_unselected']/select";
public static $openVisualAcuity = "//*[@id='inactive_elements']//*[contains(text(), 'Visual Acuity')]";
public static $openleftva = "//div[@id='active_elements']/div[2]/div[2]/div[2]/div/div/button"; //needs unique id
public static $snellenleft = "//select[@id='visualacuity_reading_0_value']";
public static $readingleft = "//select[@id='visualacuity_reading_0_method_id']";
public static $openrightva = "//button[@type='button']";
public static $snellenright = "//select[@id='visualacuity_reading_1_value']";
public static $readingright = "//select[@id='visualacuity_reading_1_method_id']";
public static $openIntraocularPressure = "//*[@id='inactive_elements']//*[contains(text(), 'Intraocular Pressure')]";
public static $intraocularright = "//select[@id='element_ophciexamination_intraocularpressure_right_reading_id']";
public static $instrumentright = "//select[@id='element_ophciexamination_intraocularpressure_right_instrument_id']";
public static $intraocularleft = "//select[@id='element_ophciexamination_intraocularpressure_left_reading_id']";
public static $instrumentleft = "//select[@id='element_ophciexamination_intraocularpressure_left_instrument_id']";

public static $openDilation = "//*[@id='inactive_elements']//*[contains(text(), 'Dilation')]";
public static $dilationright = "//select[@id='dilation_drug_right']";
public static $dropsleft = "//select[@name='dilation_treatment[0][drops]']";
public static $dilationleft = "//select[@id='dilation_drug_left']";
public static $dropsright = "//select[@name='dilation_treatment[1][drops]']";

public static $expandrefraction = "//*[@id='inactive_elements']//*[@data-element-type-name='refraction']";

public static $sphereleft = "//select[@id='element_ophciexamination_refraction_left_sphere_sign']";
public static $sphereleftint = "//select[@id='element_ophciexamination_refraction_left_sphere_integer']";
public static $sphereleftfraction = "//select[@id='element_ophciexamination_refraction_left_sphere_fraction']";
public static $cylinderleft = "//select[@id='element_ophciexamination_refraction_left_cylinder_sign']";
public static $cylinderleftint = "//select[@id='element_ophciexamination_refraction_left_cylinder_integer']";
public static $cylinderleftfraction = "//select[@id='element_ophciexamination_refraction_left_cylinder_fraction']";
public static $sphereleftaxis = "//input[@id='element_ophciexamination_refraction_left_axis']";
public static $spherelefttype = "//select[@id='element_ophciexamination_refraction_left_type_id']";


public static $sphereright = "//select[@id='element_ophciexamination_refraction_right_sphere_sign']";
public static $sphererightint = "//select[@id='element_ophciexamination_refraction_right_sphere_integer']";
public static $sphererightfraction = "//select[@id='element_ophciexamination_refraction_right_sphere_fraction']";
public static $cylinderright = "//select[@id='element_ophciexamination_refraction_right_cylinder_sign']";
public static $cylinderrightint = "//select[@id='element_ophciexamination_refraction_right_cylinder_integer']";
public static $cylinderrightfraction = "//select[@id='element_ophciexamination_refraction_right_cylinder_fraction']";
public static $sphererightaxis = "//input[@id='element_ophciexamination_refraction_right_axis']";
public static $sphererighttype = "//select[@id='element_ophciexamination_refraction_right_type_id']";

public static $expandgonioscopy = "//*[@id='inactive_elements']//*[@data-element-type-name='gonioscopy']";
public static $expandadnexalcomorbidity = "//*[@id='inactive_elements']//*[@data-element-type-name='adnexal comorbidity']";
public static $expandanteriorsegment = "//*[@id='inactive_elements']//*[@data-element-type-name='anterior segment']";
public static $expandpupillaryabnormalities = "//*[@id='inactive_elements']//*[@data-element-type-name='pupillary abnormalities']";
public static $expandopticdisc = "//*[@id='inactive_elements']//*[@data-element-type-name='optic disc']";
public static $expandposteriorpole = "//*[@id='inactive_elements']//*[@data-element-type-name='posterior pole']";
public static $expanddiagnoses = "//*[@id='inactive_elements']//*[@data-element-type-name='diagnoses']";
public static $expandinvestigation = "//*[@id='inactive_elements']//*[@data-element-type-name='investigation']";
public static $expandclinicalmanagement = "//*[@id='inactive_elements']//*[@data-element-type-name='clinical management']";
public static $expandrisks = "//*[@id='inactive_elements']//*[@data-element-type-name='risks']";
public static $expandclinicoutcome = "//*[@id='inactive_elements']//*[@data-element-type-name='clinic outcome']";
public static $expandconclusion = "//*[@id='inactive_elements']//*[@data-element-type-name='conclusion']";

public static $saveexamination = "//*[@id='et_save']";

//operation
public static $diagnosisrighteye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_2']";
public static $diagnosislefteye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_1']";
public static $diagnosisbotheyes = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_3']";
public static $operationdiagnosis = "//select[@id='element_ophtroperationbooking_diagnosis_disorder_id']";
public static $operationprocedure = "//*[@id='select_procedure_id_procs']";
public static $operationRightEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_2']";
public static $operationBothEyes = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_3']";
public static $operationLeftEye = "//input[@id='Element_OphTrOperationbooking_Operation_eye_id_1']";
public static $consultantyes = "//input[@id='element_ophtroperationbooking_operation_consultant_required_1']";
public static $consultantno = "//input[@id='element_ophtroperationbooking_operation_consultant_required_0']";
public static $anaesthetictopical= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_1']";
public static $anaestheticlac= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_2']";
public static $anaestheticla= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_3']";
public static $anaestheticlas= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_4']";
public static $anaestheticga= "//input[@id='element_ophtroperationbooking_operation_anaesthetic_type_id_5']";
public static $postopstayyes = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
public static $postopstayno = "//input[@id='element_ophtroperationbooking_operation_overnight_stay_1']";
public static $operationsite = "//select[@id='element_ophtroperationbooking_operation_site_id']";
public static $routineoperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_1']";
public static $urgentoperation = "//input[@id='element_ophtroperationbooking_operation_priority_id_2']";
public static $decisionopen = "//input[@id='element_ophtroperationbooking_operation_decision_date_0']";
public static $addcomments = "//textarea[@id='element_ophtroperationbooking_operation_comments']";
public static $schedulelater = "//button[@id='et_schedulelater']";
public static $scheduleandsavenow = "//button[@id='et_schedulenow']";
public static $schedulenowbutton = "//a[@id='btn_schedule-now']/span";
public static $theatresessiondate = "//*[@class='available']";
public static $theatresessiontime = "//*[@class='timeblock available bookable']";
public static $sessioncomments = "//textarea[@id='session_comments']";
public static $operationcomments = "//textarea[@id='operation_comments']";
public static $confirmslot = "//button[@id='confirm_slot']";

//Phasing
public static $phasinginstrumentright = "//select[@id='element_ophciphasing_intraocularpressure_right_instrument_id']";
public static $phasingdilationright = "//input[@id='element_ophciphasing_intraocularpressure_right_dilated_1']";
public static $phasingpressureright = "//input[@id='intraocularpressure_reading_0_value']";
public static $phasingcommentsright = "//textarea[@id='element_ophciphasing_intraocularpressure_right_comments']";
public static $phasinginstrumentleft = "//select[@id='element_ophciphasing_intraocularpressure_left_instrument_id']";
public static $phasingdilationleft = "//input[@id='element_ophciphasing_intraocularpressure_left_dilated_1']";
public static $phasingpressureleft = "//input[@id='intraocularpressure_reading_1_value']";
public static $phasingcommentsleft = "//textarea[@id='element_ophciphasing_intraocularpressure_left_comments']";

//Prescription
public static $prescriptiondropdown = "//*[@id='common_drug_id']";
public static $prescriptionstandardset = "//*[@id='drug_set_id']";
public static $prescriptiondose = "//*[@id='prescription_item_0_dose']";
public static $prescriptionroute = "//*[@id='prescription_item_0_route_id']";
public static $prescriptionoptions = "//*[@id='prescription_item_0_route_option_id']";
public static $prescriptionfrequency = "//*[@id='prescription_item_0_frequency_id']";
public static $prescriptionduration = "//*[@id='prescription_item_0_duration_id']";
public static $prescriptioncomments = "//textarea[@id='element_ophdrprescription_details_comments']";
    


}