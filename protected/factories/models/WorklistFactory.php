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

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Worklist;
use WorklistAttribute;
use WorklistDefinition;
use Institution;
use PathwayType;
use PatientIdentifier;
use PatientIdentifierType;

class WorklistFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'worklist_definition_id' => WorklistDefinition::factory()->forInstitution($this->institutionWithPatientIdentifierType()),
            'start' => (new \DateTime('now'))->format('Y-m-d 00:00:00'),
            'end' => (new \DateTime('now'))->format('Y-m-d 23:59:59'),
        ];
    }

    /**
     * A state to specify the step types that should be part of the default pathway
     * for the parent worklist_definition.
     *
     * @see PathwayTypeStepFactory
     * @param array $type_short_names
     * @return self
     */
    public function withStepsOfType(array $type_short_names = []): self
    {
        return $this->state(function (array $attributes) use ($type_short_names) {
            if (($attributes['worklist_definition_id'] ?? null) instanceof ModelFactory) {
                $attributes['worklist_definition_id'] = $attributes['worklist_definition_id']
                    ->withStepsOfType($type_short_names);
            }

            return $attributes;
        });
    }

    public function withPatientAttributes($count = 1): self
    {
        return $this->afterCreating(function (Worklist $instance) use ($count) {
            return WorklistAttribute::factory()->count($count)->create(['worklist_id' => $instance]);
        });
    }

    protected function institutionWithPatientIdentifierType(): Institution
    {
        $criteria = new \CDbCriteria();

        $patientIdentifierType = PatientIdentifierType::model()->find(array('order'=>'id ASC'));

        $criteria->addColumnCondition(['id' => $patientIdentifierType->institution_id]);
        $criteria->order = 'RAND()';
        $criteria->limit = 1;

        return Institution::model()->find($criteria);
    }
}
