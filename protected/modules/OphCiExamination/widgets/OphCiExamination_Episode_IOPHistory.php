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
use OEModule\OphCiExamination\models as ExamModels;
use OEModule\OphCiPhasing\models as PhasingModels;

class OphCiExamination_Episode_IOPHistory extends \EpisodeSummaryWidget
{
    public $collapsible = true;
    public $openOnPageLoad = true;

    public function run()
    {
        $chart = $this->createWidget('FlotChart', array('chart_id' => 'iop-history-chart'))
            ->configureChart(array(
                'colors' => array('#4daf4a', '#984ea3', '#4daf4a', '#984ea3'),
            ))
            ->configureXAxis(array('mode' => 'time'))
            ->configureYAxis('mmHg', array(
                'min' => 0,
                'max' => 80,
            ))
            ->configureSeries('RE', array(
                'points' => array('show' => true),
                'lines' => array(
                    'show' => true,
                ),
            ))
            ->configureSeries('LE', array(
                'points' => array('show' => true),
                'lines' => array('show' => true),
            ))
            ->configureSeries('Target RE', array(
                'colors' => array('#fff', '#fff', '#fff'),
                'points' => array('show' => true),
                'dashes' => array(
                    'show' => true,
                    'style' => array(6),
                ),
            ))
            ->configureSeries('Target LE', array(
                'points' => array('show' => true),
                'dashes' => array(
                    'show' => true,
                    'style' => array(6),
                ),
            ));

        $events = $this->event_type->api->getEvents($this->episode->patient, false);

        foreach ($events as $event) {
            if (($iop = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'))) {
                $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
                $this->addIop($chart, $iop, $timestamp, 'right');
                $this->addIop($chart, $iop, $timestamp, 'left');
            }
        }

        $plan = $this->event_type->api->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan',
            $this->episode->patient,
            false
        );

        if ($plan) {
            $this->addTargetIop($chart, $plan, 'right');
            $this->addTargetIop($chart, $plan, 'left');
        }

        $this->render('OphCiExamination_Episode_IOPHistory', array('chart' => $chart));
    }

    public function run_oescape($widgets_no = 1)
    {
        $this->render('OphCiExamination_OEscape_IOPHistory');
    }

    protected function addIop(\FlotChart $chart, ExamModels\Element_OphCiExamination_IntraocularPressure $iop, $timestamp, $side)
    {
        if (($reading = $iop->getReading($side))) {
            $seriesName = strtoupper($side[0]).'E';
            $chart->addPoint($seriesName, $timestamp, $reading, "{$reading} mmHg");
        }
    }

    protected function addTargetIop(\FlotChart $chart, ExamModels\Element_OphCiExamination_OverallManagementPlan $plan, $side)
    {
        if (($target = $plan->{"{$side}_target_iop"})) {
            $seriesName = 'Target '.strtoupper($side[0]).'E';
            $chart->addPoint($seriesName, $chart->getXMin(), $target->name, "{$target->name} mmHg");
            $chart->addPoint($seriesName, $chart->getXMax(), $target->name, "{$target->name} mmHg");
        }
    }

