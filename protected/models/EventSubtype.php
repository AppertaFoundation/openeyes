<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

 /**
  * @property string $event_subtype - string identifier (Note, different from usual model pattern)
  * @property string $display_name
  * @property ElementType[] $element_types
  */
class EventSubtype extends BaseActiveRecordVersioned
{
    use HasFactory;

    protected $auto_update_relations = true;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_subtype';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // Only define rules for those attributes with user inputs.
        return [
            ['event_subtype, dicom_modality_code, icon_name, display_name, element_type_entries', 'safe'],
            // Remove attributes that should not be searched.
            ['event_subtype, dicom_modality_code, icon_name, display_name', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'element_type_entries' => [self::HAS_MANY, 'EventSubtypeElementEntry', 'event_subtype', 'order' => 'display_order ASC'],
            'element_types' => [self::MANY_MANY, 'ElementType', 'event_subtype_element_entries(event_subtype, element_type_id)', 'order' => 'element_types_element_types.display_order ASC'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'event_subtype' => 'Event Subtype',
            'dicom_modality_code' => 'Dicom Modality Code',
            'icon_name' => 'Icon Name',
            'display_name' => 'Display Name',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('subtype_mnemonic', $this->subtype_mnemonic, true);
        $criteria->compare('dicom_modality_code', $this->dicom_modality_code, true);
        $criteria->compare('icon_name', $this->icon_name, true);
        $criteria->compare('display_name', $this->display_name, true);

        return new CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }

    public function getEventIcon($type = 'small')
    {
        // 'medium' PNGs are the equivalent size as 'large' SVG backgrounds
        if ($type === 'medium') {
            $type = 'large';
        }

        return "<i class=\"oe-i-e {$type} {$this->icon_name} \" ></i > ";
    }

    public function getElementTypes()
    {
        return $this->element_types;
    }
}
