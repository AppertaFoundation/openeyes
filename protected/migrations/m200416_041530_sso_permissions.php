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
        // The authitem table has collation of utf8_bin which creates error in ceating foreign keys
        $this->execute('alter table sso_default_user_roles convert to character set utf8 collate utf8_bin');
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
}
