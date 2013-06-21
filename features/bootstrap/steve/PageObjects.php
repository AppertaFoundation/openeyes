<?php
/**
 * created by jetbrains phpstorm.
 * user: admin
 * date: 17/06/2013
 * time: 08:49
 * to change this template use file | settings | file templates.
 */

class pageobjects {

//login
public $login = "//input[@id='loginform_username']";
public $pass = "//input[@id='loginform_password']";
public $siteid = "//select[@id='loginform_siteid']";
public $loginbutton = "//button[@id='login_button']";
public $mainsearch = "//input[@id='query']";
public $searchsubmit = "//button[@type='submit']";

//diagnosis/patient view page
public $opthdiagnosis = "//button[@id='btn-add_new_ophthalmic_diagnosis']";
public $opthdisorder = "//select[@id='diagnosisselection_ophthalmic_disorder_id']";
public $opthrighteye = "//input[@name='diagnosis_eye']";
public $opthlefteye = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[3]";
public $opthbotheyes = "//form[@id='add-ophthalmic-diagnosis']/div[3]/input[2]";
public $opthday = "//select[@name='diagnosis_day']";
public $opthmonth = "//select[@name='diagnosis_month']";
public $opthyear = "//select[@name='diagnosis_year']";
public $opthsavebutton = "//button[@type='submit']";
public $sysdiagnosis = "//button[@id='btn-add_new_systemic_diagnosis']";
public $sysdisorder = "//select[@id='diagnosisselection_systemic_disorder_id']";
public $sysnoneside = "(//input[@name='diagnosis_eye'])[4]";
public $sysrightside = "(//input[@name='diagnosis_eye'])[5]";
public $sysbothside = "(//input[@name='diagnosis_eye'])[6]";
public $sysleftside = "(//input[@name='diagnosis_eye'])[7]";
public $sysday = "(//select[@name='diagnosis_day'])[2]";
public $sysdmonth = "(//select[@name='diagnosis_month'])[2]";
public $sysyear = "(//select[@name='diagnosis_year'])[2]";
public $syssavebutton = "(//button[@type='submit'])[5]";
public $selectallergy = "//select[@id='allergy_id']";
public $addallergy = "//button[@id='btn_add_allergy']";
public $cviedit = "//button[@id='btn-edit_oph_info']";
public $cvistatus = "//select[@id='patientophinfo_cvi_status_id']";
public $cviday = "(//select[@name='diagnosis_day'])[3]";
public $cvimonth = "(//select[@name='diagnosis_month'])[3]";
public $cviyear = "(//select[@name='diagnosis_year'])[3]";
public $cvisave = "(//button[@type='submit'])[7]";
public $addmedication = "//button[@id='btn-add_medication']";
public $medicationselect = "//select[@id='drug_id']";
public $medicationroute = "//select[@id='route_id']";
public $medicationfrequency = "//select[@id='frequency_id']";
public $medicationcalendar = "//input[@id='start_date']";
//public $passdatefromtable = "//*[@id='ui-datepicker-div']/table/tbody//a[contains(text(),'"+ openeyesstepdefs.datepass+"')]";

//*[@id='ui-datepicker-div']/table/tbody/tr[2]/td[3]/a
//public $passdatefromtable2 = "//a[contains(text(),'"+ openeyesstepdefs.datepass2+"')]";
public $medicationsave = "(//button[@type='submit'])[9]";
public $removediagnosislink = "//*[@class='small removediagnosis']//*[contains(text(), 'remove')]";
public $removediagnosis = "//*[contains(text(), 'remove diagnosis')]";
public $removemedicationlink = "//*[@class='small removemedication']//*[contains(text(), 'remove')]";
public $removemedication = "//*[contains(text(), 'remove medication')]";
public $removeallergylink = "//*[@class='small removeallergy']//*[contains(text(), 'remove')]";
public $removeallergy = "//*[@class='classy red venti btn_remove_allergy']//*[contains(text(), 'remove allergy')]";

//episodes & events page
public $createviewepisodeevent = "//*[@id='content']//*[contains(text(), 'create or view episodes and events')]";
public $addnewevent = "//button[@id='addnewevent']";

//adding new event
public $anaestheticsatisfaction = "//*[@id='add-event-select-type']//*[contains(text(), 'anaesthetic satisfaction audit')]";
public $consentform = "//*[@id='add-event-select-type']//*[contains(text(), 'consent form')]";
public $correspondence = "//*[@id='add-event-select-type']//*[contains(text(), 'correspondence')]";
public $examination = "//*[@id='add-event-select-type']//*[contains(text(), 'examination')]";
public $operationbooking = "//*[@id='add-event-select-type']//*[contains(text(), 'operation booking')]";
public $operationnote = "//*[@id='add-event-select-type']//*[contains(text(), 'operation note')]";
public $phasing = "//*[@id='add-event-select-type']//*[contains(text(), 'phasing')]";
public $prescription = "//*[@id='add-event-select-type']//*[contains(text(), 'prescription')]";

//anaesthetic satisfaction audit
//anaesthesist
public $anaesthetist= "//select[@id='element_ophouanaestheticsatisfactionaudit_anaesthetist_anaesthetist_select']";
//satisfaction
public $pain = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_pain']";
public $nausea = "//input[@id='element_ophouanaestheticsatisfactionaudit_satisfaction_nausea']";
public $vomitcheckbox = "//div[@id='div_element_ophouanaestheticsatisfactionaudit_satisfaction_vomited']/div[2]/input[2]"; //id element didnt work for this checkbox
//vital signs
public $respirotaryrate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_respiratory_rate_id']";
public $oxygensaturation = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_oxygen_saturation_id']";
public $systolicbloodpressure = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_systolic_id']";
public $bodytemp = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_body_temp_id']";
public $heartrate = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_heart_rate_id']";
public $consciouslevelavpu = "//select[@id='element_ophouanaestheticsatisfactionaudit_vitalsigns_conscious_lvl_id']";
//notes
public $comments = "//textarea[@id='element_ophouanaestheticsatisfactionaudit_notes_comments']";
public $dischargeyes = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_1']";
public $dischargeno = "//input[@id='element_ophouanaestheticsatisfactionaudit_notes_ready_for_discharge_id_2']";

public $saveevent = "//button[@id='et_save_draft']";
public $cancelevent = "//*[@id='clinical-create']//*[contains(text(), 'cancel')]";
public $cancelexam = "//*[@id='clinical-create']/div[1]/div/ul/li[2]/a/span";

//correspondence
public $sitedropdown = "//select[@id='elementletter_site_id']";
public $addresstarget = "//select[@id='address_target']";
public $letteraddress = "//textarea[@id='elementletter_address']";
public $letterdate = "//input[@id='elementletter_date_0']";
public $clinicdate = "//input[@id='elementletter_clinic_date_0']";
public $macro = "//select[@id='macro']";
public $letterintro = "//textarea[@id='elementletter_introduction']";
public $letterref = "//textarea[@id='elementletter_re']";
public $introduction = "//select[@id='introduction']";
public $diagnosis = "//select[@id='diagnosis']";
public $management = "//select[@id='management']";
public $drugs = "//select[@id='drugs']";
public $outcome = "//select[@id='outcome']";
public $letterfooter = "//textarea[@id='elementletter_footer']";
public $lettercc = "//select[@id='cc']";
public $letterelement = "//textarea[@id='elementletter_cc']";
public $addenclosure = "//button[@type='button']//*[contains(text(), 'add')]";

//examination
public $history = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='blurred vision, ']";
public $severity = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='mild, ']";
public $onset = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[3]//*[@value='gradual onset, ']";
public $eye = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[4]//*[@value='left eye, ']";
public $duration = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[5]//*[@value='1 week, ']";
public $opencomorbidities = "//div[@id='active_elements']/div/div[4]/div/h5";
public $addcomorbidities = "//div[@id='comorbidities_unselected']/select";
public $openleftva = "//div[@id='active_elements']/div[2]/div[2]/div[2]/div/div/button"; //needs unique id
public $snellenleft = "//select[@id='visualacuity_reading_0_value']";
public $readingleft = "//select[@id='visualacuity_reading_0_method_id']";
public $openrightva = "//button[@type='button']";
public $snellenright = "//select[@id='visualacuity_reading_1_value']";
public $readingright = "//select[@id='visualacuity_reading_1_method_id']";
public $intraocularright = "//select[@id='element_ophciexamination_intraocularpressure_right_reading_id']";
public $instrumentright = "//select[@id='element_ophciexamination_intraocularpressure_right_instrument_id']";
public $intraocularleft = "//select[@id='element_ophciexamination_intraocularpressure_left_reading_id']";
public $instrumentleft = "//select[@id='element_ophciexamination_intraocularpressure_left_instrument_id']";

