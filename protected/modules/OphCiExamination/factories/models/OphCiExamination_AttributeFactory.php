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

namespace OEModule\OphCiExamination\factories\models;

use ElementType;
use Institution;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeElement;
use OE\factories\models\traits\MapsDisplayOrderForFactory;
use OEModule\OphCiExamination\models\OphCiExamination_Attribute;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeOption;

class OphCiExamination_AttributeFactory extends ModelFactory
{
    use MapsDisplayOrderForFactory;

    protected ?ElementType $for_element_type = null;
    protected int $prepopulated_option_count = 0;
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'label' => $this->faker->words(2, true),
            'institution_id' => Institution::factory()
        ];
    }

    public function withInstitution($institution = null): self
    {
        $institution ??= \Institution::factory();

        return $this->state([
            'institution_id' => $institution
        ]);
    }

    public function withInstallation(): self
    {
        return $this->state([
            'institution_id' => null
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function ($instance) {
            if (!$this->for_element_type) {
                $this->for_element_type = $this->getValidElementType();
            }

            $attribute_element = OphCiExamination_AttributeElement::factory()->create([
                'attribute_id' => $instance->id,
                'element_type_id' => $this->for_element_type->id
            ]);

            $instance->attribute_elements = [$attribute_element];

            for ($i = 0; $i < $this->prepopulated_option_count; $i++) {
                OphCiExamination_AttributeOption::factory()
                    ->create([
                        'value' => $this->faker->words(2, true),
                        'attribute_element_id' => $attribute_element->id
                    ]);
            }
        });
    }

    public function forElementType($element_type_class = null)
    {
        $this->for_element_type = $this->getValidElementType($element_type_class);
        return $this;
    }

    public function withOptionCount($count = 3)
    {
        $this->prepopulated_option_count = $count;
        return $this;
    }

    protected function getValidElementType(string $element_type_class = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = "JOIN event_type et ON et.id = t.event_type_id";
        $criteria->addCondition("et.class_name = :event_type_class");
        $params = [':event_type_class' => 'OphCiExamination'];

        if ($element_type_class) {
            $criteria->addCondition('t.class_name = :element_type_class');
            $params[':element_type_class'] = $element_type_class;
        }
        $criteria->params = $params;
        $criteria->order = 'RAND()';

        return ElementType::model()->find($criteria);
    }
}
