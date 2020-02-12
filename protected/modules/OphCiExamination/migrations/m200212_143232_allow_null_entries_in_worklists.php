<?php

class m200212_143232_allow_null_entries_in_worklists extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('worklist_definition_display_context', 'worklist_definition_id', 'INTEGER(11) NULL');
        $this->alterColumn('worklist_definition_mapping', 'worklist_definition_id', 'INTEGER(11) NULL');
        $this->alterColumn('worklist_definition_mapping_value', 'worklist_definition_mapping_id', 'INTEGER(11) NULL');
    }

    public function down()
    {
        $this->alterColumn('worklist_definition_display_context', 'worklist_definition_id', 'INTEGER(11) NOT NULL');
        $this->alterColumn('worklist_definition_mapping', 'worklist_definition_id', 'INTEGER(11) NOT NULL');
        $this->alterColumn('worklist_definition_mapping_value', 'worklist_definition_mapping_id', 'INTEGER(11) NOT NULL');
    }
}
