<?php

class m180321_132657_systemic_diagnoses_set extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_systemic_diagnoses_set',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' =>  'int(10) unsigned',
            ), true
        );

        $this->createOETable('ophciexamination_systemic_diagnoses_set_assignment',
            array(
                'id' => 'pk',
                'systemic_diagnoses_set_entry_id' => 'int(11)',
                'systemic_diagnoses_set_id' => 'int(11)',
            ), true
        );

        $this->createOETable('ophciexamination_systemic_diagnoses_set_entry',
            array(
                'id' => 'pk',
                'disorder_id' => 'BIGINT(20) UNSIGNED',
                'gender' => 'varchar(1) NULL',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',

            ), true);

        $this->addForeignKey('exam_systemic_diagnoses_set_subspecialty', 'ophciexamination_systemic_diagnoses_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('exam_systemic_diagnoses_set_firm', 'ophciexamination_systemic_diagnoses_set', 'firm_id', 'firm', 'id');

        $this->addForeignKey('exam_systemic_diagnoses_set_assignment_diag_e', 'ophciexamination_systemic_diagnoses_set_assignment', 'systemic_diagnoses_set_entry_id', 'ophciexamination_systemic_diagnoses_set_entry', 'id');
        $this->addForeignKey('exam_systemic_diagnoses_set_assignment_set', 'ophciexamination_systemic_diagnoses_set_assignment', 'systemic_diagnoses_set_id', 'ophciexamination_systemic_diagnoses_set', 'id');

        $this->addForeignKey('exam_systemic_diagnoses_set_e', 'ophciexamination_systemic_diagnoses_set_entry', 'disorder_id', 'disorder', 'id');

    }


    public function down()
    {
    }
}