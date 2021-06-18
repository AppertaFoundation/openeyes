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

/**
 * This is the model class for table "medication_set_auto_rule_medication".
 *
 * The followings are the available columns in table 'medication_set_auto_rule_medication':
 * @property integer $id
 * @property integer $medication_id
 * @property integer $medication_set_id
 * @property integer $include_parent
 * @property integer $include_children
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationSet $medicationSet
 * @property Medication $medication
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class MedicationSetAutoRuleMedication extends BaseActiveRecordVersioned
{
    private $delete_with_tapers = false;
    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set_auto_rule_medication';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('medication_set_id, medication_id', 'required'),
            array('medication_set_id, medication_id, include_parent, include_children, default_dose', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('include_parent, include_children, default_route_id, default_frequency_id, default_duration_id, default_dose_unit_term, default_dispense_condition_id, default_dispense_location_id, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('tapers', 'safe'), // auto update relation
            array(
                'id, medication_set_id, medication_id, include_parent,
                include_children, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on'=>'search'
            ),
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
            'medication' => array(self::BELONGS_TO, Medication::class, 'medication_id'),
            'tapers' => array(self::HAS_MANY, 'MedicationSetAutoRuleMedicationTaper', 'medication_set_auto_rule_id'),
            'medicationSet' => array(self::BELONGS_TO, MedicationSet::class, 'medication_set_id'),
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'defaultDuration' => array(self::BELONGS_TO, MedicationDuration::class, 'default_duration_id'),
            'defaultDispenseCondition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'default_dispense_condition_id'),
            'defaultDispenseLocation' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'default_dispense_location_id'),
            'defaultFrequency' => array(self::BELONGS_TO, 'MedicationFrequency', 'default_frequency_id'),
            'defaultForm' => array(self::BELONGS_TO, 'MedicationForm', 'default_form_id'),
            'defaultRoute' => array(self::BELONGS_TO, 'MedicationRoute', 'default_route_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_set' => 'Medication Set',
            'medication_id' => 'Medication',
            'include_parent' => 'Include Parent',
            'include_children' => 'Include Children',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    public function deleteWithTapers()
    {
        $this->delete_with_tapers = true;
        return $this;
    }

    public function beforeDelete()
    {
        if ($this->delete_with_tapers === true) {
            MedicationSetAutoRuleMedicationTaper::model()->deleteAllByAttributes(['medication_set_auto_rule_id' => $this->id]);
        }

        return parent::beforeDelete();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('medication_set_id', $this->medication_set_id);
        $criteria->compare('medication_id', $this->medication_id);
        $criteria->compare('include_parent', $this->include_parent);
        $criteria->compare('include_children', $this->include_children);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationSetAutoRuleMedication the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
