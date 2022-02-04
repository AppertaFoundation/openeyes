<?php

class m210719_053115_add_setting_for_pathway_wait_times extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'worklist_wait_time',
            array(
                'id' => 'pk',
                'label' => 'tinytext',
                'wait_minutes' => 'int(32)',
                'display_order' => 'int(8)'
            ),
            true
        );
    }

    public function down()
    {
        $this->dropOETable('worklist_wait_time', true);
    }
}
