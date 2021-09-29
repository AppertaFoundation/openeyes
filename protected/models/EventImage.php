<?php

/**
 * This is the model class for table "event_image".
 *
 * The followings are the available columns in table 'event_image':
 * @property integer $id
 * @property string $event_id
 * @property string $image_data
 * @property integer $status_id
 * @property integer $eye_id
 * @property integer $page
 * @property string $message
 * @property int $last_modified_user_id
 * @property string $last_modified_date
 * @property int $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property EventImageStatus $status
 * @property Eye $eye
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
            array('status_id, page, eye_id', 'numerical', 'integerOnly' => true),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('image_data, message, last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'status' => array(self::BELONGS_TO, 'EventImageStatus', 'status_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'attachmentData' => [self::BELONGS_TO, 'AttachmentData', 'attachment_data_id'],
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
     * Get the latest events that has not had an image generated for it yet.
     * Events that have failed image generation are skipped.
     * Events that don't have their module loaded are also skipped.
     *
     * @param integer $event_count The number of events to find
     *
     * @return Event[]
     */
    public function getNextEventsToImage($event_count = 1, $debug = null)
    {
        $cmd = Yii::app()->db->createCommand()
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
            ->order('event.last_modified_date DESC');

        if ($event_count!==INF) {
            $cmd = $cmd->limit($event_count);
        }
            $event_ids  = $cmd->queryColumn();


        /* @var Event[] $events */
        $events = Event::model()->findAllByPk($event_ids);
        if ($debug) {
            echo "\n  Found " . count($events) ." events without images";
        }
        // restrict to only include events from modules that are loaded
        return array_filter($events, function ($event) {
            /* @var Event $event */
            return Yii::app()->getModule($event->eventType->class_name);
        });
    }

    public function getImageUrl()
    {
        $options = array('id' => $this->event_id);
        if ($this->eye_id !== null) {
            $options['eye'] = $this->eye_id;
        }

        if ($this->page !== null) {
            $options['page'] = $this->page;
        }

        $options['document_number'] = $this->document_number;
        $options['last_modified'] = $this->last_modified_date;
        return Yii::app()->createUrl('eventImage/view', $options);
    }
}
