<?php

namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "et_ophciexamination_cvi_status".
 *
 * The followings are the available columns in table 'et_ophciexamination_cvi_status':
 * @property integer $id
 * @property string $event_id
 * @property string $cvi_status_id
 * @property string $element_date
 * @property string $created_date
 * @property string $created_user_id
 * @property string $last_modified_date
 * @property string $last_modified_user_id
 *
 * The followings are the available model relations:
 * @property PatientOphInfoCviStatus $cviStatus
 * @property Event $event
 */
class Element_OphCiExamination_CVI_Status extends \BaseEventTypeElement
{

    public static $BLIND_STATUS = 'Severely Sight Impaired';
    public static $NOT_BLIND_STATUS = 'Sight Impaired';
    public static $NOT_ELIGIBLE_STATUS = 'Not eligible';
    public static $UNKNOWN_STATUS = 'Unknown';
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_cvi_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('cvi_status_id element_date', 'required'),
            array('event_id, cvi_status_id, created_user_id, last_modified_user_id', 'length', 'max'=>10),
            array('event_id, element_date, created_date, last_modified_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, cvi_status_id, element_date, created_date, created_user_id, last_modified_date, last_modified_user_id', 'safe', 'on'=>'search'),
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
            'cviStatus' => array(self::BELONGS_TO, 'PatientOphInfoCviStatus', 'cvi_status_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
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
            'cvi_status_id' => 'Cvi Status',
            'element_date' => 'Element Date',
            'created_date' => 'Created Date',
            'created_user_id' => 'Created User',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_user_id' => 'Last Modified User',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('event_id',$this->event_id,true);
        $criteria->compare('cvi_status_id',$this->cvi_status_id,true);
        $criteria->compare('element_date',$this->element_date,true);
        $criteria->compare('created_date',$this->created_date,true);
        $criteria->compare('created_user_id',$this->created_user_id,true);
        $criteria->compare('last_modified_date',$this->last_modified_date,true);
        $criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphCiExamination_CVI_Status the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}