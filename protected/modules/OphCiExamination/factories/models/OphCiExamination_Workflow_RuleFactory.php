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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\factories\models;

use ElementType;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\OphCiExamination_Attribute;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;

class OphCiExamination_Workflow_RuleFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'workflow_id' => OphCiExamination_Workflow::factory()->useExisting(),
            'subspecialty_id' => \Subspecialty::factory()->useExisting()
        ];
    }

    /**
     * @param OphCiExamination_Workflow|int $workflow
     * @return self
     */
    public function forWorkflow($workflow)
    {
        return $this->state([ 'workflow_id' => $workflow ]);
    }

    /**
     * @param Subspecialty|int $subspecialty
     * @return self
     */
    public function forSubspecialty($subspecialty)
    {
        return $this->state([ 'subspecialty_id' => $subspecialty ]);
    }

    /**
     * @param Firm|int $firm
     * @return self
     */
    public function forFirm($firm)
    {
        return $this->state([ 'firm_id' => $firm ]);
    }
}
