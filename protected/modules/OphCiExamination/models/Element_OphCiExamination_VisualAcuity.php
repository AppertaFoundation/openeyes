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

use OEModule\OphCiExamination\models\interfaces\BEOSidedData;
use OEModule\OphCiExamination\widgets\VisualAcuity as VisualAcuityWidget;

/**
 * This is the main record element for Visual Acuity, which records VA for left, right and BEO eyes.
 * BEO was introduced later than Left and Right Readings, when the interface approach was adopted
 * for the element. The simple recording method does not support BEO, and is provided for backward
 * compatibility WRT complog integration.
 *
 * Whilst "unable_to_assess" and "behaviour_assessed" would appear to be two sides of the same coin, they
 * in fact support slightly different clinical meaning. One of these two items (or a reading) is required.
 * Behaviour Assessed is provided to indicate that although no readings were posssible, the patient does have
 * good visual function. This needs to be explicitly recorded, and should be expanded upon in the relevant
 * comment. The motivation for this was to support the recording of a patient that can Fix and Follow light or an
 * object, as part of Strabismus.
 *
 * This is the model class for table "et_ophciexamination_visualacuity".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property bool $left_unable_to_assess
 * @property bool $right_unable_to_assess
 * @property bool $beo_unable_to_assess
 * @property bool $left_behaviour_assessed
 * @property bool $right_behaviour_assessed
 * @property bool $beo_behaviour_assessed
 * @property bool $left_eye_missing
 * @property bool $right_eye_missing
 * @property bool $cvi_alert_dismissed
 * @property string $record_mode
 * @property string $left_notes
 * @property string $right_notes
 * @property string $beo_notes
 *
 * The followings are the available model relations:
 * @property OphCiExamination_VisualAcuityUnit $unit
 * @property OphCiExamination_VisualAcuity_Reading[] $readings
 * @property OphCiExamination_VisualAcuity_Reading[] $left_readings
 * @property OphCiExamination_VisualAcuity_Reading[] $right_readings
 * @property OphCiExamination_VisualAcuity_Reading[] $beo_readings
 * @property \User $user
 * @property \User $usermodified
 * @property \Eye eye
 * @property \EventType $eventType
 * @property \Event $event
 *
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */
class Element_OphCiExamination_VisualAcuity extends \BaseEventTypeElement implements BEOSidedData
{
    use traits\CustomOrdering;
    use traits\HasBEOSidedData;
    use traits\HasChildrenWithEventScopeValidation;

    const RECORD_MODE_COMPLEX = 'complex';
    const RECORD_MODE_SIMPLE = 'simple';

    public $service;
    protected $auto_update_relations = true;
    protected $relation_defaults = [
        'left_readings' => [
            'side' => OphCiExamination_VisualAcuity_Reading::LEFT,
        ],
        'right_readings' => [
            'side' => OphCiExamination_VisualAcuity_Reading::RIGHT,
        ],
        'beo_readings' => [
            'side' => OphCiExamination_VisualAcuity_Reading::BEO,
        ]
    ];

    protected const EVENT_SCOPED_CHILDREN = [
        'right_readings' => 'with_head_posture',
        'left_readings' => 'with_head_posture',
        'beo_readings' => 'with_head_posture',
    ];

