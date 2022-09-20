<?php

class m220916_160353_add_index_to_event_worklist_patient_id extends OEMigration
{
    public function up()
    {
        if (!$this->dbConnection->createCommand("SHOW INDEX FROM event WHERE Key_name = 'event_ibfk_worklist_patient'")->queryScalar()) {
            $this->createIndex("event_ibfk_worklist_patient", 'event', 'worklist_patient_id');
        }
    }

    public function down()
    {
        $this->dropIndex("event_ibfk_worklist_patient", 'event');
    }
}
