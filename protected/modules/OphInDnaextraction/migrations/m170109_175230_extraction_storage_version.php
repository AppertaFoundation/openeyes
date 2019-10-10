<?php

class m170109_175230_extraction_storage_version extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophindnaextraction_storage_address');
    }

    public function down()
    {
         $this->dropTable('ophindnaextraction_storage_address_version');
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