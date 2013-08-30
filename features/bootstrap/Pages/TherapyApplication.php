<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Therapy extends Page
{
//    protected $path = "/site/OphDrPrescription/Default/create?patient_id={parentId}"; - NEEDS TO BE UPDATED WHEN WORKING!

    protected $elements = array(
        #Diagnosis
        'addRightSide' => array('xpath' => "//*[@id='clinical-create']/div[3]/div/div[1]/div[2]/a"),
        'leftDiagnosis' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis1_id']"),
        'rightDiagnosis' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis1_id']"),
        'rightSecondaryTo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis2_id']"),
        'leftSecondaryTo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis2_id']"),

        #Patient Suitability
        'rightTreatment' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_treatment_id']"),
        'leftTreatment' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_treatment_id']"),
        'rightAngiogramDate' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_angiogram_baseline_date_0']"),
        'leftAngiogramDate' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_angiogram_baseline_date_0']"),

        #Relative ContraIndications
        'cerebrovascularYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_1']"),
        'cerebrovascularNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_0']"),
        'ischaemicYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_1']"),
        'ischaemicNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_0']"),
        'myocardialYes' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_1']"),
        'myocardialNo' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_0']"),

        'consultant' => array('xpath' => "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_consultant_id']"),
    );

    #Exceptional Circumstances
    # To Code - Choices of routes to be defined

    //Use $saveExamination to Save Intravitreal injection

}



