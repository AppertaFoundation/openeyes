<?php

class m170110_152227_remove_migration_route_fk extends CDbMigration
{
    public function up()
    {
            $this->alterColumn('medication', 'route_id', 'int(10) unsigned DEFAULT NULL');
    }

    public function down()
    {
            $this->alterColumn('medication', 'route_id', 'int(10) unsigned NOT NULL');
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
