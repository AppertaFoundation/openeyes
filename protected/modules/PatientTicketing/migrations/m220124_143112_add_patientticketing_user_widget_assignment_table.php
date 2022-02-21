<?php

class m220124_143112_add_patientticketing_user_widget_assignment_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('patientticketing_user_widget_assignment', [
            'id' => 'pk',
            'widget_id' => 'VARCHAR(255)',
            'ticket_id' => 'INTEGER NOT NULL',
            'queue_id' => 'INTEGER NOT NULL',
            'user_id' => 'INT(10) UNSIGNED NOT NULL',
        ], true);

        $this->addForeignKey(
            'patientticketing_user_widget_ass_q_id',
            'patientticketing_user_widget_assignment',
            'queue_id',
            'patientticketing_queue',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_user_widget_ass_t_id',
            'patientticketing_user_widget_assignment',
            'ticket_id',
            'patientticketing_ticket',
            'id'
        );
        $this->addForeignKey(
            'patientticketing_user_widget_ass_u_id',
            'patientticketing_user_widget_assignment',
            'user_id',
            'user',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('patientticketing_user_widget_ass_q_id', 'patientticketing_user_widget_assignment');
        $this->dropForeignKey('patientticketing_user_widget_ass_t_id', 'patientticketing_user_widget_assignment');
        $this->dropForeignKey('patientticketing_user_widget_ass_u_id', 'patientticketing_user_widget_assignment');
        $this->dropOETable('patientticketing_user_widget_assignment', true);
    }
}
