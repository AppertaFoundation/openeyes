<?php
/**
 * This is the model class for table "eur_event_result".
 *
 * The followings are the available columns in table 'eur_event_result':
 * @property integer $id
 * @property integer $result
 * @property integer $event_id
 * @property integer $eye_num
 * @property integer $eye_side
 *
 * The followings are the available model relations:
 * @property EURAnswerResult[] $eurAnswerResults
 */
class EUREventResults extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'eur_event_results';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_num, result', 'required'),
            array('eye_side', 'safe'),
            // The following rule is used by search().
            array('id, result, eye_num, event_id, eye_side', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eurAnswerResults' => array(self::HAS_MANY, 'EURAnswerResults', 'res_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'result' => 'Result',
            'eye_num' => 'Eye Number',
            'eye_side' => 'Eye Side',
            'event_id' => 'Event',
        );
    }

    public function setDefaultOptions(Patient $patient = null)
    {
        $eurAnswers = array();
        $rows = EURQuestions::model()->findAll();

        foreach ($rows as $row) {
            $new_answer_result = new EURAnswerResult();
            $new_answer_result->question_id = $row->id;
            $eurAnswers[] = $new_answer_result;
        }
        $this->eurAnswerResults = $eurAnswers;
        parent::setDefaultOptions($patient);
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
        $criteria->compare('result', $this->result, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_num', $this->eye_num, true);
        $criteria->compare('eye_side', $this->eye_side, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EUREventResults the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
