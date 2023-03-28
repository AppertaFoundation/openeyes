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

namespace OEModule\OphGeneric\tests\unit\models;

use OELog;
use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\HFAEntry;

/**
 * Class BirthHistoryTest
 *
 * @package OEModule\OphGeneric\tests\unit\models
 * @covers \OEModule\OphGeneric\models\HFA
 * @group sample-data
 * @group ophgeneric
 * @group visual-fields
 * @group hfa
 */
class HFATest extends \ModelTestCase
{
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = HFA::class;

    /** @test */
    public function validation_error_is_set_when_no_hfa_entries_set()
    {
        $instance = new HFA();

        $instance->hfaEntry = [];

        $result = $instance->validate();

        $this->assertFalse($result);
    }

    /**
     * @return array
     */
    public function hfaentry_attribute_values()
    {
        return [
            'one hfa entry is valid' => [
                [
                    HFAEntry::factory()->create()
                ]
            ],
            'two hfa entres are valid' => [
                [
                    HFAEntry::factory()->create(),
                    HFAEntry::factory()->create()
                ]
            ],
        ];
    }

    /**
     * @param $attr
     * @test
     * @dataProvider hfaentry_attribute_values
     */
    public function no_validation_error_when_hfa_entry_set(array $attr)
    {
        $instance = new HFA();

        $instance->hfaEntry = $attr;

        $this->assertTrue($instance->validate());
    }
}
