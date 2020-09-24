<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ophtroperationchecklists_documentation_results".
 *
 * The followings are the available columns in table 'ophtroperationchecklists_documentation_results':
 * @property integer $id
 * @property integer $element_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property string $comment
 * @property int $set_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property OphTrOperationchecklists_Answers $checklistAnswers
 * @property Element_OphTrOperationchecklists_Documentation $element
 * @property OphTrOperationchecklists_Questions $question
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphTrOperationchecklists_DocumentationResults extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationchecklists_documentation_results';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, question_id, answer_id', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('comment, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, question_id, answer_id, comment, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'checklistAnswers' => array(self::BELONGS_TO, 'OphTrOperationchecklists_Answers', 'answer_id'),
            'element' => array(self::BELONGS_TO, 'Element_OphTrOperationchecklists_Documentation', 'element_id'),
            'question' => array(self::BELONGS_TO, 'OphTrOperationchecklists_Questions', 'question_id'),
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
            'question_id' => 'Question',
            'answer_id' => 'Answer',
            'comment' => 'Comment',
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
        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('answer_id', $this->answer_id);
        $criteria->compare('comment', $this->comment, true);
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
     * @return OphTrOperationchecklists_DocumentationResults the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        // If answer and comment are empty then set them to null before saving to the db.
        if ($this->comment === '') {
            $this->comment = null;
        }

        if ($this->answer === '') {
            $this->answer = null;
        }

        return parent::beforeSave();
    }
}
