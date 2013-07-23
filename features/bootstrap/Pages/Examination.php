<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Examination extends Page
{
    public  $history = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='blurred vision, ']";
    public  $severity = "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='mild, ']";
    public  $onset = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[3]//*[@value='gradual onset, ']";
    public  $eye = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[4]//*[@value='left eye, ']";
    public  $duration = "//div[@id='div_element_ophciexamination_history_description']/div/div/select[5]//*[@value='1 week, ']";
    public  $openComorbidities = "//div[@id='active_elements']/div/div[4]/div/h5";
    public  $addComorbidities = "//div[@id='comorbidities_unselected']/select";
    public  $openVisualAcuity = "//*[@id='inactive_elements']//*[contains(text(), 'Visual Acuity')]";
    public  $openLeftVa = "//div[@id='active_elements']/div[2]/div[2]/div[2]/div/div/button"; //needs unique id
    public  $snellenLeft = "//select[@id='visualacuity_reading_0_value']";
    public  $readingLeft = "//select[@id='visualacuity_reading_0_method_id']";
    public  $openRightVa = "//button[@type='button']";
    public  $snellenRight = "//select[@id='visualacuity_reading_1_value']";
    public  $readingRight = "//select[@id='visualacuity_reading_1_method_id']";
    public  $openIntraocularPressure = "//*[@id='inactive_elements']//*[contains(text(), 'Intraocular Pressure')]";
    public  $intraocularRight = "//select[@id='element_ophciexamination_intraocularpressure_right_reading_id']";
    public  $instrumentRight = "//select[@id='element_ophciexamination_intraocularpressure_right_instrument_id']";
    public  $intraocularLeft = "//select[@id='element_ophciexamination_intraocularpressure_left_reading_id']";
    public  $instrumentLeft = "//select[@id='element_ophciexamination_intraocularpressure_left_instrument_id']";

    public  $openDilation = "//*[@id='inactive_elements']//*[contains(text(), 'Dilation')]";
    public  $dilationRight = "//select[@id='dilation_drug_right']";
    public  $dropsLeft = "//select[@name='dilation_treatment[0][drops]']";
    public  $dilationLeft = "//select[@id='dilation_drug_left']";
    public  $dropsRight = "//select[@name='dilation_treatment[1][drops]']";

    public  $expandRefraction = "//*[@id='inactive_elements']//*[@data-element-type-name='refraction']";

    public  $sphereLeft = "//select[@id='element_ophciexamination_refraction_left_sphere_sign']";
    public  $sphereLeftInt = "//select[@id='element_ophciexamination_refraction_left_sphere_integer']";
    public  $sphereLeftFraction = "//select[@id='element_ophciexamination_refraction_left_sphere_fraction']";
    public  $cylinderLeft = "//select[@id='element_ophciexamination_refraction_left_cylinder_sign']";
    public  $cylinderLeftInt = "//select[@id='element_ophciexamination_refraction_left_cylinder_integer']";
    public  $cylinderLeftFraction = "//select[@id='element_ophciexamination_refraction_left_cylinder_fraction']";
    public  $sphereLeftAxis = "//input[@id='element_ophciexamination_refraction_left_axis']";
    public  $sphereLeftType = "//select[@id='element_ophciexamination_refraction_left_type_id']";


    public  $sphereRight = "//select[@id='element_ophciexamination_refraction_right_sphere_sign']";
    public  $sphereRightInt = "//select[@id='element_ophciexamination_refraction_right_sphere_integer']";
    public  $sphereRightFraction = "//select[@id='element_ophciexamination_refraction_right_sphere_fraction']";
    public  $cylinderRight = "//select[@id='element_ophciexamination_refraction_right_cylinder_sign']";
    public  $cylinderRightInt = "//select[@id='element_ophciexamination_refraction_right_cylinder_integer']";
    public  $cylinderRightFraction = "//select[@id='element_ophciexamination_refraction_right_cylinder_fraction']";
    public  $sphereRightAxis = "//input[@id='element_ophciexamination_refraction_right_axis']";
    public  $sphereRightType = "//select[@id='element_ophciexamination_refraction_right_type_id']";

    public  $expandGonioscopy = "//*[@id='inactive_elements']//*[@data-element-type-name='gonioscopy']";
    public  $expandaAdnexalComorbidity = "//*[@id='inactive_elements']//*[@data-element-type-name='adnexal comorbidity']";
    public  $expandAnteriorSegment = "//*[@id='inactive_elements']//*[@data-element-type-name='anterior segment']";
    public  $expandPupillaryAbnormalities = "//*[@id='inactive_elements']//*[@data-element-type-name='pupillary abnormalities']";
    public  $expandOpticDisc = "//*[@id='inactive_elements']//*[@data-element-type-name='optic disc']";
    public  $expandPosteriorPole = "//*[@id='inactive_elements']//*[@data-element-type-name='posterior pole']";
    public  $expandDiagnoses = "//*[@id='inactive_elements']//*[@data-element-type-name='diagnoses']";
    public  $expandInvestigation = "//*[@id='inactive_elements']//*[@data-element-type-name='investigation']";
    public  $expandClinicalManagement = "//*[@id='inactive_elements']//*[@data-element-type-name='clinical management']";
    public  $expandRisks = "//*[@id='inactive_elements']//*[@data-element-type-name='risks']";
    public  $expandClinicOutcome = "//*[@id='inactive_elements']//*[@data-element-type-name='clinic outcome']";
    public  $expandConclusion = "//*[@id='inactive_elements']//*[@data-element-type-name='conclusion']";

    public  $saveExamination = "//*[@id='et_save']";
}