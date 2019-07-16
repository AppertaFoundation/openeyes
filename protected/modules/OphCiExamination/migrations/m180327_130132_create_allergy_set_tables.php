<?php

class m180327_130132_create_allergy_set_tables extends \OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_allergy_set_entry',
            array(
                'id' => 'pk',
                'ophciexamination_allergy_id' => 'int(11)',
                'gender' => 'varchar(1) NULL',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',

            ),true);

        $this->createOETable('ophciexamination_allergy_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_allergy_entry_id' => 'int(11)',
                'allergy_set_id' => 'int(11)',
            ),true
        );
        $this->createOETable('ophciexamination_allergy_set',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' =>  'int(10) unsigned',
            ),true
        );

        $this->addColumn('ophciexamination_allergy_entry', 'has_allergy', 'tinyint(1) NOT NULL DEFAULT 1 AFTER allergy_id');
        $this->addColumn('ophciexamination_allergy_entry_version', 'has_allergy', 'tinyint(1) NOT NULL DEFAULT 1 AFTER allergy_id');

        $this->addForeignKey('ophciexamination_allergy_set_subspecialty', 'ophciexamination_allergy_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_firm', 'ophciexamination_allergy_set', 'firm_id', 'firm', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment', 'ophciexamination_allergy_entry_id', 'ophciexamination_allergy_set_entry', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment', 'allergy_set_id', 'ophciexamination_allergy_set', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_e', 'ophciexamination_allergy_set_entry', 'ophciexamination_allergy_id', 'ophciexamination_allergy', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophciexamination_allergy_set_subspecialty', 'ophciexamination_allergy_set');
        $this->dropForeignKey('ophciexamination_allergy_set_firm', 'ophciexamination_allergy_set');
        $this->dropForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment');
        $this->dropForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment');
        $this->dropForeignKey('ophciexamination_allergy_set_e', 'ophciexamination_allergy_set_entry');

        $this->dropOETable('ophciexamination_allergy_set_entry', true);
        $this->dropOETable('ophciexamination_allergy_set_assignment', true);
        $this->dropOETable('ophciexamination_allergy_set', true);

        $this->dropColumn('ophciexamination_allergy_entry', 'has_allergy');
        $this->dropColumn('ophciexamination_allergy_entry_version', 'has_allergy');
    }
}