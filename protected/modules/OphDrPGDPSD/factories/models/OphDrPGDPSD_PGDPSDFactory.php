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
namespace OEModule\OphDrPGDPSD\factories\models;

use Institution;
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OELog;
use OEModule\OphDrPGDPSD\models\{
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_PGDPSDMeds,
    OphDrPGDPSD_Assignment,
    OphDrPGDPSD_AssignedTeam
};
use Patient;
use WorklistPatient;

class OphDrPGDPSD_PGDPSDFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(3),
            'institution_id' => Institution::factory()->useExisting(),
            'type' => $this->faker->randomElement(['PSD', 'PGD']),
        ];
    }

    /**
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function psd(): self
    {
        return $this->state([
            'type' => 'PSD'
        ]);
    }

    /**
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function pgd(): self
    {
        return $this->state([
            'type' => 'PGD'
        ]);
    }

    /**
     * @param Institution|InstitutionFactory|int|string $institution
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function forInstitution($institution): self
    {
        $institution ??= Institution::factory();

        return $this->state([
            'institution_id' => $institution
        ]);
    }

    /**
     * @param array $users An array of [User|UserFactory|string|int|null, ...]
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function forUsers($users): self
    {
        return $this->afterCreating(static function (OphDrPGDPSD_PGDPSD $pgdpsd) use ($users) {
            array_map(static function ($user) use ($pgdpsd) {
                return OphDrPGDPSD_AssignedUser::factory()->forPGDPSD($pgdpsd)->forUser($user)->create();
            }, $users);
        });
    }

    /**
     * @param array $teams An array of [Team|TeamFactory|string|int|null, ...]
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function forTeams($teams): self
    {
        return $this->afterCreating(static function (OphDrPGDPSD_PGDPSD $pgdpsd) use ($teams) {
            array_map(static function ($team) use ($pgdpsd) {
                return OphDrPGDPSD_AssignedTeam::factory()->forPGDPSD($pgdpsd)->forTeam($team)->create();
            }, $teams);
        });
    }

    /**
     * @param int $count
     * @return OphDrPGDPSD_PGDPSDFactory
     */
    public function withMeds($count = 1): self
    {
        return $this->afterCreating(function (OphDrPGDPSD_PGDPSD $pgdpsd) use ($count) {
            $pgdpsd->assigned_meds = OphDrPGDPSD_PGDPSDMeds::factory()
                ->forPGDPSD($pgdpsd)
                ->count($count)
                ->create();

            $pgdpsd->save(false);
        });
    }
}
