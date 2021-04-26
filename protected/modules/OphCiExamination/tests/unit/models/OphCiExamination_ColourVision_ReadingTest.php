<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Value;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class OphCiExamination_ColourVision_ReadingTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading
 * @group sample-data
 * @group strabismus
 * @group colour-vision
 */
class OphCiExamination_ColourVision_ReadingTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use HasCorrectionTypeAttributeToTest;

    protected $element_cls = OphCiExamination_ColourVision_Reading::class;

    /** @test */
    public function method_property_is_derived_from_value_assignment()
    {
        $instance = $this->getElementInstance();
        $this->assertNull($instance->method);

        $value = $this->getRandomLookup(OphCiExamination_ColourVision_Value::class);
        $instance->value = $value;
        $this->assertEquals($value->method_id, $instance->method->id);
    }
}
