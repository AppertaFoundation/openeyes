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

namespace OEModule\OphCoCorrespondence\tests\unit;

use ModelCollection;
use OE\factories\ModelFactory;
use Institution;
use User;
use Patient;
use PatientContactAssignment;
use Contact;
use OELog;

/**
 * @package OEModule\OphCoCorrespondence\tests\unit
 * 
 * class ColourVisionTest
 * 
 * @group sample-data
 * @group shortcode
 */
class OptometristShortcodeTest extends \OEDbTestCase
{
    use \MakesApplicationRequests;

    private User $user;
    private Institution $institution;

    public function setUp()
    {
        $this->createUserWithInstitution();
    }

    /** @test */
    public function optometrist_address_shortcode()
    {
        OELog::log('sausage');

        $patient = Patient::factory()->create();
        $optometrist = Contact::factory()->ofType('Optometrist')->create();
        
        PatientContactAssignment::factory()->create(['patient_id' => $patient->id, 'contact_id' => $optometrist->id]);

        $optometrist_address = $optometrist->correspondAddress ?? $optometrist->address;
        
        $this->performShortcodeTest($patient, '[pod]', implode('<br>', $optometrist_address->getLetterArray(false)));
    }

    protected function performShortcodeTest(Patient $patient, $shortcode, $expected)
    {
        $response = $this->actingAs($this->user, $this->institution)->post('/OphCoCorrespondence/Default/ExpandStrings', ['patient_id' => $patient->id, 'text' => $shortcode]);
                        
        $this->assertEquals($expected, $response->response);
    }

    protected function createUserWithInstitution()
    {
        $this->user = User::model()->findByAttributes(['first_name' => 'admin']);

        $this->institution = Institution::factory()
            ->withUserAsMember($this->user)
            ->create();
    }
}