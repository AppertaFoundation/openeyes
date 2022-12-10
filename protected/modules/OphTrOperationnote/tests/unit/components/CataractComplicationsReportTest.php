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
    protected function getEmptyReport()
    {
        $report = $this->getMockBuilder('_WrapperCataractComplicationReport')
            ->setMethods(['allComplications'])
            ->disableOriginalConstructor()
            ->getMock();

        $report->setEmptyTest();

        $report->expects($this->any())
            ->method('allComplications')
            ->will($this->returnValue(array()));

        return $report;
    }

    protected function getReport()
    {
        $report = $this->getMockBuilder('_WrapperCataractComplicationReport')
            ->setMethods(['allComplications', 'getTotalComplications', 'getTotalOperations'])
            ->disableOriginalConstructor()
            ->getMock();

        $report->expects($this->any())
            ->method('getTotalComplications')
            ->will($this->returnValue(7));

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

        $report->expects($this->any())
            ->method('getTotalOperations')
            ->will($this->returnValue(23));

        return $report;
    }

    public function testGetTotalComplications()
    {
        $report = $this->getMockBuilder('_WrapperCataractComplicationReport')
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $totalComplications = $report->getTotalComplications('all');
        $this->assertEquals(7, $totalComplications);

        return $totalComplications;
    }

    /**
     * Test the data set.
     *
     * @depends testGetTotalComplications
     */
    public function testDataSet()
    {
        $report = $this->getReport();

        $data = $report->dataSet();
        $this->assertEquals(28.57, round($data[0]['y'], 2));
        $this->assertEquals(2, $data[0]['total']);
        $this->assertEquals([4289, 582], $data[0]['event_list']);
    }

    /**
     * Test an empty data set.
     */
    public function testEmptyDataSet()
    {
        $report = $this->getEmptyReport();

        $totalComplications = $report->getTotalComplications('all');
        $this->assertEquals(0, $totalComplications);
        $data = $report->dataSet();
        $this->assertEmpty($data);
    }

    /**
     * Test the series generation for the graph.
     */
    public function testTracesJson()
    {
        $report = $this->getReport();
        $this->assertIsString($report->tracesJson());
    }

    /**
     * Test the series generation for the graph with an empty dataset.
     */
    public function testTracesJsonWithEmptyDataset()
    {
        $report = $this->getEmptyReport();
        $this->assertIsString($report->tracesJson());
    }

    /**
     * Test the configuration for the graph.
     */
    public function testPlotlyConfig()
    {
        $report = $this->getReport();

        $expected_config = json_encode(array(
            'type' => 'bar',
            'title' => 'Complication Profile<br><sub>Total Complications: 7 Total Operations: 23</sub>',
            'showlegend' => false,
            'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
            'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
            'font' => array(
                'family' => 'Roboto,Helvetica,Arial,sans-serif',
            ),
            'xaxis' => array(
                'title' => 'Number of cases',
                'showline' => true,
                'showgrid' => true,
                'ticks' => 'outside',
            ),
            'yaxis' => array(
                'title' => 'Complication',
                'tickvals' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
                'ticktext' => array('PC rupture with vitreous loss', 'Op Cancelled', 'Decentered IOL', 'Vitreous loss', 'Hyphaema', 'Zonule rupture no vitreous loss', 'Lens fragments into vitreous', 'Other', 'Zonular dialysis', 'Phaco wound burn'),
                'autorange' => 'reversed',
                'automargin' => 'true',
                'showline' => true,
                'showgrid' => false,
                'tickfont' => array(
                    'size' => '9.5',
                ),
            ),
            'margin' => array(
                'l' => 150,
            ),
        ));

        $config = $report->plotlyConfig();
        $this->assertEquals($expected_config, $config);
    }
}

class _WrapperCataractComplicationReport extends CataractComplicationsReport
{
    public $allSurgeons = true;
    private $empty_test = false;

    protected function queryDatas($surgeon, $dateFrom, $dateTo)
    {
        if (!$this->empty_test) {
            return [
                array('cataract_id' => '8509', 'name' => 'PC rupture with vitreous loss', 'event_id' => '4289'),
                array('cataract_id' => '1234', 'name' => 'Op Cancelled', 'event_id' => '3958'),
                array('cataract_id' => '123', 'name' => 'PC rupture with vitreous loss', 'event_id' => '582'),
                array('cataract_id' => '12', 'name' => 'Decentered IOL', 'event_id' => '1497'),
                array('cataract_id' => '2573', 'name' => 'Vitreous loss', 'event_id' => '1641'),
                array('cataract_id' => '9344', 'name' => 'Hyphaema', 'event_id' => '1480'),
                array('cataract_id' => '3246', 'name' => 'Zonular dialysis', 'event_id' => '45679'),
            ];
        }

        return array();
    }

    public function setEmptyTest()
    {
        $this->empty_test = true;
    }
}
