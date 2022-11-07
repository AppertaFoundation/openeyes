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
use Address;

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

    /** @test */
    public function optometrist_address_shortcode()
    {
        $patient = Patient::factory()->create();
        $optometrist = Contact::factory()->ofType('Optometrist')->create();

        PatientContactAssignment::factory()->create(['patient_id' => $patient->id, 'contact_id' => $optometrist->id]);

        $this->performShortcodeTest($patient, '[pod]', implode('<br>', $optometrist->address->getLetterArray(false)));
    }
    
    /** @test */
    public function optometrist_address_shortcode_correspond_address_correctly_takes_precidence()
    {
        $patient = Patient::factory()->create();
        $optometrist = Contact::factory()->withCorrespondAddress()->ofType('Optometrist')->create();

        PatientContactAssignment::factory()->create(['patient_id' => $patient->id, 'contact_id' => $optometrist->id]);

        $this->performShortcodeTest($patient, '[pod]', implode('<br>', $optometrist->correspondAddress->getLetterArray(false)));
    }

    protected function performShortcodeTest(Patient $patient, $shortcode, $expected)
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $response = $this->actingAs(
                $user, 
                $institution
            )->post('/OphCoCorrespondence/Default/ExpandStrings', 
            [
                'patient_id' => $patient->id, 
                'text' => $shortcode
            ]);

        $this->assertEquals($expected, $response->response);
    }

    protected function createUserWithInstitution()
    {
        $user = User::model()->findByAttributes(['first_name' => 'admin']);
        return [
            $user,
            Institution::factory()
                ->withUserAsMember($user)
                ->create()
        ];
    }
}
