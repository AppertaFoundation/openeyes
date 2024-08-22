<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphCiExamination_FurtherFindingsTest extends ActiveRecordTestCase
{
    /**
     * @var Element_OphCiExamination_FurtherFindings
     */
    protected $model;
    public $fixtures = array(
        'finding' => '\Finding',
        'elFurtherFindings' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings',
        'furtherFindingsAssignment' => 'OEModule\OphCiExamination\models\OphCiExamination_FurtherFindings_Assignment',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings();
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings::model
     */
    public function testModel()
    {
        $this->assertEquals('OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings', get_class($this->model), 'Class name should match model.');
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophciexamination_further_findings', $this->model->tableName());
    }

    public function testGetFurtherFindingsAssigned()
    {
        $etFF = $this->elFurtherFindings('et_further_findings1')->getFurtherFindingsAssigned();
        $this->assertCount(1, $etFF);

        $this->assertEquals(2, $etFF[0]);
    }

    public function testGetFurtherFindingsAssignedString()
    {
        $etFFString = $this->elFurtherFindings('et_further_findings2')->getFurtherFindingsAssignedString();
        $this->assertEquals('Finding 1, Finding 3: test twotwotwo', $etFFString);
    }

    public function testGetFurtherFindingsAssignedString_ignoreids()
    {
        $etFFString = $this->elFurtherFindings('et_further_findings2')->getFurtherFindingsAssignedString(array(1));
        $this->assertEquals('Finding 3: test twotwotwo', $etFFString);

        $etFFString = $this->elFurtherFindings('et_further_findings2')->getFurtherFindingsAssignedString(array(3));
        $this->assertEquals('Finding 1', $etFFString);
    }
}
