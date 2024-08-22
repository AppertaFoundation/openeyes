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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 */
class OphCoCorrespondence_SignatureTest extends OEDbTestCase
{
    use WithTransactions;

    protected $original_module_api;

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphCoCorrespondence');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->original_module_api = \Yii::app()->getComponent('moduleAPI');
    }

    public function tearDown(): void
    {
        \Yii::app()->setComponent('moduleAPI', $this->original_module_api);
        parent::tearDown();
    }

    public function getStubbedProtectedFile()
    {
        $stub = $this->getMockBuilder(ProtectedFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getThumbnail'])
            ->getMock();

        $stub->method('getThumbnail')->willReturn(null);

        return $stub;
    }

    /** @test */
    public function footer_text_is_retrieved_from_the_api_correctly()
    {
        $consultant = \User::factory()->withContact()->create();
        $firm = \Firm::factory()->withConsultant($consultant)->create();

        $model = OphCoCorrespondence_Signature::factory()
            ->asConsultant($consultant)
            ->forFirm($firm)
            ->create();

        $model->signatureFile = $this->getStubbedProtectedFile();

        $correspondence_api = new OphCoCorrespondence_API();

        $module_api = $this->getMockBuilder(\ModuleAPI::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])->getMock();

        $module_api->method('get')->willReturn($correspondence_api);

        \Yii::app()->setComponent('moduleAPI', $module_api);

        $this->assertStringContainsString($consultant->contact->title, $model->getPrintout());
        $this->assertStringContainsString($consultant->contact->first_name, $model->getPrintout());
        $this->assertStringContainsString($consultant->contact->last_name, $model->getPrintout());
    }
}
