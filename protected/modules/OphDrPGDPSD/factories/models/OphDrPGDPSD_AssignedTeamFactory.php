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

use OE\factories\ModelFactory;

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_PGDPSD;
use Team;

class OphDrPGDPSD_AssignedTeamFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'pgdpsd_id' => OphDrPGDPSD_PGDPSD::factory(),
            'team_id' => Team::factory()
        ];
    }

    /**
     * @param OphDrPGDPSD_PGDPSD|OphDrPGDPSD_PGDPSDFactory|string|int|null $pgdpsd
     * @return OphDrPGDPSD_AssignedTeamFactory
     */
    public function forPGDPSD($pgdpsd = null): self
    {
        $pgdpsd ??= OphDrPGDPSD_PGDPSD::factory();

        return $this->state([
            'pgdpsd_id' => $pgdpsd
        ]);
    }

    /**
     * @param Team|TeamFactory|string|int|null $team
     * @return OphDrPGDPSD_AssignedTeamFactory
     */
    public function forTeam($team = null): self
    {
        $team ??= Team::factory();

        return $this->state([
            'team_id' => $team
        ]);
    }
}
