<?php

class m200623_235654_create_operationchecklists_dilation_tables extends OEMigration
{
    public function safeUp()
    {
        // Creating Table
        $this->createOETable('ophtroperationchecklists_dilation', array(
            'id' => 'pk',
            'checklist_result_id' => 'int(11)',
            'is_not_required' => 'tinyint(1) unsigned',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_dilation_crid_fk',
            'ophtroperationchecklists_dilation',
            'checklist_result_id',
            'ophtroperationchecklists_admission_results',
            'id'
        );

        $this->createOETable('ophtroperationchecklists_dilation_treatment', array(
            'id' => 'pk',
            'dilation_id' => 'int(11)',
            'drug_id' => 'int(10) unsigned NOT NULL',
            'drops' => 'int(10) unsigned NOT NULL',
            'treatment_time' => 'time NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_dilation_treatment_diid_fk',
            'ophtroperationchecklists_dilation_treatment',
            'dilation_id',
            'ophtroperationchecklists_dilation',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_dilation_treatment_drid_fk',
            'ophtroperationchecklists_dilation_treatment',
            'drug_id',
            'ophciexamination_dilation_drugs',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophtroperationchecklists_dilation_treatment', true);
        $this->dropOETable('ophtroperationchecklists_dilation', true);
    }
}
