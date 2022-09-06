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

use DoctorGrade;
use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk;
use SplitEventTypeElement;

/**
 * Note: PcrRisk factory does not calculate validated PcrRisk values from random data
 */
class Element_OphCiExamination_PcrRiskFactory extends ModelFactory
{
    // risk values must always be recorded, and doctor_grade/pupil size are required by PcrRisk model
    // which is auto-saved when this element is populated
    protected array $selected_sided_attributes = ['pupil_size', 'doctor_grade_id', 'pcr_risk', 'excess_risk'];

    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiExamination'),
            'eye_id' => $this->faker->randomElement([SplitEventTypeElement::LEFT, SplitEventTypeElement::RIGHT, SplitEventTypeElement::BOTH])
        ];
    }

    public function rightSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::RIGHT
            ];
        });
    }

    public function leftSideOnly()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::LEFT
            ];
        });
    }

    public function bothSided()
    {
        return $this->state(function ($attributes) {
            return [
                'eye_id' => SplitEventTypeElement::BOTH
            ];
        });
    }

    public function withAllAnswers()
    {

        $this->selected_sided_attributes = $this->newModel()->sidedFields();

        return $this;
    }

    public function withDiabetic()
    {
        return $this->addSelectedSidedAttribute('diabetic');
    }

    protected function getUnresolvedAttributes()
    {
        // as a final state, we are getting generated data for the selected sided keys
        // this pattern may be abstracted to a final state definition in the future
        // if it transpires it's useful
        $this->state(
            function (array $attributes) {
                return array_merge(
                    // NB. Because PcrRisk is broken, we have to store data for both sides
                    // regardless of whether or not that side has explicitly been recorded
                    // a future improvement will only do this if the given side is selected
                    $this->getDataForPrefix('right_'),
                    $this->getDataForPrefix('left_'),
                    // provide the given attributes last, because this ensures specific values
                    // provided for the definition take precedence
                    $attributes
                );
            }
        );

        return parent::getUnresolvedAttributes();
    }

    protected function addSelectedSidedAttribute($attribute)
    {
        $this->selected_sided_attributes = array_unique(array_merge($this->selected_sided_attributes, [$attribute]));

        return $this;
    }

    protected function getDataForPrefix($prefix)
    {
        $attributes = array_filter(
            $this->getSidedAttributeDefinitions(),
            function ($key) {
                return in_array($key, $this->selected_sided_attributes);
            },
            ARRAY_FILTER_USE_KEY
        );


        foreach ($attributes as $attribute => $value) {
            $attributes[$prefix . $attribute] = $value;
            unset($attributes[$attribute]);
        }

        return $attributes;
    }

    protected function getSidedAttributeDefinitions()
    {
        return [
            'glaucoma' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'pxf' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'diabetic' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'pupil_size' => $this->faker->randomElement(['Large', 'Medium', 'Small']),
            'no_fundal_view' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'axial_length_group' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'brunescent_white_cataract' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'alpha_receptor_blocker' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'doctor_grade_id' => ModelFactory::factoryFor(DoctorGrade::class)->useExisting(),
            'can_lie_flat' => $this->faker->randomElement(['NK', 'N', 'Y']),
            'pcr_risk' => $this->faker->randomFloat(2, 0.1, 20),
            'excess_risk' => $this->faker->randomFloat(2, 0.1, 20)
        ];
    }
}
