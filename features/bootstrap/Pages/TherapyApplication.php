<?php

class Therapy

{

#Diagnosis
public static $addRightSide = "//*[@id='clinical-create']/div[3]/div/div[1]/div[2]/a";
public static $leftDiagnosis = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis1_id']";
public static $rightDiagnosis = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis1_id']";
public static $rightSecondaryTo = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis2_id']";
public static $leftSecondaryTo = "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis2_id']";

#Patient Suitability
public static $rightTreatment = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_treatment_id']";
public static $leftTreatment = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_treatment_id']";
public static $rightAngiogramDate = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_angiogram_baseline_date_0']";
public static $leftAngiogramDate = "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_angiogram_baseline_date_0']";

#Relative ContraIndications
public static $cerebrovascularYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_1']";
public static $cerebrovascularNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_0']";
public static $ischaemicYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_1']";
public static $ischaemicNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_0']";
public static $myocardialYes = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_1']";
public static $myocardialNo = "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_0']";

public static $consultant = "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_consultant_id']";

#Exceptional Circumstances
# To Code - Choices of routes to be defined

//Use $saveExamination to Save Intravitreal injection

}



