<?php
use Behat\Behat\Exception\BehaviorException;
class Examination extends OpenEyesPage {
	protected $path = "OphCiExamination/Default/create?patient_id={patientId}";
	protected $elements = array (
		'history' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_History_description']//*[@value='blurred vision, ']"
		),
		'severity' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_History_description']//*[@value='mild, ']"
		),
		'onset' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_History_description']//*[@value='gradual onset, ']"
		),
		'eye' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_History_description']//*[@value='left eye, ']"
		),
		'duration' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_History_description']//*[@value='1 week, ']"
		),
        'activeElements' => array(
            'css' => ".js-active-elements"
        ),
        'removeIcon' => array(
            'css' => ".js-remove-element"
        ),
        'Allergy' => array(
            'css' => ".OEModule_OphCiExamination_models_Allergies"
        ),
        'allergyPopup' => array(
            'xpath' => "//*[@id='history-allergy-popup']"
        ),
        'addAllergyBtn' => array(
            'xpath' => "//*[@id='add-allergy-btn']"
        ),
        'NVA' => array (
            'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity"
        ),
        'leftEye' => array(
            'css' => ".left-eye"
        ),
        'rightEye' => array(
            'css' => ".right-eye"
        ),
        'addSide' => array(
            'css' => ".add-side"
        ),
        'NVAMethod' => array(
            'css' => ".add-options[data-id=method]"
        ),
        'NVAReading' => array(
            'css' => ".add-options[data-id=reading_val]"
        ),
        'addReading' => array(
            'css' => ".addReading"
        ),
        'confirmAdderButton' => array(
            'css' => ".oe-add-select-search button"
        ),
        'addLeftNVA' => array (
          'xpath' => "//*[@class='OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity']//*[@class='left-eye']//*[@class='add-side']"
        ),
		'addRightNVA' => array (
		    'xpath' => "//*[@class='OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity']//*[@class='right-eye']//*[@class='add-side']"
        ),
		'openComorbidities' => array (
			'xpath' => "//div[@class='sub-elements inactive']//*[@data-element-type-name='Comorbidities']"
		),
		'addComorbidities' => array (
			'xpath' => "//select[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Comorbidities_items']"
		),
		'expandVisualAcuity' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Visual Acuity')]"
		),
		'expandNearVisualAcuity' => array (
			'xpath' => "//*[@id='side-element-Near-Visual-Acuity']"
		),
        'expandAllergies' => array (
            'xpath' => "//*[@id='side-element-Allergies']"
        ),

		'expandAnteriorSegment' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Anterior Segment')]"
		),
		'expandVisualFunction' => array (
			'xpath' => "//*[@class='collapse-group-header'][contains(text(),'Visual Function')]"
		),
        'expandHistory' => array (
            'xpath' => "//*[@class='collapse-group-header'][contains(text(),'History')]"
        ),
		'visualAcuityUnitChange' => array (
			'xpath' => "//*[@id='visualacuity_unit_change']"
		),
		'nearVisualAcuityUnitChange' => array (
			'xpath' => "//*[@id='nearvisualacuity_unit_change']"
		),
		'addRightVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye button.addReading"
		),
		'firstRightVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(1) .va-selector"
		),
		'firstRightVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(1) .method_id"
		),
		'firstRightNearVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity .right-eye .va_readings tr:nth-of-type(1) .va-selector"
		),
		'firstRightNearVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity .right-eye .va_readings tr:nth-of-type(1) .method_id"
		),
		'rightTubePatch' => array (
			//'xpath' => "//*[@id='Patchright_5']//*[@class='icon-ed-Patch']"
			'xpath' => "//*[@id='Patchright_315']//*[@class='ed-button']"
		),
		'rightMaterial' => array (
			'xpath' => "//*[@id='ed_canvas_edit_right_315_material_control']"
		),
		'removeFirstRightVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(1) .removeReading"
		),
		'secondRightVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(2) .va-selector"
		),
		'secondRightVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(2) .method_id"
		),
		'removeSecondRightVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .right-eye .va_readings tr:nth-of-type(2) .removeReading"
		),

		'addLeftVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye button.addReading"
		),
		'firstLeftVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(1) .va-selector"
		),
		'firstLeftVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(1) .method_id"
		),
		'firstLeftNearVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity .left-eye .va_readings tr:nth-of-type(1) .va-selector"
		),
		'firstLeftNearVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity .left-eye .va_readings tr:nth-of-type(1) .method_id"
		),
		'removeFirstLeftVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(1) .removeReading"
		),
		'secondLeftVisualAcuityReading' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(2) .va-selector"
		),
		'secondLeftVisualAcuityCorrection' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(2) .method_id"
		),
		'removeSecondLeftVisualAcuity' => array (
			'css' => ".OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .left-eye .va_readings tr:nth-of-type(2) .removeReading"
		),

		'expandDRGrading' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'DR Grading')]"
		),

		'openIntraocularPressure' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Intraocular Pressure')]"
		),
		'addIntraocularRight' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_add_left']"
		),
		'intraocularRight' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_left_values_0_reading_id']"
		),
		'instrumentRight' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_left_values_0_instrument_id']"
		),
		'addIntraocularLeft' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_add_right']"
		),
		'intraocularLeft' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_right_values_0_reading_id']"
		),
		'instrumentLeft' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_right_values_0_instrument_id']"
		),

		'openDilation' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Dilation')]"
		),
		'dilationRight' => array (
			'xpath' => "//select[@id='dilation_drug_right']"
		),
		'dropsLeft' => array (
			'css' => "#dilation_left select"
		),
		'dilationLeft' => array (
			'xpath' => "//select[@id='dilation_drug_left']"
		),
		'dropsRight' => array (
			'css' => "#dilation_right select"
		),
		'removeDilationLeft' => array (
			'xpath' => "//*[@id='dilation_left']//*[contains(text(),'Remove')]"
		),
		'dilationTimeRight' => array (
			'css' => "#dilation_left input:first-child"
		),
		'dilationTimeLeft' => array (
			'css' => "#dilation_right input:first-child"
		),
		'dilationTimeErrorLeft' => array (
			//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Dilation: Left treatment (1): Invalid treatment time')]"
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Left treatment (1)')]"
		),
		'dilationTimeErrorRight' => array (
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Right treatment (1)')]"
		),

		'expandRefraction' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Refraction')]"
		),

		'sphereLeft' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_sphere_sign']"
		),
		'sphereLeftInt' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_sphere_integer']"
		),
		'sphereLeftFraction' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_sphere_fraction']"
		),
		'cylinderLeft' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_cylinder_sign']"
		),
		'cylinderLeftInt' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_cylinder_integer']"
		),
		'cylinderLeftFraction' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_cylinder_fraction']"
		),
		'sphereLeftAxis' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_axis']"
		),
		'sphereLeftType' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_type_id']"
		),

		'sphereRight' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_sphere_sign']"
		),
		'sphereRightInt' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_sphere_integer']"
		),
		'sphereRightFraction' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_sphere_fraction']"
		),
		'cylinderRight' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_cylinder_sign']"
		),
		'cylinderRightInt' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_cylinder_integer']"
		),
		'cylinderRightFraction' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_cylinder_fraction']"
		),
		'sphereRightAxis' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_axis']"
		),
		'sphereRightType' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_type_id']"
		),

		'leftAdnexalComorbidity' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_AdnexalComorbidity_left_description']"
		),
		'rightAdnexalComorbidity' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_AdnexalComorbidity_right_description']"
		),

		'leftAbnormality' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_PupillaryAbnormalities_left_abnormality_id']"
		),
		'rightAbnormality' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_PupillaryAbnormalities_right_abnormality_id']"
		),

		'diagnosesLeftEye' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_Diagnosis_eye_id_1']"
		),
		'diagnosesRightEye' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_Diagnosis_eye_id_2']"
		),
		'diagnosesBothEyes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_OphCiExamination_Diagnosis_eye_id_3']"
		),
		'diagnosesCommonDiagnosis' => array (
			'xpath' => "//*[@id='DiagnosisSelection_disorder_id']"
		),
		'principalDiagnosis' => array (
			'xpath' => "//*[@id='OphCiExamination_diagnoses']//*[@name='principal_diagnosis']"
		),

		'addInvestigation' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_Investigation_description']"
		),

		'riskComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_Risks_comments']"
		),

		'cataractManagementComments' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_Management_comments']"
		),
		'selectFirstEye' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_eye_id_1']"
		),
		'selectSecondEye' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_eye_id_2']"
		),
		'cityRoad' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_city_road'][2]"
		),
		'satellite' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_satellite'][2]"
		),
		'straightforward' => array (
			'xpath' => "//input[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_fast_track']"
		),
		'postOpRefractiveTarget' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_target_postop_refraction']"
		),
		'discussedWithPatientYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_correction_discussed_1']"
		),
		'discussedWithPatientNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_correction_discussed_0']"
		),
		'suitableForSurgeon' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_suitable_for_surgeon_id']"
		),
		'supervisedCheckbox' => array (
			'xpath' => "//input[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_supervised']"
		),

		// 'previousRefractiveSurgeryYes' => array('xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_previous_refractive_surgery_1']//*[@value='1']"),
		'previousRefractiveSurgeryYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_previous_refractive_surgery_1']"
		),
		'previousRefractiveSurgeryNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_previous_refractive_surgery_0']"
		),
		'VitrectomisedEyeYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_vitrectomised_eye_1']"
		),
		'VitrectomisedEyeNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_vitrectomised_eye_0']"
		),

		'rightLaserStatusChoice' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_LaserManagement_right_laser_status_id']"
		),
		'leftLaserStatusChoice' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_LaserManagement_left_laser_status_id']"
		),
		'rightLaserType' => array (
			'xpath' => "//select[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_LaserManagement_left_lasertype_id']"
		),
		'leftLaserType' => array (
			'xpath' => "//select[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_LaserManagement_right_lasertype_id']"
		),

		'noTreatmentCheckbox' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_no_treatment'][2]"
		),
		'noTreatmentReason' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_no_treatment_reason_id']"
		),

		'rightChoroidalRetinal' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_diagnosis1_id']//*[@value='75971007']"
		),
		'diagnosesOfCataract' => array (
			'xpath' => "//*[@id='DiagnosisSelection_condition']//*[contains(text(),'Cataract')]"
		),
		'rightSecondaryTo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_diagnosis2_id']"
		),
		'leftChoroidalRetinal' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_diagnosis1_id']//*[@value='75971007']"
		),
		'leftSecondaryTo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_diagnosis2_id']"
		),
		'rightMacularRetinal' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_diagnosis1_id']//*[@value='37231002']"
		),
		'leftMacularRetinal' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_diagnosis1_id']//*[@value='37231002']"
		),
		'rightVenousRetinalBranchOcclusion' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_diagnosis2_id']//*[@value='24596005']"
		),
		'leftDiabeticMacularOedema' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_diagnosis2_id']//*[@value='312912001']"
		),
		'rightIntendedTreatment' => array (
			'xpath' => "//select[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_treatment_id']"
		),
		'leftIntendedTreatment' => array (
			'xpath' => "//select[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_treatment_id']"
		),

		'expandVisualFields' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Visual Fields')]"
		),
		'expandGonioscopy' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Gonioscopy')]"
		),
		'expandAdnexalComorbidity' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Adnexal Comorbidity')]"
		),
		'expandAnteriorSegment' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Anterior Segment')]"
		),
		'expandPupillaryAbnormalities' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Pupillary Abnormalities')]"
		),
		'expandOpticDisc' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Optic Disc')]"
		),
		'expandPosteriorPole' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Posterior Pole')]"
		),
		'expandDiagnoses' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Diagnoses')]"
		),
		'expandInvestigation' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Investigation')]"
		),

		'expandClinicalManagement' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Clinical Management')]"
		),
		'expandCataractSurgicalManagement' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Cataract Surgical Management')]"
		),
		'expandLaserManagement' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Laser Management')]"
		),

		'expandInjectionManagement' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Injection Management')]"
		),

		'expandOverallManagement' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Overall Management')]"
		),
		'OverallClinicInterval' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_clinic_interval_id']"
		),
		'OverallPhoto' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_photo_id']"
		),
		'OverallOCT' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_oct_id']"
		),
		'OverallVisualFields' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_hfa_id']"
		),
		'OverallComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_comments']"
		),
		'OverallLeftTargetIOP' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_right_target_iop_id']"
		),
		'gonioDropdown' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_gonio_id']"
		),
		'OverallLeftGonio' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_right_gonio_id']"
		),
		'OverallRightTargetIOP' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_left_target_iop_id']"
		),
		'OverallRightGonio' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_left_gonio_id']"
		),

		'expandCurrentManagement' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Current Management plan')]"
		),
		'referralOtherService' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_other_service']"
		),
		'referralRefraction' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_refraction']"
		),
		'referralLVA' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_lva']"
		),
		'referralOrthoptics' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_orthoptics']"
		),
		'referralCLClinic' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_cl_clinic']"
		),

		'investigationsVF' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_vf']"
		),
		'investigationsUS' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_us']"
		),
		'investigationsBiometry' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_biometry']"
		),
		'investigationsOCT' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_oct']"
		),
		'investigationsHRT' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_hrt']"
		),
		'investigationsDiscPhotos' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_disc_photos']"
		),
		'investigationsEDT' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_edt']"
		),

		'rightGlaucomaStatus' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_glaucoma_status_id']"
		),
		'rightDropProblems' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_drop-related_prob_id']"
		),
		'rightDrops' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_drops_id']"
		),
		'rightSurgery' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_surgery_id']"
		),

		'leftGlaucomaStatus' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_glaucoma_status_id']"
		),
		'leftDropProblems' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_drop-related_prob_id']"
		),
		'leftDrops' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_drops_id']"
		),
		'leftSurgery' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_surgery_id']"
		),

		'rightCrtIncreaseLowerHundredYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_1_1']"
		),
		'rightCrtIncreaseLowerHundredNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_1_0']"
		),
		'rightCrtIncreaseMoreThanHundredYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_2_1']"
		),
		'rightCrtIncreaseMoreThanHundredNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_2_0']"
		),
		'rightLossOfFiveLettersYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_3_1']"
		),
		'rightLossOfFiveLettersNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_3_0']"
		),
		'rightLossOfFiveLettersHigherThanFiveYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_4_1']"
		),
		'rightLossOfFiveLettersHigherThanFiveNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_4_0']"
		),

		'leftCrtIncreaseLowerHundredYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_1_1']"
		),
		'leftCrtIncreaseLowerHundredNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_1_0']"
		),
		'leftCrtIncreaseMoreThanHundredYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_2_1']"
		),
		'leftCrtIncreaseMoreThanHundredNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_2_0']"
		),
		'leftLossOfFiveLettersYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_3_1']"
		),
		'leftLossOfFiveLettersNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_3_0']"
		),
		'leftLossOfFiveLettersHigherThanFiveYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_4_1']"
		),
		'leftLossOfFiveLettersHigherThanFiveNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_4_0']"
		),

		'rightFailedLaserYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_5_1']"
		),
		'rightFailedLaserNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_5_0']"
		),
		'rightUnsuitableForLaserYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_6_1']"
		),
		'rightUnsuitableForLaserNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_6_0']"
		),
		'rightPreviousOzurdexYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_7_1']"
		),
		'rightPreviousOzurdexNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_Answer_7_0']"
		),

		'leftCrtIncreaseMoreThanFourHundredYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_8_1']"
		),
		'leftCrtIncreaseMoreThanFourHundredNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_8_0']"
		),
		'leftFovealDamageYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_9_1']"
		),
		'leftFovealDamageNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_9_0']"
		),
		'leftFailedLaserYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_10_1']"
		),
		'leftFailedLaserNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_10_0']"
		),
		'leftUnsuitableForLaserYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_11_1']"
		),
		'leftUnsuitableForLaserNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_11_0']"
		),
		'leftPreviousAntiVEGFyes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_12_1']"
		),
		'leftPreviousAntiVEGFno' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_Answer_12_0']"
		),

		'expandRisks' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Risks')]"
		),
		'expandClinicOutcome' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Clinic Outcome')]"
		),

		'clinicalOutcomeFollowUp' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_status_id']//*[@value='1']"
		),
		'clinicalOutcomeQuantity' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_followup_quantity']"
		),
		'clinicalOutcomePeriod' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_followup_period_id']"
		),
		'clinicalOutcomeSuitablePatientTickbox' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_community_patient']"
		),
		'clinialOutcomeRole' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_role_id']"
		),

		'clinicalOutcomeDischarge' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_status_id']//*[@value='2']"
		),

		'expandConclusion' => array (
			'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'Conclusion')]"
		),
		'conclusionOption' => array (
			'xpath' => "//*[@id='dropDownTextSelection_OEModule_OphCiExamination_models_Element_OphCiExamination_Conclusion_description']"
		),

		'saveExamination' => array (
			'xpath' => "//*[@id='et_save']"
		),
		'examSavedOk' => array (
			'xpath' => "//*[@id='flash-success']"
		),

		// 'CancelExaminationEvent' => array('xpath' => "//*[@id='flash-success']"),

		'CancelExaminationEvent' => array (
			'xpath' => "//*[contains(text(),'Cancel')]"
		),

		'existingRightAxisCheck' => array (
			'xpath' => "//*[@class='js-element-eye right-eye column']//*[contains(text(),'145')]"
		),
		'existingLeftAxisCheck' => array (
			'xpath' => "//*[@class='js-element-eye left-eye column']//*[contains(text(),'38')]"
		),
		'addAllElements' => array (
			'xpath' => "//*[@class='add-all']"
		),
		'removeAllElements' => array (
			'xpath' => "//section[@class='element']"
		),
		'removeAllValidationError' => array (
			//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Event: Cannot create an event without at least one element')]"
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Examination: Cannot create an event without at least one element')]"
		),
		'historyValidationError' => array (
			//spaces in text needs fixing
			//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'History: Description cannot be blank.')]"
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'History:')]"
		),
		'conclusionValidationError' => array (
			//'xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Conclusion: Description cannot be blank.')]"
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Conclusion:')]"
		),
		'investigationValidationError' => array (
			'xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Investigation: Description cannot be blank when there are no child elements')]"
		),
		'dilationValidationErrorLeft' => array (
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Dilation:')]"
			//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Dilation: Dilation: Left Treatments cannot be blank.')]"
		),
		'dilationValidationErrorRight' => array (
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Dilation:')]"
			//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Dilation: Right Treatments cannot be blank.')]"
		),
		'removeRefractionRightSide' => array (
			'xpath' => "//*[@class='js-element-eye right-eye column side right']"
		),
		'removeAllComorbidities' => array (
			'xpath' => "//*[@class='field-row comorbidities-multi-select']//a[contains(text(),'Remove all')]"
		),

		// DR Grading
		'diabetesTypeOne' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_secondarydiagnosis_disorder_id_46635009']"
		),
		'diabetesTypeTwo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_secondarydiagnosis_disorder_id_44054006']"
		),
		'leftClinicalGradingRetino' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_clinicalret_id']"
		),
		'rightClinicalGradingRetino' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_clinicalret_id']"
		),
		'leftNSCRetino' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscretinopathy_id']"
		),
		'rightNSCRetino' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscretinopathy_id']"
		),
		'leftRetinoPhotoYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscretinopathy_photocoagulation_1']"
		),
		'leftRetinoPhotoNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscretinopathy_photocoagulation_0']"
		),
		'rightRetinoPhotoYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscretinopathy_photocoagulation_1']"
		),
		'rightRetinoPhotoNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscretinopathy_photocoagulation_0']"
		),
		'leftClinicalGradingMaculo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_clinicalmac_id']"
		),
		'rightClinicalGradingMaculo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_clinicalmac_id']"
		),
		'leftNSCMaculo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscmaculopathy_id']"
		),
		'rightNSCMaculo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscmaculopathy_id']"
		),
		'leftMaculoPhotoYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscmaculopathy_photocoagulation_1']"
		),
		'leftMaculoPhotoNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_right_nscmaculopathy_photocoagulation_0']"
		),
		'rightMaculoPhotoYes' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscmaculopathy_photocoagulation_1']"
		),
		'rightMaculoPhotoNo' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_DRGrading_left_nscmaculopathy_photocoagulation_0']"
		),

		'leftUnableToAssess' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity_left_unable_to_assess']"
		),
		'rightUnableToAssess' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity_right_unable_to_assess']"
		),
		'leftEyeMissing' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity_left_eye_missing']"
		),
		'rightEyeMissing' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity_right_eye_missing']"
		),
		'rightRisks' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_risks']"
		),

		// 'leftRisks' => array('xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_risks']"),
		'leftRisks' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_risks']//*[@value='1']"
		),
		'rightInjectionComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_right_comments']"
		),
		'leftInjectionComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_InjectionManagementComplex_left_comments']"
		),

		'leftRAPD' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualFunction_left_rapd']"
		),
		'rightRAPD' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualFunction_right_rapd']"
		),
		'leftRAPDComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualFunction_left_comments']"
		),
		'rightRAPDComments' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_VisualFunction_right_comments']"
		),

		'expandColourVision' => array (
			'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'Colour Vision')]"
		),
		'leftColourVision' => array (
			'xpath' => "//*[@id='colourvision_method_left']"
		),
		'rightColourVision' => array (
			'xpath' => "//*[@id='colourvision_method_right']"
		),
		'leftColourVisionValue' => array (
			'xpath' => "//select[@name='OEModule_OphCiExamination_models_Element_OphCiExamination_ColourVision[left_readings][0][value_id]']"
		),
		'rightColourVisionValue' => array (
			'xpath' => "//select[@name='OEModule_OphCiExamination_models_Element_OphCiExamination_ColourVision[right_readings][0][value_id]']"
		),
		'rightAnteriorSegmentDescription' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_description']"
		),
		'leftAnteriorSegmentDescription' => array (
			'xpath' => "//*[@id='OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_description']"
		),
		'rightEyePCRRiskContainer' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskRightEye']"
		),
		'leftEyePCRRiskContainer' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskLeftEye']"
		),
		'rightEyePCRRisk' =>array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskRightEyeLabel']//*[contains(text(),'Right Eye - PCR Risk')]"
		),
		'leftEyePCRRisk' =>array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskLeftEyeLabel']//*[contains(text(),'Left Eye - PCR Risk')]"
		),
		'pcrGlaucoma' => array(
			'xpath' => "//*[@class='pcrrisk_glaucoma']"
		),
		'pcrPXF' => array(
			'xpath' => "//*[@class='pcrrisk_pxf_phako']"
		),
		'pcrDiabetic' => array(
			'xpath' => "//*[@class='pcrrisk_diabetic']"
		),
		'pcrPupil' => array(
			'xpath' => "//*[@class='pcrrisk_pupil_size']"
		),
		'pcrFundal' => array(
			'xpath' => "//*[@class='pcrrisk_no_fundal_view']"
		),
		'pcrAxial' => array(
			'xpath' => "//*[@class='pcrrisk_axial_length']"
		),
		'pcrCataract' => array(
			'xpath' => "//*[@class='pcrrisk_brunescent_white_cataract']"
		),
		'pcrARB' => array(
			'xpath' => "//*[@class='pcrrisk_arb']"
		),
		'pcrDoctor' => array(
			'xpath' => "//*[@class='pcr_doctor_grade']"
		),
		'pcrLie' => array(
			'xpath' => "//*[@class='pcrrisk_lie_flat']"
		),
		'pcrValue' => array(
			'xpath' => "//*[@class='pcr-span']"
		),
		'referenceLinkOnPCRRightEyeBlock' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskRightEye']//*[contains(text(),'Calculation data derived from')]"
		),
		'referenceLinkOnPCRLeftEyeBlock' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskLeftEye']//*[contains(text(),'Calculation data derived from')]"
		),
		'referenceLinkOnPCRRightEyeBlockLink' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskRightEye']//*[@class='large-8 column pcr-risk-data-link']//*[contains(text(),'The Cataract National Dataset')]"
		),
		'referenceLinkOnPCRLeftEyeBlockLink' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskLeftEye']//*[@class='large-8 column pcr-risk-data-link']//*[contains(text(),'The Cataract National Dataset')]"
		),
		'referencePage' => array(
			'xpath' => "//*[contains(text(),'The Cataract National Dataset')]"
		)
	)
	;


	public function sectionListOpen($sectionName){

		if ($sectionName=='Comorbidities'
			||$sectionName=='Colour Vision'
			||$sectionName=='Current Management'
			||$sectionName=='Overall Management'
			||$sectionName=='Injection Management'
			||$sectionName=='Visual Acuity'
			||$sectionName=='Near Visual Acuity'
			||$sectionName=='Laser Management'
			||$sectionName=='Cataract Surgical Management'
			||$sectionName=='Glaucoma Overall Management plan'
			||$sectionName=='Glaucoma Current Management plan')
		{
			$this->elements['examinationSectionListHeader'] = array(
				'xpath' => "//*[@class='sub-elements-list']//*[contains(text(),'$sectionName')]"
			);
		}

		else{
			$this->elements['examinationSectionListHeader'] = array(
				'xpath' => "//*[@class='optional-elements-list']//*[contains(text(),'$sectionName')]"
			);

		}
		return $this->find ( 'xpath', $this->getElement ( 'examinationSectionListHeader' )->getXpath () );
	}

	public function openExaminationSectionList($sectionName){
		if($this->sectionListOpen($sectionName)){
			$this->getElement('examinationSectionListHeader')->click();
			sleep(3);
		}
	}




	public function history() {
		$this->getElement ( 'history' )->click ();
		$this->getElement ( 'severity' )->click ();
		$this->getElement ( 'onset' )->click ();
		$this->getElement ( 'eye' )->click ();
		$this->getElement ( 'duration' )->click ();
	}
	protected function isComorbitiesCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'openComorbidities' )->getXpath () );
	}
	public function openComorbidities() {
		if ($this->isComorbitiesCollapsed ()) {

			$this->getElement ( 'openComorbidities' )->click ();
			$this->waitForElementDisplayBlock('addComorbidities' );
			//$this->getSession ()->wait ( 3000, 'window.$ && $.active == 0' );
		}
	}
	public function addComorbiditiy($com) {
		$this->getElement ( 'addComorbidities' )->selectOption ( $com );
	}
	public function openVisualAcuity() {
		$element = $this->getElement ( 'expandVisualAcuity' );
		//$this->scrollWindowToElement ( $element );
		//$this->getSession ()->wait ( 2000 );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}

	public function openNearVisualAcuity() {
		$element = $this->getElement ( 'expandNearVisualAcuity' );

		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}

	public function openAllergies() {
		$element = $this->getElement ( 'expandAllergies' );

		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}

	public function openAnteriorSegment() {
		$element = $this->getElement ( 'expandAnteriorSegment' );
		//$this->scrollWindowToElement ( $element );
		//$this->getSession ()->wait ( 2000 );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function selectASegmentAndMaterial( $material ) {
		$this->getElement ( 'rightTubePatch' )->click ();
		$this->getSession ()->wait ( 5000 );
		$this->getElement ( 'rightMaterial' )->selectOption ( $material );
	}
	protected function isVisualFunctionCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'expandVisualFunction' )->getXpath () );
	}
	public function openVisualFunction() {
		if ($this->isVisualFunctionCollapsed ()) {
			$element = $this->getElement ( 'expandVisualFunction' );
			//$this->scrollWindowToElement ( $element );
			$element->click ();
			$this->getSession ()->wait ( 1000, 'window.$ && $.active == 0' );
		}
	}
	public function selectVisualAcuity($unit) {
		$this->waitForElementDisplayBlock ( '#visualacuity_unit_change' );

		$this->getElement ( 'visualAcuityUnitChange' )->selectOption ( $unit );
		$this->waitForElementDisplayBlock ( '#visualacuity_unit_change' );
	}
	public function selectNearVisualAcuity($unit) {
		$this->waitForElementDisplayBlock ( '#nearvisualacuity_unit_change' );

		$this->getElement ( 'nearVisualAcuityUnitChange' )->selectOption ( $unit );
		$this->waitForElementDisplayBlock ( '#nearvisualacuity_unit_change' );
	}
	public function selectRightVA($metre, $method) {
		$this->getElement ( 'firstRightVisualAcuityReading' )->selectOption ( $metre );
		$this->getElement ( 'firstRightVisualAcuityCorrection' )->selectOption ( $method );
	}
	public function selectLeftVA($metre, $method) {
		$this->getElement ( 'firstLeftVisualAcuityReading' )->selectOption ( $metre );
		$this->getElement ( 'firstLeftVisualAcuityCorrection' )->selectOption ( $method );
	}


	public function ensureNVASide($side){
	    //not sure if this will work without the wait on a slower machine
//        $this->waitForElementDisplayBlock(
//            '.OEModule_OphCiExamination_models_Element_OphCiExamination_NearVisualAcuity',
//            2000
//        );
        $addNva = $this->getElementAtChain(array('NVA', $side.'Eye', 'addSide'));
        if ($addNva->isValid() && $addNva->isVisible()){
            $addNva->click();
        }
    }

    public function addAllergyReading($allergy){
        $this->getElementAtChain(['Allergy', 'addAllergyBtn'])->click();
        $this->elements['Allergy_val'] = array(
            'css' => 'li[data-label='.$allergy.']'
        );
        $this->elements['confirmAddButton'] = array('css'=>'.add-icon-btn');
        $this->getElementAtChain(['Allergy', 'allergyPopup', 'Allergy_val'])->click();
        $this->getElementAtChain(['Allergy', 'allergyPopup', 'confirmAddButton'])->click();
    }

    /***
     * Adds a visual acuity reading
     * @param $side [left|right] which eye to add to
     * @param $reading string reading for the eye
     * @param $method string how the measurement was made (glasses, unaided etc)
     */
    public function addNVAReading($side, $reading, $method){
        $this->getElementAtChain(array('NVA', $side.'Eye', 'addReading'))->click();
        $this->elements['NVAReading_val'] = array(
            'css' => 'li[data-id='.$reading.']'
        );
        $this->elements['NVAMethod_val'] = array(
            'css' => 'li[data-id='.$method.']'
        );
        $this->elements['confirmAddButton'] = array('css'=>'.add-icon-btn');
        $this->getElementAtChain(array('NVA', $side.'Eye', 'NVAReading', 'NVAReading_val'))->click();
        $this->getElementAtChain(array('NVA', $side.'Eye', 'NVAMethod', 'NVAMethod_val'))->click();
        $this->getElementAtChain(array('NVA', $side.'Eye', 'confirmAddButton'))->click();

    }

    /***
     * @param $links array(string) the various elements to be pathed through
     * @return null|\Behat\Mink\Element\NodeElement|mixed|null|\SensioLabs\Behat\PageObjectExtension\PageObject\Element
     *          Returns the element at the end of the chain, or null
     */
    public function getElementAtChain($links){
        $finalElement = $this->getElement($links[0]);
        foreach ($links as $index => $linkElement){
            if ($index === 0){continue;}
            $finalElement = $finalElement->find('xpath', $this->getElement($linkElement)->getXpath());
        }
        return $finalElement;
    }



	public function selectRightNVA($metre, $method) {
		$this->getElement ( 'firstRightNearVisualAcuityReading' )->selectOption ( $metre );
		$this->getElement ( 'firstRightNearVisualAcuityCorrection' )->selectOption ( $method );
	}

	public function selectLeftNVA($metre, $method) {
		$this->getElement ( 'firstLeftNearVisualAcuityReading' )->selectOption ( $metre );
		$this->getElement ( 'firstLeftNearVisualAcuityCorrection' )->selectOption ( $method );
	}

	public function addLeftVA($reading, $method) {
		$this->getElement ( 'addLeftVisualAcuity' )->click ();
		$this->getSession ()->wait ( 2000 );
		$this->getElement ( 'secondLeftVisualAcuityReading' )->selectOption ( $reading );
		$this->getElement ( 'secondLeftVisualAcuityCorrection' )->selectOption ( $method );
	}
	public function addRightVA($reading, $method) {
		$this->getElement ( 'addRightVisualAcuity' )->click ();
		$this->getSession ()->wait ( 2000 );
		$this->getElement ( 'secondRightVisualAcuityReading' )->selectOption ( $reading );
		$this->getElement ( 'secondRightVisualAcuityCorrection' )->selectOption ( $method );
	}
	public function removeFirstLeftVA() {
		$this->getElement ( 'removeFirstLeftVisualAcuity' )->click ();
	}
	public function removeFirstRightVA() {
		$this->getElement ( 'removeFirstRightVisualAcuity' )->click ();
	}
	public function removeSecondLeftVA() {
		$this->getElement ( 'removeSecondLeftVisualAcuity' )->click ();
	}
	public function removeSecondRightVA() {
		$this->getElement ( 'removeSecondRightVisualAcuity' )->click ();
	}
	protected function isIntraocularPressureCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'openIntraocularPressure' )->getXpath () );
	}
	public function expandIntraocularPressure() {
		if ($this->isIntraocularPressureCollapsed ()) {
			$element = $this->getElement ( 'openIntraocularPressure' );
			$this->scrollWindowToElement ( $element );
			$element->click ();
			$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		}
	}
	public function leftIntracocular($pressure, $instrument) {
		$this->getElement ( 'intraocularLeft' )->selectOption ( $pressure );
		$this->getElement ( 'instrumentLeft' )->selectOption ( $instrument );
	}
	public function rightIntracocular($pressure, $instrument) {
		$this->getElement ( 'intraocularRight' )->selectOption ( $pressure );
		$this->getElement ( 'instrumentRight' )->selectOption ( $instrument );
	}
	protected function isDilationCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'openDilation' )->getXpath () );
	}
	public function openDilation() {
		if ($this->isDilationCollapsed ()) {
			$element = $this->getElement ( 'openDilation' );
			$this->scrollWindowToElement ( $element );
			$element->click ();
			//$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
			$this->waitForElementDisplayBlock('dilationLeft');
		}
	}
	public function dilationRight($dilation, $drops) {
		$this->getElement ( 'dilationRight' )->selectOption ( $dilation );
		$this->getElement ( 'dropsRight' )->selectOption ( $drops );
	}
	public function dilationLeft($dilation, $drops) {
		$this->getElement ( 'dilationLeft' )->selectOption ( $dilation );
		$this->getElement ( 'dropsLeft' )->selectOption ( $drops );
	}
	public function dilationRightTime($time) {
		$this->getElement ( 'dilationTimeRight' )->setValue ( $time );
	}
	public function dilationLeftTime($time) {
		$this->getElement ( 'dilationTimeLeft' )->setValue ( $time );
	}
	protected function hasDilationTimeErrorDisplayed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'dilationTimeErrorLeft' )->getXpath () ) && ( bool ) $this->find ( 'xpath', $this->getElement ( 'dilationTimeErrorRight' )->getXpath () );
	}
	public function dilationTimeErrorValidation() {
		if (!$this->hasDilationTimeErrorDisplayed ()) {
			throw new BehaviorException ( "WARNING!!!  Dilation Invalid time error NOT displayed WARNING!!!" );
		}
	}
	protected function isRefractionCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'expandRefraction' )->getXpath () );
	}
	public function openRefraction() {
		if ($this->isRefractionCollapsed ()) {
			$this->getElement ( 'expandRefraction' )->click ();
			$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		}
	}
	public function leftRefractionDetails($sphere, $integer, $fraction) {
		$this->getElement ( 'sphereRight' )->selectOption ( $sphere );
		$this->getElement ( 'sphereRightInt' )->selectOption ( $integer );
		$this->getElement ( 'sphereRightFraction' )->selectOption ( $fraction );
	}
	public function leftCyclinderDetails($cylinder, $integer, $fraction) {
		$this->getElement ( 'cylinderRight' )->selectOption ( $cylinder );
		$this->getElement ( 'cylinderRightInt' )->selectOption ( $integer );
		$this->getElement ( 'cylinderRightFraction' )->selectOption ( $fraction );
	}
	public function leftAxis($axis) {
		$this->waitForElementDisplayBlock ( '#Element_OphCiExamination_Refraction_right_axis' );
		$this->getElement ( 'sphereRightAxis' )->setValue ( $axis );
	}
	public function leftType($type) {
		$this->getElement ( 'sphereRightType' )->selectOption ( $type );
	}
	public function RightRefractionDetails($sphere, $integer, $fraction) {
		$this->getElement ( 'sphereLeft' )->selectOption ( $sphere );
		$this->getElement ( 'sphereLeftInt' )->selectOption ( $integer );
		$this->getElement ( 'sphereLeftFraction' )->selectOption ( $fraction );
	}
	public function RightCyclinderDetails($cylinder, $integer, $fraction) {
		$this->getElement ( 'cylinderLeft' )->selectOption ( $cylinder );
		$this->getElement ( 'cylinderLeftInt' )->selectOption ( $integer );
		$this->getElement ( 'cylinderLeftFraction' )->selectOption ( $fraction );
	}
	public function RightAxis($axis) {
		$this->waitForElementDisplayBlock ( '#Element_OphCiExamination_Refraction_left_axis' );
		$this->getElement ( 'sphereLeftAxis' )->setValue ( $axis );
		$this->getElement ( 'sphereLeftAxis' )->blur ();
	}
	public function RightType($type) {
		$this->getElement ( 'sphereLeftType' )->selectOption ( $type );
		$this->getElement ( 'sphereLeftAxis' )->blur ();
	}
	public function expandVisualFields() {
		$this->getElement ( 'expandVisualFields' )->click ();
	}
	public function expandGonioscopy() {
		$this->getElement ( 'expandGonioscopy' )->click ();
	}
	protected function isAdnexalCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'expandAdnexalComorbidity' )->getXpath () );
	}
	public function expandAdnexalComorbidity() {
		if ($this->isAdnexalCollapsed ()) {
			$element = $this->getElement ( 'expandAdnexalComorbidity' );
			$this->scrollWindowToElement ( $element );
			$element->click ();
			$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		}
	}
	public function leftAdnexal($left) {
		$this->getElement ( 'leftAdnexalComorbidity' )->setValue ( $left );
	}
	public function rightAdnexal($left) {
		$this->getElement ( 'rightAdnexalComorbidity' )->setValue ( $left );
	}
	public function expandAnteriorSegment() {
		$this->getElement ( 'expandAnteriorSegment' )->click ();
	}
	public function expandPupillaryAbnormalities() {
		$element = $this->getElement ( 'expandPupillaryAbnormalities' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->waitForElementDisplayBlock('leftAbnormality');
		//$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function leftPupillaryAbnormality($left) {
		$this->getElement ( 'leftAbnormality' )->setValue ( $left );
	}
	public function rightPupillaryAbnormality($right) {
		$this->getElement ( 'rightAbnormality' )->setValue ( $right );
	}
	public function expandOpticDisc() {
		$this->getElement ( 'expandOpticDisc' )->click ();
	}
	public function expandPosteriorPole() {
		$this->getElement ( 'expandPosteriorPole' )->click ();
	}
	public function isDiagnosesCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'expandDiagnoses' )->getXpath () );
	}
	public function expandDiagnoses() {
		if ($this->isDiagnosesCollapsed ()) {
			$element = $this->getElement ( 'expandDiagnoses' );
			$this->scrollWindowToElement ( $element );
			$element->click ();
			$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		}
	}
	public function diagnosesLeftEye() {
		sleep ( 5 );
		$element = $this->getElement ( 'diagnosesLeftEye' );
		//$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}

	public function diagnosesOfCataract() {
		$this->getElement ( 'diagnosesOfCataract' )->click();
		$this->getSession ()->wait ( 5000 );
	}

	public function diagnosesRightEye() {
		$this->getElement ( 'diagnosesRightEye' )->click ();
	}
	public function diagnosesBothEyes() {
		sleep ( 5 );
		$this->getElement ( 'diagnosesBothEyes' )->doubleClick ();
	}
	public function principalDiagnosis() {
		$this->getElement ( 'principalDiagnosis' )->click ();
	}
	public function expandInvestigation() {
		$element = $this->getElement ( 'expandInvestigation' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function addInvestigation($investigation) {
		$this->getElement ( 'addInvestigation' )->setValue ( $investigation );
	}
	public function expandClinicalManagement() {
		$element = $this->getElement ( 'expandClinicalManagement' );
		$this->scrollWindowToElement ( $element );
		$this->getSession ()->wait ( 2000 );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function expandCataractSurgicalManagement() {
		$element = $this->getElement ( 'expandCataractSurgicalManagement' );
		$this->scrollWindowToElement ( $element );
		sleep(3);
		$element->click ();
		sleep(3);
	}
	public function cataractManagementComments($comments) {
		$this->getElement ( 'cataractManagementComments' )->selectOption ( $comments );
	}
	public function selectFirstEye() {
		$this->getElement ( 'selectFirstEye' )->click ();
	}
	public function selectSecondEye() {
		$this->getElement ( 'selectSecondEye' )->click ();
	}
	public function cityRoad() {
		$this->getElement ( 'cityRoad' )->check ();
	}
	public function satellite() {
		$this->getElement ( 'satellite' )->check ();
	}
	public function straightforward() {
		$element = $this->getElement ( 'straightforward' );
		//$this->scrollWindowToElement ( $element );
		$element->check ();
	}
	public function postOpRefractiveTarget($target) {
		//$this->getElement ( 'postOpRefractiveTarget' )->mouseOver ( $target );
		// THIS ISNT WORKING UNLESS WE HAVE A SLIDER MECHANISM FOR BEHAT

		$this->getElement ( 'postOpRefractiveTarget' )->setValue ( $target );
	}
	public function discussedWithPatientYes() {
		$element = $this->getElement ( 'discussedWithPatientYes' );
		//$this->scrollWindowToElement ( $element );
		$element->click ();
	}
	public function discussedWithPatientNo() {
		$element = $this->getElement ( 'discussedWithPatientNo' );
		//$this->scrollWindowToElement ( $element );
		$element->click ();
	}
	public function suitableForSurgeon($surgeon) {
		$this->getElement ( 'suitableForSurgeon' )->click ();
		$this->waitForElementDisplayBlock('suitableForSurgeon');
		$this->getElement ( 'suitableForSurgeon' )->setValue ( $surgeon );
		sleep (5);
		// selenium.KeyPressNative("9");
		// selenium.KeyPressNative("32");
	}
	public function supervisedCheckbox() {
		$this->getElement ( 'supervisedCheckbox' )->check ();
	}
	public function previousRefractiveSurgeryYes() {
		// $this->getElement('previousRefractiveSurgeryYes')->Click();
		sleep ( 5 );
		$this->getElement ( 'previousRefractiveSurgeryYes' )->doubleClick ();
		// $this->getSession()->wait(5000, 'window.$ && $.active == 0');
	}
	public function previousRefractiveSurgeryNo() {
		$this->getElement ( 'previousRefractiveSurgeryNo' )->click ();
	}
	public function vitrectomisedEyeYes() {
		$this->getElement ( 'VitrectomisedEyeYes' )->click ();
	}
	public function vitrectomisedEyeNo() {
		$this->getElement ( 'VitrectomisedEyeNo' )->click ();
	}
	public function expandLaserManagement() {
		$this->getElement ( 'expandLaserManagement' )->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function RightLaserStatusChoice($laser) {
		$this->getElement ( 'rightLaserStatusChoice' )->selectOption ( $laser );
	}
	public function LeftLaserStatusChoice($laser) {
		$this->getElement ( 'leftLaserStatusChoice' )->selectOption ( $laser );
	}
	public function leftLaser($laser) {
		$this->getElement ( 'leftLaserType' )->selectOption ( $laser );
	}
	public function rightLaser($laser) {
		$this->getElement ( 'rightLaserType' )->selectOption ( $laser );
	}
	public function expandInjectionManagement() {
		$this->getElement ( 'expandInjectionManagement' )->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function noTreatment() {
		$this->getElement ( 'noTreatmentCheckbox' )->check ();
	}
	public function noTreatmentReason($treatment) {
		$this->getElement ( 'noTreatmentReason' )->selectOption ( $treatment );
	}
	public function rightChoroidalRetinal() {
		$this->getElement ( 'rightChoroidalRetinal' )->click ();
		$this->getSession ()->wait ( 1000 );
	}
	public function rightSecondaryTo($secondary) {
		$this->getElement ( 'rightSecondaryTo' )->selectOption ( $secondary );
		$this->getSession ()->wait ( 1000 );
	}
	public function leftChoroidalRetinal() {
		$this->getElement ( 'leftChoroidalRetinal' )->click ();
	}
	public function leftSecondaryTo($secondary) {
		$this->getElement ( 'leftSecondaryTo' )->selectOption ( $secondary );
		$this->getSession ()->wait ( 1000 );
	}
	public function rightIntendedTreatment($treatment) {
		$this->getElement ( 'rightIntendedTreatment' )->selectOption ( $treatment );
		$this->getSession ()->wait ( 1000 );
	}
	public function leftIntendedTreatment($treatment) {
		$this->getElement ( 'leftIntendedTreatment' )->selectOption ( $treatment );
		$this->getSession ()->wait ( 1000 );
	}
	public function rightCRTIncreaseLowerThanHundredYes() {
		sleep ( 5 );
		$this->getElement ( 'rightCrtIncreaseLowerHundredYes' )->doubleClick ();
	}
	public function rightCRTIncreaseLowerThanHundredNo() {
		$this->getElement ( 'rightCrtIncreaseLowerHundredNo' )->doubleClick ();
	}
	public function rightCRTIncreaseMoreThanHundredYes() {
		$this->getElement ( 'rightCrtIncreaseMoreThanHundredYes' )->doubleClick ();
	}
	public function rightCRTIncreaseMoreThanHundredNo() {
		$this->getElement ( 'rightCrtIncreaseMoreThanHundredNo' )->doubleClick ();
	}
	public function rightLossOfFiveLettersYes() {
		$this->getElement ( 'rightLossOfFiveLettersYes' )->doubleClick ();
	}
	public function rightLossOfFiveLettersNo() {
		$this->getElement ( 'rightLossOfFiveLettersNo' )->doubleClick ();
	}
	public function rightLossOfFiveLettersHigherThanFiveYes() {
		$this->getElement ( 'rightLossOfFiveLettersHigherThanFiveYes' )->doubleClick ();
	}
	public function rightLossOfFiveLettersHigherThanFiveNo() {
		$this->getElement ( 'rightLossOfFiveLettersHigherThanFiveNo' )->doubleClick ();
	}
	public function leftCRTIncreaseLowerThanHundredYes() {
		sleep ( 5 );
		$this->getElement ( 'leftCrtIncreaseLowerHundredYes' )->doubleClick ();
	}
	public function leftCRTIncreaseLowerThanHundredNo() {
		$this->getElement ( 'leftCrtIncreaseLowerHundredNo' )->doubleClick ();
	}
	public function leftCRTIncreaseMoreThanHundredYes() {
		$this->getElement ( 'leftCrtIncreaseMoreThanHundredYes' )->doubleClick ();
	}
	public function leftCRTIncreaseMoreThanHundredNo() {
		$this->getElement ( 'leftCrtIncreaseMoreThanHundredNo' )->doubleClick ();
	}
	public function leftLossOfFiveLettersYes() {
		$this->getElement ( 'leftLossOfFiveLettersYes' )->doubleClick ();
	}
	public function leftLossOfFiveLettersNo() {
		$this->getElement ( 'leftLossOfFiveLettersNo' )->doubleClick ();
	}
	public function leftLossOfFiveLettersHigherThanFiveYes() {
		$this->getElement ( 'leftLossOfFiveLettersHigherThanFiveYes' )->doubleClick ();
	}
	public function leftLossOfFiveLettersHigherThanFiveNo() {
		$this->getElement ( 'leftLossOfFiveLettersHigherThanFiveNo' )->doubleClick ();
	}
	public function rightMacularRetinal() {
		$this->getElement ( 'rightMacularRetinal' )->click ();
	}
	public function leftMacularRetinal() {
		$this->getElement ( 'leftMacularRetinal' )->click ();
	}
	public function rightSecondaryVenousRetinalBranchOcclusion() {
		$this->getElement ( 'rightVenousRetinalBranchOcclusion' )->click ();
		// $this->getSession()->wait(5000);
	}
	public function leftSecondaryDiabeticMacularOedema() {
		$this->getElement ( 'leftDiabeticMacularOedema' )->click ();
		// $this->getSession()->wait(5000);
	}
	public function leftCrtIncreaseMoreThanFourHundredYes() {
		$this->getElement ( 'leftCrtIncreaseMoreThanFourHundredYes' )->click ();
	}
	public function leftCrtIncreaseMoreThanFourHundredNo() {
		$this->getElement ( 'leftCrtIncreaseMoreThanFourHundredNo' )->click ();
	}
	public function leftFovealDamageYes() {
		$this->getElement ( 'leftFovealDamageYes' )->click ();
	}
	public function leftFovealDamageNo() {
		$this->getElement ( 'leftFovealDamageNo' )->click ();
	}
	public function leftFailedLaserYes() {
		$this->getElement ( 'leftFailedLaserYes' )->click ();
	}
	public function leftFailedLaserNo() {
		$this->getElement ( 'leftFailedLaserNo' )->click ();
	}
	public function leftUnsuitableForLaserYes() {
		$this->getElement ( 'leftUnsuitableForLaserYes' )->click ();
	}
	public function leftUnsuitableForLaserNo() {
		$this->getElement ( 'leftUnsuitableForLaserNo' )->click ();
	}
	public function leftPreviousAntiVEGFyes() {
		$this->getElement ( 'leftPreviousAntiVEGFyes' )->click ();
	}
	public function leftPreviousAntiVEGFno() {
		$this->getElement ( 'leftPreviousAntiVEGFno' )->click ();
	}
	public function rightFailedLaserYes() {
		$element = $this->getElement ( 'rightFailedLaserYes' );
		//$this->scrollWindowToElement ( $element );
		$element->focus();
		$element->click ();
	}
	public function rightFailedLaserNo() {
		$this->getElement ( 'rightFailedLaserNo' )->click ();
	}
	public function rightUnsuitableForLaserYes() {
		$this->getElement ( 'rightUnsuitableForLaserYes' )->click ();
	}
	public function rightUnsuitableForLaserNo() {
		$this->getElement ( 'rightUnsuitableForLaserNo' )->click ();
	}
	public function rightPreviousOzurdexYes() {
		$this->getElement ( 'rightPreviousOzurdexYes' )->click ();
	}
	public function rightPreviousOzurdexNo() {
		$this->getElement ( 'rightPreviousOzurdexNo' )->click ();
	}
	public function expandRisks() {
		$element = $this->getElement ( 'expandRisks' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function riskComments($comments) {
		$this->getElement ( 'riskComments' )->setValue ( $comments );
	}
	public function expandClinicalOutcome() {
		$this->getElement ( 'expandClinicOutcome' )->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function clinicalOutcomeFollowUp() {
		$this->getElement ( 'clinicalOutcomeFollowUp' )->click ();
	}
	public function clinicalFollowUpQuantity($quantity) {
		$this->getElement ( 'clinicalOutcomeQuantity' )->selectOption ( $quantity );
	}
	public function clinicalFollowUpPeriod($period) {
		$this->getElement ( 'clinicalOutcomePeriod' )->selectOption ( $period );
	}
	public function clinicalSuitablePatient() {
		$this->getElement ( 'clinicalOutcomeSuitablePatientTickbox' )->check ();
	}
	public function clinicalRole($role) {
		$this->getElement ( 'clinialOutcomeRole' )->selectOption ( $role );
	}
	public function clinicalOutcomeDischarge() {
		$this->getElement ( 'clinicalOutcomeDischarge' )->click ();
	}
	protected function isConclusionCollapsed() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'expandConclusion' )->getXpath () );
		;
	}
	public function expandConclusion() {
		if ($this->isConclusionCollapsed ()) {

			$element = $this->getElement ( 'expandConclusion' );
			$this->scrollWindowToElement ( $element );
			$element->click ();
			$this->getSession ()->wait ( 3000, 'window.$ && $.active == 0' );
		}
	}
	public function conclusionOption($option) {
		$this->getElement ( 'conclusionOption' )->selectOption ( $option );
	}
	protected function hasExaminationSaved() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'examSavedOk' )->getXpath () );
		;
	}
	public function saveExaminationAndConfirm() {
		$this->getElement ( 'saveExamination' )->click ();
		$this->waitForElementDisplayBlock('examSavedOk');
		if (!$this->hasExaminationSaved ()) {
			throw new BehaviorException ( "WARNING!!!  Examination has NOT been saved!!  WARNING!!" );
		}
	}
	public function saveExaminationOnly() {
		$this->getElement ( 'saveExamination' )->click ();
	}

	// VALIDATION TESTS
	protected function doesRightAxisExist() {
		$this->waitForElementDisplayBlock ( '.js-element-eye.right-eye.column' );
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'existingRightAxisCheck' )->getXpath () );
	}
	protected function doesLeftAxisExist() {
		$this->waitForElementDisplayBlock ( '.js-element-eye.left-eye.column' );
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'existingLeftAxisCheck' )->getXpath () );
	}
	public function rightAxisCheck() {
		if (!$this->doesRightAxisExist ()) {
			throw new BehaviorException ( "RIGHT AXIS NOT SAVED!!!" );
		}
	}
	public function leftAxisCheck() {
		if (!$this->doesLeftAxisExist ()) {
			throw new BehaviorException ( "LEFT AXIS NOT SAVED!!!" );
		}
	}
	public function addAllElements() {
		$element = $this->getElement ( 'addAllElements' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 8000, 'window.$ && $.active == 0' );
	}
	public function addAllElementsValidationError() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'historyValidationError' )->getXpath () );
		// (bool) $this->find('xpath', $this->getElement('dilationValidationErrorLeft')->getXpath()) &&
		// (bool) $this->find('xpath', $this->getElement('dilationValidationErrorRight')->getXpath()) &&
		// (bool) $this->find('xpath', $this->getElement('conclusionValidationError')->getXpath());
		// (bool) $this->find('xpath', $this->getElement('investigationValidationError')->getXpath());
		// These keep changing every release so I have only enabled the History until the exact ones are confirmed
	}
	public function addAllElementsValidationCheck() {
		if (!$this->addAllElementsValidationError ()) {
			throw new BehaviorException ( "ADD ALL ERRORS HAVE NOT BEEN DISPLAYED CORRECTLY" );
		}
	}
	public function removeAllElements() {
		$element = $this->getElement ( 'activeElements' );
		//Why not get them all and just close them all in one? Because they all get explicit xpath indexes
        // [4] for instance, which causes crashes when [1] through [3] are already gone and
        // there is no [4]
		while ($closeElement = $element->find('css', '.js-remove-element')){
            $closeElement->click();
        }
	}
	public function removeAllValidationError() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'removeAllValidationError' )->getXpath () );
	}
	public function removeAllValidationCheck() {
		sleep(40);
		if (!$this->removeAllValidationError ()) {
			throw new BehaviorException ( "REMOVE ALL ERROR HAS NOT BEEN DISPLAYED!!!" );
		}
	}
	public function historyValidationError() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'historyValidationError' )->getXpath () );
	}
	public function historyValidationCheck() {
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		if (!$this->historyValidationError ()) {
			throw new BehaviorException ( "HISTORY VALIDATION ERROR!!!" );
		}
	}
	public function conclusionValidationError() {
		sleep ( 5 );
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'conclusionValidationError' )->getXpath () );
		//$this->$homepage = $this->getPage ( 'Homepage' );

	}
	public function conclusionValidationCheck() {
		if (!$this->conclusionValidationError ()) {
			throw new BehaviorException ( "CONCLUSION VALIDATION ERROR!!!" );
		}
	}

	public function ConfirmLeavePage() {
		//$this->driver.Keyboard.PressKey(Keys.Enter);

		$this->mink->getSession()->getDriver();
		$driver = $this->getSession()->getDriver();
		$alert = $driver.SwitchTo.Alert();
		$alert.Accept();
	}
	public function CancelExaminationEvent() {
		$this->getElement ( 'CancelExaminationEvent' )->click ();
	}
	public function dilationValidationError() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'dilationValidationErrorLeft' )->getXpath () ) && ( bool ) $this->find ( 'xpath', $this->getElement ( 'dilationValidationErrorRight' )->getXpath () );
	}
	public function dilationValidationCheck() {
		if (!$this->dilationValidationError ()) {
			throw new BehaviorException ( "DILATION VALIDATION ERROR!!!" );
		}
	}
	public function removeRefractionRightSide() {
		$this->getElement ( 'removeRefractionRightSide' )->click ();
	}
	public function removeAllComorbidities() {
		$this->getElement ( 'removeAllComorbidities' )->click ();
	}
	public function removeLeftDilation() {
		$this->getElement ( 'removeDilationLeft' )->click ();
		// $this->getSession()->wait(10000);
	}
	public function expandDRGrading() {
		$element = $this->getElement ( 'expandDRGrading' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function diabetesTypeOne() {
		$this->getElement ( 'diabetesTypeOne' )->check ();
	}
	public function diabetesTypeTwo() {
		$this->getElement ( 'diabetesTypeTwo' )->check ();
	}
	public function leftClinicalGradingRetino($grading) {
		$this->getElement ( 'leftClinicalGradingRetino' )->selectOption ( $grading );
	}
	public function rightClinicalGradingRetino($grading) {
		$this->getElement ( 'rightClinicalGradingRetino' )->selectOption ( $grading );
	}
	public function leftNSCRetino($nsc) {
		$this->getElement ( 'leftNSCRetino' )->selectOption ( $nsc );
	}
	public function rightNSCRetino($nsc) {
		$this->getElement ( 'rightNSCRetino' )->selectOption ( $nsc );
	}
	public function leftRetinoPhotoYes() {
		$this->getElement ( 'leftRetinoPhotoYes' )->click ();
	}
	public function leftRetinoPhotoNo() {
		$this->getElement ( 'leftRetinoPhotoNo' )->click ();
	}
	public function rightRetinoPhotoYes() {
		$this->getElement ( 'rightRetinoPhotoYes' )->click ();
	}
	public function rightRetinoPhotoNo() {
		$this->getElement ( 'rightRetinoPhotoNo' )->click ();
	}
	public function leftClinicalGradingMaculo($grading) {
		$this->getElement ( 'leftClinicalGradingMaculo' )->selectOption ( $grading );
	}
	public function rightClinicalGradingMaculo($grading) {
		$this->getElement ( 'rightClinicalGradingMaculo' )->selectOption ( $grading );
	}
	public function leftNSCMaculo($nsc) {
		$this->getElement ( 'leftNSCMaculo' )->selectOption ( $nsc );
	}
	public function rightNSCMaculo($nsc) {
		$this->getElement ( 'rightNSCMaculo' )->selectOption ( $nsc );
	}
	public function leftMaculoPhotoYes() {
		$this->getElement ( 'leftMaculoPhotoYes' )->click ();
	}
	public function leftMaculoPhotoNo() {
		$this->getElement ( 'leftMaculoPhotoNo' )->click ();
	}
	public function rightMaculoPhotoYes() {
		$this->getElement ( 'rightMaculoPhotoYes' )->click ();
	}
	public function rightMaculoPhotoNo() {
		$this->getElement ( 'rightMaculoPhotoNo' )->click ();
	}
	public function leftUnableAssess() {
		$this->getElement ( 'leftUnableToAssess' )->click ();
	}
	public function rightUnableAssess() {
		$this->getElement ( 'rightUnableToAssess' )->click ();
	}
	public function leftEyeMissing() {
		$this->getElement ( 'leftEyeMissing' )->click ();
	}
	public function rightEyeMissing() {
		$this->getElement ( 'rightEyeMissing' )->click ();
	}
	public function expandOverallManagement() {
		$this->getElement ( 'expandOverallManagement' )->click ();
		$this->getSession ()->wait ( 8000, 'window.$ && $.active == 0' );
	}
	public function clinicalInterval($interval) {
		$this->getElement ( 'OverallClinicInterval' )->selectOption ( $interval );
	}
	public function photo($photo) {
		$this->getElement ( 'OverallPhoto' )->selectOption ( $photo );
	}
	public function OCT($oct) {
		$this->getElement ( 'OverallOCT' )->selectOption ( $oct );
	}
	public function visualFields($visual) {
		$this->getElement ( 'OverallVisualFields' )->selectOption ( $visual );
	}
	public function overallComments($comments) {
		$this->getElement ( 'OverallComments' )->setValue ( $comments );
	}
	public function rightIOP($iop) {
		$this->getElement ( 'OverallLeftTargetIOP' )->selectOption ( $iop );
	}
	public function leftIOP($iop) {
		$this->getElement ( 'OverallRightTargetIOP' )->selectOption ( $iop );
	}
	public function gonioDropdown($gonio) {
		$this->getElement ( 'gonioDropdown' )->selectOption ( $gonio );
	}
	public function rightGonio($gonio) {
		$this->getElement ( 'OverallLeftGonio' )->selectOption ( $gonio );
	}
	public function leftGonio($gonio) {
		$this->getElement ( 'OverallRightGonio' )->selectOption ( $gonio );
	}
	public function expandCurrentManagement() {
		$element = $this->getElement ( 'expandCurrentManagement' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function referralOther() {
		$this->getElement ( 'referralOtherService' )->click ();
	}
	public function referralRefraction() {
		$this->getElement ( 'referralRefraction' )->click ();
	}
	public function referralLVA() {
		$this->getElement ( 'referralLVA' )->click ();
	}
	public function referralOrthoptics() {
		$this->getElement ( 'referralOrthoptics' )->click ();
	}
	public function referralCLClinic() {
		$this->getElement ( 'referralCLClinic' )->click ();
	}
	public function investigationsVF() {
		$this->getElement ( 'investigationsVF' )->click ();
	}
	public function investigationsUS() {
		$this->getElement ( 'investigationsUS' )->click ();
	}
	public function investigationsBiometry() {
		$this->getElement ( 'investigationsBiometry' )->click ();
	}
	public function investigationsOCT() {
		$this->getElement ( 'investigationsOCT' )->click ();
	}
	public function investigationsHRT() {
		$this->getElement ( 'investigationsHRT' )->click ();
	}
	public function investigationsDiscPhotos() {
		$this->getElement ( 'investigationsDiscPhotos' )->click ();
	}
	public function investigationsEDT() {
		$this->getElement ( 'investigationsEDT' )->click ();
	}
	public function leftGlaucomaStatus($status) {
		$this->getElement ( 'rightGlaucomaStatus' )->selectOption ( $status );
	}
	public function leftDropProblems($problem) {
		$this->getElement ( 'rightDropProblems' )->selectOption ( $problem );
	}
	public function leftDrops($drops) {
		$this->getElement ( 'rightDrops' )->selectOption ( $drops );
	}
	public function leftSurgery($surgery) {
		$this->getElement ( 'rightSurgery' )->selectOption ( $surgery );
	}
	public function rightGlaucomaStatus($status) {
		$this->getElement ( 'leftGlaucomaStatus' )->selectOption ( $status );
	}
	public function rightDropProblems($problem) {
		$this->getElement ( 'leftDropProblems' )->selectOption ( $problem );
	}
	public function rightDrops($drops) {
		$this->getElement ( 'leftDrops' )->selectOption ( $drops );
	}
	public function rightSurgery($surgery) {
		$this->getElement ( 'leftSurgery' )->selectOption ( $surgery );
	}
	public function rightRisks($risks) {
		$this->getElement ( 'leftRisks' )->selectOption ( $risks );
	}
	public function leftRisks($risks) {
		$this->getElement ( 'rightRisks' )->selectOption ( $risks );
	}
	public function rightInjectionComments($comments) {
		$this->getElement ( 'leftInjectionComments' )->setValue ( $comments );
	}
	public function leftInjectionComments($comments) {
		$this->getElement ( 'rightInjectionComments' )->setValue ( $comments );
	}
	public function leftRAPD() {
		$this->getElement ( 'leftRAPD' )->click ();
	}
	public function rightRAPD() {
		$this->getElement ( 'rightRAPD' )->click ();
	}
	public function leftRAPDComments($comments) {
		$this->getElement ( 'leftRAPDComments' )->setValue ( $comments );
	}
	public function rightRAPDComments($comments) {
		$this->getElement ( 'rightRAPDComments' )->setValue ( $comments );
	}

	public function openHistory(){
        $history_element = $this->getElement('expandHistory');
        if ($history_element->getAttribute('data-collapse')){
            $history_element->click();
        }
    }

	public function openColourVision() {
		$element = $this->getElement ( 'expandColourVision' );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function leftColourVision($vision) {
		$this->getElement ( 'leftColourVision' )->selectOption ( $vision );
	}
	public function rightColourVision($vision) {
		$this->getElement ( 'rightColourVision' )->selectOption ( $vision );
	}
	public function leftColourVisionValue($value) {
		$this->getElement ( 'leftColourVisionValue' )->selectOption ( $value );
	}
	public function rightColourVisionValue($value) {
		$this->getElement ( 'rightColourVisionValue' )->selectOption ( $value );
		// $this->getElement('rightColourVisionValue')->select
	}
	public function anteriorSegmentDescription($description) {
		$this->getElement ( 'rightAnteriorSegmentDescription' )->setValue ( $description );
		$this->getElement ( 'leftAnteriorSegmentDescription' )->setValue ( $description );
	}
	public function iAddTheChangesToLeftEye(){
		$this->getDriver()->dragTo($this->getElement('eyeDrawStart'),$this->getElement('eyeDrawEnd'));
	}

	public function clickOnRightEyePCRRISK(){
		$this->getElement('rightEyePCRRisk')->click();
		sleep(2);
	}

	public function clickOnLeftEyePCRRISK(){
		$this->getElement('leftEyePCRRisk')->click();
		sleep(2);
	}
	public function referenceLinkOnPCRRightEyeBlock(){
		$this->getElement('referenceLinkOnPCRRightEyeBlock');
	}
	public function referenceLinkOnPCRLeftEyeBlock(){
		$this->getElement('referenceLinkOnPCRLeftEyeBlock');
	}
	public function clickOnReferenceLinkOnPCRRightEyeBlock(){
		$this->getElement('referenceLinkOnPCRRightEyeBlockLink')->click();
		sleep(3);
	}
	public function clickOnReferenceLinkOnPCRLeftEyeBlock(){
		$this->getElement('referenceLinkOnPCRLeftEyeBlockLink')->click();
		sleep(3);
	}
	public function onPCRReferencePage(){
		if(!$this->getElement('referencePage')->isVisible()){
			throw new BehaviorException ( "Page not Displayed!!" );
		}
	}

	public function checkPcrDefaultValues()
	{
        $defaults = array(
            '.pcrrisk_glaucoma' => 'NK',
            '.pcrrisk_diabetic' => 'NK',
            '.pcrrisk_no_fundal_view' => 'NK',
            '.pcrrisk_brunescent_white_cataract' => 'NK',
            '.pcr_doctor_grade' => '1',
            '.pcrrisk_pxf_phako' => 'NK',
            '.pcrrisk_pupil_size' => 'Medium',
            '.pcrrisk_axial_length' => 'NK',
            '.pcrrisk_arb' => 'NK',
            '.pcrrisk_lie_flat' => 'Y'
        );

		foreach(array(
                    $this->getElement('rightEyePCRRiskContainer'),
                    $this->getElement('leftEyePCRRiskContainer')
                ) as $pcrBlock){
            foreach($defaults as $input => $defaultValue){

                $element = $pcrBlock->find('css', $input);
				if(!$element){
					throw new Exception($input . ' does not exist');
				}
                if($element->getValue() !== $defaultValue){
                    throw new Exception($input . ' does not have a default value');
                }
            }
        }
	}

	public function setPcrValue($side, $option, $value)
	{
		$side = $this->getElement(strtolower($side).'EyePCRRiskContainer');
		$option = $side->find('xpath', $this->elements['pcr'.$option]['xpath']);
		$option->selectOption($value);
	}

	public function checkPcrCalculatedValue($side, $value)
	{
		$side = $this->getElement(strtolower($side).'EyePCRRiskContainer');
		$element = $side->find('xpath', $this->elements['pcrValue']['xpath']);

		if($element->getText() !== $value){
			throw new Exception('PCR value does not match');
		}
	}
}
