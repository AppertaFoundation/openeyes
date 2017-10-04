<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CataractComplicationsReportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the data set.
     */
    public function testDataSet()
    {
        $report = $this->getMockBuilder('CataractComplicationsReport')
            ->setMethods(array('queryData', 'allComplications'))
            ->disableOriginalConstructor()
            ->getMock();

        $report->expects($this->any())
            ->method('queryData')
            ->will($this->returnValue(array(
                array('name' => 'PC rupture with vitreous loss', 'complication_count' => '2'),
                array('name' => 'Op Cancelled', 'complication_count' => '1'),
                array('name' => 'PC rupture with vitreous loss', 'complication_count' => '1'),
                array('name' => 'Decentered IOL', 'complication_count' => '4'),
                array('name' => 'Vitreous loss', 'complication_count' => '8'),
                array('name' => 'Hyphaema', 'complication_count' => '3'),
                array('name' => 'Zonular dialysis', 'complication_count' => '1'),
            )));

        $report->expects($this->any())
            ->method('allComplications')
            ->will($this->returnValue(array(
                array('name' => 'PC rupture with vitreous loss'),
                array('name' => 'Op Cancelled'),
                array('name' => 'Decentered IOL'),
                array('name' => 'Vitreous loss'),
                array('name' => 'Hyphaema'),
                array('name' => 'Zonule rupture no vitreous loss'),
                array('name' => 'Lens fragments into vitreous'),
                array('name' => 'Other'),
                array('name' => 'Zonular dialysis'),
                array('name' => 'Phaco wound burn'),
            )));

        $totalComplications = $report->getTotalComplications();
        $this->assertEquals(20, $totalComplications); //test the total complications

        $data = $report->dataSet();
        $this->assertEquals(10, $data[0]);
        $this->assertEquals(5, $data[1]);
        $this->assertEquals(20, $data[2]);
        $this->assertEquals(40, $data[3]);
        $this->assertEquals(15, $data[4]);
    }

    /**
     * Test an empty data set.
     */
    public function testEmptyDataSet()
    {
        $report = $this->getMockBuilder('CataractComplicationsReport')
            ->setMethods(array('queryData', 'allComplications'))
            ->disableOriginalConstructor()
            ->getMock();

        $report->expects($this->any())
            ->method('queryData')
            ->will($this->returnValue(array()));

        $report->expects($this->any())
            ->method('allComplications')
            ->will($this->returnValue(array(
                array('name' => 'PC rupture with vitreous loss'),
                array('name' => 'Op Cancelled'),
                array('name' => 'Decentered IOL'),
                array('name' => 'Vitreous loss'),
                array('name' => 'Hyphaema'),
                array('name' => 'Zonule rupture no vitreous loss'),
                array('name' => 'Lens fragments into vitreous'),
                array('name' => 'Other'),
                array('name' => 'Zonular dialysis'),
                array('name' => 'Phaco wound burn'),
            )));

        $totalComplications = $report->getTotalComplications();
        $this->assertEquals(0, $totalComplications);
        $data = $report->dataSet();
        $this->assertEmpty($data);
    }

    /**
     * Test the series generation for the graph.
     */
    public function testSeries()
    {
        $dataSet = array(0, 1);

        $report = $this->getMockBuilder('CataractComplicationsReport')
            ->setMethods(array('dataSet'))
            ->disableOriginalConstructor()
            ->getMock();

        $report->expects($this->any())
            ->method('dataSet')
            ->will($this->returnValue(array($dataSet)));

        $seriesJson = $report->seriesJson();
        $seriesDecoded = json_decode($seriesJson, true);

        $this->assertInternalType('string', $seriesJson); //is a string
        $this->assertCount(1, $seriesDecoded); //Has 1 series when decoded
        $this->assertEquals('Complications', $seriesDecoded[0]['name']);
        $this->assertEquals($dataSet, $seriesDecoded[0]['data'][0]); //First series is the PCR data
    }

    /**
     * Test the configuration for the graph.
     */
    public function testConfig()
    {
        $report = new CataractComplicationsReport(Yii::app());
        $config = $report->graphConfig();
        $configDecoded = json_decode($config, true);

        $this->assertInternalType('string', $config); //is a string
        $this->assertEquals('bar', $configDecoded['chart']['type']); // check it looks ok
        $this->assertEquals(false, $configDecoded['credits']['enabled']); //check globabl config merge
        $this->assertEquals('CataractComplicationsReport', $configDecoded['chart']['renderTo']);
    }
}
