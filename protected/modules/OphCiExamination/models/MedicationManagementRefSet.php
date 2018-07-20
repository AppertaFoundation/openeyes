<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class MedicationManagementTag
 * @package OEModule\OphCiExamination\models
 *
 * @property \RefSet $ref_set
 * @property int $ref_set_id
 * @property int $id
 */

class MedicationManagementRefSet extends \BaseActiveRecordVersioned
{
    public function tableName()
    {
        return 'ophciexamination_medication_management_ref_set';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, ref_set_id', 'safe'),
            array('ref_set_id', 'required'),
        );
    }

    public function relations()
    {
        return array(
            'ref_set' => array(self::BELONGS_TO, \RefSet::class, 'ref_set_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'ref_set_id' => 'Ref Set'
        );
    }
}