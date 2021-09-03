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
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

class OphCiExamination_Episode_VisualAcuityHistory extends \EpisodeSummaryWidget
{
    public $collapsible = true;
    public $openOnPageLoad = true;

    protected $chart_id = 'va-history-chart';
    protected $va_unit_input = 'va_history_unit_id';

    protected $va_axis;
    protected $va_ticks;
    protected $vfi_ticks;
    protected $va_unit;
    protected $va_y_min;
    protected $va_y_max;

    public function run()
    {
        $this->resolveVAUnit();

        $chart = $this->configureChart();
        $this->addData($chart);

        $this->render(get_class($this), array('va_unit' => $this->va_unit, 'chart' => $chart));
    }

    /**
     * @param int $widgets_no
     * @throws CException
     * @phpcs:disable  PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function run_oescape($widgets_no = 1)
    {
        $this->resolveVAUnit();
        $this->configureChart();

        $this->render("OphCiExamination_OEscape_VisualAcuityHistory", array('va_unit' => $this->va_unit, 'widget_no' => $widgets_no));
    }

    /**
     * Moves the values for CF, HM, PL, NPL for better graphing
     * @param $val float|int value of Visual Acuity to be adjusted
     * @return float|int adjusted value
     */
    public function getAdjustedVA($val)
    {
        return $val > 4 ? $val : ($val-4) * 10;
    }



    /**
     * @return FlotChart
     */
    public function configureChart() {
        $this->vfi_ticks = $this->getVfiChartTicks();
        $va_ticks = $this->getVAChartTicks();
        $va_len = sizeof($va_ticks);
        $step = $va_len/20;
        $no_numeric_val_count = 4;   //keep the 4 no number labels: CF, HM, PL, NPL
        $this->va_ticks = array_slice($va_ticks, 0, $no_numeric_val_count);

        for ($i = $no_numeric_val_count; $i<=$va_len-$step; $i+=$step) {
            array_push($this->va_ticks, $va_ticks[$i]);
        }

        $this->va_axis = (string)$this->va_unit->name;
        $chart = $this->createWidget('FlotChart', array('chart_id' => $this->chart_id))
            ->configureXAxis(array('mode' => 'time'))
            ->configureYAxis(
                $this->va_axis,
                array('position' => 'left', 'min' => $this->va_y_min, 'max' => $this->va_y_max, 'ticks' => $this->va_ticks)
            )
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
     * @param models\OphCiExamination_VisualAcuity_Reading $reading
     * @param string                                $side
     */
    protected function addVaReading($event, \FlotChart $chart, models\OphCiExamination_VisualAcuity_Reading $reading, $side)
    {
        $series_name = "Visual Acuity ({$side})";
        $label = "{$series_name}\n{$reading->unit->name}: {$reading->convertTo($reading->value)} {$reading->method->name}";
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($event->event_date), $reading->value, $label);
    }

