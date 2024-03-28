<?php
/**
 * (C) Copyright Apperta Foundation 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group pgdpsd
 */
class ReportTest extends OEDbTestCase
{
    use WithTransactions;
    use MakesApplicationRequests;

    public static function reportRoutesProvider(): array
    {
        return [
            'drug administration report form' => ['OphDrPGDPSD/report/DaReport'],
            'download drug adminstration report' => ['OphDrPGDPSD/report/downloadreport', 'post', ['report-name' => 'foo']],
            'display drug adminstration report' => ['OphDrPGDPSD/report/runreport', 'post']
        ];
    }

    /**
     * @test
     * @dataProvider reportRoutesProvider
     */
    public function cannot_access_route_as_guest($route, $method = 'get', array $data = [])
    {
        $response = $method === 'get' ? $this->get($route) : $this->$method($route, $data);
        $response->assertException(CHttpException::class, ['statusCode' => 403]);
    }

    /**
     * @test
     * @dataProvider reportRoutesProvider
     */
    public function cannot_access_route_as_unpermissioned_user($route, $method = 'get', array $data = [])
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $method === 'get' ? $this->get($route) : $this->$method($route, $data);

        $response->assertException(CHttpException::class, ['statusCode' => 403]);
    }

    /**
     * @test
     * @dataProvider reportRoutesProvider
     */
    public function can_access_route_as_surgeon_user($route, $method = 'get', array $data = [])
    {
        if (str_contains($route, 'download')) {
            $this->markTestSkipped('Cannot test this route for access because headers are modified');
        }

        $this->mockCurrentContext();
        $user = User::factory()->create(['is_surgeon' => true]);

        $this->actingAs($user);
        $response = $method === 'get' ? $this->get($route) : $this->$method($route, $data);

        $response->assertSuccessful();
    }

    /**
     * @test
     * @dataProvider reportRoutesProvider
     */
    public function can_access_route_as_with_report_auth($route, $method = 'get', array $data = [])
    {
        if (str_contains($route, 'download')) {
            $this->markTestSkipped('Cannot test this route for access because headers are modified');
        }

        $this->mockCurrentContext();
        $user = User::factory()->withAuthItems(['Report'])->create();

        $this->actingAs($user);
        $response = $method === 'get' ? $this->get($route) : $this->$method($route, $data);

        $response->assertSuccessful();
    }
}
