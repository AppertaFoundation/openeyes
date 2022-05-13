<?php

class m220505_091700_add_deleted_to_patientticketing_ticket extends OEMigration
{
    public function safeUp()
    {
        if (!isset($this->dbConnection->schema->getTable('patientticketing_ticket', true)->columns['deleted'])) {
            $this->addOEColumn('patientticketing_ticket', 'deleted', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0', true);
        }
    }

    public function safeDown()
    {
        if (isset($this->dbConnection->schema->getTable('patientticketing_ticket', true)->columns['deleted'])) {
            $this->dropOEColumn('patientticketing_ticket', 'deleted', true);
        }
    }
}