    public $cvi_alert_dismissed;
    public $unit_id;
    protected $current_unit;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_visualacuity';
    }

    public function getWidgetClass()
    {
        if ($this->record_mode === self::RECORD_MODE_COMPLEX) {
            return VisualAcuityWidget::class;
        }
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [
                'record_mode, right_readings, left_readings, eye_id, unit_id, left_unable_to_assess,
                right_unable_to_assess, left_eye_missing, right_eye_missing, cvi_alert_dismissed,
                left_behaviour_assessed, right_behaviour_assessed, left_notes, right_notes, beo_behaviour_assessed,
                beo_unable_to_assess, beo_readings, beo_notes',
                'safe'
            ],
            ['eye_id', 'sideAttributeValidation'],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            ['id, event_id, eye_id', 'safe', 'on' => 'search'],
        ];
    }

    public function sidedFields(?string $side = null): array
    {
        if ($side && $side === 'beo') {
            return ['unable_to_assess', 'behaviour_assessed', 'readings'];
        }

        return ['unable_to_assess', 'behaviour_assessed', 'eye_missing', 'readings'];
    }

    public function sidedDefaults(): array
    {
        return [];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'eventType' => [self::BELONGS_TO, 'EventType', 'event_type_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'readings' => [self::HAS_MANY, OphCiExamination_VisualAcuity_Reading::class, 'element_id'],
            'right_readings' => [
                self::HAS_MANY,
                OphCiExamination_VisualAcuity_Reading::class,
                'element_id',
                'on' => 'right_readings.side = ' . OphCiExamination_VisualAcuity_Reading::RIGHT
            ],
            'left_readings' => [
                self::HAS_MANY,
                OphCiExamination_VisualAcuity_Reading::class,
                'element_id',
                'on' => 'left_readings.side = ' . OphCiExamination_VisualAcuity_Reading::LEFT
            ],
            'beo_readings' => [
                self::HAS_MANY,
                OphCiExamination_VisualAcuity_Reading::class,
                'element_id',
                'on' => 'beo_readings.side = ' . OphCiExamination_VisualAcuity_Reading::BEO
            ],
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
            'beo_unable_to_assess' => 'Unable to assess BEO',
            'left_unable_to_assess' => 'Unable to assess',
            'right_unable_to_assess' => 'Unable to assess',
            'left_eye_missing' => 'Eye missing',
            'right_eye_missing' => 'Eye missing',
            'beo_behaviour_assessed' => 'Visual behaviour assessed',
            'left_behaviour_assessed' => 'Visual behaviour assessed',
            'right_behaviour_assessed' => 'Visual behaviour assessed',
            'cvi_alert_dismissed' => 'Is CVI alert dismissed',
            'right_notes' => 'Comments',
            'left_notes' => 'Comments',
            'beo_notes' => 'Comments'
        );
    }

    /**
     * @return OphCiExamination_VisualAcuityUnit|null
     */
    public function getUnit()
    {
        if (!$this->current_unit) {
            $this->current_unit = $this->getCurrentUnitFromState() ?: $this->getDefaultUnit();
        }

        return $this->current_unit;
    }

    protected function afterValidate()
    {
        foreach (['beo' => 'BEO', 'left' => 'Left side', 'right' => 'Right side'] as $side => $label) {
            if (!$this->hasEye($side)) {
                continue;
            }

            if ($this->eyeCanHaveReadings($side)) {
                $this->validateSideReadings($side, $label);
            }

            if (!$this->eyeCanHaveReadings($side) && count($this->{"{$side}_readings"}) > 0) {
                $this->validateNotUnassessable($side, $label);
            }
        }

        $this->validateSideForRecordMode();

        parent::afterValidate();
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->unit_id = $this->getDefaultUnitId();
    }

    public function setUpdateOptions()
    {
        parent::setUpdateOptions();
        // ensure unit_id is set when updating for simple record mode
        $this->unit_id = $this->getUnit() ? $this->getUnit()->id : null;
    }

    /**
     * Array of unit values for dropdown.
     *
     * @param int $unit_id
     * @param bool $selectable - whether want selectable values or all unit values
     *
     * @return array
     */
    public function getUnitValues($unit_id = null, $selectable = true)
    {
        if ($unit_id) {
            $unit = OphCiExamination_VisualAcuityUnit::model()->findByPk($unit_id);
        } else {
            $unit = $this->unit;
        }
        if ($selectable) {
            return \CHtml::listData($unit->selectableValues, 'base_value', 'value');
        } else {
            return \CHtml::listData($unit->values, 'base_value', 'value');
        }
    }

    /**
     * @param $excluded_unit_id
     * @param bool $is_near
     * @return \CActiveRecord[]
     */

    public function getUnits($excluded_unit_id, $is_near)
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = 'id <> :unit_id AND active = 1';
        $criteria->addCondition($is_near ? 'is_near = 1' : 'is_va = 1');

        $criteria->params = array(':unit_id' => $excluded_unit_id);
        $criteria->order = 'name';
        return OphCiExamination_VisualAcuityUnit::model()->findAll($criteria);
    }

    /**
     * @param int|null $unit_id
     * @param bool $is_near
     *
     * @return array
     */
    public function getUnitValuesForForm($unit_id = null, $is_near = false)
    {
        if ($unit_id) {
            $unit = OphCiExamination_VisualAcuityUnit::model()->findByPk($unit_id);
        } else {
            $unit = $this->unit;
        }

        $unit_values = $unit->selectableValues;

        $tooltip_units = $this->getUnits($unit->id, $is_near);

        $options = array();


        // getting the conversion values
        foreach ($unit_values as $uv) {
            $idx = (string)$uv->base_value;
            $options[$idx] = array('data-tooltip' => array());
            foreach ($tooltip_units as $tt) {
                $last = null;
                foreach ($tt->values as $tt_val) {
                    if ($tt_val->base_value >= $uv->base_value) {
                        $val = $tt_val->value;

                        if ($last != null && (abs($uv->base_value - $tt_val->base_value) > abs($uv->base_value - $last->base_value))) {
                            $val = $last->value;
                        }
                        $map = array('name' => $tt->name, 'value' => $val, 'approx' => false);
                        if ($tt_val->base_value < $uv->base_value) {
                            $map['approx'] = true;
                        }
                        $options[$idx]['data-tooltip'][] = $map;
                        break;
                    }

                    $last = $tt_val;
                }
            }
            // need to JSONify the options data
            $options[$idx]['data-tooltip'] = \CJSON::encode($options[$idx]['data-tooltip']);
        }

        return array(\CHtml::listData($unit_values, 'base_value', 'value'), $options);
    }

    /**
     * Get a combined string of the different readings. If a unit_id is given, the readings will
     * be converted to unit type of that id.
     *
     * @param string $side
     * @param null $unit_id
     *
     * @return string
     */
    public function getCombined($side, $unit_id = null)
    {
        $combined = array();
        foreach ($this->getNamedReadings($side, $unit_id) as $method => $value) {
            $combined[] = $value . ' ' . $method;
        }

        return implode(', ', $combined);
    }

    public function getNamedReadings($side, $unit_id = null)
    {
        $readings = array();
        foreach ($this->{$side . '_readings'} as $reading) {
            $readings[$reading->method->name] = $reading->convertTo($reading->value, $unit_id);
            if ($this->record_mode === self::RECORD_MODE_COMPLEX) {
                $readings[$reading->method->name] .= " " . $reading->getComplexAttributesString();
            }
        }
        return $readings;
    }

    public function getReadingStateAndNotes($side)
    {
        if (!$this->hasEye($side)) {
            return null;
        }

        return implode(" ", array_filter(
            [
                $this->getNotRecordedTextForSide($side),
                $this->{"{$side}_notes"}
            ],
            function ($text_item) {
                return !empty($text_item);
            }
        ));
    }

    /**
     * Get the best reading for the given side.
     *
     * @param string $side
     *
     * @return OphCiExamination_VisualAcuity_Reading|null
     */
    public function getBestReading($side)
    {
        $best = null;
        foreach ($this->{$side . '_readings'} ?? [] as $reading) {
            if (!$best || $reading->value >= $best->value) {
                $best = $reading;
            }
        }

        return $best;
    }

    /**
     * Get the best reading based on the type
     *
     * @param string $side
     * @param $method
     */
    public function getBestReadingByMethods($side, $methods)
    {
        $best = null;
        foreach ($methods as $method) {
            foreach ($this->{$side . '_readings'} as $reading) {
                if ($reading->method->id == $method->id) {
                    if (!$best || $reading->value >= $best->value) {
                        $best = $reading;
                    }
                }
            }
        }
        if ($best) {
            return $best->convertTo($best->value);
        }
        return $best;
    }

    /**
     * Get the best reading for the given side.
     *
     * @param string $side
     *
     * @return OphCiExamination_VisualAcuity_Reading|null
     */
    public function getAllReadings($side)
    {
        $r = array();
        foreach ($this->{$side . '_readings'} as $reading) {
            $r[] = $reading;
        }

        return $r;
    }

    /**
     * Get the best reading for the specified side in current units.
     *
     * @param string $side
     *
     * @return string
     */
    public function getBest($side)
    {
        $best = $this->getBestReading($side);
        if ($best) {
            return $best->convertTo($best->value);
        }
    }

    /**
     * Convenience function for generating string of why a reading wasn't recorded for a side.
     *
     * @param $side
     *
     * @return string
     */
    public function getNotRecordedTextForSide($side)
    {
        if ($this->hasEye($side) && !$this->{$side . '_readings'}) {
            if ($this->{"{$side}_behaviour_assessed"}) {
                return $this->getAttributeLabel("{$side}_behaviour_assessed");
            }

            $unable_to_assess = $side . '_unable_to_assess';
            $eye_missing = $side . '_eye_missing';

            if ($this->$unable_to_assess) {
                $text = $this->getAttributeLabel($unable_to_assess);
                if ($this->hasAttribute($eye_missing) && $this->$eye_missing) {
                    $text .= ', ' . $this->getAttributeLabel($eye_missing);
                }

                return $text;
            }

            if ($this->hasAttribute($eye_missing) && $this->$eye_missing) {
                return $this->getAttributeLabel($eye_missing);
            }

            return 'not recorded';
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * returns the default letter string for the va readings. Converts all readings to standard unit
     * configured for VA.
     *
     * @return string
     */
    public function getLetter_string()
    {
        return $this->record_mode === self::RECORD_MODE_COMPLEX
            ? $this->letterStringComplex()
            : $this->letterStringSimple();
    }

    /**
     * Get the list of currently used method ids.
     */
    public function getMethodValues()
    {
        $method_values = array();

        foreach ($this->readings as $reading) {
            $method_values[] = $reading->method_id;
        }

        return $method_values;
    }

    public function canViewPrevious()
    {
        return true;
    }

    public function canCopy()
    {
        return true;
    }

    public function getViewTitle()
    {
        if ($this->record_mode === self::RECORD_MODE_COMPLEX || !$this->unit) {
            return parent::getViewTitle();
        }

        return $this->getElementTypeName() . ' <small>' . $this->unit->name . '</small>';
    }

    /***
     * @param $eye_side 'left'|'right'|'beo' which side
     * @return bool whether the eye is marked as being able to have readings
     */
    public function eyeCanHaveReadings($eye_side)
    {
        if (!$this->hasEye($eye_side)) {
            return null;
        }

        return !(
            ((bool)$this->{$eye_side . '_behaviour_assessed'})
            || ((bool)$this->{$eye_side . '_unable_to_assess'})
            || ($this->hasAttribute("{$eye_side}_eye_missing") && (bool)$this->{"{$eye_side}_eye_missing"})
        );
    }

    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }

    public function getReading($side)
    {
        if (!$values = $this->{"{$side}_readings"}) {
            return;
        }
        $sum = 0;
        foreach ($values as $value) {
            if ($value->value) {
                $sum += $value->value;
            }
        }
        return round($sum / count($values));
    }

    protected function getDefaultUnitId()
    {
        return $this->getSetting('unit_id');
    }

    protected function getDefaultUnit()
    {
        return OphCiExamination_VisualAcuityUnit::model()
            ->findByPk($this->getDefaultUnitId());
    }

    protected function getCurrentUnitFromState()
    {
        if ($this->unit_id) {
            return OphCiExamination_VisualAcuityUnit::model()->findByPk($this->unit_id);
        } else {
            foreach (['right', 'left'] as $side) {
                if ($this->{"{$side}_readings"} && count($this->{"{$side}_readings"})) {
                    return $this->{"{$side}_readings"}[0]->unit;
                }
            }
        }
    }

    protected function validateSideReadings($side, $label)
    {
        if (count($this->{"{$side}_readings"}) === 0) {
            $this->addError($side, "$label has no data.");
        } elseif (!$this->hasUniqueReadingMethodsForSide($side)) {
            $this->addError("{$side}_readings", "Each method type can only be recorded once for $side");
        }
    }

    protected function validateNotUnassessable($side, $label)
    {
        if ($this->{"{$side}_unable_to_assess"}) {
            $this->addError(
                "{$side}_unable_to_assess",
                'Cannot be ' . $this->getAttributeLabel("{$side}_unable_to_assess") . ' with VA readings.'
            );
        }

        if ($this->hasAttribute("{$side}_eye_missing") && $this->{"{$side}_eye_missing"}) {
            $this->addError(
                "{$side}_eye_missing",
                'Cannot be ' . $this->getAttributeLabel("{$side}_eye_missing") . ' with VA readings.'
            );
        }
    }

    /**
     * Simple sanity check in case VA being added outside of standard form components
     */
    protected function validateSideForRecordMode()
    {
        if ($this->record_mode === self::RECORD_MODE_COMPLEX) {
            return;
        }
        if ($this->hasBeo()) {
            $this->addError('eye_id', 'Cannot record BEO in this mode.');
        }
    }

    protected function letterStringSimple(OphCiExamination_VisualAcuityUnit $va_unit = null)
    {
        return $this->getApp()->controller->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.va',
            [
                'left' => $this->getNamedReadings('left', $va_unit ? $va_unit->id : null),
                'right' => $this->getNamedReadings('right', $va_unit ? $va_unit->id : null),
                'va_unit' => $va_unit ? $va_unit->name : null,
                'title' => $this->getElementTypeName()
            ],
            true
        );
    }

    protected function letterStringComplex(OphCiExamination_VisualAcuityUnit $va_unit = null)
    {
        return $this->getApp()->controller->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.va_complex',
            [
                'readings_by_method' => $this->getReadingsInColumns($va_unit),
                'comments' => $this->getCommentsInColumns(),
                'va_unit' => $va_unit ? $va_unit->name : null,
                'title' => $this->getElementTypeName()
            ],
            true
        );
    }

    protected function getReadingsInColumns(OphCiExamination_VisualAcuityUnit $va_unit = null)
    {
        $unit_id = $va_unit ? $va_unit->id : null;
        $readings = [
            'beo' => $this->getNamedReadings('beo', $unit_id),
            'left' => $this->getNamedReadings('left', $unit_id),
            'right' => $this->getNamedReadings('right', $unit_id),
        ];

        $result = [];
        foreach (['beo', 'right', 'left'] as $side) {
            foreach ($readings[$side] as $method => $value) {
                $result[$method][$side] ??= $value;
            }
        }

        return $result;
    }

    protected function getCommentsInColumns()
    {
        return array_reduce(['right', 'left', 'beo'], function ($result, $side) {
            $content = $this->getReadingStateAndNotes($side);
            if (strlen($content)) {
                $result[$side] = $content;
            }
            return $result;
        }, []);

    }

    protected function hasUniqueReadingMethodsForSide($side)
    {
        $readings = $this->{"{$side}_readings"} ?? [];
        $method_ids = array_map(function ($reading) {
            return $reading->method_id;
        }, $readings);

        return array_unique($method_ids) === $method_ids;
    }
}
