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

namespace OEModule\OphCiExamination\tests\traits;

use OEModule\OphCiExamination\models\CorrectionGiven;

trait InteractsWithCorrectionGiven
{
    use \WithFaker;
    use \InteractsWithEventTypeElements;

    protected function generateSavedCorrectionGiven($attrs = [])
    {
        $element = new CorrectionGiven();
        if (!isset($attrs['eye_id'])) {
            $element->setHasLeft();
            $element->setHasRight();
        }
        $element->setAttributes($this->generateCorrectionGivenData($attrs));

        return $this->saveElement($element);
    }

    protected function generateCorrectionGivenData($attrs = [])
    {
        return array_merge(
            $this->generateCorrectionGivenDataForSide(
                'right',
                array_filter(
                    $attrs,
                    function ($value, $key) {
                        return str_starts_with($key, 'right');
                    },
                    ARRAY_FILTER_USE_BOTH
                )
            ),
            $this->generateCorrectionGivenDataForSide(
                'left',
                array_filter(
                    $attrs,
                    function ($value, $key) {
                        return str_starts_with($key, 'left');
                    },
                    ARRAY_FILTER_USE_BOTH
                )
            )
        );
    }

    protected function generateCorrectionGivenDataForSide($side, $attrs = [])
    {
        $as_found = $attrs["{$side}_as_found"] ?? $this->faker->boolean();
        return array_merge(
            [
                "{$side}_as_found" => (bool) $as_found ? '1' : '0',
                "{$side}_as_found_element_type_id" => $as_found ? $this->getValidAsFoundElementType()->id : null,
                "{$side}_refraction" => $this->fakeRefraction()
            ],
            $attrs
        );
    }

    protected function getInvalidAsFoundElementType($count = 1)
    {
        $exclude_valid_types_criteria = new \CDbCriteria();
        $exclude_valid_types_criteria->addNotInCondition(
            'class_name',
            CorrectionGiven::SOURCE_ELEMENT_TYPES
        );
        return $this->getRandomLookup(\ElementType::class, $count, $exclude_valid_types_criteria);
    }

    protected function getValidAsFoundElementType($count = 1)
    {
        $valid_types_criteria = new \CDbCriteria();
        $valid_types_criteria->addInCondition(
            'class_name',
            CorrectionGiven::SOURCE_ELEMENT_TYPES
        );
        return $this->getRandomLookup(\ElementType::class, $count, $valid_types_criteria);
    }
}
