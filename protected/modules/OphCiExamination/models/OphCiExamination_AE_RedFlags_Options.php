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
class OphCiExamination_AE_RedFlags_Options extends \BaseActiveRecordVersioned
{
    use \MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return \ReferenceData::LEVEL_INSTITUTION | \ReferenceData::LEVEL_SITE | \ReferenceData::LEVEL_SPECIALTY | \ReferenceData::LEVEL_SUBSPECIALTY | \ReferenceData::LEVEL_FIRM;
    }

    protected function mappingColumn(int $level): string
    {
        return $this->tableName().'_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return BaseActiveRecord the static model class
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
        return 'ophciexamination_ae_red_flags_option';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('id, name, active', 'safe'),
                array('name, active', 'required'),
                array('id, name, active', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),

            'institutions' => array(self::MANY_MANY, 'Institution', $this->tableName().'_institution('.$this->tableName().'_id, institution_id)'),
            'sites' => array(self::MANY_MANY, 'Site', $this->tableName().'_site('.$this->tableName().'_id, site_id)'),
            'subspecialtys' => array(self::MANY_MANY, 'Subspecialty', $this->tableName().'_subspecialty('.$this->tableName().'_id, subspecialty_id)'),
            'specialtys' => array(self::MANY_MANY, 'specialty', $this->tableName().'_specialty('.$this->tableName().'_id, specialty_id)'),
            'firms' => array(self::MANY_MANY, 'firm', $this->tableName().'_firm('.$this->tableName().'_id, firm_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
                'name' => 'Option name',
                'active' => 'Current and valid option',
        );
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

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->event_id, true);

        return new \CActiveDataProvider($this, array(
                'criteria' => $criteria,
        ));
    }
}
