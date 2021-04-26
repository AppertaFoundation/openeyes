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

use OEModule\OphCiExamination\models\PrismFusionRange;
use OEModule\OphCiExamination\models\PrismFusionRange_Entry;
use OEModule\OphCiExamination\models\traits\HasCorrectionType;
use OEModule\OphCiExamination\tests\traits\InteractsWithPrismFusionRange;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class PrismFusionRangeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\PrismFusionRange
 * @group sample-data
 * @group strabismus
 * @group prism-fusion-range
 * @group pfr
 */
class PrismFusionRangeTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use HasWithHeadPostureEntriesToTest;

    protected $element_cls = PrismFusionRange::class;

    /** @test */
    public function check_entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('entries', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['entries'][0]);
        $this->assertEquals(PrismFusionRange_Entry::class, $relations['entries'][1]);
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
    public function at_least_one_entry_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'entries', 'cannot be blank');
        $instance->entries = [$this->createValidatingModelMock(PrismFusionRange_Entry::class)];
        $this->assertTrue($instance->validate('entries'));
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $entry = new PrismFusionRange_Entry();
        $entry->with_head_posture = PrismFusionRange_Entry::$WITH_HEAD_POSTURE;
        $instance->entries = [$entry];
        return [$instance, 'entries.0'];
    }
}
