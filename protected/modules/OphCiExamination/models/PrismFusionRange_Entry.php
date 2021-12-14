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

use OEModule\OphCiExamination\models\traits\HasRelationOptions;

/**
 * Class PrismFusionRange_Entry
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property int $prism_over_eye_id
 * @property string $display_prism_over_eye
 * @property int $near_bo
 * @property int $near_bi
 * @property int $near_bu
 * @property int $near_bd
 * @property int $distance_bo
 * @property int $distance_bi
 * @property int $distance_bu
 * @property int $distance_bd
 * @property int $correctiontype_id
 * @property CorrectionType $correctiontype
 * @property bool $with_head_posture
 * @property string $display_with_head_posture
 */
class PrismFusionRange_Entry extends \BaseEventTypeElement
{
    use traits\HasCorrectionType;
    use traits\HasWithHeadPosture;
    use HasRelationOptions;

    protected $correction_type_attributes = ['correctiontype_id'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_prismfusionrange_entry';
    }

    public function rules()
    {
        return array_merge(
            [
                [
                    'prism_over_eye_id, near_bi, near_bo, near_bd, near_bu, distance_bi, distance_bo, distance_bd, ' .
                    'distance_bu, comments',
                    'safe'
                ],
                [
                    'prism_over_eye_id', 'required'
                ],
                [
                    'near_bi, near_bo, near_bd, near_bu, distance_bi, distance_bo, distance_bd, distance_bu',
                    \OEAtLeastOneRequiredValidator::class
                ],
                [
                    'prism_over_eye_id',
                    'in',
                    'range' => [\Eye::RIGHT, \Eye::LEFT],
                    'message' => '{attribute} is invalid'
                ],
                [
                    'near_bi, near_bo, distance_bi, distance_bo', 'numerical',
                    'integerOnly' => true,
                    'min' => 0,
                    'max' => 45
                ],
                [
                    'near_bd, near_bu, distance_bd, distance_bu', 'numerical',
                    'integerOnly' => true,
                    'min' => 0,
                    'max' => 25
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
                'element' => [self::BELONGS_TO, PrismFusionRange::class, 'element_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType(),
        );
    }

    public function attributeLabels()
    {
        return [
            'prism_over_eye_id' => 'Prism over',
            'near_bi' => 'Base in',
            'near_bo' => 'Base out',
            'near_bu' => 'Base up',
            'near_bd' => 'Base down',
            'distance_bi' => 'Base in',
            'distance_bo' => 'Base out',
            'distance_bu' => 'Base up',
            'distance_bd' => 'Base down',
            'correctiontype_id' => 'Correction',
            'with_head_posture' => 'CHP'
        ];
    }

    public function getDisplay_prism_over_eye()
    {
        return [
            \Eye::RIGHT => 'right',
            \Eye::LEFT => 'left'
        ][(int) $this->prism_over_eye_id] ?? '';
    }

    public function getDisplay_labelled_prism_over_eye()
    {
        $val = $this->display_prism_over_eye;
        return $val ? sprintf("%s %s", $this->getAttributeLabel('prism_over_eye_id'), $val) : '';
    }
}
