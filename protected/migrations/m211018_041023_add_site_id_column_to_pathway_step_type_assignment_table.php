<?php

class m211018_041023_add_site_id_column_to_pathway_step_type_assignment_table extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('pathway_step_type_preset_assignment', 'site_id', 'int(10) unsigned AFTER preset_id', true);
        $this->addForeignKey('pathway_step_type_assignment_site_fk', 'pathway_step_type_preset_assignment', 'site_id', 'site', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('pathway_step_type_assignment_site_fk', 'pathway_step_type_preset_assignment');
        $this->dropOEColumn('pathway_step_type_preset_assignment', 'site_id', true);
    }
}
