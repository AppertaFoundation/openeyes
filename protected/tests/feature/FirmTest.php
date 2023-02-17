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

use Firm;
use ModelTestCase;

/**
 * @group sample-data
 * @group feature
 * @group firm
 */
class FirmTest extends ModelTestCase
{
    protected $element_cls = Firm::class;

    /** @test */
    public function update_duplicated_firm_should_not_pass_the_validation()
    {
        $original_firm = Firm::factory()->create();

        $firm_form_update = Firm::factory()->create([
            "institution_id" => $original_firm->institution_id,
            "subspecialty_id" => $original_firm->subspecialty_id
        ]);

        $firm_form_update->name = $original_firm->name;

        $this->assertAttributeInvalid($firm_form_update, 'name', 'already exists');
    }
}
