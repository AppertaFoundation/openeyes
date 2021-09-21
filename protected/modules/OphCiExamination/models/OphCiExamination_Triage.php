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

/**
 * This is the model class for table "ophciexamination_triage".
 *
 * The followings are the available columns in table:
 *
 *
 */
class OphCiExamination_Triage extends \BaseActiveRecordVersioned
{
    public static function model($class_name = null)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'ophciexamination_triage';
    }

    public function rules()
    {
        return [
            ['time, treat_as_adult, priority_id, chief_complaint_id, eye_id', 'required'],
            ['element_id, time, treat_as_adult, site_id, priority_id, chief_complaint_id, eye_injury_id, eye_id, comments', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Triage', 'element_id'],
            'priority' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Triage_Priority', 'priority_id'],
            'chief_complaint' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Triage_ChiefComplaint', 'chief_complaint_id'],
            'eye_injury' => [self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Triage_EyeInjury', 'eye_injury_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
        ];
    }

    public function getChiefComplaint()
    {
        $chief_complaint_text = null;
        if ($this->chief_complaint) {
            $chief_complaint_text = $this->chief_complaint->description;
        }
        if ($this->eye_injury) {
            $chief_complaint_text = $chief_complaint_text . " - " . $this->eye_injury->description;
        }
        return $chief_complaint_text;
    }
}
