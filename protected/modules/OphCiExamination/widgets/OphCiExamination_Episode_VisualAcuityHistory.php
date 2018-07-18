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
use OEModule\OphCiExamination\models;

class OphCiExamination_Episode_VisualAcuityHistory extends \EpisodeSummaryWidget
{
    public $collapsible = true;
    public $openOnPageLoad = true;

    protected $chart_id = 'va-history-chart';
    protected $va_unit_input = 'va_history_unit_id';

    protected $va_axis;
    protected $va_ticks;
    private $va_unit;

    public function run()
    {
        $va_unit_id = @$_GET[$this->va_unit_input] ?: models\Element_OphCiExamination_VisualAcuity::model()->getSetting('unit_id');
        $this->va_unit = models\OphCiExamination_VisualAcuityUnit::model()->findByPk($va_unit_id);

        $chart = $this->configureChart();
        $this->addData($chart);

        $this->render(get_class($this), array('va_unit' => $this->va_unit, 'chart' => $chart));
    }

    public function run_oescape(){
        $va_unit_id = @$_GET[$this->va_unit_input] ?:  models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name'=>'ETDRS Letters'))->id;
        $this->va_unit = models\OphCiExamination_VisualAcuityUnit::model()->findByPk($va_unit_id);

        $this->configureChart();

        $this->render("OphCiExamination_OEscape_VisualAcuityHistory", array('va_unit' => $this->va_unit));
    }
    /**
     * @return FlotChart
     */
    public function configureChart()
    {
        $va_ticks = array();
        foreach ($this->va_unit->selectableValues as $value) {
            if ($value->base_value < 10 || ($this->va_unit->name == 'ETDRS Letters' && $value->value % 10)) {
                continue;
            }

            /*
                OE-7011
                Replacing the charts completely with highcharts will come in OE3.x (with OEScape)
                until that we need to fix this overlapping labels
                FlotChart's tickFormatter function won't apply as "'ticks' => $va_ticks" are provided
            */
            $label = ($value->value == '6/9.5') ? '' : $value->value;

            $va_ticks[] = array($value->base_value, $label);
        }

        $this->va_ticks = $va_ticks;
        $this->va_axis = "{$this->va_unit->name}";

        $chart = $this->createWidget('FlotChart', array('chart_id' => $this->chart_id))
            ->configureXAxis(array('mode' => 'time'))
            ->configureYAxis($this->va_axis, array('position' => 'left', 'min' => 1, 'max' => 150, 'ticks' => $va_ticks))
            ->configureSeries('Visual Acuity (right)', array('yaxis' => $this->va_axis, 'lines' => array('show' => true), 'points' => array('show' => true)))
            ->configureSeries('Visual Acuity (left)', array('yaxis' => $this->va_axis, 'lines' => array('show' => true), 'points' => array('show' => true)));

        return $chart;
    }

    /**
     * @param \FlotChart $chart
     */
    public function addData(\FlotChart $chart)
    {
        foreach ($this->event_type->api->getElements('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity', $this->episode->patient, false) as $va) {
            if (($reading = $va->getBestReading('right'))) {
                $this->addVaReading($va->event, $chart, $reading, 'right');
            }
            if (($reading = $va->getBestReading('left'))) {
                $this->addVaReading($va->event, $chart, $reading, 'left');
            }
        }
    }

    /**
     * @param Event                                 $event
     * @param \FlotChart                            $chart
     * @param OphCiExamination_VisualAcuity_Reading $reading
     * @param string                                $side
     */
    protected function addVaReading($event, \FlotChart $chart, models\OphCiExamination_VisualAcuity_Reading $reading, $side)
    {
        $series_name = "Visual Acuity ({$side})";
        $label = "{$series_name}\n{$reading->element->unit->name}: {$reading->convertTo($reading->value)} {$reading->method->name}";
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($event->event_date), $reading->value, $label);
    }

    public function getVaData(){
        $va_data_list = array('right'=>array(), 'left'=>array());
        foreach ($this->event_type->api->getElements('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity', $this->episode->patient, false) as $va) {
            if (($reading = $va->getBestReading('right'))) {
                array_push($va_data_list['right'],array( 'y'=>(float)$reading->value,'x'=>Helper::mysqlDate2JsTimestamp($reading->created_date)));
            }
            if (($reading = $va->getBestReading('left'))) {
                array_push($va_data_list['left'],array('y'=>(float)$reading->value, 'x'=>Helper::mysqlDate2JsTimestamp($reading->created_date)));
            }
        }
        foreach (['left', 'right'] as $side){
            usort($va_data_list[$side], array("EpisodeSummaryWidget","sortData"));
        }
        return $va_data_list;
    }

    public function getVaAxis() {
        return $this->va_axis;
    }

    public function getVaTicks() {
        $tick_data = array('tick_position'=> array(), 'tick_labels'=> array());
        foreach ($this->va_ticks as $tick){
            array_push($tick_data['tick_position'],(float)$tick[0]);
            array_push($tick_data['tick_labels'], $tick[1]);
        }
        return $tick_data;
    }

}
