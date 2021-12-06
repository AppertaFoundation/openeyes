<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/**
 * This is the model class for table "ophtrconsent_sup_consent_question_answer".
 *
 * The followings are the available columns in table 'ophtrconsent_sup_consent_question_answer':
 *
 * @property int    $id
 * @property int    $question_assignment_id
 * @property int    $display_order
 * @property string $name
 * @property string $display
 * @property string $answer_output
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User                             $createdUser
 * @property User                             $lastModifiedUser
 * @property OphtrconsentSupplementaryconsent $question
 */
class Ophtrconsent_SupplementaryConsentQuestionAnswer extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_sup_consent_question_answer';
    }

    /**
     * @return array validation rules for model attributes
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['question_assignment_id', 'required'],
            ['name, display', 'length', 'max' => 250],
            ['answer_output', 'length', 'max' => 500],
            ['display_order, name, display, answer_output', 'safe'],
            // The following rule is used by search().
            ['id, question_assignment_id, name, display, answer_output, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'questionAssignment' => [self::BELONGS_TO, 'Ophtrconsent_SupplementaryConsentQuestionAssignment', 'question_assignment_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_assignment_id' => 'Question',
            'name' => 'Answer name',
            'display' => 'Answer Text',
            'answer_output' => 'Answer printed text  [Optional]',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
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
     *                             based on the search/filter conditions
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('question_assignment_id', $this->question_assignment_id);
        $criteria->compare('name', $this->name);
        $criteria->compare('display', $this->display, true);
        $criteria->compare('answer_output', $this->answer_output, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name
     *
     * @return SupplyQuestionOptions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
