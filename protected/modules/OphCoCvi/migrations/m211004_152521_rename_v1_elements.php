<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m211004_152521_rename_v1_elements extends OEMigration
{
    private const RENAMED_ELEMENTS = [
        'OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo_V1',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics_V1',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1'
    ];

    public function safeUp()
    {
        foreach (self::RENAMED_ELEMENTS as $class) {
            $old_class = substr($class, 0, -3);

            $old_element_type_id = $this->getIdOfElementTypeByClassName($old_class);
            if ($old_element_type_id) {
                $this->delete('element_type',
                    'id = :id',
                    array(':id' => $old_element_type_id));
            }

            $element_type_id = $this->getIdOfElementTypeByClassName($class);
            if ($element_type_id) {
                $this->update('element_type', [
                    'class_name' => $old_class
                ], 'id = :id', array(':id' => $element_type_id));
            }
        }
    }


    public function safeDown()
    {
        foreach (self::RENAMED_ELEMENTS as $class) {
            $element_type_id = $this->getIdOfElementTypeByClassName($class);
            if ($element_type_id) {
                $this->update('element_type', [
                    'class_name' => $class
                ], 'id = :id', array(':id' => $element_type_id));
            }
        }
    }
}
