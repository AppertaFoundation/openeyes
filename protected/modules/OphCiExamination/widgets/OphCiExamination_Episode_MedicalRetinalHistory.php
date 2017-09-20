<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models;

Yii::import('OphCiExamination.widgets.OphCiExamination_Episode_VisualAcuityHistory');

class OphCiExamination_Episode_MedicalRetinalHistory extends OphCiExamination_Episode_VisualAcuityHistory
{
    protected $chart_id = 'mr-history-chart';
    protected $va_unit_input = 'mr_history_va_unit_id';

    protected $injections = array();

    public function configureChart()
    {
        $chart = parent::configureChart();

        $sft_axis = 'Central SFT (µm)';

        $chart->configureYAxis($sft_axis, array('position' => 'right', 'min' => 50, 'max' => 1500))
            ->configureSeries('Central SFT (right)', array('yaxis' => $sft_axis, 'lines' => array('show' => true), 'points' => array('show' => true)))
            ->configureSeries('Central SFT (left)', array('yaxis' => $sft_axis, 'lines' => array('show' => true), 'points' => array('show' => true)));

        return $chart;
    }

    /**
     * @param \FlotChart $chart
     */
    public function addData(\FlotChart $chart)
    {
        parent::addData($chart);

        foreach ($this->event_type->api->getEvents($this->episode->patient, false) as $event) {
            if (($oct = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_OCT'))) {
                if ($oct->hasRight()) {
                    $this->addSftReading($chart, $oct, 'right');
                }
                if ($oct->hasLeft()) {
                    $this->addSftReading($chart, $oct, 'left');
                }
            }
        }

        $injMin = null;
        $injMax = null;

        if (($injectionApi = Yii::app()->moduleAPI->get('OphTrIntravitrealinjection'))) {
            foreach ($injectionApi->previousInjections($this->episode->patient, $this->episode, 'right') as $injection) {
                $this->addInjection($chart, $this->va_axis, $injection, 'right', $injMin, $injMax);
            }
            foreach ($injectionApi->previousInjections($this->episode->patient, $this->episode, 'left') as $injection) {
                $this->addInjection($chart, $this->va_axis, $injection, 'left', $injMin, $injMax);
            }
        }
        ksort($this->injections);

        if ($chart->hasData()) {
            // If there are injections at the extremes, expand the chart to make room to display them
            $extra = min($chart->getXMax() - $chart->getXMin(), 31536000000) / 4;
            $min = $injMin ? min($chart->getXMin(), $injMin - $extra) : $chart->getXMin();
            $max = $injMax ? max($chart->getXMax(), $injMax + $extra) : $chart->getXMax();

            $chart->configureXAxis(
                array(
                    'mode' => 'time',
                    'min' => max($min, $max - 31536000000),  // limit default display to the last year
                    'max' => $max,
                    'panRange' => array($min, $max),
                )
            );
        }
    }

    /**
     * @param \FlotChart                            $chart
     * @param models\Element_OphCiExamination_OCT   $oct
     * @param string                                $side
     */
    protected function addSftReading(\FlotChart $chart, models\Element_OphCiExamination_OCT $oct, $side)
    {
        $series_name = "Central SFT ({$side})";
        $sft = $oct->{"{$side}_sft"};
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($oct->last_modified_date), $sft, "{$series_name}\n{$sft} µm");
    }

    /**
     * @param \FlotChart $chart
     * @param string     $va_axis
     * @param array      $injection
     * @param string     $side
     * @param float|null &$injMin
     * @param float|null &$injMax
     */
    protected function addInjection(\FlotChart $chart, $va_axis, array $injection, $side, &$injMin, &$injMax)
    {
        $drug = $injection["{$side}_drug"];
        $timestamp = Helper::mysqlDate2JsTimestamp($injection['date']);

        $chart->configureSeries($drug, array('yaxis' => $va_axis, 'bars' => array('show' => true)));
        $chart->addPoint($drug, $timestamp, 149);

        $this->injections[$timestamp][$side] = $drug;

        if ($side === 'right' && (!$injMin || $timestamp < $injMin)) {
            $injMin = $timestamp;
        }
        if ($side === 'left' && (!$injMax || $timestamp > $injMax)) {
            $injMax = $timestamp;
        }
    }
}
