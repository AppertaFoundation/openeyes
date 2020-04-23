<?php

class m160823_100518_add_user_id_to_unique_codes_mapping extends CDbMigration
{
    public function up()
    {
        $this->addColumn('unique_codes_mapping', 'user_id', 'int(10) unsigned');
        $this->addColumn('unique_codes_mapping_version', 'user_id', 'int(10) unsigned');
        $this->alterColumn('unique_codes_mapping', 'event_id', 'int(10) unsigned null');
        $this->alterColumn('unique_codes_mapping_version', 'event_id', 'int(10) unsigned null');
        $this->addForeignKey('unique_codes_mapping_user_id_fk', 'unique_codes_mapping', 'user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('unique_codes_mapping_user_id_fk', 'unique_codes_mapping');
        $this->dropColumn('unique_codes_mapping', 'user_id');
        $this->dropColumn('unique_codes_mapping_version', 'user_id');
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
