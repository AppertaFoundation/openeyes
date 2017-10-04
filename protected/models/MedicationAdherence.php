<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "medication".
 *
 * The followings are the available columns in table 'medication':
 *
 * @deprecated since v2.0
 * @property int $id
 * @property int $adherence_level_id
 * @property int $patient_id
 * @property int $medication_adherence_level_id
 */
class MedicationAdherence extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'archive_medication_adherence';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('patient_id, medication_adherence_level_id, comments', 'safe'),
            array('patient_id, medication_adherence_level_id', 'required'),
            array('comments', 'default', 'setOnEmpty' => true, 'value' => null),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'id'),
            'level' => array(self::BELONGS_TO, 'MedicationAdherenceLevel', 'medication_adherence_level_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'medication_adherence_level_id' => 'Adherence Level',
            'comments' => 'Comments',
        );
    }
}
