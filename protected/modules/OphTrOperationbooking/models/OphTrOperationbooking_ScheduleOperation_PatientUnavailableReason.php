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

/**
 * This is the model class for table "ophtroperationbooking_scheduleope_patientunavailreason".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 * @property int $display_order
 *
 * The followings are the available model relations:
 */
class OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'patientunavailreason_id';
    }

    /*protected function softDeleteMappings(): bool
    {
        return true;
    }*/

    protected function mappingModelName(int $level): string
    {
        if ($level === ReferenceData::LEVEL_INSTITUTION) {
            return 'PatientUnavailableReason_Institution';
        }

        return '';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason|BaseActiveRecord the static model class
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
        return 'ophtroperationbooking_scheduleope_patientunavailreason';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * set a default display order for a new record.
     */
    protected function afterConstruct()
    {
        parent::afterConstruct();
        if (!$this->display_order) {
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order desc';
            $criteria->limit = 1;
            $model = get_class($this) . '::model';
            $bottom = $model()->find($criteria);
            if ($bottom) {
                $this->display_order = $bottom->display_order + 1;
            } else {
                $this->display_order = 1;
            }
        }
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('name, display_order', 'safe'),
                array('name, display_order', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
                array('id, name', 'safe', 'on' => 'search'),
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
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophtroperationbooking_patientunavailreason_institution(patientunavailreason_id, institution_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
