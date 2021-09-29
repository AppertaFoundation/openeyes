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

class m210801_134940_remove_signature_elements extends OEMigration
{
    private const RETIRED_ELEMENTS = [
        'et_ophcocvi_patient_signature' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature',
        'et_ophcocvi_consultant_signature' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsultantSignature'
    ];

    public function safeUp()
    {
        foreach (self::RETIRED_ELEMENTS as $class) {
            $element_type_id = $this->getIdOfElementTypeByClassName($class);
            if ($element_type_id) {
                $this->delete('signature_request', 'element_type_id = ?', [$element_type_id]);
                $this->deleteElementType('OphCoCvi', $class);
            }
        }
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphCoCvi");
        foreach (self::RETIRED_ELEMENTS as $table => $class) {
            $this->createElementType($event_type_id, $class, [
                'display_order' => 20,
                'required' => 1,
            ]);
        }
    }
}
