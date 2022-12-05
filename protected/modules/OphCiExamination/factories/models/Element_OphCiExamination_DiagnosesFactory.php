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

    public function make($attributes = [], $canCreate = false)
    {
        // this has to happen at the end of the factory make chain
        $this->afterMaking(function (Element_OphCiExamination_Diagnoses $element) {
            if (!count($element->diagnoses)) {
                return;
            }
            $this->ensureElementHasPrincipalDiagnosis($element);
        });

        return parent::make($attributes, $canCreate);
    }

    public function create($attributes = [])
    {
        $this->afterCreating(function (Element_OphCiExamination_Diagnoses $element) {
            // if diagnoses have been "made" they must be associated with the created
            // element record and saved.
            foreach ($element->diagnoses as $diagnosis_entry) {
                $diagnosis_entry->element_diagnoses_id = $element->id;
                $diagnosis_entry->save();
            }
        });

        return parent::create($attributes);
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
     * Generates bilateral diagnosis records to attach to the element.
     * If count is provided, then the factory will ensure unique disorders are attached. Otherwise
     * it is up to the calling code to ensure conflicting disorder ids are not attached to the same
     * Diagnoses element.
     *
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withBilateralDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::BOTH);
    }

    /**
     * Generates right sided diagnosis records to attach to the element.
     * If count is provided, then the factory will ensure unique disorders are attached. Otherwise
     * it is up to the calling code to ensure conflicting disorder ids are not attached to the same
     * Diagnoses element.
     *
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withRightDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::RIGHT);
    }

    /**
     * Generates left sided diagnosis records to attach to the element.
     * If count is provided, then the factory will ensure unique disorders are attached. Otherwise
     * it is up to the calling code to ensure conflicting disorder ids are not attached to the same
     * Diagnoses element.
     *
     * @param mixed $disorders - array of disorders, or integer to represent the number of diagnoses to assign
     * @return void
     */
    public function withLeftDiagnoses($disorders = []): self
    {
        return $this->withDiagnoses($disorders, Eye::LEFT);
    }

    protected function withDiagnoses($disorders, $laterality): self
    {
        return $this->afterMaking(function (Element_OphCiExamination_Diagnoses $element) use ($disorders, $laterality) {
            if (is_int($disorders)) {
                $disorders = $this->getUniqueDisordersFor($element, $disorders);
            }
            $this->addDisordersTo($element, $disorders, $laterality);
        });
    }

    private function ensureElementHasPrincipalDiagnosis(Element_OphCiExamination_Diagnoses $element): void
    {
        $has_principal_diagnosis = count(
            array_filter($element->diagnoses, function ($diagnosis) {
                return (bool) $diagnosis->principal;
            })
        ) > 0;

        if (!$has_principal_diagnosis) {
            $element->diagnoses[array_rand($element->diagnoses)]->principal = true;
        }
    }

    private function addDisordersTo(Element_OphCiExamination_Diagnoses $element, array $disorders = [], $laterality = Eye::BOTH): void
    {
        $element->diagnoses = array_merge(
            $element->diagnoses ?? [],
            array_map(
                function ($disorder) use ($laterality) {
                    return OphCiExamination_Diagnosis::factory()
                        ->make([
                            'element_diagnoses_id' => null,
                            'eye_id' => $laterality,
                            'disorder_id' => $disorder->id
                        ]);
                },
                $disorders
            )
        );
    }

    /**
     * Retrieves disorders for the given count that have not already been attached to the given Diagnoses element, so
     * they can be reliably attached to the Diagnoses element
     *
     * @param Element_OphCiExamination_Diagnoses $element
     * @param integer $count
     * @param array $exclude_disorder_ids
     * @return array
     */
    private function getUniqueDisordersFor(Element_OphCiExamination_Diagnoses $element, $count = 1): array
    {
        $current_disorder_ids = array_map(function ($diagnosis) {
            return $diagnosis->disorder_id;
        }, $element->diagnoses ?? []);

        return $this->getDisordersThatAreNotIn($current_disorder_ids, $count);
    }

    /**
     * Gets unique existing disorders
     *
     * @param mixed $exclude_disorder_ids
     * @param mixed $count
     * @return array
     */
    private function getDisordersThatAreNotIn(array $exclude_disorder_ids = [], $count = 1): array
    {
        $disorders = Disorder::factory()
            ->existingforOphthalmology()
            ->count($count)
            ->make();

        $unique_disorders = array_filter(
            $disorders,
            function ($disorder) use ($exclude_disorder_ids) {
                return !in_array($disorder->id, $exclude_disorder_ids);
            }
        );

        if (count($unique_disorders) < $count) {
            // recursive call with the additional disorder ids to be excluded
            return array_merge(
                $unique_disorders,
                $this->getDisordersThatAreNotIn(
                    array_merge(
                        $exclude_disorder_ids,
                        array_map(
                            function ($disorder) {
                                return $disorder->id;
                            },
                            $unique_disorders
                        )
                    ),
                    $count - count($unique_disorders)
                )
            );
        }

        return $unique_disorders;
    }
}
