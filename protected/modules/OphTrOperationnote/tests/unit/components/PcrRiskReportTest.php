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
class PcrRiskReportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the actual calculation.
     */
    public function testCalculatedPcrRisk()
    {
        $pcr = $this->getMockBuilder('PcrRiskReport')
            ->setMethods(array('queryData'))
            ->disableOriginalConstructor()
            ->getMock();

        $pcr->expects($this->any())
            ->method('queryData')
            ->will($this->returnValue(array(
                array('complication' => null, 'risk' => '4.80'),
                array('complication' => 'None', 'risk' => '3.67'),
                array('complication' => 'PC rupture with vitreous loss', 'risk' => '4.76'),
                array('complication' => 'Op Cancelled', 'risk' => '4.78'),
                array('complication' => 'PC rupture with vitreous loss', 'risk' => '1.46'),
                array('complication' => 'None', 'risk' => '2.77'),
                array('complication' => 'None', 'risk' => '7.86'),
                array('complication' => 'None', 'risk' => null),
            )));

        $pcr->expects($this->any())
            ->method('average')
            ->will($this->returnValue(1.92));

        $pcrCases = 2;
        $totalCases = 8;
        $sumPcrRisk = 4.80 + 3.67 + 4.76 + 4.78 + 1.46 + 2.77 + 7.86;
        $adjustedRate = (($pcrCases / $totalCases) / ($sumPcrRisk / $totalCases)) * 1.92;

        $output = $pcr->dataSet();
        $this->assertCount(1, $output); //We should only have one surgeons data.
        $this->assertEquals($totalCases, $output[0][0]); //They should have the number of surgeries above
        $this->assertEquals($adjustedRate, $output[0][1]);
    }

    /**
     * Test the series generation for the graph.
     */
    public function testSeries()
    {
        $dataSet = array(8, 1.56);

        $pcr = $this->getMockBuilder('PcrRiskReport')
            ->setMethods(array('dataSet'))
            ->disableOriginalConstructor()
            ->getMock();

        $pcr->expects($this->any())
            ->method('dataSet')
            ->will($this->returnValue(array($dataSet)));

        $seriesJson = $pcr->seriesJson();
        $seriesDecoded = json_decode($seriesJson, true);

        $this->assertInternalType('string', $seriesJson); //is a string
        $this->assertCount(3, $seriesDecoded); //Has 3 series when decoded
        $this->assertEquals('Current Surgeon', $seriesDecoded[0]['name']);
        $this->assertEquals($dataSet, $seriesDecoded[0]['data'][0]); //First series is the PCR data

        $this->assertEquals('Upper 98%', $seriesDecoded[1]['name']);
        $this->assertEquals('Upper 95%', $seriesDecoded[2]['name']);
    }

    /**
     * Test the configuration for the graph.
     */
    public function testConfig()
    {
        $pcr = new PcrRiskReport(Yii::app());
        $config = $pcr->graphConfig();
        $configDecoded = json_decode($config, true);

        $this->assertInternalType('string', $config); //is a string
        $this->assertEquals('spline', $configDecoded['chart']['type']); // check it looks ok
        $this->assertEquals(false, $configDecoded['credits']['enabled']); //check globabl config merge
        $this->assertEquals('PcrRiskReport', $configDecoded['chart']['renderTo']);
    }
}
