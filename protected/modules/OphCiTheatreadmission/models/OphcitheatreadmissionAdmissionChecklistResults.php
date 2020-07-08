<?php

/**
 * This is the model class for table "ophcitheatreadmission_admission_checklist_results".
 *
 * The followings are the available columns in table 'ophcitheatreadmission_admission_checklist_results':
 * @property integer $id
 * @property integer $element_id
 * @property string $question_id
 * @property string $answer_id
 * @property string $answer
 * @property string $comment
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property OphcitheatreadmissionChecklistAnswers $checklistAnswers
 * @property Element_OphCiTheatreadmission_AdmissionChecklist $element
 * @property OphcitheatreadmissionChecklistQuestions $question
 * @property OphCiTheatreadmission_Dilation $dilation
 * @property OphCiTheatreadmission_Observations $observations
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphcitheatreadmissionAdmissionChecklistResults extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphcitheatreadmissionAdmissionChecklistResults the static model class
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
        return 'ophcitheatreadmission_admission_checklist_results';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id', 'required'),
            array('element_id', 'numerical', 'integerOnly' => true),
            array('question_id, answer_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array(
                'element_id, question_id, answer_id, answer, comment, dilation, observations, last_modified_date, created_date',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, element_id, question_id, answer_id, answer, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search'
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
            'question' => array(self::BELONGS_TO, 'OphcitheatreadmissionChecklistQuestions', 'question_id'),
            'checklistAnswers' => array(self::BELONGS_TO, 'OphcitheatreadmissionChecklistAnswers', 'answer_id'),
            'element' => array(self::BELONGS_TO, 'Element_OphCiTheatreadmission_AdmissionChecklist', 'element_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'dilation' => array(self::HAS_ONE, 'OphCiTheatreadmission_Dilation', 'checklist_result_id'),
            'observations' => array(self::HAS_ONE, 'OphCiTheatreadmission_Observations', 'checklist_result_id'),
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
            'answer' => 'Answer',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('question_id', $this->question_id, true);
        $criteria->compare('answer_id', $this->answer_id, true);
        $criteria->compare('answer', $this->answer, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the relation name for a given question section
     */
    public function getRelationForSection($section_name) {
        $relations = $this->relations();
        foreach ($relations as $rel => $data) {
            if ($data[1] === CHtml::modelName($section_name)) {
                return $rel;
            }
        }
        return null;
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