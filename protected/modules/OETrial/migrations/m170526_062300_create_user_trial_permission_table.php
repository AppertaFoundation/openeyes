<?php

class m170526_062300_create_user_trial_permission_table extends OEMigration
{
    const IS_VERSIONED = true;

    public function up()
    {
        $this->createOETable('user_trial_permission', array(
            'id' => 'pk',
            'user_id' => 'int(10) unsigned NOT NULL',
            'trial_id' => 'int(11) NOT NULL',
            'permission' => 'int(10) unsigned NOT NULL',
            'role' => 'varchar(255)',
        ), self::IS_VERSIONED);

        $this->addForeignKey('user_trial_permission_trial_fk', 'user_trial_permission', 'trial_id', 'trial', 'id');
        $this->addForeignKey('user_trial_permission_user_fk', 'user_trial_permission', 'user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropTable('user_trial_permission', self::IS_VERSIONED);
    }
}