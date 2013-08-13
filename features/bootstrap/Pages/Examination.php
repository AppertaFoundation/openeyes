<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Examination extends Page
{
    protected $path = "OphCiExamination/Default/create?patient_id=19434";

    protected $elements = array(
        'history' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='blurred vision, ']"),
        'severity' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='mild, ']"),
        'onset' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='gradual onset, ']"),
        'eye' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='left eye, ']"),
        'duration' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='1 week, ']"),
        'openComorbidities' => array('xpath' => "//div[@id='active_elements']/div/div[4]/div/h5"),
        'addComorbidities' => array('xpath' => "//div[@id='comorbidities_unselected']/select"),
        'openVisualAcuity' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Visual Acuity')]"),
        'visualActuityUnitChange' => array('xpath' => "//select[@id='visualacuity_reading_0_value']"),
        'openLeftVA' => array('xpath' => "//*[@id='active_elements']/div[2]/div[3]/div[2]/div[1]/div[1]/button//*[contains(text(),'Add')]"),
        'snellenLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_value']"),
        'readingLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_method_id']"),
        'openRightVA' => array('xpath' => "//*[@id='active_elements']/div[2]/div[3]/div[1]/div[1]/div[1]/button//*[contains(text(),'Add')]"),
        'snellenRight' => array('xpath' => "//select[@id='visualacuity_reading_1_value']"),
        'readingRight' => array('xpath' => "//select[@id='visualacuity_reading_1_method_id']")


);
    public function history ()
    {
        $this->getElement('history')->click();
        $this->getElement('severity')->click();
        $this->getElement('onset')->click();
        $this->getElement('eye')->click();
        $this->getElement('duration')->click();
    }

    public function openComorbidities ()
    {
        $this->getElement('openComorbidities')->click();
    }


    public static  $openIntraocularPressure = "//*[@id='inactive_elements']//*[contains(text(), 'Intraocular Pressure')]";
    public static  $intraocularRight = "//select[@id='element_ophciexamination_intraocularpressure_right_reading_id']";
    public static  $instrumentRight = "//select[@id='element_ophciexamination_intraocularpressure_right_instrument_id']";
    public static  $intraocularLeft = "//select[@id='element_ophciexamination_intraocularpressure_left_reading_id']";
    public static  $instrumentLeft = "//select[@id='element_ophciexamination_intraocularpressure_left_instrument_id']";
    protected $elements = array(
        'history' => array('xpath' => "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='blurred vision, ']"),
        'severity' => array('xpath' => "//*[@id='dropdowntextselection_element_ophciexamination_history_description']//*[@value='mild, ']"),
        'onset' => array('xpath' => "//div[@id='div_element_ophciexamination_history_description']/div/div/select[3]//*[@value='gradual onset, ']"),
        'eye' => array('xpath' => "//div[@id='div_element_ophciexamination_history_description']/div/div/select[4]//*[@value='left eye, ']"),
        'duration' => array('xpath' => "//div[@id='div_element_ophciexamination_history_description']/div/div/select[5]//*[@value='1 week, ']"),
        'openComorbidities' => array('xpath' => "//div[@id='active_elements']/div/div[4]/div/h5"),
        'addComorbidities' => array('xpath' => "//div[@id='comorbidities_unselected']/select"),
        'openVisualAcuity' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Visual Acuity')]"),
        'openLeftVa' => array('xpath' => "//div[@id='active_elements']/div[2]/div[2]/div[2]/div/div/button"), //needs unique id
        'snellenLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_value']"),
        'readingLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_method_id']"),
        'openRightVa' => array('xpath' => "//button[@type='button']"),
        'snellenRight' => array('xpath' => "//select[@id='visualacuity_reading_1_value']"),
        'readingRight' => array('xpath' => "//select[@id='visualacuity_reading_1_method_id']"),
        'openIntraocularPressure' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Intraocular Pressure')]"),
        'intraocularRight' => array('xpath' => "//select[@id='element_ophciexamination_intraocularpressure_right_reading_id']"),
        'instrumentRight' => array('xpath' => "//select[@id='element_ophciexamination_intraocularpressure_right_instrument_id']"),
        'intraocularLeft' => array('xpath' => "//select[@id='element_ophciexamination_intraocularpressure_left_reading_id']"),
        'instrumentLeft' => array('xpath' => "//select[@id='element_ophciexamination_intraocularpressure_left_instrument_id']"),

        'openDilation' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Dilation')]"),
        'dilationRight' => array('xpath' => "//select[@id='dilation_drug_right']"),
        'dropsLeft' => array('xpath' => "//select[@name='dilation_treatment[0][drops]']"),
        'dilationLeft' => array('xpath' => "//select[@id='dilation_drug_left']"),
        'dropsRight' => array('xpath' => "//select[@name='dilation_treatment[1][drops]']"),

        'expandRefraction' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='refraction']"),

        'sphereLeft' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_sphere_sign']"),
        'sphereLeftInt' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_sphere_integer']"),
        'sphereLeftFraction' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_sphere_fraction']"),
        'cylinderLeft' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_cylinder_sign']"),
        'cylinderLeftInt' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_cylinder_integer']"),
        'cylinderLeftFraction' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_cylinder_fraction']"),
        'sphereLeftAxis' => array('xpath' => "//input[@id='element_ophciexamination_refraction_left_axis']"),
        'sphereLeftType' => array('xpath' => "//select[@id='element_ophciexamination_refraction_left_type_id']"),


        'sphereRight' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_sphere_sign']"),
        'sphereRightInt' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_sphere_integer']"),
        'sphereRightFraction' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_sphere_fraction']"),
        'cylinderRight' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_cylinder_sign']"),
        'cylinderRightInt' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_cylinder_integer']"),
        'cylinderRightFraction' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_cylinder_fraction']"),
        'sphereRightAxis' => array('xpath' => "//input[@id='element_ophciexamination_refraction_right_axis']"),
        'sphereRightType' => array('xpath' => "//select[@id='element_ophciexamination_refraction_right_type_id']"),

        'expandGonioscopy' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='gonioscopy']"),
        'expandaAdnexalComorbidity' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='adnexal comorbidity']"),
        'expandAnteriorSegment' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='anterior segment']"),
        'expandPupillaryAbnormalities' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='pupillary abnormalities']"),
        'expandOpticDisc' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='optic disc']"),
        'expandPosteriorPole' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='posterior pole']"),
        'expandDiagnoses' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='diagnoses']"),
        'expandInvestigation' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='investigation']"),
        'expandClinicalManagement' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='clinical management']"),
        'expandRisks' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='risks']"),
        'expandClinicOutcome' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='clinic outcome']"),
        'expandConclusion' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='conclusion']"),

        'saveExamination' => array('xpath' => "//*[@id='et_save']"),
    );
}