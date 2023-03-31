<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group worklist
 */
class WorklistManagementTest extends OEDbTestCase
{
    use WithTransactions;
    use MocksSession;
    use MakesApplicationRequests;

    /** @test */
    public function worklist_filter_can_be_deleted()
    {
        list($user, $institution) = $this->createPermissionedUserWithInstitution(['OprnWorklist']);
        $this->mockCurrentContext(null, null, $institution);

        $worklist_filter = WorklistFilter::factory()->forSite(Site::factory()->create())->create();

        $this->actingAs($user, $institution)
            ->post('/Worklist/deleteFilter', ['id' => $worklist_filter->getPrimaryKey()]);

        $this->assertEmpty(WorklistFilter::model()->findByPk($worklist_filter->getPrimaryKey()));
    }

    /** @test */
    public function not_found_response_for_missing_worklist_filter()
    {
        list($user, $institution) = $this->createPermissionedUserWithInstitution(['OprnWorklist']);
        $this->mockCurrentContext(null, null, $institution);

        $worklist_filter = WorklistFilter::factory()->forSite(Site::factory()->create())->create();
        $pk = $worklist_filter->getPrimaryKey();
        $worklist_filter->delete();

        $response = $this->actingAs($user, $institution)
            ->post('/Worklist/deleteFilter', ['id' => $pk]);

        $response->assertException(CHttpException::class, ['statusCode' => 404]);
    }

    public function createPermissionedUserWithInstitution(array $permissions = [])
    {
        // TODO: when merged in with 6.7.x, should use the enhanced UserFactory to
        // TODO: actually create permissioned use with given auth items.

        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}
