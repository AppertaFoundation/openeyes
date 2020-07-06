<?php

/**
 * This is the model class for table "ophcitheatreadmission_event_elementset_assignment_version".
 *
 * The followings are the available columns in table 'ophcitheatreadmission_event_elementset_assignment_version':
 * @property integer $id
 * @property integer $step_id
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
 * @property OphCiTheatreadmission_ElementSet $step
 */
class OphCiTheatreadmission_Event_ElementSet_Assignment extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcitheatreadmission_event_elementset_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('step_id', 'numerical', 'integerOnly'=>true),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, step_id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date, version_date, version_id', 'safe', 'on'=>'search'),
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
            'step' => array(self::BELONGS_TO, 'OphCiTheatreadmission_ElementSet', 'step_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'step_id' => 'Step',
            'event_id' => 'Event',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'version_date' => 'Version Date',
            'version_id' => 'Version',
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
        $criteria->compare('step_id', $this->step_id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('version_date', $this->version_date, true);
        $criteria->compare('version_id', $this->version_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphCiTheatreadmission_Event_ElementSet_Assignment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
