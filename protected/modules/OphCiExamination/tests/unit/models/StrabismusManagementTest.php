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

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\StrabismusManagement;
use OEModule\OphCiExamination\models\StrabismusManagement_Entry;
use OEModule\OphCiExamination\tests\traits\InteractsWithStrabismusManagement;

/**
 * Class StrabismusManagementTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\StrabismusManagement
 * @group sample-data
 * @group strabismus
 * @group strabismus-management
 */
class StrabismusManagementTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithStrabismusManagement;

    protected $element_cls = StrabismusManagement::class;

    /** @test */
    public function check_entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('entries', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['entries'][0]);
        $this->assertEquals(StrabismusManagement_Entry::class, $relations['entries'][1]);
    }

    /** @test */
    public function check_attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertContains('entries', $safe);
    }

    /** @test */
    public function entries_required_when_comments_not_set()
    {
        $instance = $this->getElementInstance();
        $instance->entries = [];

        $this->assertAttributeInvalid($instance, 'entries', 'cannot be blank');
    }

    /** @test */
    public function entries_not_required_when_comments_set()
    {
        $instance = $this->getElementInstance();
        $instance->comments = $this->faker->words(7, true);

        $this->assertAttributeValid($instance, 'entries');
    }

    /** @test */
    public function valid_with_valid_entries()
    {
        $instance = $this->getElementInstance();
        $instance->entries = [$this->createValidatingModelMock(StrabismusManagement_Entry::class)];

        $this->assertTrue($instance->validate());
    }

    /** @test */
    public function invalid_with_invalid_entry()
    {
        $instance = $this->getElementInstance();
        $instance->entries = [
            $this->createValidatingModelMock(StrabismusManagement_Entry::class),
            $this->createInvalidModelMock(StrabismusManagement_Entry::class)
        ];

        $this->assertFalse($instance->validate());
    }

    public function letter_string_provider()
    {
        return [
            [
                [
                    'foo'
                ],
                null,
                'Strabismus Management: foo'
            ],
            [
                [
                    'foo', 'bar', 'moo'
                ],
                null,
                'Strabismus Management: foo, bar, moo'
            ],
            [
                [

                ],
                null,
                'Strabismus Management: No entries'
            ],
            [
                [
                    'this', 'has', 'a', 'comment'
                ],
                "I am a comment",
                'Strabismus Management: this, has, a, comment I am a comment'
            ],
        ];
    }

    /**
     * @param $entry_strings
     * @param $comments
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($entry_strings, $comments, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->entries = array_map(
            function ($entry_string) {
                $entry = $this->createMock(StrabismusManagement_Entry::class);
                $entry->method('__toString')
                    ->willReturn($entry_string);
                return $entry;
            },
            $entry_strings
        );

        $instance->comments = $comments;

        $this->assertEquals($expected, $instance->letter_string);
    }
}
