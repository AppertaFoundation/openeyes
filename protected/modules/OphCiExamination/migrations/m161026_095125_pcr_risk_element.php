<?php

class m161026_095125_pcr_risk_element extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'et_ophciexamination_pcr_risk',
            array(
                'id' => 'pk',
                'event_id' => 'INTEGER(10) UNSIGNED NOT NULL',
                'eye_id' => 'INTEGER(10) UNSIGNED NOT NULL DEFAULT 3',
                'left_glaucoma' => 'VARCHAR(2)',
                'left_pxf' => 'VARCHAR(2)',
                'left_diabetic' => 'VARCHAR(2)',
                'left_pupil_size' => 'VARCHAR(10) NOT NULL',
                'left_no_fundal_view' => 'VARCHAR(2)',
                'left_axial_length_group' => 'INTEGER(2)',
                'left_brunescent_white_cataract' => 'VARCHAR(2)',
                'left_alpha_receptor_blocker' => 'VARCHAR(2)',
                'left_doctor_grade_id' => 'INTEGER(11) NOT NULL',
                'left_can_lie_flat' => 'VARCHAR(2)',
                'left_pcr_risk' => 'DECIMAL(5,2)',
                'left_excess_risk' => 'DECIMAL(5,2)',
                'right_glaucoma' => 'VARCHAR(2)',
                'right_pxf' => 'VARCHAR(2)',
                'right_diabetic' => 'VARCHAR(2)',
                'right_pupil_size' => 'VARCHAR(10) NOT NULL',
                'right_no_fundal_view' => 'VARCHAR(2)',
                'right_axial_length_group' => 'INTEGER(2)',
                'right_brunescent_white_cataract' => 'VARCHAR(2)',
                'right_alpha_receptor_blocker' => 'VARCHAR(2)',
                'right_doctor_grade_id' => 'INTEGER(11) NOT NULL',
                'right_can_lie_flat' => 'VARCHAR(2)',
                'right_pcr_risk' => 'DECIMAL(5,2)',
                'right_excess_risk' => 'DECIMAL(5,2)',
                'CONSTRAINT el_pcr_eye foreign key (eye_id) references eye (id)',
                'CONSTRAINT el_right_pcr_doctor_grade foreign key (right_doctor_grade_id) references doctor_grade (id)',
                'CONSTRAINT el_left_pcr_doctor_grade foreign key (left_doctor_grade_id) references doctor_grade (id)',
            ),
            true
        );

        $this->createElementType('OphCiExamination', 'PCR Risk', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk',
            'display_order' => '50',
        ));
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_pcr_risk', true);
        $this->delete('element_type', 'class_name = "Element_OphCiExamination_PcrRisk"');
    }
}
