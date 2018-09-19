<?php

class m170521_234542_create_trial_table extends OEMigration
{
    const VERSIONED = true;

    public function up()
    {
        $this->createOETable('trial_type', array(
            'id' => 'pk',
            'name' => 'varchar(64) NOT NULL',
            'code' => 'varchar(64) NOT NULL',
        ));

        $this->insert('trial_type', array('name' => 'Intervention', 'code' => 'INTERVENTION'));
        $this->insert('trial_type', array('name' => 'Non-Intervention', 'code' => 'NON_INTERVENTION'));

        $this->createOETable('trial', array(
            'id' => 'pk',
            'trial_type_id' => 'int(11) NOT NULL',
            'name' => 'varchar(200) collate utf8_bin NOT NULL',
            'description' => 'text',
            'owner_user_id' => 'int(10) unsigned NOT NULL',
            'principle_investigator_user_id' => 'int(10) unsigned NOT NULL',
            'coordinator_user_id' => 'int(10) unsigned',
            'is_open' => 'int(1) NOT NULL',
            'started_date' => 'datetime',
            'closed_date' => 'datetime',
            'external_data_link' => 'mediumtext',
        ), self::VERSIONED
        );

        $this->addForeignKey('trial_type_id_fk',
            'trial', 'trial_type_id',
            'trial_type', 'id');

        $this->addForeignKey('trial_owner_fk',
            'trial', 'owner_user_id',
            'user', 'id');
        $this->addForeignKey('trial_principle_investigator_user_id_fk',
            'trial', 'principle_investigator_user_id',
            'user', 'id');
        $this->addForeignKey('trial_coordinator_user_id_fk',
            'trial', 'coordinator_user_id',
            'user', 'id');

    }

    public function down()
    {
        $this->dropOETable('trial', self::VERSIONED);
        $this->dropOETable('trial_type');
    }
}
