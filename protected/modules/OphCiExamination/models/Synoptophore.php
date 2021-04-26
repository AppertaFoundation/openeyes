<?php

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\widgets\Synoptophore as SynoptophoreWidget;

/**
 * Class Synoptophore
 * @package OEModule\OphCiExamination\models
 * @property string $comments
 * @property string $eye_id
 * @property integer $angle_from_primary
 * @property Synoptophore_ReadingForGaze $readings
 */
class Synoptophore extends \BaseEventTypeElement implements SidedData
{
    use traits\CustomOrdering;
    use traits\HasSidedData;
    use traits\HasRelationOptions;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $relation_defaults = [
        'left_readings' => [
            'eye_id' => \Eye::LEFT,
        ],
        'right_readings' => [
            'eye_id' => \Eye::RIGHT,
        ]
    ];

    protected $widgetClass = SynoptophoreWidget::class;
    public const ANGLES_FROM_PRIMARY = [15, 20];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_synoptophore';
    }

    public function rules()
    {
        return array_merge(
            [
                ['event_id, eye_id, entries, comments', 'safe'],
                ['right_readings', 'requiredIfSide', 'side' => 'right'],
                ['left_readings', 'requiredIfSide', 'side' => 'left'],
                ['eye_id', 'sideAttributeValidation'],
                ['angle_from_primary', 'required'],
                [
                    'angle_from_primary', 'in',
                    'range' => self::ANGLES_FROM_PRIMARY,
                    'message' => '{attribute} is invalid'
                ],
                ['comments', 'length', 'min' => 5]
            ]
        );
    }

    public function sidedFields(?string $side = null): array
    {
        return [];
    }

    public function sidedDefaults(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'event' => [self::BELONGS_TO, 'Event', 'event_id'],
                'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
                'right_readings' => [
                    self::HAS_MANY,
                    Synoptophore_ReadingForGaze::class,
                    'element_id',
                    'on' => 'right_readings.eye_id = ' . \Eye::RIGHT
                ],
                'left_readings' => [
                    self::HAS_MANY,
                    Synoptophore_ReadingForGaze::class,
                    'element_id',
                    'on' => 'left_readings.eye_id = ' . \Eye::LEFT
                ],
            ],
        );
    }

    public function attributeLabels()
    {
        return [
            'comments' => 'Comments',
            'angle_from_primary' => 'Angle From Primary',
            'left_readings' => 'Readings',
            'right_readings' => 'Readings',
            'view_header' => 'Eye Fixation'
        ];
    }

    public function getReadingForSideByGazeType($side, $gaze_type)
    {
        if (!$this->hasEye($side)) {
            return null;
        }
        foreach ($this->{"{$side}_readings"} as $reading) {
            if ($reading->gaze_type == $gaze_type) {
                return $reading;
            }
        }
    }

    protected function afterValidate()
    {
        parent::afterValidate();

        foreach (['left', 'right'] as $side) {
            if (!$this->hasEye($side)) {
                continue;
            }
            if (!$this->hasUniqueGazeTypesForSide($side)) {
                $this->addError("{$side}_readings", "Each gaze type can only be recorded once for a side.");
            };
        }
    }

    protected function hasUniqueGazeTypesForSide($side)
    {
        $readings = $this->{"{$side}_readings"} ?? [];
        $gaze_types = array_map(function ($reading) {
            return $reading->gaze_type;
        }, $readings);

        return array_unique($gaze_types) === $gaze_types;
    }
}
