<?php

class m160216_150501_store_pcr_risk_options extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'pcr_risk_values',
            array(
                'id' => 'pk',
                'patient_id' => 'INTEGER(10) UNSIGNED NOT NULL',
                'eye_id' => 'INTEGER(10) UNSIGNED NOT NULL',
                'glaucoma' => 'VARCHAR(1)',
                'pxf' => 'VARCHAR(1)',
                'diabetic' => 'VARCHAR(1)',
                'pupil_size' => 'VARCHAR(10) NOT NULL',
                'no_fundal_view' => 'VARCHAR(1)',
                'axial_length_group' => 'INTEGER(1)',
                'brunescent_white_cataract' => 'VARCHAR(1)',
                'alpha_receptor_blocker' => 'VARCHAR(1)',
                'doctor_grade_id' => 'INTEGER(11) NOT NULL',
                'can_lie_flat' => 'VARCHAR(1)',
                'CONSTRAINT pcr_patient foreign key (patient_id) references patient (id)',
                'CONSTRAINT pcr_eye foreign key (eye_id) references eye (id)',
                'CONSTRAINT pcr_doctor_grade foreign key (doctor_grade_id) references doctor_grade (id)',
                'CONSTRAINT unique_patient_eye UNIQUE (patient_id, eye_id)',
            ),
            true
        );
    }

    public function down()
    {
        $this->dropOETable('pcr_risk_values', true);
    }
}
