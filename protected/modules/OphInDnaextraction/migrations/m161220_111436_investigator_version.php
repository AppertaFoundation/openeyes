<?php

class m161220_111436_investigator_version extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophindnaextraction_dnatests_investigator');
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_dnatests_investigator_version');
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