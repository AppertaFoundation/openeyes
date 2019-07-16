<?php

class m170404_084439_optometrist_fields extends CDbMigration
{
    public function up()
    {
        $this->addColumn('automatic_examination_event_log', 'optometrist','varchar(255)');
        $this->addColumn('automatic_examination_event_log', 'goc_number','varchar(255)');
        $this->addColumn('automatic_examination_event_log', 'optometrist_address','text');

        $this->addColumn('automatic_examination_event_log_version', 'optometrist','varchar(255)');
        $this->addColumn('automatic_examination_event_log_version', 'goc_number','varchar(255)');
        $this->addColumn('automatic_examination_event_log_version', 'optometrist_address','text');
    }

    public function down()
    {
        $this->dropColumn('automatic_examination_event_log_version', 'optometrist_address');
        $this->dropColumn('automatic_examination_event_log_version', 'goc_number');
        $this->dropColumn('automatic_examination_event_log_version', 'optometrist');

        $this->dropColumn('automatic_examination_event_log', 'optometrist_address');
        $this->dropColumn('automatic_examination_event_log', 'goc_number');
        $this->dropColumn('automatic_examination_event_log', 'optometrist');
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