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

class OphCiExamination_Episode_KeratometryChartRight extends \EpisodeSummaryWidget
{
    public $collapsible = true;
    public $openOnPageLoad = true;

    protected $chart_id = 'keratometry-history-right-chart';
    protected $kera_unit_input = 'keratometry_history_unit_id';

    protected $kera_axis;

    private $kera_unit;

    public function run()
    {
        // TODO: should be using API methods here and need to fix that it's restricted to the episode
        $criteria = new CDbCriteria();
        $criteria->compare('episode_id', $this->episode->id);
        $criteria->compare('event_type_id', $this->event_type->id);
        $criteria->order = 'event_date';

        foreach (Event::model()->findAll($criteria) as $event) {
            if (($this->kera_unit = models\Element_OphCiExamination_Keratometry::model()->findAll())) {
                break;
            }
        }

        $chart = $this->configureChart();
        $this->addData($chart);

        $this->render(get_class($this), array('kera_unit' => $this->kera_unit, 'chart' => $chart));
    }

    /**
     * @return FlotChart
     */
    public function configureChart()
    {

        $this->kera_axis = "Keratometry";


        $chart = $this->createWidget('FlotChart', array('chart_id' => $this->chart_id))
            ->configureXAxis(array('mode' => 'time'))
            ->configureYAxis($this->kera_axis, array('position' => 'left', 'min' => 30, 'max' => 110))
            ->configureYAxis("KmaxY", array('position' => 'right', 'min' => 30, 'max' => 110))
            ->configureSeries('Kmax', array('yaxis' => "KmaxY", 'lines' => array('show' => true), 'points' => array('show' => true)))
            ->configureSeries('K1', array('yaxis' => $this->kera_axis, 'lines' => array('show' => true), 'points' => array('show' => true)))
            ->configureSeries('Intervention', array('yaxis' => $this->kera_axis, 'lines' => array('show' => true), 'points' => array('show' => true)))
            ->configureSeries('K2', array('yaxis' => $this->kera_axis, 'lines' => array('show' => true), 'points' => array('show' => true)));

        return $chart;
    }

    /**
     * @param \FlotChart $chart
     */
    public function addData(\FlotChart $chart)
    {
        foreach ($this->event_type->api->getEventsInEpisode($this->episode->patient, $this->episode) as $event) {
            if (($reading = models\Element_OphCiExamination_Keratometry::model()->findAll('event_id = ' . $event->id))) {
                $this->addKeraReading($event, $chart, $reading);
            }
        }
        if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
            $interventionDate = $api->getLastOperationDate($this->episode->patient);
            echo $interventionDate;
            $chart->addPoint("Intervention", Helper::mysqlDate2JsTimestamp($interventionDate), '1', "Intervention");
            $chart->addPoint("Intervention", Helper::mysqlDate2JsTimestamp($interventionDate), '180', "Intervention");
        }

    }

    /**
     * @param Event                                 $event
     * @param \FlotChart                            $chart
     * @param                                       $reading
     */
    protected function addKeraReading($event, \FlotChart $chart, $reading)
    {
        $series_name = "Kmax";
        $label = "{$series_name} - {$event->event_date}";
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($event->event_date), $reading[0]['right_kmax_value'], $label);
        $series_name = "K1";
        $label = "{$series_name} - {$event->event_date}";
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($event->event_date), $reading[0]['right_anterior_k1_value'], $label);
        $series_name = "K2";
        $label = "{$series_name} - {$event->event_date}";
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($event->event_date), $reading[0]['right_anterior_k2_value'], $label);
    }
}
