<?php

class m220113_152620_update_event_site_id_from_patientticketing extends OEMigration
{
    public function safeUp()
    {
        if ($this->isColumnExist('patientticketing_ticketqueue_assignment', 'assignment_site_id')) {
            $sql = <<<SQL
            SELECT t.event_id AS event_id, a.assignment_site_id as site_id
            FROM patientticketing_ticketqueue_assignment a
            JOIN patientticketing_ticket t ON t.`id` = a.`ticket_id`; 
SQL;
            $result = $this->dbConnection->createCommand($sql)->queryAll();
            foreach ($result as $item) {
                if ($item['event_id'] && $item['site_id']) {
                    $this->execute("UPDATE event SET site_id = :site_id WHERE id = :event_id", [
                        'event_id' => $item['event_id'],
                        'site_id' => $item['site_id']
                    ]);
                }
            }
        }
    }

    public function safeDown()
    {
        echo "This migration does not support down migration.\n";
        return false;
    }
}
