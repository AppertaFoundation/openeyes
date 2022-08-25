<?php

class m220823_090840_adding_delete_pending_index extends CDbMigration
{
    public function safeUp()
    {
        if ($this->dbConnection->createCommand("SHOW INDEX FROM event WHERE Key_name = 'idx_event_deleted_pending'")->queryScalar()) {
            $this->execute("DROP INDEX idx_event_deleted_pending ON event");
        }
        $this->execute("CREATE INDEX idx_event_deleted_pending ON event(delete_pending)");
    }

    public function safeDown()
    {
        if ($this->dbConnection->createCommand("SHOW INDEX FROM event WHERE Key_name = 'idx_event_deleted_pending'")->queryScalar()) {
            $this->execute("DROP INDEX idx_event_deleted_pending ON event");
        }
    }
}
