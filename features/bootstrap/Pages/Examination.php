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

    public static  $openDilation = "//*[@id='inactive_elements']//*[contains(text(), 'Dilation')]";
    public static  $dilationRight = "//select[@id='dilation_drug_right']";
    public static  $dropsLeft = "//select[@name='dilation_treatment[0][drops]']";
    public static  $dilationLeft = "//select[@id='dilation_drug_left']";
    public static  $dropsRight = "//select[@name='dilation_treatment[1][drops]']";

    public static  $expandRefraction = "//*[@id='inactive_elements']//*[@data-element-type-name='refraction']";

    public static  $sphereLeft = "//select[@id='element_ophciexamination_refraction_left_sphere_sign']";
    public static  $sphereLeftInt = "//select[@id='element_ophciexamination_refraction_left_sphere_integer']";
    public static  $sphereLeftFraction = "//select[@id='element_ophciexamination_refraction_left_sphere_fraction']";
    public static  $cylinderLeft = "//select[@id='element_ophciexamination_refraction_left_cylinder_sign']";
    public static  $cylinderLeftInt = "//select[@id='element_ophciexamination_refraction_left_cylinder_integer']";
    public static  $cylinderLeftFraction = "//select[@id='element_ophciexamination_refraction_left_cylinder_fraction']";
    public static  $sphereLeftAxis = "//input[@id='element_ophciexamination_refraction_left_axis']";
    public static  $sphereLeftType = "//select[@id='element_ophciexamination_refraction_left_type_id']";


    public static  $sphereRight = "//select[@id='element_ophciexamination_refraction_right_sphere_sign']";
    public static  $sphereRightInt = "//select[@id='element_ophciexamination_refraction_right_sphere_integer']";
    public static  $sphereRightFraction = "//select[@id='element_ophciexamination_refraction_right_sphere_fraction']";
    public static  $cylinderRight = "//select[@id='element_ophciexamination_refraction_right_cylinder_sign']";
    public static  $cylinderRightInt = "//select[@id='element_ophciexamination_refraction_right_cylinder_integer']";
    public static  $cylinderRightFraction = "//select[@id='element_ophciexamination_refraction_right_cylinder_fraction']";
    public static  $sphereRightAxis = "//input[@id='element_ophciexamination_refraction_right_axis']";
    public static  $sphereRightType = "//select[@id='element_ophciexamination_refraction_right_type_id']";

    public static  $expandGonioscopy = "//*[@id='inactive_elements']//*[@data-element-type-name='gonioscopy']";
    public static  $expandaAdnexalComorbidity = "//*[@id='inactive_elements']//*[@data-element-type-name='adnexal comorbidity']";
    public static  $expandAnteriorSegment = "//*[@id='inactive_elements']//*[@data-element-type-name='anterior segment']";
    public static  $expandPupillaryAbnormalities = "//*[@id='inactive_elements']//*[@data-element-type-name='pupillary abnormalities']";
    public static  $expandOpticDisc = "//*[@id='inactive_elements']//*[@data-element-type-name='optic disc']";
    public static  $expandPosteriorPole = "//*[@id='inactive_elements']//*[@data-element-type-name='posterior pole']";
    public static  $expandDiagnoses = "//*[@id='inactive_elements']//*[@data-element-type-name='diagnoses']";
    public static  $expandInvestigation = "//*[@id='inactive_elements']//*[@data-element-type-name='investigation']";
    public static  $expandClinicalManagement = "//*[@id='inactive_elements']//*[@data-element-type-name='clinical management']";
    public static  $expandRisks = "//*[@id='inactive_elements']//*[@data-element-type-name='risks']";
    public static  $expandClinicOutcome = "//*[@id='inactive_elements']//*[@data-element-type-name='clinic outcome']";
    public static  $expandConclusion = "//*[@id='inactive_elements']//*[@data-element-type-name='conclusion']";

    public static  $saveExamination = "//*[@id='et_save']";
}