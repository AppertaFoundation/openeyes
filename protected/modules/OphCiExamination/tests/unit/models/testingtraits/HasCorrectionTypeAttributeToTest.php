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

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\traits\HasCorrectionType;

trait HasCorrectionTypeAttributeToTest
{
    use \HasRelationOptionsToTest;
    use \HasModelAssertions;

    /** @test */
    public function uses_has_correction_type_trait()
    {
        $uses = static::classUsesRecursive($this->getElementInstance());
        $this->assertContains(HasCorrectionType::class, $uses);
    }

    /** @test */
    public function check_correctiontype_relation_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('correctiontype', $instance->relations());
    }

    /** @test */
    public function check_correctiontype_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'correctiontype_id', CorrectionType::class);
        $this->assertContains('correctiontype_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function check_correctiontype_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'correctiontype', CorrectionType::class);
    }

}
