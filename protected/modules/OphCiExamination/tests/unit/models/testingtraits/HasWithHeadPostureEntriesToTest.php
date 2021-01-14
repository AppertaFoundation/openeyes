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

use OEModule\OphCiExamination\models\HeadPosture;

/**
 * Trait HasWithHeadPostureEntriesToTest
 *
 * Standard tests for elements that have child records with head posture
 * and therefore may require the HeadPosture element to be recorded in the event
 *
 * @package OEModule\OphCiExamination\tests\unit\models\testingtraits
 */
trait HasWithHeadPostureEntriesToTest
{
    public function assertInstanceHasErrorsWithoutHeadPostureElement($instance, $expected_error_attribute)
    {
        $this->assertEmpty($instance->getErrors($expected_error_attribute));
        $instance->eventScopeValidation([]);
        $this->assertCount(1, $instance->getErrors($expected_error_attribute));
    }

    public function assertInstanceDoesNotHaveErrorsWithHeadPostureElement($instance, $expected_valid_attribute)
    {
        $this->assertEmpty($instance->getErrors($expected_valid_attribute));
        $instance->eventScopeValidation([new HeadPosture()]);
        $this->assertEmpty($instance->getErrors($expected_valid_attribute));
    }

    /** @test */
    public function entries_event_scope_validation_fail_when_head_posture_required()
    {
        [$instance, $entry_attribute] = $this->getElementInstanceWithHeadPostureEntry();
        $this->assertInstanceHasErrorsWithoutHeadPostureElement($instance, $entry_attribute);
    }

    /** @test */
    public function entries_event_scope_validation_success_when_head_posture_required()
    {
        [$instance, $entry_attribute] = $this->getElementInstanceWithHeadPostureEntry();
        $this->assertInstanceDoesNotHaveErrorsWithHeadPostureElement($instance, $entry_attribute);
    }

}