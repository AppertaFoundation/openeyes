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
    private $va_unit;

    public function run()
    {
        $va_unit_id = @$_GET[$this->va_unit_input] ?: models\Element_OphCiExamination_VisualAcuity::model()->getSetting('unit_id');
        $this->va_unit = models\OphCiExamination_VisualAcuityUnit::model()->findByPk($va_unit_id);

        $chart = $this->configureChart();
        $this->addData($chart);

        $this->render(get_class($this), array('va_unit' => $this->va_unit, 'chart' => $chart));
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
            $va_ticks[] = array($value->base_value, $value->value);
        }

        $this->va_axis = "Visual Acuity ({$this->va_unit->name})";

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
}
