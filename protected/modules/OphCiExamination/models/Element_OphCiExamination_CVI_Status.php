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
    use traits\CustomOrdering;

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
            array('element_date', 'OEFuzzyDateValidatorNotFuture'),
            array('cvi_status_id', 'required'),
            array('event_id, cvi_status_id, created_user_id, last_modified_user_id', 'length', 'max'=>10),
            array('event_id, element_date, created_date, last_modified_date', 'safe'),
            array('element_date', 'default', 'setOnEmpty' => true, 'value' => null),
            // The following rule is used by search().
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

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => [],
                'fuzzy_date_field' => 'element_date',
            ),
        );
    }

}
