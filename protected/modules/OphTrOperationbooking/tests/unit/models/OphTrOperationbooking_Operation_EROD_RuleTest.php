<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_Operation_EROD_RuleTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'specialties' => 'Specialty',
        'subspecialties' => 'Subspecialty',
        'service_subspecialty_assignment' => 'ServiceSubspecialtyAssignment',
        'firms' => 'Firm',
    );

    public function getModel()
    {
        return OphTrOperationbooking_Operation_EROD_Rule::model();
    }

    public static function setUpBeforeClass(): void
    {
        date_default_timezone_set('UTC');
    }

    public function testNoItemsRaisesError()
    {
        $test = new OphTrOperationbooking_Operation_EROD_Rule();
        $test->subspecialty_id = $this->subspecialties('subspecialty1')->id;
        $this->assertFalse($test->validate());
        $errs = $test->getErrors();
        $this->assertArrayHasKey('items', $errs);
    }

    public function testCanValidateWithItem()
    {
        $test = new OphTrOperationbooking_Operation_EROD_Rule();
        $test->subspecialty_id = $this->subspecialties('subspecialty1')->id;
        $item = new OphTrOperationbooking_Operation_EROD_Rule_Item();
        $item->item_type = 'firm';
        $item->item_id = $this->firms('firm1')->id;
        $test->items = array($item);
        $this->assertTrue($test->validate());
    }
}
