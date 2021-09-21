<?php

class m210111_035237_add_institution_id_to_worklist extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('worklist_display_context', 'institution_id', 'int(10) unsigned', false);
        $this->addOEColumn('worklist_definition_display_context', 'institution_id', 'int(10) unsigned', false);

        $this->addForeignKey('worklist_display_context_institution_id_fk', 'worklist_display_context', 'institution_id', 'institution', 'id');
        $this->addForeignKey('worklist_definition_display_context_institution_id_fk', 'worklist_definition_display_context', 'institution_id', 'institution', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('worklist_display_context_institution_id_fk', 'worklist_display_context');
        $this->dropForeignKey('worklist_definition_display_context_institution_id_fk', 'worklist_definition_display_context');

        $this->dropOEColumn('worklist_display_context', 'institution_id', false);
        $this->dropOEColumn('worklist_definition_display_context', 'institution_id', false);
    }
}
