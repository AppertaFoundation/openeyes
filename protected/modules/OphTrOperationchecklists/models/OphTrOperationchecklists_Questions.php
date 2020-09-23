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
 * This is the model class for table "ophtroperationchecklists_questions".
 *
 * The followings are the available columns in table 'ophtroperationchecklists_questions':
 * @property string $id
 * @property int $element_type_id
 * @property string $question
 * @property integer $mandatory
 * @property integer $is_comment_field_required
 * @property bool $requires_answer
 * @property integer $display_order
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property OphTrOperationchecklists_QuestionAnswerAssignment[] $questionAnswerAssignments
 * @property OphTrOperationchecklists_QuestionSection $section
 * @property OphTrOperationchecklists_QuestionRelationships $parentQuestions
 * @property OphTrOperationchecklists_QuestionRelationships $subQuestion
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphTrOperationchecklists_Questions extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationchecklists_questions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question, mandatory, is_comment_field_required, display_order', 'required'),
            array('mandatory, is_comment_field_required, display_order', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, question, mandatory, is_comment_field_required, display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'questionAnswerAssignments' => array(self::HAS_MANY, 'OphTrOperationchecklists_QuestionAnswerAssignment', 'question_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'parentQuestions' => array(self::HAS_MANY, 'OphTrOperationchecklists_QuestionRelationships', 'parent_question_id'),
            'subQuestion' => array(self::HAS_ONE, 'OphTrOperationchecklists_QuestionRelationships', 'sub_question_id'),
            'section' => array(self::HAS_ONE, 'OphTrOperationchecklists_QuestionSection', 'question_id'),
            'elementType' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'question' => 'Question',
            'mandatory' => 'Mandatory',
            'is_comment_field_required' => 'Is Comment Field Required',
            'display_order' => 'Display Order',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('question', $this->question, true);
        $criteria->compare('mandatory', $this->mandatory);
        $criteria->compare('is_comment_field_required', $this->is_comment_field_required);
        $criteria->compare('display_order', $this->display_order);
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
     * @return OphTrOperationchecklists_Questions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     */
    public function isSubQuestion()
    {
        if ($this->subQuestion) {
            return true;
        }
        return false;
    }

    /**
     * This method returns all the question ids that are relevant to the anaesthetic type.
     */
    public static function getAnaestheticTypeQuestionRelationIds()
    {
        $ids = [];

        $criteria = new \CDbCriteria();
        $criteria->select = 't.id';
        $anaestheticTypeQuestions = ['Has the patient followed pre-operative fasting instructions?',
            'Arrangements made for responsible adult to accompany patient home and look after patient for 24 hours?',
            'Fasted'];
        $criteria->addInCondition('t.question', $anaestheticTypeQuestions);
        $anaestheticTypeQuestionRelationIds = self::model()->findAll($criteria);

        foreach ($anaestheticTypeQuestionRelationIds as $anaestheticTypeQuestionRelationId) {
            $ids[] = intval($anaestheticTypeQuestionRelationId->id);
            $subQuestions = self::model()->findByPk($anaestheticTypeQuestionRelationId->id)->parentQuestions;
            foreach ($subQuestions as $subQuestion) {
                $ids[] = intval($subQuestion->sub_question_id);
            }
        }
        return $ids;
    }

    public static function getDuplicateQuestionIds()
    {
        $ids = [];

        $duplicateQuestions = ['Last ate at' => 'Food', 'Last drank at' => 'Water', 'Food' => 'Food', 'Water' => 'Water'];

        foreach ($duplicateQuestions as $q1 => $q2) {
            $criteria = new \CDbCriteria();
            $criteria->select = 't.id';
            $criteria->addInCondition('t.question', array($q1, $q2));
            $questionIds = self::model()->findAll($criteria);
            if (sizeof($questionIds) == 2 ) {
                $ids[$questionIds[0]->id] = $questionIds[1]->id;
            } else {
                $ids[$questionIds[0]->id] = $questionIds[0]->id;
            }
        }
        return $ids;
    }

    public function getSavedResponses($element, $eventId, $nextStep)
    {
        $duplicateQuestionIds = self::getDuplicateQuestionIds();
        $return = [];
        // check which element this question belongs to and get the response.
        if ($nextStep->position === '2') {
            $results = Element_OphTrOperationchecklists_Admission::model()->find('event_id = :event_id', array(':event_id' => $eventId))->checklistResults;
            foreach ($results as $result) {
                if (array_key_exists($result->question_id, $duplicateQuestionIds)) {
                    $return[$duplicateQuestionIds[$result->question_id]] =  ['answer_id' => $result->answer_id, 'answer' => $result->answer];
                }
            }
        } elseif ($nextStep->position === '3' || $nextStep->position === '4') {
            $results = $element::model()->with(array(
                'checklistResults' => array(
                    'condition'=>'checklistResults.set_id=' . ($nextStep->position-1),
                )))->find('event_id = :event_id', array(':event_id' => $eventId))->checklistResults;
            foreach ($results as $result) {
                $return[$result->question_id] =  ['answer_id' => $result->answer_id, 'answer' => $result->answer];
            }
        }
        return $return;
    }
}
