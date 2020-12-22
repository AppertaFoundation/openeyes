<?php

/*
 * To make frequency_id to be set with default as null to make it optional in patient summary
 */
class m160928_073106_drop_medication_frequency_foreign_key extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('medication', 'frequency_id', 'int(10) unsigned DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('medication', 'frequency_id', 'int(10) unsigned NOT NULL');
    }

}
