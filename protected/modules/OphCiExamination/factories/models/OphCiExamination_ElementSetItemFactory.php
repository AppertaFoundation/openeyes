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

namespace OEModule\OphCiExamination\factories\models;

use ElementType;
use EventType;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\components\ExaminationHelper;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;

class OphCiExamination_ElementSetItemFactory extends ModelFactory
{
    protected static ?array $valid_element_types = null;

    public function definition(): array
    {
        return [
            'set_id' => OphCiExamination_ElementSet::factory(),
            'element_type_id' => function (array $attributes) {
                return $this->faker->randomElement($this->validElementTypes());
            }
        ];
    }

    /**
     * Associate with specific ElementSet
     *
     * @param OphCiExamination_ElementSet|OphCiExamination_ElementSetFactory $element_set
     * @return self
     */
    public function forElementSet(OphCiExamination_ElementSet|OphCiExamination_ElementSetFactory $element_set): self
    {
        return $this->state([
            'set_id' => $element_set
        ]);
    }

    /**
     * Mark the item mandatory
     *
     * @return self
     */
    public function mandatory(): self
    {
        return $this->state([
            'is_mandatory' => true
        ]);
    }

    /**
     * Create item for specific element class
     *
     * @param string $element_class
     * @return self
     */
    public function forElementClass(string $element_class): self
    {
        return $this->state([
            'element_type_id' => $this->getElementTypeForClass($element_class)
        ]);
    }

    protected function getElementTypeForClass(string $element_class): ElementType
    {
        $matched = array_filter(
            $this->validElementTypes(),
            function ($element_type) use ($element_class) {
                return $element_type->class_name === $element_class;
            }
        );
        if (count($matched) !== 1) {
            throw new \RuntimeException("Cannot find valid element type for class $element_class");
        }

        return array_pop($matched);
    }

    protected function validElementTypes()
    {
        if (self::$valid_element_types === null) {
            $filter_list = ExaminationHelper::elementFilterList();

            self::$valid_element_types = array_filter(
                EventType::model()
                    ->findByAttributes(['class_name' => 'OphCiExamination'])
                    ->getAllElementTypes(),
                function ($element_type) use ($filter_list) {
                    return !in_array($element_type->class_name, $filter_list);
                }
            );
        }

        return self::$valid_element_types;
    }
}
