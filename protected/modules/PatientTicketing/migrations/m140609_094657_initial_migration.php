<?php

class m140609_094657_initial_migration extends OEMigration
{
    private $authitems = array(
        array('name' => 'Patient Tickets', 'type' => 2),
        array('name' => 'OprnViewPatientTickets', 'type' => 0),
        array('name' => 'OprnEditPatientTicket', 'type' => 0),
    );

    private $parents = array(
        'OprnViewPatientTickets' => 'Patient Tickets',
        'OprnEditPatientTicket' => 'TaskEditPatientData',
    );

    public function up()
    {
        $this->createOETable('patientticketing_priority', array(
                    'id' => 'pk',
                    'name' => 'string NOT NULL',
                    'display_order' => 'integer NOT NULL',
                    'colour' => 'string',
                ), true);

        $this->createOETable('patientticketing_ticket', array(
                    'id' => 'pk',
                    'patient_id' => 'int(10) unsigned NOT NULL',
                    'priority_id' => 'integer',
                    'report' => 'text',
                    'assignee_user_id' => 'int(10) unsigned',
                    'assignee_date' => 'datetime',
                    'event_id' => 'int(10) unsigned',
                ), true);

        $this->addForeignKey(
            'patientticketing_ticket_priid',
            'patientticketing_ticket',
            'priority_id',
            'patientticketing_priority',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_ticket_assuiid',
            'patientticketing_ticket',
            'assignee_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_ticket_evid',
            'patientticketing_ticket',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('patientticketing_queue', array(
                    'id' => 'pk',
                    'name' => 'string NOT NULL',
                    'description' => 'text',
                    'active' => 'boolean NOT NULL DEFAULT true',
                    'report_definition' => 'text',
                    'is_initial' => 'boolean NOT NULL',
                ), true);

        $this->createOETable('patientticketing_queueoutcome', array(
                    'id' => 'pk',
                    'active' => 'boolean NOT NULL DEFAULT true',
                    'queue_id' => 'integer NOT NULL',
                    'outcome_queue_id' => 'integer',
                    'display_order' => 'integer NOT NULL',
                ), true);

        $this->addForeignKey(
            'patientticketing_queueoutcome_qid',
            'patientticketing_queueoutcome',
            'queue_id',
            'patientticketing_queue',
            'id'
        );

        $this->addForeignKey(
            'patientticketing_queueoutcome_oqid',
            'patientticketing_queueoutcome',
            'outcome_queue_id',
            'patientticketing_queue',
            'id'
        );

        $this->createOETable('patientticketing_ticketqueue_assignment', array(
                    'id' => 'pk',
                    'ticket_id' => 'integer NOT NULL',
                    'queue_id' => 'integer NOT NULL',
                    'assignment_date' => 'datetime NOT NULL',
                    'assignment_user_id' => 'int(10) unsigned NOT NULL',
                    'assignment_firm_id' => 'int(10) unsigned NOT NULL',
                    'notes' => 'text',
                    'details' => 'text',
                ), true);

        $this->addForeignKey(
            'patientticketing_ticketqueue_ass_qid',
            'patientticketing_ticketqueue_assignment',
            'queue_id',
            'patientticketing_queue',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_ticketqueue_ass_tid',
            'patientticketing_ticketqueue_assignment',
            'ticket_id',
            'patientticketing_ticket',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_ticketqueue_ass_auid',
            'patientticketing_ticketqueue_assignment',
            'assignment_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_ticketqueue_ass_afid',
            'patientticketing_ticketqueue_assignment',
            'assignment_firm_id',
            'firm',
            'id'
        );

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        foreach ($this->authitems as $authitem) {
            $this->insert('authitem', $authitem);
        }

        foreach ($this->parents as $child => $parent) {
            $this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
        }
    }

    public function down()
    {
        foreach ($this->parents as $child => $parent) {
            $this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
        }

        foreach ($this->authitems as $authitem) {
            $this->delete('authitem', 'name = ?', array($authitem['name']));
        }

        $this->dropOETable('patientticketing_ticketqueue_assignment', true);
        $this->dropOETable('patientticketing_queueoutcome', true);
        $this->dropOETable('patientticketing_queue', true);
        $this->dropOETable('patientticketing_ticket', true);
        $this->dropOETable('patientticketing_priority', true);
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
