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

namespace OEModule\OphDrPGDPSD\factories\models;

use OE\factories\ModelFactory;

use Medication;
use MedicationRoute;
use MedicationAttributeOption;

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_PGDPSD;

class OphDrPGDPSD_PGDPSDMedsFactory extends ModelFactory
{
    protected static $dose_unit_terms = null;

    public function definition(): array
    {
        return [
            'pgdpsd_id' => OphDrPGDPSD_PGDPSD::factory(),
            'medication_id' => Medication::factory(),
            'dose' => $this->faker->numberBetween(1, 100),
            'dose_unit_term' => $this->faker->randomElement($this->getDoseUnitTerms()),
            'route_id' => MedicationRoute::factory()->useExisting()
        ];
    }

    /**
     * @param OphDrPGDPSD_PGDPSD|OphDrPGDPSD_PGDPSDFactory|string|int $pgdpsd
     * @return OphDrPGDPSD_PGDPSDMedsFactory
     */
    public function forPGDPSD($pgdpsd = null): self
    {
        $pgdpsd ??= OphDrPGDPSD_PGDPSD::factory();

        return $this->state([
            'pgdpsd_id' => $pgdpsd
        ]);
    }

    protected function getDoseUnitTerms(): array
    {
        if (empty(static::$dose_unit_terms)) {
            static::$dose_unit_terms = MedicationAttributeOption::model()->with('medicationAttribute')->findAll('medicationAttribute.name = ?', [Medication::ATTR_UNIT_OF_MEASURE]);

            static::$dose_unit_terms = array_map(static function ($term) {
                return $term->description;
            }, static::$dose_unit_terms);
        }

        return static::$dose_unit_terms;
    }
}
