<?php

class m200623_235653_create_theatreadmission_dilation_tables extends OEMigration
{
    public function safeUp()
    {
        // Creating Table
        $this->createOETable('ophcitheatreadmission_dilation', array(
                'id' => 'pk',
                'checklist_result_id' => 'int(11)',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophcitheatreadmission_dilation_crid_fk',
            'ophcitheatreadmission_dilation',
            'checklist_result_id',
            'ophcitheatreadmission_admission_checklist_results',
            'id'
        );

        $this->createOETable('ophcitheatreadmission_dilation_treatment', array(
            'id' => 'pk',
            'dilation_id' => 'int(11)',
            'drug_id' => 'int(10) unsigned NOT NULL',
            'drops' => 'int(10) unsigned NOT NULL',
            'treatment_time' => 'time NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophcitheatreadmission_dilation_treatment_diid_fk',
            'ophcitheatreadmission_dilation_treatment',
            'dilation_id',
            'ophcitheatreadmission_dilation',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dilation_treatment_drid_fk',
            'ophcitheatreadmission_dilation_treatment',
            'drug_id',
            'ophciexamination_dilation_drugs',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophcitheatreadmission_dilation_treatment', true);
        $this->dropOETable('ophcitheatreadmission_dilation', true);
    }
}
