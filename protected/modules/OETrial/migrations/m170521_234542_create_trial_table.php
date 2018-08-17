<?php

class m170521_234542_create_trial_table extends OEMigration
{
    const VERSIONED = true;

    public function up()
    {
        $this->createOETable('trial', array(
            'id' => 'pk',
            'trial_type' => 'integer unsigned not null',
            'name' => 'varchar(64) collate utf8_bin NOT NULL',
            'description' => 'text',
            'owner_user_id' => 'int(10) unsigned NOT NULL',
            'status' => 'int(10) unsigned NOT NULL',
            'started_date' => 'datetime',
            'closed_date' => 'datetime',
            'external_data_link' => 'varchar(255) collate utf8_bin',
        ), self::VERSIONED
        );

        $this->addForeignKey('trial_owner_fk', 'trial', 'owner_user_id', 'user', 'id');

    }

    public function down()
    {
        $this->dropOETable('trial', self::VERSIONED);
    }
}
