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

trait InteractsWithCommonPreviousSystemicOperation
{
    use WithFaker;

    public function generateCommonPreviousSystemicOperation(?array $attributes = []): CommonPreviousSystemicOperation
    {
        $instance = new CommonPreviousSystemicOperation();
        $instance->setAttributes($this->generateCommonPreviousSystemicOperationData($attributes));
        $instance->save();

        return $instance;
    }

    public function generateCommonPreviousSystemicOperationData(?array $attributes = []): array
    {
        return array_merge(
            [
                'name' => $this->faker->words(2, true),
                'display_order' => $this->faker->randomDigit()
            ],
            $attributes
        );
    }

    public function generateCommonPreviousSystemicOperationForInstitution(
        Institution $institution,
        ?array $attributes = []
    ): CommonPreviousSystemicOperation {
        $instance = $this->generateCommonPreviousSystemicOperation($attributes);

        $this->attachCommonPreviousSystemicOperationToInstitution($instance, $institution);

        return $instance;
    }

    public function attachCommonPreviousSystemicOperationToInstitution(
        CommonPreviousSystemicOperation $commonPreviousSystemicOperation,
        Institution $institution
    ) {
        $link = new CommonPreviousSystemicOperation_Institution();
        $link->setAttributes([
            'common_previous_systemic_operation_id' => $commonPreviousSystemicOperation->id,
            'institution_id' => $institution->id
        ]);
        $link->save();
    }
}
