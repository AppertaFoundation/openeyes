<?php

/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m230516_182500_move_device_information_element_to_bottom extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'element_type',
            ['display_order' => 700],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphGeneric\models\DeviceInformation']
        );

        return true;
    }

    public function safeDown()
    {
        $this->update(
            'element_type',
            ['display_order' => 11],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphGeneric\models\DeviceInformation']
        );

        return true;
    }
}
