<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use OE\factories\models\traits\HasEventTypeElementStates;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\SystemicDiagnoses;
use Eye;
use Disorder;
use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;

class SystemicDiagnosesFactory extends ModelFactory
{
    use HasEventTypeElementStates;

    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
        ];
    }

    public function create($attributes = [])
    {
        $this->afterMaking(function (SystemicDiagnoses $element) {
            // auto-relations causes related instances to be validated during a save
            // regardless of whether the parent is validating or not.
            // Systemic Diagnosis has an unusual behaviour that validates side_id is set
            // and then nulls it if it's -9 (NA) prior to save
            // so here we force unset values to -9 to ensure that they will validate
            foreach ($element->diagnoses as $diagnosis_entry) {
                if (!isset($diagnosis_entry->side_id)) {
                    $diagnosis_entry->side_id = -9;
                }
            }
        });

        return parent::create($attributes);
    }
    /**
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withDiagnoses($disorders = []): self
    {
        return $this->afterMaking(function (SystemicDiagnoses $element) use ($disorders) {
            if (is_int($disorders)) {
                $disorders = Disorder::factory()
                    ->existingForSystemic()
                    ->count($disorders)
                    ->make();
            }

            $element->diagnoses = array_merge(
                $element->diagnoses ?? [],
                array_map(
                    function ($disorder) {
                        return SystemicDiagnoses_Diagnosis::factory()->make([
                            'element_id' => null,
                            'disorder_id' => $disorder->id
                        ]);
                    },
                    $disorders
                )
            );
        });
    }

    protected function mapModelToFormData($model): array
    {
        $result = [
            'entries' => [],
            'present' => "1"
        ];

        foreach ($model->diagnoses as $i => $entry) {
            $result['entries'][] = [
                'disorder_id' => $entry->disorder_id,
                'date' => $entry->date,
                'has_disorder' => $entry->has_disorder,
                'na_eye' => "-9"
            ];
        }

        return $result;
    }
}