    public function getVaData(){
        $va_data_list = array('right'=>array(), 'left'=>array());
        foreach ($this->event_type->api->getElements(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
            $this->patient,
            false
        ) as $va) {
            foreach (['left', 'right'] as $side) {
                $reading = $va->getBestReading($side);
                if ($reading) {
                    $va_value = $this->getAdjustedVA((float)$reading->value);
                    $va_data_list[$side][] = array( 'y'=>$va_value,'x'=>Helper::mysqlDate2JsTimestamp($va->event->event_date));
                }
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($va_data_list[$side], array("EpisodeSummaryWidget","sortData"));
        }
        return $va_data_list;
    }

    public function getPlotlyVaData() {
        $va_data_list = [
            'right' => [],
            'left' => [],
            'beo' => []
        ];
        $va_plotly_list = [
            'right' => ['x' => [], 'y' => []],
            'left' => ['x' => [], 'y' => []],
            'beo' => ['x' => [], 'y' => []]
        ];

        foreach (
            $this->event_type->api->getElements(
                Element_OphCiExamination_VisualAcuity::class,
                $this->patient,
                false
            ) as $va
        ) {
            foreach (['left', 'right', 'beo'] as $side) {
                $reading = $va->getBestReading($side);
                if ($reading) {
                    $va_value = $this->getAdjustedVA((float)$reading->value);
                    $va_data_list[$side][] = [
                        'y' => $va_value,
                        'x' => date('Y-m-d', Helper::mysqlDate2JsTimestamp($va->event->event_date) / 1000)
                    ];
                }
            }
        }
        foreach (['left', 'right', 'beo'] as $side) {
            usort($va_data_list[$side], array("EpisodeSummaryWidget","sortData"));
            foreach ($va_data_list[$side] as $item) {
                $va_plotly_list[$side]['x'][] = $item['x'];
                $va_plotly_list[$side]['y'][] = $item['y'];
            }
        }

        return $va_plotly_list;
    }

    public function getPlotlyVfiData()
    {
        $vfi_data_list = [
            'right' => [],
            'left' => []
        ];
        $vfi_plotly_list = [
            'right' => ['x' => [], 'y' => []],
            'left' => ['x' => [], 'y' => []]
        ];
        $generic_api = Yii::app()->moduleAPI->get('OphGeneric');
        $hfa = $generic_api->getElements(\OEModule\OphGeneric\models\HFA::class, $this->patient, false);
        foreach ($hfa as $h) {
            $reading = $h->getMeanDeviation();
            if ($reading['side'] === 'right') {
                $vfi_data_list['right'][] = [
                    'y' => $reading['vfi'],
                    'x' => date('Y-m-d', Helper::mysqlDate2JsTimestamp($h->event->event_date) / 1000)
                ];
            } else {
                $vfi_data_list['left'][] = [
                    'y' => $reading['vfi'],
                    'x' => date('Y-m-d', Helper::mysqlDate2JsTimestamp($h->event->event_date) / 1000)
                ];
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($vfi_data_list[$side], array("EpisodeSummaryWidget","sortData"));
            foreach ($vfi_data_list[$side] as $item) {
                $vfi_plotly_list[$side]['x'][] = $item['x'];
                $vfi_plotly_list[$side]['y'][] = $item['y'];
            }
        }

        return $vfi_plotly_list;
    }

    public function getVaAxis() {
        return $this->va_axis;
    }

    public function getVaTicks() {
        $tick_data = array('tick_position'=> array(), 'tick_labels'=> array());
        foreach ($this->va_ticks as $tick) {
            array_push($tick_data['tick_position'], (float)$tick[0]);
            array_push($tick_data['tick_labels'], $tick[1]);
        }
        return $tick_data;
    }

    public function getVfiTicks() {
        $tick_data = array('tick_position'=> array(), 'tick_labels'=> array());
        foreach ($this->vfi_ticks as $tick) {
            array_push($tick_data['tick_position'], $tick[0]);
            array_push($tick_data['tick_labels'], $tick[1]);
        }
        return $tick_data;
    }

    /**
     * Sets the va_unit property based on the requested value, or from the system setting
     * configuration for Visual Acuity unit_id
     */
    protected function resolveVAUnit()
    {
        $va_unit_id = $_GET[$this->va_unit_input] ?? models\Element_OphCiExamination_VisualAcuity::model()->getSetting('unit_id');
        $this->va_unit = models\OphCiExamination_VisualAcuityUnit::model()->findByPk($va_unit_id);
    }

    /**
     * @return array
     */
    protected function getVAChartTicks()
    {
        foreach ($this->va_unit->selectableValues as $value) {
            $va_ticks[] = array($this->getAdjustedVA($value->base_value), $value->value);
        }
        if ($va_ticks[0][1] !== 'NPL') {
            array_unshift($va_ticks, [$this->getAdjustedVA(4), 'CF']);
            array_unshift($va_ticks, [$this->getAdjustedVA(3), 'HM']);
            array_unshift($va_ticks, [$this->getAdjustedVA(2), 'PL']);
            array_unshift($va_ticks, [$this->getAdjustedVA(1), 'NPL']);
        }

        return $va_ticks;
    }

    protected function getVFIChartTicks()
    {
        for ($value = -30; $value < 10;$value += 5) {
            $vfi_ticks[] = array($value, $value);
        }
        return $vfi_ticks;
    }
}
