<?php

use OE\factories\models\traits\HasFactory;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @property string $name
 */
class OphDrPrescription_DispenseCondition extends BaseActiveRecordVersioned
{
    use HasFactory;
    use MappedReferenceData;
    use HasFactory;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'dispense_condition_id';
    }

    protected function mappingModelName(int $level): string
    {
        return 'OphDrPrescription_DispenseCondition_Institution';
    }

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrprescription_dispense_condition';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order', 'required'),
            array('display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('created_user_id', 'length', 'max' => 10),
            array('created_date, name, display_order, created_user_id, last_modified_user_id, last_modified_date, dispense_condition_institutions', 'safe'),
            array('id, caption', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'dispense_condition_institutions' => [self::HAS_MANY, 'OphDrPrescription_DispenseCondition_Institution', 'dispense_condition_id'],
            'institutions' => [self::MANY_MANY, 'Institution', 'ophdrprescription_dispense_condition_institution(dispense_condition_id, institution_id)']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'display_order' => 'Display Order',
            'created_date' => 'Created Date',
            'created_user_id' => 'Created By',
        );
    }

    public function getDisplayName()
    {

        $replace['{form_type}'] = SettingMetadata::model()->getSetting('prescription_form_format');
        $name = $this->name;

        foreach ($replace as $from => $to) {
            $name = str_replace($from, $to, $name);
        }

        return $name;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return ['order' => 'display_order'];
    }

    public function getLocationsForCurrentInstitution()
    {
        $locations = array();
        $dc_institution = OphDrPrescription_DispenseCondition_Institution::model()->findByAttributes(
            [
                'institution_id' => Yii::app()->session['selected_institution_id'],
                'dispense_condition_id' => $this->id
            ]
        );

        if (isset($dc_institution->dispense_location_institutions)) {
            foreach ($dc_institution->dispense_location_institutions as $dl_institution) {
                $locations[] = $dl_institution->dispense_location;
            }
        }
        return $locations;
    }

    public function withSettings($overprint_setting, $fpten_dispense_condition_id)
    {
        $condition = array();
        if ($overprint_setting === 'off') {
            $condition = array(
                'condition' => "id != :fpten_id",
                'params' => array(
                    ':fpten_id' => $fpten_dispense_condition_id
                )
            );
        }
        $this->getDbCriteria()->mergeWith($condition);
        return $this;
    }
}
