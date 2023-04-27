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

namespace OEModule\OphCiExamination\factories\models;

use Institution;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
use ReferenceData;

class OphCiExamination_WorkflowFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'institution_id' => Institution::factory()->useExisting(),
        ];
    }

    /**
     * Generated workflow will be at the installation level
     *
     * @return self
     */
    public function forInstallation(): self
    {
        return $this->state([
            'institution_id' => null
        ]);
    }

    /**
     * Default behaviour for factory is to create an institution level workflow for an existing institution.
     *
     * This state provides more fine grained control of what institution that is.
     *
     * @param Institution|InstitutionFactory|int|string|null $institution
     * @return OphCiExamination_WorkflowFactory
     */
    public function forInstitution($institution = null): self
    {
        $institution ??= ModelFactory::factoryFor(Institution::class);

        return $this->state([
            'institution_id' => $institution,
        ]);
    }

    public function forElementSet(?OphCiExamination_ElementSet $element_set = null): self
    {
        return $this->afterCreating(function (OphCiExamination_Workflow $workflow) use ($element_set) {
            if ($element_set) {
                $element_set->workflow_id = $workflow->id;
            } else {
                OphCiExamination_ElementSet::factory()->create(['workflow_id' => $workflow]);
            }
        });
    }
}
