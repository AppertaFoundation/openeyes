<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200416_041530_sso_permissions extends OEMigration
{
    private $rights = array(
        'source' => 'SSO',
        'global_firm_rights' => 1,
        'is_consultant' => 0,
        'is_surgeon' => 0
    );
    private $firms = array(
        'sso_user_id' => 1,
        'firm_id' => 1
    );
    private $roles = array(
        'sso_user_id' => 1,
        'roles' => 'User'
    );


    public function safeUp()
    {
        $this->createOETable('sso_default_user_rights', array(
            'id' => 'pk',
            'source' => 'varchar(10)',
            'global_firm_rights' => 'tinyint(1)',
            'is_consultant' => 'tinyint(1)',
            'is_surgeon' => 'tinyint(1)',
        ), true);

        $this->createOETable('sso_default_user_firms', array(
            'sso_user_id' => 'int(10)',
            'firm_id' => 'int(10) unsigned',
            ), true);
        $this->addForeignKey(
            'fk_rights_firms',
            'sso_default_user_firms',
            'sso_user_id',
            'sso_default_user_rights',
            'id'
        );
        $this->addForeignKey(
            'fk_firms_firms',
            'sso_default_user_firms',
            'firm_id',
            'firm',
            'id'
        );

        $this->createOETable('sso_default_user_roles', array(
            'sso_user_id' => 'int(10)',
            'roles' => 'varchar(64) not null'
        ), true);
        // In some databases the authitem table has utf8_bin collation, which causes issues with creating FKs
        // As we're not currently sure of the consequences of changing the collation of authitem, we change sso_default_user_roles to match authitem
        echo("\nauthitem_charset: " . $authitem_charset  = $this->getTableCharset("authitem"));
        echo("\nauthitem_collation: " . $authitem_collation = $this->getTableCollation("authitem"));
        echo("\nsso_charset: " . $sso_charset = $this->getTableCharset("sso_default_user_roles"));
        echo("\nsso_collation: " . $sso_collation = $this->getTableCollation("sso_default_user_roles"));

        if ($authitem_charset != $sso_charset || $authitem_collation != $sso_collation) {
            echo "\n\nChanging character set of sso_default_user_roles to match authitem\n\n";
            $this->dbConnection->createCommand("ALTER TABLE sso_default_user_roles CONVERT TO CHARACTER SET :charset COLLATE :collation")->execute(array(':charset' => $authitem_charset, ':collation' => $authitem_collation));
            // and also change _version table
            $this->dbConnection->createCommand("ALTER TABLE sso_default_user_roles_version CONVERT TO CHARACTER SET :charset COLLATE :collation")->execute(array(':charset' => $authitem_charset, ':collation' => $authitem_collation));
        } else {
            echo "\n\nCharacter sets match, moving on\n\n";
        }

        $this->addForeignKey(
            'fk_rights_roles',
            'sso_default_user_roles',
            'sso_user_id',
            'sso_default_user_rights',
            'id'
        );
        $this->addForeignKey(
            'fk_roles_roles',
            'sso_default_user_roles',
            'roles',
            'authitem',
            'name'
        );

        $this->insert('sso_default_user_rights', $this->rights);
        $this->insert('sso_default_user_firms', $this->firms);
        $this->insert('sso_default_user_roles', $this->roles);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_rights_firms', 'sso_default_user_firms');
        $this->dropForeignKey('fk_firms_firms', 'sso_default_user_firms');
        $this->dropForeignKey('fk_rights_roles', 'sso_default_user_roles');
        $this->dropForeignKey('fk_roles_roles', 'sso_default_user_roles');

        $this->dropOETable('sso_default_user_rights', true);
        $this->dropOETable('sso_default_user_firms', true);
        $this->dropOETable('sso_default_user_roles', true);
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
