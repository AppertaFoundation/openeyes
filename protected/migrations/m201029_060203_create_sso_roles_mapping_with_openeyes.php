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

        // In some databases the authitem table has utf8_bin collation, which causes issues with creating FKs
        // As we're not currently sure of the consequences of changing the collation of authitem, we change sso_roles_authassignment to match authitem
        echo("\nauthitem_charset: " . $authitem_charset  = $this->getTableCharset("authitem"));
        echo("\nauthitem_collation: " . $authitem_collation = $this->getTableCollation("authitem"));
        echo("\nsso_charset: " . $sso_charset = $this->getTableCharset("sso_roles_authassignment"));
        echo("\nsso_collation: " . $sso_collation = $this->getTableCollation("sso_roles_authassignment"));

        if ($authitem_charset != $sso_charset || $authitem_collation != $sso_collation) {
            echo "\n\nChanging character set of sso_roles_authassignment to match authitem\n\n";
            $this->dbConnection->createCommand("ALTER TABLE sso_roles_authassignment CONVERT TO CHARACTER SET :charset COLLATE :collation")->execute(array(':charset' => $authitem_charset, ':collation' => $authitem_collation));
            // and also change _version table
            $this->dbConnection->createCommand("ALTER TABLE sso_roles_authassignment_version CONVERT TO CHARACTER SET :charset COLLATE :collation")->execute(array(':charset' => $authitem_charset, ':collation' => $authitem_collation));
        } else {
            echo "\n\nCharacter sets match, moving on\n\n";
        }

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

        $this->insert('sso_roles', array('name' => 'admin'));
        $admin_id = $this->dbConnection->createCommand()->select('id')->from('sso_roles')->where('name="admin"')->queryScalar();
        $this->insert('sso_roles_authassignment', array('sso_role_id' => $admin_id, 'authitem_role' => 'admin'));

        $this->insert('audit_action', array('name' => 'SSO-role-modified'));
        $this->insert('audit_action', array('name' => 'SSO-role-deleted'));
        $this->insert('audit_type', array('name' => 'SSO'));
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

     /**
     * Returns the character set (as a string) for a given table name
     * @param table the name of the table to query
     * @return string
     */
    private function getTableCharset($table)
    {
        return $this->dbConnection->createCommand("SELECT CCSA.character_set_name FROM information_schema.`TABLES` T,
                                                information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
                                            WHERE CCSA.collation_name = T.table_collation
                                            AND T.table_schema = DATABASE()
                                            AND T.table_name = :tablename;")->queryScalar(array(':tablename' => $table ));
    }

    /**
     * Returns the character set (as a string) for a given table name
     * @param table the name of the table to query
     * @return string
     */
    private function getTableCollation($table)
    {
        return $this->dbConnection->createCommand("SELECT T.TABLE_COLLATION FROM information_schema.`TABLES` T
                                                    WHERE T.table_schema = DATABASE()
                                                    AND T.table_name = :tablename;")->queryScalar(array(':tablename' => $table ));
    }
}
