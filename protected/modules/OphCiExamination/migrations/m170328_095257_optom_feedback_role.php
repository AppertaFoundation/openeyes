<?php

class m170328_095257_optom_feedback_role extends OEMigration
{

    public function up()
    {
        $this->insert('authitem', array('name' => 'Optom co-ordinator', 'type' => 2));

        $this->createOETable('ophciexamination_invoice_status', array(
            'id' => 'pk',
            'name' => 'VARCHAR(64) NOT NULL',
            'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
        ));

        $this->insert('ophciexamination_invoice_status', array('name' => 'Received'));
        $this->insert('ophciexamination_invoice_status', array('name' => 'Order raised'));
        $this->insert('ophciexamination_invoice_status', array('name' => 'Paid'));
        $this->insert('ophciexamination_invoice_status', array('name' => 'Rejected'));

        $this->addColumn('automatic_examination_event_log', 'invoice_status_id','integer unsigned not null DEFAULT 0');
        $this->addColumn('automatic_examination_event_log', 'comment','text');

        $this->addColumn('automatic_examination_event_log_version', 'invoice_status_id','integer unsigned not null');
        $this->addColumn('automatic_examination_event_log_version', 'comment','text');
    }

    public function down()
    {
        $this->dropColumn('automatic_examination_event_log_version', 'comment');
        $this->dropColumn('automatic_examination_event_log_version', 'invoice_status_id');

        $this->dropColumn('automatic_examination_event_log', 'comment');
        $this->dropColumn('automatic_examination_event_log', 'invoice_status_id');
        $this->dropTable('ophciexamination_invoice_status');
        $this->delete('authitem', "name = 'Optom co-ordinator'");
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