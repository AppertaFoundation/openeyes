<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphTrOperationbooking_PreAssessmentTest extends \ModelTestCase
{
    protected $element_cls = Element_OphTrOperationbooking_PreAssessment::class;

    public function testTableName()
    {
        $model = $this->getElementInstance();
        $this->assertEquals('et_ophtroperationbooking_preassessment', $model->tableName());
    }

    public function testRelations(): void
    {
        $model = $this->getElementInstance();
        self::assertCount(7, $model->relations());
        self::assertNotNull($model->user);
        self::assertNotNull($model->usermodified);
    }

    public function testAttributeLabels()
    {
        $model = $this->getElementInstance();
        $expected = array(
            'id' => 'ID',
            'event_id' => 'Event',
            'type_id' => 'Type of pre-assessment patient requires?',
            'location_id' => 'Location of pre-assessment',
        );

        $this->assertEquals($expected, $model->attributeLabels(), 'Attribute labels should match.');
    }

    public function testPreassessmentTypes()
    {
        $model = $this->getElementInstance();
        $pre_assessment_types = $model->getPreassessmentTypes();
        $this->assertIsArray($pre_assessment_types);
        $this->assertNotEmpty($pre_assessment_types);
        $this->assertArrayHasKey('data-use-location', $pre_assessment_types[1]);
    }
}
