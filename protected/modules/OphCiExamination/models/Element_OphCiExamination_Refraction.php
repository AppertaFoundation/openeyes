<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\traits\HasSidedData;
use OEModule\OphCiExamination\widgets\Refraction;

/**
 * This is the model class for table "et_ophciexamination_refraction".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property array $right_readings
 * @property array $left_readings
 * @property OphCiExamination_Refraction_Reading|null $right_priority_reading
 * @property OphCiExamination_Refraction_Reading|null $left_priority_reading
 */
class Element_OphCiExamination_Refraction extends \BaseEventTypeElement implements SidedData
{
    use traits\CustomOrdering;
    use HasSidedData;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $relation_defaults = array(
        'left_readings' => array(
            'eye_id' => SidedData::LEFT,
        ),
        'right_readings' => array(
            'eye_id' => SidedData::RIGHT,
        ),
    );

    public $widgetClass = Refraction::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_refraction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['event_id, eye_id, right_readings, right_notes, left_readings, left_notes', 'safe'],
            ['id, event_id, eye_id', 'safe', 'on' => 'search'],
        ];
    }

    public function sidedFields(?string $side = null): array
    {
        return [];
    }

    public function sidedDefaults(): array
    {
        return [];
    }

    public function canCopy()
    {
        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'eventType' => [self::BELONGS_TO, 'EventType', 'event_type_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'right_readings' => [
                self::HAS_MANY,
                OphCiExamination_Refraction_Reading::class,
                'element_id',
                'on' => 'right_readings.eye_id = ' . SidedData::RIGHT
            ],
            'left_readings' => [
                self::HAS_MANY,
                OphCiExamination_Refraction_Reading::class,
                'element_id',
                'on' => 'left_readings.eye_id = ' . SidedData::LEFT
            ],
            'right_priority_reading' => [
                self::HAS_ONE,
                OphCiExamination_Refraction_Reading::class,
                'element_id',
                'on' => 'right_priority_reading.eye_id = ' . SidedData::RIGHT,
                'with' => ['type'],
                'order' => '-type.priority desc limit 1'
            ],
            'left_priority_reading' => [
                self::HAS_ONE,
                OphCiExamination_Refraction_Reading::class,
                'element_id',
                'on' => 'left_priority_reading.eye_id = ' . SidedData::LEFT,
                'with' => ['type'],
                'order' => '-type.priority desc limit 1'
            ]
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_notes' => 'Comments',
            'right_notes' => 'Comments'
        );
    }

    /**
     * Returns a string representation of the
     * @param $side
     * @return string
     */
    public function getPriorityReadingCombined($side)
    {
        $reading = $this->{"{$side}_priority_reading"};

        return $reading ? $reading->refraction_display : "";
    }

    /**
     * @param $side
     * @return array
     */
    public function getPriorityReadingDataAttributes($side)
    {
        $reading = $this->{"{$side}_priority_reading"};

        return [
            'sphere' => $reading ? $reading->sphere : null,
            'cylinder' => $reading ? $reading->cylinder : null,
            'axis' => $reading ? $reading->axis : null,
            'type' => $reading ? $reading->type_display : null
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getLetter_string()
    {
        return sprintf("Refraction: R: %s, L: %s",
            $this->getCombinedString('right'),
            $this->getCombinedString('left'));
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }

    protected function afterValidate()
    {
        foreach (['right', 'left'] as $side) {
            if (!$this->hasEye($side)) {
                continue;
            }

            $this->validateReadingsForSide($side);
        }
        parent::afterValidate();
    }

    protected function validateReadingsForSide($side)
    {
        $readings_attr = "{$side}_readings";
        $readings = $this->$readings_attr;

        if (!is_array($readings) || count($readings) === 0) {
            $this->addError($readings_attr, "cannot be blank.");
        } elseif (!$this->hasUniqueReadingTypesForSide($side)) {
            $this->addError("{$side}_readings", "Each reading type can only be recorded once for $side");
        }
    }

    protected function hasUniqueReadingTypesForSide($side)
    {
        $readings = $this->{"{$side}_readings"} ?? [];
        $types = array_map(function ($reading) {
            // get the type id, or the entered string for other type
            return $reading->type_id ?? $reading->type_other;
        }, $readings);

        return array_unique($types) === $types;
    }

    protected function getCombinedString($side)
    {
        $readings = $this->{"{$side}_readings"};
        if (!count($readings)) {
            return "NR";
        }

        return implode(
            ", ",
            array_map(
                function ($reading) {
                    return (string)$reading;
                },
                $readings
            )
        );
    }
}
