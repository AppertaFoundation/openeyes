<?php

class m141105_211010_move_report_to_assignment extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_ticketqueue_assignment', 'report', 'text');
        $this->addColumn('patientticketing_ticketqueue_assignment_version', 'report', 'text');
        $tickets = $this->dbConnection->createCommand('SELECT * FROM patientticketing_ticket')->queryAll();
        foreach ($tickets as $t) {
            $this->update(
                'patientticketing_ticketqueue_assignment',
                array(
                    'report' => $t['report'],
                ),
                'ticket_id = :tid',
                array(
                    ':tid' => $t['id'],
                )
            );
        }
        $this->dropColumn('patientticketing_ticket', 'report');
        $this->dropColumn('patientticketing_ticket_version', 'report');
    }

    public function down()
    {
        $this->addColumn('patientticketing_ticket', 'report', 'text');
        $this->addColumn('patientticketing_ticket_version', 'report', 'text');
        $assignments = $this->dbConnection->createCommand('SELECT * FROM patientticketing_ticketqueue_assignment ORDER BY created_date DESC')->queryAll();
        $done = array();
        foreach ($assignments as $ass) {
            if ($ass['report'] && !in_array($ass['ticket_id'], $done)) {
                $this->update(
                    'patientticketing_ticket',
                    array(
                        'report' => $ass['report'],
                    ),
                    'id = :tid',
                    array(
                        ':tid' => $ass['ticket_id'],
                    )
                );
                $done[] = $ass['ticket_id'];
            }
        }
        $this->dropColumn('patientticketing_ticketqueue_assignment', 'report');
        $this->dropColumn('patientticketing_ticketqueue_assignment_version', 'report');
    }
}
