<?php

class m170106_121818_extraction_box_version extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophindnaextraction_dnaextraction_box');
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_dnaextraction_box_version');
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
