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

use OEModule\OphCiExamination\models\traits\HasRelationOptions;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;

/**
 * This is the model class for table "ophciexamination_visualacuity_reading".
 *
 * @property int $id
 * @property int $element_id
 * @property int $side
 * @property int $value
 * @property string $display_value
 * @property int $method_id
 * @property int $source_id
 * @property int $fixation_id
 * @property int $occluder_id
 *
 * @property Element_OphCiExamination_VisualAcuity $element
 * @property OphCiExamination_VisualAcuityUnit $unit
 * @property OphCiExamination_VisualAcuity_Method $method
 * @property OphCiExamination_VisualAcuityFixation $fixation
 * @property OphCiExamination_VisualAcuityOccluder $occluder
 */
class OphCiExamination_VisualAcuity_Reading extends \BaseActiveRecordVersioned
{
    use HasWithHeadPosture;
    use HasRelationOptions;

    const BEO = 2;
    const LEFT = 1;
    const RIGHT = 0;

    protected $relation_options_to_skip = ['unit'];

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected static $complex_relations = ["source", "fixation", "occluder"];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_visualacuity_reading';
    }

    /**
     * @return array validation rules for model visualacuity_methods.
     */
    public function rules()
    {
        return array_merge(
            [
                ['id, unit_id, value, method_id, side, source_id, fixation_id, occluder_id', 'safe'],
                ['unit_id, value, method_id, side', 'required'],
                ['id, unit_id, value, method_id, element_id, side', 'safe', 'on' => 'search'],
            ],
            $this->rulesForWithHeadPosture()
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, Element_OphCiExamination_VisualAcuity::class, 'element_id'],
            'unit' => [self::BELONGS_TO, OphCiExamination_VisualAcuityUnit::class, 'unit_id'],
            'method' => [self::BELONGS_TO, OphCiExamination_VisualAcuity_Method::class, 'method_id'],
            'source' => [self::BELONGS_TO, OphCiExamination_VisualAcuitySource::class, 'source_id'],
            'fixation' => [self::BELONGS_TO, OphCiExamination_VisualAcuityFixation::class, 'fixation_id'],
            'occluder' => [self::BELONGS_TO, OphCiExamination_VisualAcuityOccluder::class, 'occluder_id']
        ];
    }

    public function attributeLabels()
    {
        return array(
            'method_id' => 'Method',
            'unit_id' => 'Type',
            'fixation_id' => 'Fixation',
            'source_id' => 'Source',
            'occluder_id' => 'Occluder',
            'with_head_posture' => 'CHP'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('unit_id', $this->id, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('method_id', $this->method_id, true);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('side', $this->side, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function sourceOptions()
    {
        $current_pks = $this->source_id ? [$this->source_id] : [];
        $cache_key = "distance-" . self::getRelationOptionsCacheKey(
            OphCiExamination_VisualAcuitySource::class,
            $current_pks
        );

        return self::getAndSetRelationOptionsCache(
            $cache_key,
            function () use ($current_pks) {
                return OphCiExamination_VisualAcuitySource::model()
                    ->activeOrPk($current_pks)
                    ->findAll(
                        [
                            'condition' => 'is_near = 0',
                            'order' => 'display_order asc'
                        ]
                    );
            }
        );
    }

    public function getDisplay_value()
    {
        return $this->convertTo($this->value, $this->unit_id);
    }

    /**
     * Convert a base_value (ETDRS + 5) to a different unit.
     *
     * @param int $base_value
     * @param int $unit_id
     *
     * @return string
     */
    public function convertTo($base_value, $unit_id = null)
    {
        return $this->getClosest($base_value, $unit_id)->value;
    }

    /**
     * Get the closest step value for a unit.
     *
     * @param int $base_value
     * @param int $unit_id
     *
     * @return OphCiExamination_VisualAcuityUnitValue
     */
    public function getClosest($base_value, $unit_id = null)
    {
        if (!$unit_id) {
            $unit_id = $this->unit_id;
        }
        $criteria = new \CDbCriteria();
        $criteria->select = array('*', 'ABS(base_value - :base_value) AS delta');
        $criteria->condition = 'unit_id = :unit_id';
        $criteria->params = array(':unit_id' => $unit_id, ':base_value' => $base_value);
        $criteria->order = 'delta';
        $value = OphCiExamination_VisualAcuityUnitValue::model()->find($criteria);

        return $value;
    }

    /**
     * Load model with closest base_values for current unit. This is to allow for switching units.
     *
     * @param int $unit_id
     */
    public function loadClosest($unit_id = null)
    {
        $base_value = $this->value;
        if ($base_value) {
            $value = $this->getClosest($base_value, $unit_id);
            $this->value = $value->base_value;
        }
    }

    public function isRight(): bool
    {
        return isset($this->side) && (string) $this->side === (string) self::RIGHT;
    }

    public function isLeft(): bool
    {
        return $this->side && (string) $this->side === (string) self::LEFT;
    }

    public function isBeo(): bool
    {
        return $this->side && (string) $this->side === (string) self::BEO;
    }

    public function getSideString(): ?string
    {
        return [
            self::BEO => 'beo',
            self::LEFT => 'left',
            self::RIGHT => 'right'
        ][$this->side] ?? null;
    }

    /**
     * @param $side - left|right|beo
     */
    public function setSideByString($side)
    {
        $this->side = [
            'beo' => self::BEO,
            'left' => self::LEFT,
            'right' => self::RIGHT
        ][$side] ?? null;
    }

    public function getComplexAttributesString()
    {
        $attributes = $this->getRelatedComplexAttributes();

        if ($this->withHeadPostureRecorded()) {
            $attributes[] = sprintf(
                "%s: %s",
                $this->getAttributeLabel('with_head_posture'),
                $this->display_with_head_posture
            );
        }

        return implode(", ", $attributes);
    }

    protected function getRelatedComplexAttributes()
    {
        return array_reduce(
            static::$complex_relations,
            function ($attrs, $attr) {
                if ($this->$attr) {
                    $attrs[] = $this->$attr;
                }
                return $attrs;
            },
            []
        );
    }
}
