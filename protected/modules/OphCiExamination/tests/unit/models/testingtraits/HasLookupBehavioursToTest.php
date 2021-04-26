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

namespace OEModule\OphCiExamination\tests\unit\models\testingtraits;

/**
 * Trait HasLookupBehavioursToTest
 *
 * Ensure that the WithFaker trait is in use for your test, and you provide a getElementInstance method
 * (which is available through ModelTestCase)
 *
 * @package OEModule\OphCiExamination\tests\unit\models\testingtraits
 *
 */
trait HasLookupBehavioursToTest
{
    protected array $required_fields = ['name', 'display_order'];

    /** @test */
    public function is_attaching_the_lookup_behaviour()
    {
        $instance = $this->getElementInstance();
        $this->assertContains(\LookupTable::class, $instance->behaviors());
    }

    /** @test */
    public function stringification()
    {
        $instance = $this->getElementInstance();
        $instance->name = $this->faker->word;

        $this->assertEquals($instance->name, (string)$instance);
    }

    /** @test */
    public function required_fields()
    {
        $instance = $this->getElementInstance();
        $rules = $instance->rules();

        $fields_with_required_rule = [];
        foreach ($rules as $rule) {
            if ($rule[1] === 'required') {
                $fields_with_required_rule = array_merge(
                    $fields_with_required_rule,
                    array_map('trim', explode(",", $rule[0]))
                );
            }
        }

        foreach ($this->required_fields as $required_field) {
            $this->assertContains($required_field, $fields_with_required_rule);
        }
    }
}