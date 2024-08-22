<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Element_OphTrOperationnote_ProcedureListFactory extends FactoryForOperationnoteElement
{
    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'eye_id' => $this->faker->randomElement([Eye::RIGHT, Eye::LEFT])
            ]
        );
    }

    public function create($attributes = [])
    {
        $this->afterCreating(function (Element_OphTrOperationnote_ProcedureList $procedures_element) {
            foreach ($procedures_element->procedure_assignments as $procedure_assignment) {
                $procedure_assignment->procedurelist_id = $procedures_element->id;
                $procedure_assignment->save();
            }
        });

        return parent::create($attributes);
    }

    public function withProcedures($procedures): self
    {
        return $this->afterMaking(function (Element_OphTrOperationnote_ProcedureList $procedures_element) use ($procedures) {
            if (is_int($procedures)) {
                $procedures = $this->getUniqueProceduresFor($procedures_element, $procedures);
            }
            if (!is_array($procedures)) {
                $procedures = [$procedures];
            }
            $this->addProceduresTo($procedures_element, $procedures);
        });
    }

    public function forLeftEye(): self
    {
        return $this->state(['eye_id' => Eye::LEFT]);
    }

    public function forRightEye(): self
    {
        return $this->state(['eye_id' => Eye::RIGHT]);
    }

    private function addProceduresTo(Element_OphTrOperationnote_ProcedureList $procedures_element, array $procedures): void
    {
        $procedures_element->procedure_assignments = array_merge(
            $procedures_element->procedure_assignments ?? [],
            array_map(
                function ($procedure) {
                    return OphTrOperationnote_ProcedureListProcedureAssignment::factory()
                        ->make([
                            'procedurelist_id' => null,
                            'proc_id' => is_string($procedure)
                                ? Procedure::factory()->useExisting([
                                    'term' => $procedure
                                ])
                                : $procedure
                        ]);
                },
                $procedures
            )
        );
    }

    private function getUniqueProceduresFor(Element_OphTrOperationnote_ProcedureList $procedures_element, int $count = 1): array
    {
        $already_assigned_proc_ids = array_map(
            function ($assignment) {
                return $assignment->proc_id;
            },
            $procedures_element->procedure_assignments
        );

        return $this->getProceduresThatAreNotIn($already_assigned_proc_ids, $count);
    }

    private function getProceduresThatAreNotIn(array $exclude_procedure_ids, int $count = 1): array
    {
        $criteria = new \CDbCriteria();
        $criteria->addNotInCondition('id', $exclude_procedure_ids);
        $criteria->limit = $count;

        return Procedure::model()->findAll($criteria);
    }
}
