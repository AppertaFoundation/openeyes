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


namespace OEModule\OphCiExamination\models;


/**
 * This is the model class for table "et_ophciexamination_systemicsurgery_op".
 *
 * The followings are the available columns in table 'et_ophciexamination_systemicsurgery_op':
 * @property integer $id
 * @property integer $element_id
 * @property string $side_id
 * @property string $operation
 * @property string $date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property SystemicSurgery $element
 * @property \User $lastModifiedUser
 * @property \Eye $side
 */
class SystemicSurgery_Operation extends \BaseEventTypeElement
{

    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_systemicsurgery_op';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['operation', 'required'],
            ['date, side_id, operation, had_operation', 'safe'],
            ['date', 'OEFuzzyDateValidatorNotFuture'],
            ['had_operation', 'required', 'message' => 'Checked Status cannot be blank'],
            ['side_id', 'sideValidator'],
            // The following rule is used by search().
            ['id, date, operation, had_operation', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'element' => [self::BELONGS_TO, 'SystemicSurgery', 'element_id'],
            'side' => [self::BELONGS_TO, 'Eye', 'side_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'element_id' => 'Element',
            'side_id' => 'Side',
            'operation' => 'Operation',
            'date' => 'Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    public function behaviors()
    {
        return ['OeDateFormat' => [
            'class' => 'application.behaviors.OeDateFormat',
            'date_columns' => [],
            'fuzzy_date_field' => 'date']];
    }

    /**
     * Checking whether side_id is not null
     * @param $attribute
     * @param $params
     */
    public function sideValidator($attribute, $params)
    {
        if (!$this->side_id) {
            $this->addError($attribute, "Eye must be selected");
        }
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function beforeSave()
    {
        //-9 is the N/A option but we do not save it, if null is posted that means
        //the user did not checked any checkbox so we return error in the validation part
        if ($this->side_id == -9) {
            $this->side_id = null;
        }
        return parent::beforeSave();
    }

    /**
     * @return mixed
     */
    public function getDisplayDate()
    {
        return \Helper::formatFuzzyDate($this->date);
    }

    public function getDisplayHadOperation()
    {
        if ($this->had_operation === (string) static::$PRESENT) {
            return 'Present';
        } elseif ($this->had_operation === (string) static::$NOT_PRESENT) {
            return 'Not present';
        }
        return 'Not checked';
    }

    /**
     * @return string
     */
    public function getDisplayOperation($present_prefix = true)
    {
        $display_had_operation = $present_prefix ? ('<strong>' . $this->getDisplayHadOperation() . ':</strong> ') : '';
        return  $display_had_operation . ($this->side ? $this->side->adjective  . ' ' : '') . $this->operation;
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
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('side_id', $this->side_id, true);
        $criteria->compare('operation', $this->operation, true);
        $criteria->compare('date', $this->date, true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemicSurgery_Operation the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
