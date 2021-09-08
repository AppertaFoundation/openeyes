<?php

/**
 * This is the model class for table "ophcocvi_delivery_log".
 *
 * The followings are the available columns in table 'ophcocvi_delivery_log':
 * @property integer $id
 * @property string $event_id
 * @property integer $ophcocvi_consent_consignee_id
 * @property string $attempted_at
 * @property string $status
 * @property string $error_report
 *
 * The followings are the available model relations:
 * @property \OEModule\OphCoCvi\models\OphCoCvi_ConsentConsignee $ophCoCvi_ConsentConsignee
 * @property Event $event
 */
class OphCoCvi_DeliveryLog extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcocvi_delivery_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, ophcocvi_consent_consignee_id', 'required'),
            array('ophcocvi_consent_consignee_id', 'numerical', 'integerOnly'=>true),
            array('event_id', 'length', 'max'=>10),
            array('status', 'length', 'max'=>16),
            array('attempted_at, error_report', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, ophcocvi_consent_consignee_id, attempted_at, status, error_report', 'safe', 'on'=>'search'),
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
            'ophCoCvi_ConsentConsignee' => array(self::BELONGS_TO, \OEModule\OphCoCvi\models\OphCoCvi_ConsentConsignee::class, 'ophcocvi_consent_consignee_id'),
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
            'ophcocvi_consent_consignee_id' => 'Ophcocvi Consent Consignee',
            'attempted_at' => 'Attempted At',
            'status' => 'Status',
            'error_report' => 'Error Report',
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
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('ophcocvi_consent_consignee_id', $this->ophcocvi_consent_consignee_id);
        $criteria->compare('attempted_at', $this->attempted_at, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('error_report', $this->error_report, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphCoCvi_DeliveryLog the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
