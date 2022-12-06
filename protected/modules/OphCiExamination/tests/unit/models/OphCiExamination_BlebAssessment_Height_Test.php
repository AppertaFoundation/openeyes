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
class OphCiExamination_BlebAssessment_Height_Test extends ActiveRecordTestCase
{
    /**
     * @var OphCiExamination_BlebAssessment_Height
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
        $this->model = new \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height();
    }

    /**
     * @covers OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height::model
     */
    public function testModel()
    {
        $this->assertEquals('OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height', get_class($this->model), 'Class name should match model.');
    }

    /**
     * @covers OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('ophciexamination_bleb_assessment_height', $this->model->tableName());
    }
}
