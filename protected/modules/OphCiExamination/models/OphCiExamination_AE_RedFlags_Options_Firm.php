<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_ae_red_flags".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $name
 * @property bool $active
 *
 * The followings are the available model relations:
 */
class OphCiExamination_AE_RedFlags_Options_Firm extends \BaseActiveRecordVersioned
{
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'ophciexamination_ae_red_flags_option_firm';
    }

    public function rules()
    {
        return [
            ['id, red_flag_id, firm_id', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'red_flag_id' => [self::BELONGS_TO, 'OphCiExamination_AE_RedFlags_Options', 'red_flag_id'],
            'firm' => [self::BELONGS_TO, 'Firm', 'firm_id'],
        ];
    }
}
