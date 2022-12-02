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
    public function getEmptyReport()
    {
        $report = $this->getMockBuilder('_WrapperPcrRiskReport')
            ->setMethods(['getTotalOperations', 'querySurgeonData'])
            ->disableOriginalConstructor()
            ->getMock();

        $report->setEmptyTest();

        $report->expects($this->any())
            ->method('getTotalOperations')
            ->will($this->returnValue(0));

        $report->expects($this->any())
            ->method('querySurgeonData')
            ->will($this->returnValue(array(
                array('id' => 1),
                array('id' => 2),
                array('id' => 3),
                array('id' => 6),
                array('id' => 7),
                array('id' => 11),
                array('id' => 453),
                array('id' => 833),
            )));

        return $report;
    }

    public function getReport()
    {
        $report = $this->getMockBuilder('_WrapperPcrRiskReport')
            ->setMethods(['getTotalOperations', 'querySurgeonData'])
            ->disableOriginalConstructor()
            ->getMock();

        $report->expects($this->any())
            ->method('getTotalOperations')
            ->will($this->returnValue(8));

        $report->expects($this->any())
            ->method('querySurgeonData')
            ->will($this->returnValue(array(
                array('id' => 1),
                array('id' => 2),
                array('id' => 3),
                array('id' => 6),
                array('id' => 7),
                array('id' => 11),
                array('id' => 453),
                array('id' => 833),
            )));

        return $report;
    }

    public function testDataSet()
    {
        $report = $this->getReport();

        $data = $report->dataSet();
        $expected = array('name' => 'adjusted', 'x' => 8, 'y' => 11.992504684572143, 'surgeon' => '<br><i>Surgeon: </i>Surgeon 1', 'color' => 'red');

        $this->assertEquals($expected, $data[0]);
    }

    /**
     * Test the series generation for the graph with empty DataSet.
     */
    public function testEmptyDataSet()
    {
        $report = $this->getEmptyReport();

        $data = $report->dataSet();
        $expected = array('name' => 'adjusted', 'x' => 0, 'y' => 0, 'surgeon' => '<br><i>Surgeon: </i>Surgeon 1', 'color' => 'red');

        $this->assertEquals($expected, $data[0]);
    }

    /**
     * Test the series generation for the graph.
     */
    public function testTracesJson()
    {
        $report = $this->getReport();
        $this->assertIsString($report->tracesJson());
    }

    public function testTracesJsonWithEmptyDataSet()
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
        $expected = json_encode(array(
            'type' => 'scatter',
            'showlegend' => true,
            'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
            'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
            'title' => 'PCR Rate (risk adjusted)<br><sub>Total Operations: 8</sub>',
            'font' => array(
                'family' => 'Roboto,Helvetica,Arial,sans-serif',
            ),
            'xaxis' => array(
                'title' => 'No. Operations',
                'showgrid' => false,
                'ticks' => 'outside',
                'dtick' => 100,
                'tick0' => 0,
            ),
            'yaxis' => array(
                'title' => 'PCR Rate',
                'ticks' => 'outside',
                'dtick' => 10,
                'tick0' => 0,
                'showgrid'=>true,
                'range' => [0,50],
            ),
            'legend'=> array(
                'x' => 0.8,
                'y' => 1,
                'bordercolor' => '#fff',
                'borderwidth' => 1,
                'font' => array(
                    'size' => 13
                )
            ),
            'shapes' => array(
                array(
                    'type' => 'line',
                    'xref' => 'x',
                    'yref' => 'y',
                    'line' => array(
                        'dash' =>'dot',
                        'width' => 1,
                        'color' => 'rgb(0,0,0)',
                    ),
                    'x0' => 0,
                    'x1' => 1000,
                    'y0' => 1.92,
                    'y1' => 1.92,
                )
            ),
            'hovermode' => 'closest'
        ));

        $config = $report->plotlyConfig();
        $this->assertEquals($expected, $config);
    }
}

class _WrapperPcrRiskReport extends PcrRiskReport
{
    public $allSurgeons = true;
    private $empty_test = false;
    protected $mode = 0;
    protected $totalOperations = 1000;

    protected function queryData($surgeon, $dateFrom, $dateTo)
    {
        if (!$this->empty_test) {
            return array(
                array('complication' => null, 'risk' => '4.80'),
                array('complication' => 'None', 'risk' => '3.67'),
                array('complication' => 'PC rupture with vitreous loss', 'risk' => '4.76'),
                array('complication' => 'Op Cancelled', 'risk' => '4.78'),
                array('complication' => 'PC rupture with vitreous loss', 'risk' => '1.46'),
                array('complication' => 'None', 'risk' => '2.77'),
                array('complication' => 'None', 'risk' => '7.86'),
                array('complication' => 'None', 'risk' => null),
            );
        }

        return array();
    }

    protected function isCurrentUserServiceManager()
    {
                return false;
    }

    protected function isCurrentUserById($id)
    {
            return false;
    }

    public function setEmptyTest()
    {
        $this->empty_test = true;
    }
}
