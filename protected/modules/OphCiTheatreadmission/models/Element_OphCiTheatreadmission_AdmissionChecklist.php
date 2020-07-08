<?php

/**
 * This is the model class for table "et_ophcitheatreadmission_admission_checklist".
 *
 * The followings are the available columns in table 'et_ophcitheatreadmission_admission_checklist':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 * @property OphcitheatreadmissionAdmissionChecklistResults[] $checklistResults
 */
class Element_OphCiTheatreadmission_AdmissionChecklist extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophcitheatreadmission_admission_checklist';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('checklistResults, event_id, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'checklistResults' => array(self::HAS_MANY, 'OphcitheatreadmissionAdmissionChecklistResults', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    public function setDefaultOptions(Patient $patient = null)
    {

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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
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
     * @return Element_OphCiTheatreadmission_AdmissionChecklist the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Update the admission checklist results, observations and dilations (if exists) for this element
     *
     * @throws Exception
     */
    public function saveData()
    {
        foreach ($this->checklistResults as $result) {
            $result->element_id = $this->id;
            if (!$result->save()) {
                throw new Exception('Unable to save result');
            }
            if ($result->dilation) {
                $result->dilation->checklist_result_id = $result->id;
                if (!$result->dilation->save()) {
                    throw new Exception('Unable to save dilation: ' . print_r($this->dilation->getErrors(), true));
                }
                foreach ($result->dilation->treatments as $treatment) {
                    $treatment->dilation_id = $result->dilation->id;
                    if (!$treatment->save()) {
                        throw new Exception('Unable to save dilation treatment: ' . print_r($this->dilation->getErrors(), true));
                    }
                }
            }
            if ($result->observations) {
                $result->observations->checklist_result_id = $result->id;
                OphCiTheatreadmission_Observations::model()->deleteAllByAttributes(array('checklist_result_id' => $result->id));
                if (!$result->observations->save()) {
                    throw new Exception('Unable to save observations');
                }
            }
        }
    }
}