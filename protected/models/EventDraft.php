<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "event_draft".
 *
 * The followings are the available columns in table 'event_draft':
 * @property integer $id
 * @property bool $is_auto_save
 * @property string $institution_id
 * @property string $site_id
 * @property string $episode_id
 * @property string $event_type_id
 * @property string $event_id
 * @property string $originating_url
 * @property string $event_action
 * @property string $data
 *
 * The followings are the available model relations:
 * @property Institution $institution
 * @property Site $site
 * @property Episode $episode
 * @property EventType $eventType
 * @property Event $event
 * @property User $last_updated_user
 */
class EventDraft extends BaseActiveRecord
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_draft';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['is_auto_save, institution_id, episode_id, event_type_id, data', 'required'],
            //Using the maximum size of the JSON data type in mysql
            //Magic number is used to avoid querying the db for the actual size
            ['data', 'length', 'max'=>4294967295],
            ['event_action, data', 'safe'],
            ['id, episode_id, event_type_id, event_id, data', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'institution' => [self::BELONGS_TO, Institution::class, 'institution_id'],
            'site' => [self::BELONGS_TO, Site::class, 'site_id'],
            'episode' => [self::BELONGS_TO, Episode::class, 'episode_id'],
            'eventType' => [self::BELONGS_TO, EventType::class, 'event_type_id'],
            'event' => [self::BELONGS_TO, Event::class, 'event_id'],
            'last_updated_user' => [self::BELONGS_TO, User::class, 'last_modified_user_id']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_auto_save' => 'Is Auto Save',
            'event_id' => 'Event',
            'data' => 'Data',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('is_auto_save', $this->is_auto_save, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('data', $this->data, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * removeDraftForEvent
     *
     * If an event is being updated, if there is a draft for the update it will be associated with the event.
     * If an event is being created, if there is a draft for the creation it will not be associated because
     * the event was created after the draft, so find it based on specific attributes from the event instead.
     *
     * Setting $only_remove_associated_draft to true prevents the deletion of drafts that are not associated with any event,
     * which matters when updating an event before a draft is created (e.g. saving before autosave is invoked)
     * or soft deleting an event without a draft. For create, this should be false.
     *
     * @param Event $event
     * @param bool $only_remove_associated_draft
     */
    public static function removeDraftForEvent(Event $event, bool $only_remove_associated_draft)
    {
        if ($event->draft) {
            $event->draft->delete();
        } elseif (!$only_remove_associated_draft) {
            EventDraft::model()->deleteAll(
                [
                    'join' => 'JOIN episode ON episode.id = event_draft.episode_id',
                    'condition' => 'event_id IS NULL AND episode.patient_id = :patient_id AND event_type_id = :event_type_id AND event_draft.last_modified_user_id = :last_modified_user_id',
                    'params' => [
                        ':patient_id' => $event->episode->patient_id,
                        ':event_type_id' => $event->event_type_id,
                        ':last_modified_user_id' => $event->last_modified_user_id
                    ]
                ]
            );
        }
    }

    public function getEventIcon(string $type = 'small')
    {
        if ($this->eventType) {
            return $this->eventType->getEventIcon($type, null);
        } else {
            return '';
        }
    }

    public function getEventName()
    {
        return $this->eventType ? $this->eventType->name : 'Event';
    }
}