    //Currently only gets value for examination data.
    public function getIOPData()
    {
        $iop_data_list = array('right'=>array(), 'left'=>array());
        $events = $this->event_type->api->getEvents($this->patient, false);
        foreach ($events as $event) {
            if (($iop = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'))) {
                $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
                foreach (['left', 'right'] as $side) {
                    if ($reading = $iop->getReading($side)) {
                        array_push($iop_data_list[$side], array('x'=>$timestamp, 'y'=>(float)$reading));
                    }
                }
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($iop_data_list[$side], function ($item1, $item2) {
                if ($item1['x'] === $item2['x']) {
                    return 0;
                }
                return $item1['x'] < $item2['x'] ? -1 : 1;
            });
        }
        return $iop_data_list;
    }

    //Gets values for examination and phasing data
    public function getPlotlyIOPData()
    {
        $iop_data_list = array(
            'left' => array(),
            'right' => array(),
        );

                $exam_events = Event::model()->getEventsOfTypeForPatient(EventType::model()->find('name=:name', array(':name'=>"Examination")), $this->patient);
                $phasing_events = Event::model()->getEventsOfTypeForPatient(EventType::model()->find('name=:name', array(':name'=>"Phasing")), $this->patient);

        //add exam readings
        foreach ($exam_events as $exam_event) {
                        //Try to get correct element type
                        $iop = $exam_event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure');
                        //If successful
            if ($iop) {
                //Get timestamp and event type
                                $timestamp = Helper::mysqlDate2JsTimestamp($exam_event->event_date);
                $event_type_name = strtolower(EventType::model()->findByPk($exam_event->event_type_id)->name);
                foreach (['left', 'right'] as $side) {
                    $readings = $iop->getReadings($side);
                    $comment = $iop->{$side . '_comments'} ?: '';
                    if (count($readings) > 0) {
                        foreach ($readings as $reading) {
                                $iop_data_list[$side][] = array(
                                    'id' => $exam_event->id,
                                    'event_type' => $event_type_name,
                                    'timestamp' => $timestamp,
                                    'reading' => $reading,
                                    'comment'=> $comment
                                );
                        }
                    }
                }
            }
        }

        //add phasing readings
        foreach ($phasing_events as $phasing_event) {
                        //Try to get correct element type
                        $iop = $phasing_event->getElementByClass('Element_OphCiPhasing_IntraocularPressure');
                        //If successful
            if ($iop) {
                            //Get timestamp and event type
                                $timestamp = Helper::mysqlDate2JsTimestamp($phasing_event->event_date);
                $event_type_name = strtolower(EventType::model()->findByPk($phasing_event->event_type_id)->name);
                foreach (['left', 'right'] as $side) {
                    $readings = $iop->getReadings($side);
                    $comment = $iop->{$side . '_comments'} ?: '';
                    if (count($readings) > 0) {
                        foreach ($readings as $reading) {
                            if ($reading) {
                                $iop_data_list[$side][] = array(
                                    'id' => $phasing_event->id,
                                    'event_type' => $event_type_name,
                                    'timestamp' => $timestamp,
                                    'reading' => $reading,
                                    'comment'=> $comment);
                            }
                        }
                    }
                }
            } else {
                OELog::log("Could not find IOP phasing element for event");
            }
        }
        //must be sorted to display in the correct way on the graph
        foreach (['left', 'right'] as $side) {
            usort($iop_data_list[$side], function ($item1, $item2) {
                if ($item1['timestamp'] === $item2['timestamp']) {
                    return 0;
                }
                return $item1['timestamp'] < $item2['timestamp'] ? -1 : 1;
            });
        }
                return $iop_data_list;
    }

    public function getTargetIOP()
    {
      //set the default value of iop target
        $iop_target = array('right'=>0, 'left'=>0);
        $plan = $this->event_type->api->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan',
            $this->patient,
            false
        );
        if ($plan) {
            foreach (['left', 'right'] as $side) {
                if (($target = $plan->{$side."_target_iop"})) {
                    $iop_target[$side] = (float)$target->name;
                }
            }
        }
        return $iop_target;
    }

    public static function getDrillthroughIOPDataForEvent($patient)
    {
        //Get both types of event into their own lists
        $exam_events = Event::model()->getEventsOfTypeForPatient(EventType::model()->find('name=:name', array(':name'=>"Examination")), $patient);
        $phasing_events = Event::model()->getEventsOfTypeForPatient(EventType::model()->find('name=:name', array(':name'=>"Phasing")), $patient);

        //Declare unified event list and add all relevant events

                $output = array();

        if ($exam_events) {
            foreach ($exam_events as $event) {
                        $iop = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure');
                if ($iop) {
                    $readings = self::getExaminationReadingsFormatted($iop);
                    if (count($readings) > 0) {
                        array_push($output, ...$readings);
                    }
                }
            }
        }

        if ($phasing_events) {
            foreach ($phasing_events as $event) {
                $iop = $event->getElementByClass('Element_OphCiPhasing_IntraocularPressure');
                if ($iop) {
                                        $readings = self::getPhasingReadingsFormatted($iop);
                    if (count($readings) > 0) {
                        array_push($output, ...$readings);
                    }
                }
            }
        }

        return $output;
    }

    static function getExaminationReadingsFormatted($iop_element)
    {
                $event_name = EventType::model()->findByPk($iop_element->event->event_type_id)->name;

                $readings_array = array();

                $iop_vals = ExamModels\OphCiExamination_IntraocularPressure_Value::model()->findAll("element_id=:element_id", array(":element_id" => $iop_element->id));

        foreach ($iop_vals as $reading) {
                $side = strtolower($reading->eye);

            if ($reading->instrument->name != 'Palpation') {
                                $readings_array[] = array(
                                    'event_id' => $iop_element->event_id,
                                    'event_name' => $event_name,
                                    'event_date' => $iop_element->event->event_date,
                                    'eye' => $side,
                                    'instrument_name' => $reading->instrument->name,
                                    'dilated' => 'N/A',
                                    'reading_date' => date('d/m/y', strtotime($iop_element->event->event_date)),
                                    'reading_time' => date('G:i', strtotime($reading->reading_time)),
                                    'raw_value' => $reading->reading->value,
                                    'comments' => $iop_element->{$side . '_comments'}
                                );

                                OELog::log("reading_time: ".$reading->reading_time);
            }
        }


            return $readings_array;
    }

    static function getPhasingReadingsFormatted($iop_element)
    {
                $event_name = EventType::model()->findByPk($iop_element->event->event_type_id)->name;

                $readings_array = array();

        foreach ($iop_element->readings as $reading) {
                $side = strtolower($reading->getSideAsString());

                $readings_array[] = array(
                        'event_id' => $iop_element->event_id,
                        'event_name' => $event_name,
                        'event_date' => $iop_element->event->event_date,
                        'eye' => $side,
                        'instrument_name' => $iop_element->{$side . '_instrument'}->name,
                        'dilated' => $iop_element->{$side . '_dilated'} === 1 ? 'Yes' : 'No',
                        'reading_date' => date('d/m/y', strtotime($iop_element->event->event_date)),
                        'reading_time' => date('G:i', strtotime($reading->measurement_timestamp)),
                        'raw_value' => $reading->value,
                        'comments' => $iop_element->{$side . '_comments'}
                );

                OELog::log("measurement_timestamp: ".$reading->measurement_timestamp);
        }

                return $readings_array;
    }
}
