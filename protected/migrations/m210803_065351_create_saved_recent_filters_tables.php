<?php

class m210803_065351_create_saved_recent_filters_tables extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'worklist_filter',
            [
                'id' => 'pk',
                'name' => 'varchar(100) NOT NULL',
                'filter' => 'JSON NOT NULL CHECK (JSON_VALID(filter))'
            ],
            true
        );

        // Not versioned as it's only meant for temporary data
        $this->createOETable(
            'worklist_recent_filter',
            [
                'id' => 'pk',
                'filter' => 'JSON NOT NULL CHECK (JSON_VALID(filter))'
            ],
            false
        );
    }

    public function down()
    {
        $this->dropOETable('worklist_recent_filter', false);
        $this->dropOETable('worklist_filter', true);
    }
}
