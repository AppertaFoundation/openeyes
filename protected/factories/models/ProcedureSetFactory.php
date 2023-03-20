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

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Procedure;
use ProcedureSet;
use ProcedureSetAssignment;

class ProcedureSetFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
        ];
    }

    public function create($attributes = [])
    {
        $this->afterCreating(function (ProcedureSet $procedure_set) {
            foreach ($procedure_set->procedure_assignments as $procedure_assignment) {
                $procedure_assignment->proc_set_id = $procedure_set->id;
                $procedure_assignment->save();
            }
        });

        return parent::create($attributes);
    }

    /**
     * State to get a ProcedureSet with the given procedures:
     *
     * int - that many procedures will the assigned
     * string - that procedure will be found or created
     * array - of procedures or procedure factories (which will be resolved)
     *
     * If a ProcedureSet already exists with the given procedure selection, that will be returned, rather
     * than a new one being created.
     *
     * @param mixed $procedures
     * @return ProcedureSetFactory
     */
    public function withProcedures($procedures): self
    {
        if (is_int($procedures)) {
            return $this->createWithProcedures($procedures);
        }
        if (!is_array($procedures)) {
            $procedures = [$procedures];
        }

        $resolved_procedures = array_map(
            function ($procedure) {
                if ($procedure instanceof Procedure) {
                    return $procedure;
                }
                if (is_string($procedure)) {
                    return $this->resolveProcedureFromName($procedure);
                }
                return $procedure->make();
            },
            $procedures
        );

        $this->findOrCreateAttributes = ['procedures' => $resolved_procedures];

        return $this;
    }

    /**
     * Force creation of ProcedureSet with the given procedures
     *
     * @param Procedure|int|string|array $procedures
     * @return self
     */
    public function createWithProcedures($procedures): self
    {
        // force reset of looking for existing model
        $this->findOrCreateAttributes = null;

        return $this->afterMaking(function (ProcedureSet $procedure_set) use ($procedures) {
            if (is_int($procedures)) {
                $procedures = $this->getUniqueProceduresFor($procedure_set, $procedures);
            }
            if (!is_array($procedures)) {
                $procedures = [$procedures];
            }
            $this->addProceduresTo($procedure_set, $procedures);
        });
    }

    protected function getExisting($attributes = [])
    {
        if (array_key_exists('procedures', $attributes)) {
            $ids = array_map(
                function ($procedure) {
                    return $procedure->getPrimaryKey();
                },
                $attributes['procedures']
            );

            if ($this->count) {
                return ProcedureSet::findForProcedures($ids, true);
            }
            return [ProcedureSet::findForProcedures($ids)];
        }

        return parent::getExisting($attributes);
    }

    private function addProceduresTo(ProcedureSet $procedure_set, array $procedures): void
    {
        $procedure_set->procedure_assignments = array_merge(
            $procedure_set->procedure_assignments ?? [],
            array_map(
                function ($procedure) {
                    return ProcedureSetAssignment::factory()
                        ->make([
                            'proc_set_id' => null,
                            'proc_id' => is_string($procedure)
                                ? $this->resolveProcedureFromName($procedure)
                                : $procedure
                        ]);
                },
                $procedures
            )
        );
    }

    private function getUniqueProceduresFor(ProcedureSet $procedure_set, int $count = 1): array
    {
        $already_assigned_proc_ids = array_map(
            function ($assignment) {
                return $assignment->proc_id;
            },
            $procedure_set->procedure_assignments
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

    private function resolveProcedureFromName($name): ?Procedure
    {
        return Procedure::model()->find(
            'term = :name or short_format = :name or snomed_term = :name or snomed_code = :name or aliases like :search',
            [':name' => $name, ':search' => "%{$name}%"]
        );
    }
}
