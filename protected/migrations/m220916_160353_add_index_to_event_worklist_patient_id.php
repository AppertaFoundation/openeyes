<?php

class m220916_160353_add_index_to_event_worklist_patient_id extends OEMigration
{
    public function up()
    {
        $this->createIndex("event_worklist_patient_index", 'event', 'worklist_patient_id');
    }

    public function down()
    {
        $this->dropIndex("event_worklist_patient_index", 'event');
    }
}
