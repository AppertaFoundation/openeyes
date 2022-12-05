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
class Element_OphCiExamination_OverallManagementPlan_Test extends ActiveRecordTestCase
{
    /**
     * @var Element_OphCiExamination_OverallManagementPlan
     */
    protected $model;
    public $fixtures = array(
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
        $this->model = new OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan();
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan::model
     */
    public function testModel()
    {
        $this->assertEquals('OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan', get_class($this->model), 'Class name should match model.');
        $this->assertTrue(is_subclass_of($this->model, 'SplitEventTypeElement'));
    }

    /**
     * @covers OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophciexamination_overallmanagementplan', $this->model->tableName());
    }
}
