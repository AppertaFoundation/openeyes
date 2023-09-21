<?php

/**
 * This is the model class for table "event_image_status".
 *
 * The followings are the available columns in table 'event_image_status':
 * @property integer $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property EventImage[] $eventImages
 */
class EventImageStatus extends BaseActiveRecordVersioned
{
    public const STATUS_NOT_CREATED = "NOT_CREATED";
    public const STATUS_CREATED = "CREATED";
    public const STATUS_FAILED = "FAILED";
    public const STATUS_GENERATING = "GENERATING";

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_image_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'length', 'max' => 50),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'eventImages' => array(self::HAS_MANY, 'EventImage', 'status_id'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventImageStatus the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
