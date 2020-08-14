<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class FlotChartTest extends CTestCase
{
    private FlotChart $chart;

    /**
     * @throws ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        $this->chart = new FlotChart(ComponentStubGenerator::generate('CController'));
    }

    /**
     * @covers FlotChart
     */
    public function testHasData_NoData()
    {
        $this->assertFalse($this->chart->hasData());
    }

    /**
     * @covers FlotChart
     */
    public function testHasData_Data()
    {
        $this->chart->addPoint('Series 1', 100, 100);
        $this->assertTrue($this->chart->hasData());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMin_NoData()
    {
        $this->assertNull($this->chart->getXMin());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMin_OnePoint()
    {
        $this->chart->addPoint('Series 1', 100, 100);
        $this->assertEquals(100, $this->chart->getXMin());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMin_TwoPoints()
    {
        $this->chart->addPoint('Series 1', 100, 100);
        $this->chart->addPoint('Series 1', 50, 200);
        $this->assertEquals(50, $this->chart->getXMin());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMax_NoData()
    {
        $this->assertNull($this->chart->getXMax());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMax_OnePoint()
    {
        $this->chart->addPoint('Series 1', 100, 100);
        $this->assertEquals(100, $this->chart->getXMax());
    }

    /**
     * @covers FlotChart
     */
    public function testGetXMax_TwoPoints()
    {
        $this->chart->addPoint('Series 1', 100, 100);
        $this->chart->addPoint('Series 1', 50, 200);
        $this->assertEquals(100, $this->chart->getXMax());
    }
}
