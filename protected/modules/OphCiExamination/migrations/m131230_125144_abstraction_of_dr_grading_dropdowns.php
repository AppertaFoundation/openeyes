<?php

class m131230_125144_abstraction_of_dr_grading_dropdowns extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_drgrading_clinicalmaculopathy', 'code', 'varchar(2) NOT NULL');
        $this->addColumn('ophciexamination_drgrading_clinicalretinopathy', 'code', 'varchar(2) NOT NULL');
        $this->addColumn('ophciexamination_drgrading_nscmaculopathy', 'code', 'varchar(2) NOT NULL');
        $this->addColumn('ophciexamination_drgrading_nscretinopathy', 'code', 'varchar(2) NOT NULL');

        $this->update('ophciexamination_drgrading_clinicalmaculopathy', array('code' => 'NM'), "name = 'No macular oedema'");
        $this->update('ophciexamination_drgrading_clinicalmaculopathy', array('code' => 'DN'), "name = 'Diabetic macular oedema not clinically significant'");
        $this->update('ophciexamination_drgrading_clinicalmaculopathy', array('code' => 'DS'), "name = 'Clinically significant macular oedema'");
        $this->update('ophciexamination_drgrading_clinicalmaculopathy', array('code' => 'CD'), "name = 'Centre involving diabetic macular oedema'");

        $this->createIndex('ophciexamination_drgrading_clinicalmaculopathy_code', 'ophciexamination_drgrading_clinicalmaculopathy', 'code', true);

        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'NR'), "name = 'No retinopathy'");
        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'MN'), "name = 'Mild nonproliferative retinopathy'");
        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'MO'), "name = 'Moderate nonproliferative retinopathy'");
        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'SR'), "name = 'Severe nonproliferative retinopathy'");
        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'EP'), "name = 'Early proliferative retinopathy'");
        $this->update('ophciexamination_drgrading_clinicalretinopathy', array('code' => 'HR'), "name = 'High-risk proliferative retinopathy'");

        $this->createIndex('ophciexamination_drgrading_clinicalretinopathy_code', 'ophciexamination_drgrading_clinicalretinopathy', 'code', true);

        $this->update('ophciexamination_drgrading_nscmaculopathy', array('code' => 'NO'), "name = 'M0'");
        $this->update('ophciexamination_drgrading_nscmaculopathy', array('code' => 'MA'), "name = 'M1A'");
        $this->update('ophciexamination_drgrading_nscmaculopathy', array('code' => 'MO'), "name = 'M1S'");
        $this->update('ophciexamination_drgrading_nscmaculopathy', array('code' => 'UG'), "name = 'U'");

        $this->createIndex('ophciexamination_drgrading_nscmaculopathy_code', 'ophciexamination_drgrading_nscmaculopathy', 'code', true);

        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'NO'), "name = 'R0'");
        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'BA'), "name = 'R1'");
        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'PP'), "name = 'R2'");
        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'PE'), "name = 'R3S'");
        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'PR'), "name = 'R3A'");
        $this->update('ophciexamination_drgrading_nscretinopathy', array('code' => 'UN'), "name = 'U'");

        $this->createIndex('ophciexamination_drgrading_nscretinopathy_code', 'ophciexamination_drgrading_nscretinopathy', 'code', true);
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_drgrading_nscretinopathy', 'code');
        $this->dropColumn('ophciexamination_drgrading_nscmaculopathy', 'code');
        $this->dropColumn('ophciexamination_drgrading_clinicalretinopathy', 'code');
        $this->dropColumn('ophciexamination_drgrading_clinicalmaculopathy', 'code');
    }
}
