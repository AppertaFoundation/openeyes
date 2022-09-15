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

namespace OEModule\OphCiExamination\tests\unit\models;

use ModelTestCase;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\AllergyEntry;
use OEModule\OphCiExamination\models\OphCiExaminationAllergyReaction;

/**
 * Class OphCiExaminationAllergyReactionTest
 * @covers OphCiExaminationAllergyReaction
 * @group sample-data
 * @group examination
 * @group allergies
 */
class OphCiExaminationAllergyReactionTest extends ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = OphCiExaminationAllergyReaction::class;

    /** @test */
    public function in_use_is_false_when_no_entries_are_assigned()
    {
        $reaction = ModelFactory::factoryFor(OphCiExaminationAllergyReaction::class)->create();

        $this->assertFalse($reaction->isInUse());
    }

    /** @test */
    public function in_use_is_true_when_no_entries_are_assigned()
    {
        $reaction = ModelFactory::factoryFor(OphCiExaminationAllergyReaction::class)->create();
        ModelFactory::factoryFor(AllergyEntry::class)->withReaction($reaction)->create();

        $this->assertTrue($reaction->isInUse());
    }
}
