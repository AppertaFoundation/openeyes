<?php

class m140219_143333_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_History'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Refraction'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_VisualAcuity'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_AdnexalComorbidity'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_AnteriorSegment'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_IntraocularPressure'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_PosteriorPole'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Diagnoses'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Investigation'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Conclusion'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Gonioscopy'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_OpticDisc'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Dilation'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Management'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_ClinicOutcome'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Risks'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_PupillaryAbnormalities'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_CataractSurgicalManagement'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_Comorbidities'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_AnteriorSegment_CCT'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_GlaucomaRisk'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_DRGrading'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_LaserManagement'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_InjectionManagement'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_InjectionManagementComplex'");
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCiExamination_OCT'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_History'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Refraction'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_VisualAcuity'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_AdnexalComorbidity'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_AnteriorSegment'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_IntraocularPressure'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_PosteriorPole'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Diagnoses'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Investigation'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Conclusion'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Gonioscopy'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_OpticDisc'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Dilation'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Management'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_ClinicOutcome'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Risks'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_PupillaryAbnormalities'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_CataractSurgicalManagement'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_Comorbidities'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_AnteriorSegment_CCT'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_GlaucomaRisk'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_DRGrading'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_LaserManagement'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_InjectionManagement'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_InjectionManagementComplex'");
        $this->update('element_type', array('required' => null), "class_name = 'Element_OphCiExamination_OCT'");
    }
}
