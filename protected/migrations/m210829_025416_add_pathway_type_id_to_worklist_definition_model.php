<?php

class m210829_025416_add_pathway_type_id_to_worklist_definition_model extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            'worklist_definition',
            'pathway_type_id',
            'int NOT NULL DEFAULT 1',
            true
        );

        $this->addForeignKey(
            'worklist_definition_pathway_type_fk',
            'worklist_definition',
            'pathway_type_id',
            'pathway_type',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey(
            'worklist_definition_pathway_type_fk',
            'worklist_definition',
        );

        $this->dropOEColumn(
            'worklist_definition',
            'pathway_type_id',
            true
        );
    }
}
