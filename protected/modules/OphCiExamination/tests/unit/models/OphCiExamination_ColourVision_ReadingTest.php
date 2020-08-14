<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Value;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class OphCiExamination_ColourVision_ReadingTest
 * @method colourvision_reading($fixtureId)
 * @method colourvision_method($fixtureId)
 */
class OphCiExamination_ColourVision_ReadingTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'colourvision_reading' => OphCiExamination_ColourVision_Reading::class,
        'colourvision_value' => OphCiExamination_ColourVision_Value::class,
        'colourvision_method' => OphCiExamination_ColourVision_Method::class,
    );

    private OphCiExamination_ColourVision_Reading $model;
    public function setUp()
    {
        $this->model = new OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading();
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->model);
    }

    public function getData()
    {
        return array(
            'New reading' => array(
                'fixture' => null,
                'expected' => null,
            ),
            'Existing reading' => array(
                'fixture' => 'reading1',
                'expected' => 'method1'
            )
        );
    }

    public function getModel()
    {
        return OphCiExamination_ColourVision_Reading::model();
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @param $expected
     */
    public function testgetMethod($fixture, $expected)
    {
        if ($fixture) {
            $this->model = $this->colourvision_reading($fixture);
        }
        $this->assertEquals($expected ? $this->colourvision_method($expected) : null, $this->model->getMethod());
    }
}
