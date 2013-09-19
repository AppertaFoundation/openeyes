<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Examination extends Page
{
    protected $path = "OphCiExamination/Default/create?patient_id={patientId}";

    protected $elements = array(
        'history' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='blurred vision, ']"),
        'severity' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='mild, ']"),
        'onset' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='gradual onset, ']"),
        'eye' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='left eye, ']"),
        'duration' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphCiExamination_History_description']//*[@value='1 week, ']"),
        'openComorbidities' => array('xpath' => "//div[@id='active_elements']/div/div[4]/div/h5"),
        'addComorbidities' => array('xpath' => "//*[@id='comorbidities_unselected']/select"),
        'openVisualAcuity' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(),'Visual Acuity')]"),
        'visualAcuityUnitChange' => array('xpath' => "//*[@id='visualacuity_unit_change']"),
        'openLeftVA' => array('xpath' => "//*[@id='active_elements']/div[3]//*[@class='side right eventDetail']//*[contains(text(),'Add')]"),
        'snellenLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_value']"),
        'readingLeft' => array('xpath' => "//select[@id='visualacuity_reading_0_method_id']"),
        'openRightVA' => array('xpath' => "//*[@id='active_elements']/div[3]//*[@class='side left eventDetail']//*[contains(text(),'Add')]"),
        'snellenRight' => array('xpath' => "//select[@id='visualacuity_reading_1_value']"),
        'readingRight' => array('xpath' => "//select[@id='visualacuity_reading_1_method_id']"),

        'openIntraocularPressure' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Intraocular Pressure')]"),
        'intraocularRight' => array('xpath' => "//*[@id='Element_OphCiExamination_IntraocularPressure_right_reading_id']"),
        'instrumentRight' => array('xpath' => "//*[@id='Element_OphCiExamination_IntraocularPressure_right_instrument_id']"),
        'intraocularLeft' => array('xpath' => "//*[@id='Element_OphCiExamination_IntraocularPressure_left_reading_id']"),
        'instrumentLeft' => array('xpath' => "//*[@id='Element_OphCiExamination_IntraocularPressure_left_instrument_id']"),

        'openDilation' => array('xpath' => "//*[@id='inactive_elements']//*[contains(text(), 'Dilation')]"),
        'dilationRight' => array('xpath' => "//select[@id='dilation_drug_right']"),
        'dropsLeft' => array('xpath' => "//select[@name='dilation_treatment[0][drops]']"),
        'dilationLeft' => array('xpath' => "//select[@id='dilation_drug_left']"),
        'dropsRight' => array('xpath' => "//select[@name='dilation_treatment[1][drops]']"),

        'expandRefraction' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Refraction']"),

        'sphereLeft' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_sphere_sign']"),
        'sphereLeftInt' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_sphere_integer']"),
        'sphereLeftFraction' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_sphere_fraction']"),
        'cylinderLeft' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_cylinder_sign']"),
        'cylinderLeftInt' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_cylinder_integer']"),
        'cylinderLeftFraction' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_cylinder_fraction']"),
        'sphereLeftAxis' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_axis']"),
        'sphereLeftType' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_left_type_id']"),


        'sphereRight' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_sphere_sign']"),
        'sphereRightInt' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_sphere_integer']"),
        'sphereRightFraction' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_sphere_fraction']"),
        'cylinderRight' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_cylinder_sign']"),
        'cylinderRightInt' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_cylinder_integer']"),
        'cylinderRightFraction' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_cylinder_fraction']"),
        'sphereRightAxis' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_axis']"),
        'sphereRightType' => array('xpath' => "//*[@id='Element_OphCiExamination_Refraction_right_type_id']"),

        'expandVisualFields' => array ('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Visual Fields']"),
        'expandGonioscopy' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Gonioscopy']"),
        'expandaAdnexalComorbidity' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Adnexal Comorbidity']"),
        'expandAnteriorSegment' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Anterior Segment']"),
        'expandPupillaryAbnormalities' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Pupillary Abnormalities']"),
        'expandOpticDisc' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Optic Disc']"),
        'expandPosteriorPole' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Posterior Pole']"),
        'expandDiagnoses' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Diagnoses']"),
        'expandInvestigation' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Investigation']"),
        'expandClinicalManagement' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Clinical Management']"),
        'expandRisks' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Risks']"),
        'expandClinicOutcome' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Clinic Outcome']"),
        'expandConclusion' => array('xpath' => "//*[@id='inactive_elements']//*[@data-element-type-name='Conclusion']"),

        'saveExamination' => array('xpath' => "//*[@id='et_save']"),
    );

    public function history ()
    {
        $this->getElement('history')->click();
        $this->getElement('severity')->click();
        $this->getElement('onset')->click();
        $this->getElement('eye')->click();
        $this->getElement('duration')->click();
    }

    protected function isComorbitiesCollapsed()
    {
        return (bool) $this->find('xpath', $this->getElement('openComorbidities')->getXpath());;
    }

    public function openComorbidities ()
    {
        if ($this->isComorbitiesCollapsed()) {

            $this->getElement('openComorbidities')->click();
            $this->getSession()->wait(3000, '$.active == 0');
        }
    }

    public function addComorbiditiy ($com)
    {
        $this->getElement('addComorbidities')->selectOption($com);
    }

    protected function isVisualAcuityCollapsed()
    {
        return (bool) $this->find('xpath', $this->getElement('openVisualAcuity')->getXpath());
    }

    public function openVisualAcuity ()
    {
        if ($this->isVisualAcuityCollapsed()) {
            $this->getElement('openVisualAcuity')->click();
            $this->getSession()->wait(3000, '$.active == 0');
        }
    }

    public function selectVisualAcuity ($unit)
    {
        $this->getElement('visualAcuityUnitChange')->selectOption($unit);
        $this->getSession()->wait(5000);
    }

    public function leftVisualAcuity ($metre, $method)
    {
        $this->getElement('openLeftVA')->click();
        $this->getElement('snellenLeft')->selectOption($metre);
        $this->getElement('readingLeft')->selectOption($method);
    }

    public function rightVisualAcuity ($metre, $method)
    {
        $this->getElement('openRightVA')->click();
        $this->getElement('snellenRight')->selectOption($metre);
        $this->getElement('readingRight')->selectOption($method);
    }

    protected function isIntraocularPressureCollapsed()
    {
        return (bool) $this->find('xpath', $this->getElement('openIntraocularPressure')->getXpath());
    }

    public function expandIntraocularPressure ()
    {
        if ($this->isIntraocularPressureCollapsed()){
            $this->getElement('openIntraocularPressure')->click();
        }
    }

    public function leftIntracocular ($pressure, $instrument)
    {
        $this->getElement('intraocularLeft')->selectOption($pressure);
        $this->getElement('instrumentLeft')->selectOption($instrument);
    }

    public function rightIntracocular ($pressure, $instrument)
    {
        $this->getElement('intraocularRight')->selectOption($pressure);
        $this->getElement('instrumentRight')->selectOption($instrument);

    }

    protected function isDilationCollapsed()
    {
        return (bool) $this->find('xpath', $this->getElement('openDilation')->getXpath());
    }

    public function openDilation ()
    {
        if ($this->isDilationCollapsed()){
            $this->getElement('openDilation')->click();
            $this->getSession()->wait(10000);
        }
    }

    public function dilationRight ($dilation, $drops)
    {
        $this->getElement('dilationRight')->selectOption($dilation);
        $this->getElement('dropsRight')->selectOption($drops);
    }

    public function dilationLeft ($dilation, $drops)
    {
        $this->getElement('dilationLeft')->selectOption($dilation);
        $this->getElement('dropsLeft')->selectOption($drops);
    }

    protected function isRefractionCollapsed ()
    {
        return (bool) $this->find('xpath', $this->getElement('expandRefraction')->getXpath());
    }

    public function openRefraction ()
    {
        if ($this->isRefractionCollapsed()){
            $this->getElement('expandRefraction')->click();
        }
    }

    public function leftRefractionDetails ($sphere, $integer, $fraction)
    {
        $this->getElement('sphereRight')->selectOption($sphere);
        $this->getElement('sphereRightInt')->selectOption($integer);
        $this->getElement('sphereRightFraction')->selectOption($fraction);
    }

    public function leftCyclinderDetails ($cylinder, $integer, $fraction)
    {
        $this->getElement('cylinderRight')->selectOption($cylinder);
        $this->getElement('cylinderRightInt')->selectOption($integer);
        $this->getElement('cylinderRightFraction')->selectOption($fraction);
    }

    public function leftAxis ($axis)
    {
        $this->getElement('sphereRightAxis')->setValue($axis);
    }

    public function leftType ($type)
    {
        $this->getElement('sphereRightType')->selectOption($type);
    }

    public function RightRefractionDetails ($sphere, $integer, $fraction)
    {
        $this->getElement('sphereLeft')->selectOption($sphere);
        $this->getElement('sphereLeftInt')->selectOption($integer);
        $this->getElement('sphereLeftFraction')->selectOption($fraction);
    }

    public function RightCyclinderDetails ($cylinder, $integer, $fraction)
    {
        $this->getElement('cylinderLeft')->selectOption($cylinder);
        $this->getElement('cylinderLeftInt')->selectOption($integer);
        $this->getElement('cylinderLeftFraction')->selectOption($fraction);
    }

    public function RightAxis ($axis)
    {
        $this->getElement('sphereLeftAxis')->setValue($axis);
    }

    public function RightType ($type)
    {
        $this->getElement('sphereLeftType')->selectOption($type);
    }

    public function expandVisualFields ()
    {
        $this->getElement('expandVisualFields')->click();
    }

    public function expandGonioscopy ()
    {
        $this->getElement('expandGonioscopy')->click();
    }

    public function expandAdnexalComorbidity ()
    {
        $this->getElement('expandaAdnexalComorbidity')->click();
    }

    public function expandAnteriorSegment ()
    {
        $this->getElement('expandAnteriorSegment')->click();
    }

    public function expandPupillaryAbnormalities ()
    {
        $this->getElement('expandPupillaryAbnormalities')->click();
    }

    public function expandOpticDisc ()
    {
        $this->getElement('expandOpticDisc')->click();
    }

    public function expandPosteriorPole ()
    {
       $this->getElement('expandPosteriorPole')->click();
    }

    public function expandDiagnoses ()
    {
       $this->getElement('expandDiagnoses')->click();
    }

    public function expandInvestigation ()
    {
        $this->getElement('expandInvestigation')->click();
    }

    public function expandClinicalManagement ()
    {
        $this->getElement('expandClinicalManagement')->click();
    }

    public function expandRisks ()
    {
        $this->getElement('expandRisks')->click();
    }

    public function expandClinicalOutcome ()
    {
        $this->getElement('expandClinicOutcome')->click();
    }

    public function expandConclusion ()
    {
        $this->getElement('expandConclusion')->click();
    }

    public function saveExamination ()
    {
        $this->getSession()->wait(10000);
        $this->getElement('saveExamination')->click();
    }

}