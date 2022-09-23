<?php

class m220916_160353_ensure_worklist_patient_id_is_indexed extends OEMigration
{
    public function safeUp()
    {
        if (!$this->dbConnection->createCommand("SHOW INDEX FROM event WHERE Key_name = 'event_ibfk_worklist_patient'")->queryScalar()) {
            $this->createIndex("event_ibfk_worklist_patient", 'event', 'worklist_patient_id');
        }
    }

    public function safeDown()
    {
        // Do not need to remove as it might have been added in a previous migration
    }
}
