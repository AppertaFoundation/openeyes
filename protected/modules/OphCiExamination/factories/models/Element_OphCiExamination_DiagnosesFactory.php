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

use Disorder;
use Eye;
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OE\factories\models\traits\HasEventTypeElementStates;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses;
use OEModule\OphCiExamination\models\OphCiExamination_Diagnosis;

class Element_OphCiExamination_DiagnosesFactory extends ModelFactory
{
    use HasEventTypeElementStates;

    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
        ];
    }

    public function noOphthalmicDiagnoses()
    {
        return $this->state(function ($attributes) {
            return [
                'no_ophthalmic_diagnoses_date' => $this->faker->date()
            ];
        });
    }

    /**
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withBilateralDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::BOTH);
    }

    /**
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withRightDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::RIGHT);
    }

    /**
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withLeftDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::LEFT);
    }

    protected function withDiagnoses($disorders, $laterality): self
    {
        return $this->afterCreating(function (Element_OphCiExamination_Diagnoses $element) use ($disorders, $laterality) {
            if (is_int($disorders)) {
                $disorders = Disorder::factory()
                    ->forOpthalmology()
                    ->count($disorders)
                    ->create();
            }
            foreach ($disorders as $disorder) {
                OphCiExamination_Diagnosis::factory()->create([
                    'element_diagnoses_id' => $element->id,
                    'eye_id' => $laterality,
                    'disorder_id' => $disorder->id
                ]);
            }
        });
    }
}