public $dilationright = "//select[@id='dilation_drug_right']";
public $dropsleft = "//select[@name='dilation_treatment[0][drops]']";
public $dilationleft = "//select[@id='dilation_drug_left']";
public $dropsright = "//select[@name='dilation_treatment[1][drops]']";

public $expandrefraction = "//*[@id='inactive_elements']//*[@data-element-type-name='refraction']";

public $sphereleft = "//select[@id='element_ophciexamination_refraction_left_sphere_sign']";
public $sphereleftint = "//select[@id='element_ophciexamination_refraction_left_sphere_integer']";
public $sphereleftfraction = "//select[@id='element_ophciexamination_refraction_left_sphere_fraction']";
public $cylinderleft = "//select[@id='element_ophciexamination_refraction_left_cylinder_sign']";
public $cylinderleftint = "//select[@id='element_ophciexamination_refraction_left_cylinder_integer']";
public $cylinderleftfraction = "//select[@id='element_ophciexamination_refraction_left_cylinder_fraction']";
public $sphereleftaxis = "//input[@id='element_ophciexamination_refraction_left_axis']";
public $spherelefttype = "//select[@id='element_ophciexamination_refraction_left_type_id']";


public $sphereright = "//select[@id='element_ophciexamination_refraction_right_sphere_sign']";
public $sphererightint = "//select[@id='element_ophciexamination_refraction_right_sphere_integer']";
public $sphererightfraction = "//select[@id='element_ophciexamination_refraction_right_sphere_fraction']";
public $cylinderright = "//select[@id='element_ophciexamination_refraction_right_cylinder_sign']";
public $cylinderrightint = "//select[@id='element_ophciexamination_refraction_right_cylinder_integer']";
public $cylinderrightfraction = "//select[@id='element_ophciexamination_refraction_right_cylinder_fraction']";
public $sphererightaxis = "//input[@id='element_ophciexamination_refraction_right_axis']";
public $sphererighttype = "//select[@id='element_ophciexamination_refraction_right_type_id']";

