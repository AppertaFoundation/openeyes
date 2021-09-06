<?php

class m210826_030924_create_pathway_step_type_associated_event extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'pathway_step_type_preset_assignment',
            [
                'id' => 'pk',
                'custom_pathway_step_type_id' => 'int(11)',
                'standard_pathway_step_type_id' => 'int(11)',
                'preset_short_name' => 'text',
                'preset_id' => 'int(11)',
                'subspecialty_id' => 'int(10) unsigned',
                'firm_id' => 'int(10) unsigned',
            ],
            true
        );
        $this->addForeignKey(
            'pathway_step_type_custom_type_fk',
            'pathway_step_type_preset_assignment',
            'custom_pathway_step_type_id',
            'pathway_step_type',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_type_standard_type_fk',
            'pathway_step_type_preset_assignment',
            'standard_pathway_step_type_id',
            'pathway_step_type',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_type_assignment_subspecialty_fk',
            'pathway_step_type_preset_assignment',
            'subspecialty_id',
            'subspecialty',
            'id'
        );
        $this->addForeignKey(
            'pathway_step_type_assignment_firm_fk',
            'pathway_step_type_preset_assignment',
            'firm_id',
            'firm',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('pathway_step_type_custom_type_fk', 'pathway_step_type_preset_assignment');
        $this->dropForeignKey('pathway_step_type_standard_type_fk', 'pathway_step_type_preset_assignment');
        $this->dropForeignKey('pathway_step_type_assignment_subspecialty_fk', 'pathway_step_type_preset_assignment');
        $this->dropForeignKey('pathway_step_type_assignment_firm_fk', 'pathway_step_type_preset_assignment');
        $this->dropOETable('pathway_step_type_preset_assignment', true);
    }
}
