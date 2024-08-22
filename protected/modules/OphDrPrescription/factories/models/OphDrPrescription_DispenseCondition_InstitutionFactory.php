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

use Institution;
use OE\factories\ModelFactory;
use OphDrPrescription_DispenseCondition;

class OphDrPrescription_DispenseCondition_InstitutionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'dispense_condition_id' => OphDrPrescription_DispenseCondition::factory(),
            'institution_id' => Institution::factory()
        ];
    }

    /**
     * @param OphDrPrescription_DispenseCondition|OphDrPrescription_DispenseConditionFactory|string|int $dispense_condition
     * @return OphDrPrescription_DispenseCondition_InstitutionFactory
     */
    public function forDispenseCondition($dispense_condition): self
    {
        return $this->state([
            'dispense_condition_id' => $dispense_condition
        ]);
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     * @return OphDrPrescription_DispenseCondition_InstitutionFactory
     */
    public function forInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }
}
