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

namespace OEModule\OphCiExamination\tests\feature\admin;

use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;
use Institution;
use Subspecialty;

/**
 * Class AdminControllerTest
 *
 * @covers OEModule\OphCiExamination\controllers\AdminController
 * @covers \models\Complication
 * @group sample-data
 * @group admin
 * @group examination-admin-controller
 */
class PostOpComplicationsTest extends \OEDbTestCase
{
    use \HasModelAssertions;
    use \WithTransactions;
    use \MakesApplicationRequests;

    /** @test */
    public function post_op_complications_are_saved_by_institution_and_subspeciality()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $subspecialty = Subspecialty::factory()->create();
        $complications = OphCiExamination_PostOpComplications::factory()
            ->count(rand(1, 4))
            ->useExisting()
            ->create();

        $complication_ids = array_map(function ($complication) {
            return $complication->id;
        }, $complications);

        $form_data = [
            'institution_id' => $institution->id,
            'subspecialty_id' => $subspecialty->id,
            'complication_ids' => $complication_ids
        ];

        $this->assertCount(0, OphCiExamination_PostOpComplications::model()->enabled($institution->id, $subspecialty->id)->findAll());

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/admin/updatePostOpComplications', $form_data);

        $response->assertRedirect();

        // note we use the enabled scope on the model here, because the criteria used for generating the
        // dropdown list in the UI is far more complex. This suffices to validate that the right information
        // is being saved.
        $this->assertModelArraysMatch(
            $complications,
            OphCiExamination_PostOpComplications::model()->enabled($institution->id, $subspecialty->id)->findAll()
        );
    }

    protected function createUserWithInstitution()
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}
