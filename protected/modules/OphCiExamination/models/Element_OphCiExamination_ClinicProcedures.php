<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\traits\CustomOrdering;

/**
 * This is the model class for table "et_ophciexamination_intraocularpressure".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 */
class Element_OphCiExamination_ClinicProcedures extends \BaseEventTypeElement
{
    use CustomOrdering;

    public static function model($class_name = null)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'et_ophciexamination_clinic_procedures';
    }

    public function rules()
    {
        return [
            ['event_id', 'safe']
        ];
    }

    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'entries' => [self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedures_Entry', 'element_id']
        ];
    }
}
