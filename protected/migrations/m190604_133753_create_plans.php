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
 * @copyright Copyright (c) 2011-2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m190604_133753_create_plans extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'plans_problems',
            [
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'patient_id' => 'int(10) unsigned NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'active' => 'bool default true'
            ],
            true
        );

        $this->addForeignKey(
            'plans_problems_user_fk',
            'plans_problems',
            'patient_id',
            'patient',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('plans_problems_user_fk', 'plans_problems');
        $this->dropTable('plans_problems');
    }
}
