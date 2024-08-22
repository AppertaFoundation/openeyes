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

class m230323_122010_manual_visual_fields_event_config extends OEMigration
{
    protected const EVENT_SUBTYPE = 'Visual Fields';
    protected const ELEMENT_TYPES = ['OEModule\OphGeneric\models\HFA', 'OEModule\OphGeneric\models\Comments', 'OEModule\OphGeneric\models\DeviceInformation'];
    public function safeUp()
    {
        // reset default elements
        $this->delete(
            'event_subtype_element_entries',
            'event_subtype = :subtype',
            [
                ':subtype' => self::EVENT_SUBTYPE
            ]
        );

        foreach (self::ELEMENT_TYPES as $i => $element_type) {
            $element_type_id = $this->getIdOfElementTypeByClassName($element_type);

            $this->insert(
                'event_subtype_element_entries',
                [
                    'event_subtype' => self::EVENT_SUBTYPE,
                    'element_type_id' => $element_type_id,
                    'display_order' => $i + 1
                ]
            );
        }

        return true;
    }

    public function safeDown()
    {
        $this->delete(
            'event_subtype_element_entries',
            'event_subtype = :subtype',
            [
                ':subtype' => self::EVENT_SUBTYPE
            ]
        );

        $this->update(
            'event_subtype',
            ['manual_entry' => false],
            'event_subtype = :subtype',
            [':subtype' => self::EVENT_SUBTYPE]
        );

        return true;
    }
}
