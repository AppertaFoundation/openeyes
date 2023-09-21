<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "medication_usage_code".
 *
 * The followings are the available columns in table 'medication_usage_code':
 *
 * @property int $id
 * @property string $usage_code usage code
 * @property string $name name of the usage code
 * @property string $active if the usage_code is active
 * @property string $hidden if the option is hidden (for legacy codes maybe)
 * @property string $address1
 * @property string $display_order display order
 *
 * The following are the available model relations:
 * @property MedicationSetRule $medicationSetRule
 */
class MedicationUsageCode extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @inheritDoc
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_usage_code';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['usage_code, name', 'required'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'medicationSetRule' => [self::HAS_MANY, MedicationSetRule::class, 'usage_code_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usage_code' => 'Usage Code',
            'name' => 'Name',
            'active' => 'Active',
            'hidden' => 'Hidden',
            'display_order' => 'Display order'
        ];
    }
}
