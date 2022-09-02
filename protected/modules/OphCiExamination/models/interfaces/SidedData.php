<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models\interfaces;

/**
 * Interface SidedData
 *
 * Provides constants for left and right data, and methods
 * for tracking side attributes.
 *
 * @package OEModule\OphCiExamination\models\interfaces
 */
interface SidedData
{
    const LEFT = 1;
    const RIGHT = 2;
    const BOTH = 3;

    /**
     * An array of field suffixes that we should treat as "sided".
     * e.g. 'example' would indicate 'left_example' and 'right_example'.
     *
     * The optional $side attribute allows the fields to be filtered
     * depending on the side required.
     *
     * @param null $side
     * @return array:
     */
    public function sidedFields(?string $side = null): array;

    /**
     * An associative array of field suffixes and their default values.
     * Used for initialising sided fields.
     *
     * @return array
     */
    public function sidedDefaults(): array;
}
