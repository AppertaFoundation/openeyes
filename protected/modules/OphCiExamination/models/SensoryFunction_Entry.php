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
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;

/**
 * Class SensoryFunction_Entry
 *
 * @package OEModule\OphCiExamination\models
 */
class SensoryFunction_Entry extends \BaseElement
{
    use HasRelationOptions;
    use HasWithHeadPosture;

    protected $auto_validate_relations = true;
    protected $auto_update_relations = true;

    public function tableName()
    {
        return 'ophciexamination_sensoryfunction_entry';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, correctiontypes', 'safe'],
                [
                    'entry_type_id', 'exist', 'allowEmpty' => false,
                    'attributeName' => 'id',
                    'className' => SensoryFunction_EntryType::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'distance_id', 'exist', 'allowEmpty' => false,
                    'attributeName' => 'id',
                    'className' => SensoryFunction_Distance::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'result_id', 'exist', 'allowEmpty' => false,
                    'attributeName' => 'id',
                    'className' => SensoryFunction_Result::class,
                    'message' => '{attribute} is invalid'
                ]
            ],
            $this->rulesForWithHeadPosture()
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'entry_type' => [self::BELONGS_TO, SensoryFunction_EntryType::class, 'entry_type_id'],
            'distance' => [self::BELONGS_TO, SensoryFunction_Distance::class, 'distance_id'],
            'result' =>[self::BELONGS_TO, SensoryFunction_Result::class, 'result_id'],
            'correctiontypes' => [self::MANY_MANY, CorrectionType::class,
                'ophciexamination_sensoryfunction_correction_assgnmnt(entry_id, correctiontype_id)',
                'order' => 'display_order, name']
        ];
    }

    public function attributeLabels()
    {
        return [
            'entry_type_id' => 'Test Type',
            'distance_id' => 'Distance',
            'correctiontypes' => 'Correction',
            'result_id' => 'Result',
            'with_head_posture' => 'CHP'
        ];
    }

    public function __toString()
    {
        return "{$this->entry_type}, {$this->distance}"
            . $this->stringCorrectionTypesDisplay()
            . $this->stringHeadPostureDisplay()
            . ": {$this->result}";
    }

    public function getDisplay_correctiontypes()
    {
        return $this->correctiontypes ? implode(", ", $this->correctiontypes) : '-';
    }

    private function stringCorrectionTypesDisplay()
    {
        return $this->correctiontypes && count($this->correctiontypes)
            ? ', ' . implode(", ", $this->correctiontypes)
            : '';
    }

    private function stringHeadPostureDisplay()
    {
        return $this->withHeadPostureRecorded()
            ? sprintf(", %s %s", $this->getAttributeLabel('with_head_posture'), $this->convertWithHeadPostureRecordToDisplay($this->with_head_posture))
            : '';
    }
}