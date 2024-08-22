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


trait InteractsWithCommonPreviousOperation
{
    use \WithFaker;

    public function generateCommonPreviousOperation(?array $attributes = []): CommonPreviousOperation
    {
        $instance = new CommonPreviousOperation();
        $instance->setAttributes($this->generateCommonPreviousOperationData($attributes));
        $instance->save();

        return $instance;
    }

    public function generateCommonPreviousOperationData(?array $attributes = []): array
    {
        return array_merge(
            [
                'name' => $this->faker->words(2, true)
            ],
            $attributes
        );
    }

    public function generateCommonPreviousOperationForInstitution(Institution $institution, ?array $attributes = []): CommonPreviousOperation
    {
        $instance = $this->generateCommonPreviousOperation($attributes);

        $this->attachCommonPreviousOperationToInstitution($instance, $institution);

        return $instance;
    }

    public function attachCommonPreviousOperationToInstitution(CommonPreviousOperation $commonPreviousOperation, Institution $institution)
    {
        $link = new CommonPreviousOperation_Institution();
        $link->setAttributes([
            'common_previous_operation_id' => $commonPreviousOperation->id,
            'institution_id' => $institution->id
        ]);
        $link->save();
    }
}
