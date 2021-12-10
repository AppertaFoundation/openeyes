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
 *
 */
namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "et_ophCiExamination_investigation_entry".
 *
 * The followings are the available columns in table 'et_ophCiExamination_investigation_entry':
 * @property integer $id
 * @property string $element_id
 * @property integer $investigation_code
 * @property string $comments
 * @property string $time
 * @property string $date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Element_OphCiExamination_Investigation $element
 * @property OphCiExamination_Investigation_Codes $investigationCode
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */
class OphCiExamination_Investigation_Entry extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_investigation_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, investigation_code, time, date', 'required'),
            array('element_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('investigation_code', 'numerical', 'integerOnly'=>true),
            array('comments', 'length', 'max'=>4096),
            array('last_modified_date, created_date,element_id, investigation_code, comments, time, date', 'safe'),
            array('date', 'CDateValidator', 'format' => array('d MMM yyyy','yyyy-mm-dd')),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, investigation_code, comments, time, date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'investigationCode' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes', 'investigation_code'),
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation', 'element_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Element',
            'investigation_code' => 'Investigation Code',
            'comments' => 'Comments',
            'time' => 'Time',
            'date' => 'Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('investigation_code', $this->investigation_code, true);
        $criteria->compare('comments', $this->comments, true);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('date', $this->date, true);
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
     * @return OphCiExamination_Investigation_Entry the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        $validator = new \OEDateValidator();
        if (!$validator->validateAttribute($this,'date')){
            $this->date = \Helper::convertNHS2MySQL($this->date);
        }

        return parent::beforeValidate();
    }

}
