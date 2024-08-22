<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use Site;
use Subspecialty;
use Firm;
use Period;
use OEModule\OphCiExamination\models\DischargeDestination;
use OEModule\OphCiExamination\models\DischargeStatus;
use OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Risk_Status;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role;

class ClinicOutcomeEntryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            "element_id" => Element_OphCiExamination_ClinicOutcome::factory(),
            "status_id" => OphCiExamination_ClinicOutcome_Status::factory(),
        ];
    }

    /**
     * @param Element_OphCiExamination_ClinicOutcome|int|string $element
     * @return self
     */
    public function forElement($element): self
    {
        return $this->state([
            "element_id" => $element
        ]);
    }

    public function followUpType(): self
    {
        return $this->state([
            "status_id" => OphCiExamination_ClinicOutcome_Status::factory()->followUpType(),
            "site_id" => Site::factory(),
            "subspecialty_id" => Subspecialty::factory(),
            "context_id" => Firm::factory(),
            "risk_status_id" => OphCiExamination_ClinicOutcome_Risk_Status::factory(),
            "followup_quantity" => $this->faker->numberBetween(1, 18),
            "followup_period_id" => Period::factory(),
            "followup_comments" => $this->faker->sentence(3),
            "role_id" => OphCiExamination_ClinicOutcome_Role::factory(),
        ]);
    }

    public function dischargeType(): self
    {
        return $this->state([
            "status_id" => OphCiExamination_ClinicOutcome_Status::factory()->dischargeType(),
            "discharge_status_id" => DischargeStatus::factory(),
            "discharge_destination_id" => DischargeDestination::factory(),
        ]);
    }
}
