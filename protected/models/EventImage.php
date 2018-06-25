<?php

/**
 * This is the model class for table "event_image".
 *
 * The followings are the available columns in table 'event_image':
 * @property integer $id
 * @property string $event_id
 * @property string $image_data
 * @property integer $status_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property EventImageStatus $status
 * @property User $createdUser
 * @property Event $event
 * @property User $lastModifiedUser
 */
class EventImage extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, status_id', 'required'),
            array('status_id', 'numerical', 'integerOnly' => true),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('image_data, last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'status' => array(self::BELONGS_TO, 'EventImageStatus', 'status_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventImage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * Get the latest event that has not had an image generated for it yet.
     * Events that have failed image generation are skipped.
     *
     * @return Event|null
     */
    public function getNextEventToImage()
    {
        $event_id = Yii::app()->db->createCommand()
            ->select('event.id')
            ->from('event')
            ->leftJoin('event_image', 'event_image.event_id = event.id')
            ->leftJoin('event_image_status', 'event_image_status.id = event_image.status_id')
            ->where('deleted = 0 AND episode_id IS NOT NULL AND 
                (
                  event_image.id IS NULL OR 
                  (
                    event.last_modified_date > event_image.last_modified_date AND 
                    event_image_status.name IN ("NOT_CREATED", "GENERATED")
                  )
                )')
            ->order('event.last_modified_date DESC')
            ->queryScalar();

        if ($event_id === null) {
            return null;
        }

        return Event::model()->findByPk($event_id);
    }
}