public $saveexamination = "//*[@id='et_save']";

//operation
public $diagnosisrighteye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_2']";
public $diagnosislefteye = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_1']";
public $diagnosisbotheyes = "//input[@id='element_ophtroperationbooking_diagnosis_eye_id_3']";
public $operationdiagnosis = "//select[@id='element_ophtroperationbooking_diagnosis_disorder_id']";
public $operationprocedure = "//*[@id='select_procedure_id_procs']";

public $expandgonioscopy = "//*[@id='inactive_elements']//*[@data-element-type-name='gonioscopy']";
public $expandadnexalcomorbidity = "//*[@id='inactive_elements']//*[@data-element-type-name='adnexal comorbidity']";
public $expandanteriorsegment = "//*[@id='inactive_elements']//*[@data-element-type-name='anterior segment']";
public $expandpupillaryabnormalities = "//*[@id='inactive_elements']//*[@data-element-type-name='pupillary abnormalities']";
public $expandopticdisc = "//*[@id='inactive_elements']//*[@data-element-type-name='optic disc']";
public $expandposteriorpole = "//*[@id='inactive_elements']//*[@data-element-type-name='posterior pole']";
public $expanddiagnoses = "//*[@id='inactive_elements']//*[@data-element-type-name='diagnoses']";
public $expandinvestigation = "//*[@id='inactive_elements']//*[@data-element-type-name='investigation']";
public $expandclinicalmanagement = "//*[@id='inactive_elements']//*[@data-element-type-name='clinical management']";
public $expandrisks = "//*[@id='inactive_elements']//*[@data-element-type-name='risks']";
public $expandclinicoutcome = "//*[@id='inactive_elements']//*[@data-element-type-name='clinic outcome']";
public $expandconclusion = "//*[@id='inactive_elements']//*[@data-element-type-name='conclusion']";

//Phasing
public$PhasingInstrumentRight = "//select[@id='Element_OphCiPhasing_IntraocularPressure_right_instrument_id']";
public$PhasingDilationRight = "//input[@id='Element_OphCiPhasing_IntraocularPressure_right_dilated_1']";
public$PhasingPressureRight = "//input[@id='intraocularpressure_reading_0_value']";
public$PhasingCommentsRight = "//textarea[@id='Element_OphCiPhasing_IntraocularPressure_right_comments']";
public$PhasingInstrumentLeft = "//select[@id='Element_OphCiPhasing_IntraocularPressure_left_instrument_id']";
public$PhasingDilationLeft = "//input[@id='Element_OphCiPhasing_IntraocularPressure_left_dilated_1']";
public$PhasingPressureLeft = "//input[@id='intraocularpressure_reading_1_value']";
public$PhasingCommentsLeft = "//textarea[@id='Element_OphCiPhasing_IntraocularPressure_left_comments']";

    //Prescription
public$PrescriptionDropdown = "//*[@id='common_drug_id']";
public$PrescriptionStandardSet = "//*[@id='drug_set_id']";
public$PrescriptionDose = "//*[@id='prescription_item_0_dose']";
public$PrescriptionRoute = "//*[@id='prescription_item_0_route_id']";
public$PrescriptionOptions = "//*[@id='prescription_item_0_route_option_id']";
public$PrescriptionFrequency = "//*[@id='prescription_item_0_frequency_id']";
public$PrescriptionDuration = "//*[@id='prescription_item_0_duration_id']";
public$PrescriptionComments = "//textarea[@id='Element_OphDrPrescription_Details_comments']";
    
    
}