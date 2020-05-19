<?php

class m140828_080914_queuesets extends OEMigration
{
    public function up()
    {
        $this->createOETable('patientticketing_queuesetcategory', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'display_order' => 'integer NOT NULL DEFAULT 1',
            'active' => 'boolean NOT NULL DEFAULT true',
        ), true);

        $this->createOETable('patientticketing_queueset', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'description' => 'text',
            'active' => 'boolean NOT NULL DEFAULT true',
            'category_id' => 'integer',
            'initial_queue_id' => 'integer NOT NULL',
            'summary_link' => 'boolean NOT NULL DEFAULT false',
        ), true);

        $this->addForeignKey(
            'patientticketing_queueset_catid',
            'patientticketing_queueset',
            'category_id',
            'patientticketing_queuesetcategory',
            'id'
        );

        $this->createOETable('patientticketing_queuesetuser', array(
            'id' => 'pk',
            'user_id' => 'int(10) unsigned NOT NULL',
            'queueset_id' => 'integer NOT NULL',
        ), false);

        $this->addForeignKey(
            'patientticketing_queuesetuser_uid',
            'patientticketing_queuesetuser',
            'user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'patientticketing_queuesetuser_qsid',
            'patientticketing_queuesetuser',
            'queueset_id',
            'patientticketing_queueset',
            'id'
        );

        $this->insert('patientticketing_queuesetcategory', array('name' => 'Virtual Clinic'));
        $vc_id = $this->dbConnection->getLastInsertID();

        $initial_queues = $this->dbConnection->createCommand('SELECT * FROM patientticketing_queue WHERE is_initial = 1')->queryAll();

        foreach ($initial_queues as $q) {
            $this->insert('patientticketing_queueset', array(
                            'name' => $q['name'],
                            'description' => $q['description'],
                            'initial_queue_id' => $q['id'],
                            'active' => $q['active'],
                            'summary_link' => $q['summary_link'],
                            'category_id' => $vc_id, ));
        }

        $this->dropColumn('patientticketing_queue', 'summary_link');
        $this->dropColumn('patientticketing_queue_version', 'summary_link');

        $this->insert('authitem', array('name' => 'TaskProcessQueueSet', 'type' => 1));
        $this->insert('authitemchild', array('parent' => 'Patient Tickets', 'child' => 'TaskProcessQueueSet'));
        $this->insert('authitem', array('name' => 'OprnProcessQueueSet', 'type' => 0, 'bizrule' => 'PatientTicketing.canProcessQueueSet'));
        $this->insert('authitemchild', array('parent' => 'TaskProcessQueueSet', 'child' => 'OprnProcessQueueSet'));
        $this->insert('authitem', array('name' => 'OprnCreateTicket', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskEditEvent', 'child' => 'OprnCreateTicket'));
        $this->insert('authitem', array('name' => 'OprnViewTicket', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'TaskViewClinical', 'child' => 'OprnViewTicket'));

        $this->insert('authitem', array('name' => 'TaskViewQueueSets', 'type' => 1));
        $this->insert('authitem', array('name' => 'OprnViewQueueSet', 'type' => 0));
        $this->insert('authitemchild', array('parent' => 'Patient Tickets', 'child' => 'TaskViewQueueSets'));
        $this->insert('authitemchild', array('parent' => 'TaskViewQueueSets', 'child' => 'OprnViewQueueSet'));

        // old erroneous authitems
        $this->delete('authitemchild', 'child = ?', array('OprnViewPatientTickets'));
        $this->delete('authitemchild', 'child = ?', array('OprnEditPatientTicket'));
        $this->delete('authitem', 'name = ?', array('OprnViewPatientTickets'));
        $this->delete('authitem', 'name = ?', array('OprnEditPatientTicket'));
    }

    public function down()
    {
        foreach (array('OprnProcessQueueSet', 'TaskProcessQueueSet', 'OprnViewQueueSet', 'TaskViewQueueSets', 'OprnCreateTicket', 'OprnViewTicket') as $ai) {
            $this->delete('authitemchild', 'child = ?', array($ai));
            $this->delete('authitem', 'name = ?', array($ai));
        }

        $this->addColumn('patientticketing_queue_version', 'summary_link', 'boolean NOT NULL DEFAULT false');
        $this->addColumn('patientticketing_queue', 'summary_link', 'boolean NOT NULL DEFAULT false');

        $queuesets = $this->dbConnection->createCommand('SELECT * FROM patientticketing_queueset')->queryAll();
        foreach ($queuesets as $qs) {
            $this->update('patientticketing_queue', array('summary_link' => $qs['summary_link']), "id = {$qs['initial_queue_id']}");
        }

        $this->dropOETable('patientticketing_queuesetuser', false);
        $this->dropOETable('patientticketing_queueset', true);
        $this->dropOETable('patientticketing_queuesetcategory', true);
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
