<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class CoverAndPrismCover_Entry
 * @package OEModule\OphCiExamination\models
 * @property $correctiontype_id
 * @property $cover_correction
 * @property $distance_id
 * @property $distance
 * @property $horizontal_prism_id
 * @property $horizontal_prism
 * @property $horizontal_value
 * @property $vertical_prism_id
 * @property $vertical_prism
 * @property $vertical_value
 */
class CoverAndPrismCover_Entry extends \BaseElement
{
    use traits\HasRelationOptions;
    use traits\HasWithHeadPosture;
    use traits\HasCorrectionType;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $correction_type_attributes = ['correctiontype_id'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_coverandprismcover_entry';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, distance_id, horizontal_prism_id, horizontal_value, ' .
                    'vertical_prism_id, vertical_value, comments', 'safe'],
                ['distance_id, correctiontype_id', 'required'],
                ['comments, horizontal_prism_id, vertical_prism_id', \OEAtLeastOneRequiredValidator::class],
                [
                    'horizontal_prism_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => CoverAndPrismCover_HorizontalPrism::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'horizontal_value', 'numerical',
                    'min' => '0', 'max' => '90',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'vertical_prism_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => CoverAndPrismCover_VerticalPrism::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'vertical_value', 'numerical',
                    'min' => '0', 'max' => '50',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'distance_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => CoverAndPrismCover_Distance::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'horizontal_prism_id, horizontal_value', \OERequiredTogetherValidator::class
                ],
                [
                    'vertical_prism_id, vertical_value', \OERequiredTogetherValidator::class
                ]
            ],
            $this->rulesForCorrectionType(),
            $this->rulesForWithHeadPosture()
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'element' => [self::BELONGS_TO, CoverAndPrismCover::class, 'element_id'],
                'horizontal_prism' => [self::BELONGS_TO, CoverAndPrismCover_HorizontalPrism::class, 'horizontal_prism_id'],
                'vertical_prism' => [self::BELONGS_TO, CoverAndPrismCover_VerticalPrism::class, 'vertical_prism_id'],
                'distance' => [self::BELONGS_TO, CoverAndPrismCover_Distance::class, 'distance_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType()
        );
    }

    public function attributeLabels()
    {
        return [
            'correctiontype_id' => 'Correction',
            'horizontal_prism_id' => 'Prism',
            'horizontal_value' => 'Δ Prism Value',
            'vertical_prism_id' => 'Prism',
            'vertical_value' => 'Δ Prism Value',
            'with_head_posture' => 'CHP',
            'distance_id' => 'Distance',
            'comments' => 'Comments'
        ];
    }

    public function __toString()
    {
        // [near|distance] [correction type] [used | not used] [free text] [n] Δ [h-type] [n] Δ [v-type]

        $result = [];

        if ($this->distance) {
            $result[] = $this->distance;
        }
        if ($this->correctiontype) {
            $result[] = $this->correctiontype;
        }

        if ($this->withHeadPostureRecorded()) {
            $result[] = sprintf("%s: %s", $this->getAttributeLabel('with_head_posture'), $this->display_with_head_posture);
        }

        if ($this->comments) {
            $result[] = $this->comments;
        }

        if ($this->horizontal_prism_id) {
            $result[] = $this->horizontal_value . " Δ " . $this->horizontal_prism;
        }

        if ($this->vertical_prism_id) {
            $result[] = $this->vertical_value . " Δ " . $this->vertical_prism;
        }

        return implode(", ", $result);
    }
}
