<?php

class m170407_160941_reposition_version_table_fields extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `automatic_examination_event_log` MODIFY `invoice_status_id` text AFTER `comment` ");

        $this->execute("ALTER TABLE `automatic_examination_event_log_version` MODIFY `comment` text AFTER `created_date` ");
        $this->execute("ALTER TABLE `automatic_examination_event_log_version` MODIFY  `invoice_status_id` text AFTER `comment` ");
        $this->execute("ALTER TABLE `automatic_examination_event_log_version` MODIFY  `optometrist` text AFTER `invoice_status_id` ");
        $this->execute("ALTER TABLE `automatic_examination_event_log_version` MODIFY  `goc_number` text AFTER `optometrist` ");
        $this->execute("ALTER TABLE `automatic_examination_event_log_version` MODIFY  `optometrist_address` text AFTER `goc_number` ");
    }

    public function down()
    {

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
