<?php

class m190802_065533_create_cat_prom5_event_result extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('cat_prom5_event_result', array(
            'id' => 'pk',
            'total_raw_score' => 'int(3)',
            'total_rasch_measure' => 'DECIMAL(5,2)',
            'event_id' => 'int(10) unsigned'
        ), true);

    }

    public function safeDown()
    {
        $this->dropOETable('cat_prom5_event_result', true);
    }
}
