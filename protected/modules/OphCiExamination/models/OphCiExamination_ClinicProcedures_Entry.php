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

use services\DateTime;

/**
 * This is the model class for table "ophciexamination_clinic_procedures_entry".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $element_id
 * @property int $procedure_id
 * @property int $eye_id
 * @property string $outcome_time
 * @property DateTime $date
 * @property string $comments
 * @property int $subspecialty_id
 */
class OphCiExamination_ClinicProcedures_Entry extends \BaseActiveRecordVersioned
{
    public static function model($class_name = null)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'ophciexamination_clinic_procedures_entry';
    }

    public function rules()
    {
        return [
            ['eye_id, outcome_time, date', 'required'],
            ['element_id, procedure_id, eye_id, outcome_time, date, comments, subspecialty_id, created_user_id, last_modified_user_id', 'safe']
        ];
    }

    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, 'Element_OphCiExamination_ClinicProcedures', 'element_id'],
            'procedure' => [self::BELONGS_TO, 'Procedure', 'procedure_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'subspecialty' => [self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }
}
