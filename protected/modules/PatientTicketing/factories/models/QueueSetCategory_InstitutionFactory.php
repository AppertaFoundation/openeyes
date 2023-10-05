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

namespace OEModule\PatientTicketing\factories\models;

use Institution;
use OE\factories\ModelFactory;
use OEModule\PatientTicketing\models\QueueSetCategory;

class QueueSetCategory_InstitutionFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            "category_id" => QueueSetCategory::factory(),
            "institution_id" => Institution::factory()
        ];
    }

    /**
     * @param QueueSetCategory|string|int $category
     * @return QueueSetCategory_InstitutionFactory
     */
    public function forCategory($category): self
    {
        return $this->state([
            'category_id' => $category
        ]);
    }

    /**
     * @param Institution|string|int $institution
     * @return QueueSetCategory_InstitutionFactory
     */
    public function forInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }
}
