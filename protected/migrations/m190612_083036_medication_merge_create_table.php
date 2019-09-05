<?php

class m190612_083036_medication_merge_create_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('medication_merge', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'entry_date_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'source_drug_id' => 'int(10)',
            'source_medication_id' => 'int(11)',
            'source_code' => 'varchar(255)',
            'source_name' => 'varchar(255)',
            'target_id' => 'int(11)',
            'target_code' => 'varchar(255)',
            'target_name' => 'varchar(255)',
            'status' => 'int(1) default 1',
            'merge_date' => 'TIMESTAMP' )
        );
    }

    public function down()
    {
        $this->dropTable('medication_merge');
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