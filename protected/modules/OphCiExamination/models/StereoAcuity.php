<?php

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\StereoAcuity as StereoAcuityWidget;

class StereoAcuity extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasChildrenWithEventScopeValidation;

    protected $widgetClass = StereoAcuityWidget::class;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected const EVENT_SCOPED_CHILDREN = [
        'entries' => 'with_head_posture'
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_stereoacuity';
    }

    public function rules()
    {
        return [
            ['event_id, entries', 'safe'],
            ['entries', 'required']
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, StereoAcuity_Entry::class, 'element_id']
        ];
    }

    public function getLetter_string()
    {
        return "Stereo Acuity: " . ( count($this->entries) > 0 ? implode(", ", $this->entries): "No entries" );
    }
}
