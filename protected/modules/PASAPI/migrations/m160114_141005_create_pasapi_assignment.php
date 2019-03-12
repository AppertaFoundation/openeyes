<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m160114_141005_create_pasapi_assignment extends OEMigration
{
    public function up()
    {
        $this->createOETable('pasapi_assignment', array(
            'id' => 'pk',
            'resource_type' => 'varchar(40)',
            'resource_id' => 'varchar(40)',
            'internal_type' => 'varchar(40)',
            'internal_id' => 'int(10) unsigned NOT NULL',
            'UNIQUE KEY `internal_key` (`internal_id`,`internal_type`)',
            'UNIQUE KEY `resource_key` (`resource_id`,`resource_type`)',
        ), false);
    }

    public function down()
    {
        echo "m160114_141005_create_pasapi_assignment does not support migration down.\n";

        return false;
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
