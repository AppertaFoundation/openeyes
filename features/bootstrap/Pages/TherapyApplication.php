<?php

class Therapy

{

#Diagnosis
public $addRightSide = "//*[@id='clinical-create']/div[3]/div/div[1]/div[2]/a";
public $leftDiagnosis = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis1_id']";
public $rightDiagnosis = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis1_id']";
public $rightSecondaryTo = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis2_id']";
public $leftSecondaryTo = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis2_id']";

#Patient Suitability
public $rightTreatment = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_treatment_id']";
public $leftTreatment = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_treatment_id']";
public $rightAngiogramDate = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_angiogram_baseline_date_0']";
public $leftAngiogramDate = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_angiogram_baseline_date_0']";

#Relative ContraIndications
public $cerebrovascularYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_1']";
public $cerebrovascularNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_0']";
public $ischaemicYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_1']";
public $ischaemicNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_0']";
public $myocardialYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_1']";
public $myocardialNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_0']";

public $consultant = "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_consultant_id']";

#Exceptional Circumstances
# To Code - Choices of routes to be defined

//Use $saveExamination to Save Intravitreal injection

}



