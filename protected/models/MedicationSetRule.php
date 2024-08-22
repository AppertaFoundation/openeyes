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
 * This is the model class for table "medication_set_rule".
 *
 * The followings are the available columns in table 'medication_set_rule':
 * @property integer $id
 * @property integer $medication_set_id
 * @property integer $subspecialty_id
 * @property integer $site_id
 * @property string $usage_code
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property MedicationSet $medicationSet
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Site $site
 * @property Subspecialty $subspecialty
 */
class MedicationSetRule extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_set_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['medication_set_id', 'required'],
            ['medication_set_id', 'numerical', 'integerOnly'=>true],
            ['medication_set_id', 'isNameAndRelatedRulesUnique'],
            ['subspecialty_id, site_id', 'numerical', 'integerOnly' => true, 'allowEmpty' => true],
            ['usage_code', 'length', 'max'=>255],
            ['last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['id, usage_code_id, deleted_date, last_modified_date, created_date', 'safe'],
            ['usage_code_id', 'required'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, medication_set_id, subspecialty_id, site_id, usage_code, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'site' => array(self::BELONGS_TO, Site::class, 'site_id'),
            'medicationSet' => array(self::BELONGS_TO, MedicationSet::class, 'medication_set_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'usageCode' => array(self::BELONGS_TO, 'MedicationUsageCode', 'usage_code_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'medication_set_id' => 'Medication Set',
            'subspecialty_id' => 'Subspecialty',
            'site_id' => 'Site',
            'usage_code' => 'Usage Code',
            'deleted_date' => 'Deleted Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'site.name' => 'Site'
        );
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
        $criteria->compare('subspecialty_id', $this->subspecialty_id);
        $criteria->compare('site_id', $this->site_id);
        $criteria->compare('usage_code', $this->usage_code, true);
        $criteria->compare('deleted_date', $this->deleted_date, true);
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
     * @return MedicationSetRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function isNameAndRelatedRulesUnique($attribute, $params)
    {
        $set_rules = self::model()->findAllByAttributes(
            [
                'subspecialty_id' => ($this->subspecialty_id ? $this->subspecialty_id : null),
                'site_id' => ($this->site_id ? $this->site_id : null),
                'usage_code_id' => ($this->usage_code_id ? $this->usage_code_id : null),
            ]
        );

        if ($set_rules) {
            foreach ($set_rules as $set_rule) {
                // Check that the rule hasn't already been set for this medication set and check ids incase it is an update
                if ($this->medicationSet->name === $set_rule->medicationSet->name && $this->id !== $set_rule->id) {
                    $this->addError($attribute, 'A medication set with the name '.$this->medicationSet->name.' already exists with these rules ('.($this->site ? $this->site->name.', ' : '').($this->subspecialty ? $this->subspecialty->name.', ' : '').($this->usageCode ? $this->usageCode->name.')' : ''));
                    break;
                }
            }
        }
    }
}
