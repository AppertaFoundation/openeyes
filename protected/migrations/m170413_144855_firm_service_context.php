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
 * @copyright Copyright (c) 2011-2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m170413_144855_firm_service_context extends OEMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'context_firm_label',
            'name' => 'Context Firm Label',
            'default_value' => 'context',
        ));
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'service_firm_label',
            'name' => 'Service Firm Label',
            'default_value' => 'service',
        ));
        $this->addColumn('firm', 'can_own_an_episode', 'boolean not null default true');
        $this->addColumn('firm', 'runtime_selectable', 'boolean not null default true');
        $this->addColumn('firm_version', 'can_own_an_episode', 'boolean not null default true');
        $this->addColumn('firm_version', 'runtime_selectable', 'boolean not null default true');
    }

    public function down()
    {
        $this->dropColumn('firm_version', 'runtime_selectable');
        $this->dropColumn('firm_version', 'can_own_an_episode');
        $this->dropColumn('firm', 'runtime_selectable');
        $this->dropColumn('firm', 'can_own_an_episode');

        $this->delete('setting_metadata',
            'key = :key',
            array(':key' => 'service_firm_label'));
        $this->delete('setting_metadata',
            'key = :key',
            array(':key' => 'context_firm_label'));
    }

}