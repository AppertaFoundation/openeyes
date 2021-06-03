<?php

class m201029_060203_create_sso_roles_mapping_with_openeyes extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('sso_roles', array(
            'id' => 'pk',
            'name' => 'varchar(120) not null',
        ), true);

        $this->createOETable('sso_roles_authassignment', array(
            'id' => 'pk',
            'sso_role_id' => 'int(10) not null',
            'authitem_role' => 'varchar(64) not null',
        ), true);

        // Needed to form foreign key with authitem table
        $this->execute('alter table sso_roles_authassignment convert to character set utf8 collate utf8_bin');
        $this->execute('alter table sso_roles_authassignment_version convert to character set utf8 collate utf8_bin');

        $this->execute('ALTER TABLE sso_roles ADD CONSTRAINT `name` UNIQUE (name)');

        $this->addForeignKey(
            'fk_sso_roles_id',
            'sso_roles_authassignment',
            'sso_role_id',
            'sso_roles',
            'id'
        );

        $this->addForeignKey(
            'fk_sso_roles_authitem_role',
            'sso_roles_authassignment',
            'authitem_role',
            'authitem',
            'name'
        );

        $this->insert('sso_roles', array('name'=>'admin'));
        $admin_id = $this->dbConnection->createCommand()->select('id')->from('sso_roles')->where('name="admin"')->queryScalar();
        $this->insert('sso_roles_authassignment', array('sso_role_id'=>$admin_id, 'authitem_role'=>'admin'));

        $this->insert('audit_action', array('name'=>'SSO-role-modified'));
        $this->insert('audit_action', array('name'=>'SSO-role-deleted'));
        $this->insert('audit_type', array('name'=>'SSO'));
    }

    public function safeDown()
    {
        $this->dropOETable('sso_roles_authassignment', true);
        $this->dropOETable('sso_roles', true);

        $type_id = $this->dbConnection->createCommand()->select('id')->from('audit_type')->where('name="SSO"')->queryScalar();
        $this->delete('audit', "`type_id` = :type_id", array(':type_id' => $type_id));

        $this->delete('audit_action', "`name` = 'SSO-role-modified'");
        $this->delete('audit_action', "`name` = 'SSO-role-deleted'");
        $this->delete('audit_type', "`name` = 'SSO'");
    }
}
