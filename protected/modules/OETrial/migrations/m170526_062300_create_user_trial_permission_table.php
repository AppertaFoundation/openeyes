<?php

class m170526_062300_create_user_trial_permission_table extends OEMigration
{
    const IS_VERSIONED = true;

    public function up()
    {
        $this->createOETable('trial_permission', array(
            'id' => 'pk',
            'name' => 'varchar(64) NOT NULL',
            'code' => 'varchar(64) NOT NULL',
            'can_edit' => 'int(1) unsigned NOT NULL',
            'can_view' => 'int(1) unsigned NOT NULL',
            'can_manage' => 'int(1) unsigned NOT NULL',
        ));

        $this->insert('trial_permission', array(
            'name' => 'View',
            'code' => 'VIEW',
            'can_view' => 1,
            'can_edit' => 0,
            'can_manage' => 0,
        ));
        $this->insert('trial_permission', array(
            'name' => 'Edit',
            'code' => 'EDIT',
            'can_view' => 1,
            'can_edit' => 1,
            'can_manage' => 0,
        ));
        $this->insert('trial_permission', array(
            'name' => 'Manage',
            'code' => 'MANAGE',
            'can_view' => 1,
            'can_edit' => 1,
            'can_manage' => 1,
        ));

        $this->createOETable('user_trial_assignment', array(
            'id' => 'pk',
            'user_id' => 'int(10) unsigned NOT NULL',
            'trial_id' => 'int(11) NOT NULL',
            'trial_permission_id' => 'int(11) NOT NULL',
            'role' => 'varchar(255)',
        ), self::IS_VERSIONED);

        $this->addForeignKey('user_trial_assignment_trial_fk', 'user_trial_assignment', 'trial_id', 'trial', 'id');
        $this->addForeignKey('user_trial_assignment_user_fk', 'user_trial_assignment', 'user_id', 'user', 'id');
        $this->addForeignKey(
            'user_trial_assignment_trial_permission_fk',
            'user_trial_assignment',
            'trial_permission_id',
            'trial_permission',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('user_trial_assignment', self::IS_VERSIONED);
        $this->dropOETable('trial_permission');
    }
}
