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

    /**
     * @throws CException
     * @phpcs:disable  PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function run_right_side()
    {
        $this->render(get_class($this) . '_Right');
    }

    /**
     * @param int $widgets_no
     * @throws CException
     * @phpcs:disable  PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function run_oescape($widgets_no = 1)
    {
        $this->resolveVAUnit();

        $va_ticks = $this->getVAChartTicks();


        $this->va_ticks = $va_ticks;
        $this->va_axis = "{$this->va_unit->name}";

        $this->render("OphCiExamination_OEscape_MedicalRetinalHistory", array('va_unit' => $this->va_unit));
    }
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
        $chart->addPoint($series_name, Helper::mysqlDate2JsTimestamp($oct->event->event_date), $sft, "{$series_name}\n{$sft} µm");
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

    public function getCRTData() {
        $crt_data_list = array('right' => array(), 'left' => array());
        foreach ($this->event_type->api->getEvents($this->patient, false) as $event) {
            $oct = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_OCT');
            if ($oct) {
                if ($oct->hasRight()) {
                    $crt = $oct->{"right_sft"};
                    array_push($crt_data_list['right'], array('y'=>$crt?(float)$crt:0, 'x'=>Helper::mysqlDate2JsTimestamp($oct->event->event_date)));
                }
                if ($oct->hasLeft()) {
                    $crt = $oct->{"left_sft"};
                    array_push($crt_data_list['left'], array('y'=>$crt?(float)$crt:0, 'x'=>Helper::mysqlDate2JsTimestamp($oct->event->event_date)));
                }
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($crt_data_list[$side], array("EpisodeSummaryWidget","sortData"));
        }
        return $crt_data_list;
    }


    public function getPlotlyCRTData() {
        $crt_data_list = array('right' => array(), 'left' => array());
        $crt_plotly_list = array('right'=>array('x'=>array(), 'y'=>array()), 'left'=>array('x'=>array(), 'y'=>array()));


        foreach ($this->event_type->api->getEvents($this->patient, false) as $event) {
            $oct = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_OCT');
            if ($oct) {
                if ($oct->hasRight()) {
                    $crt = $oct->{"right_sft"};
                    array_push($crt_data_list['right'], array('y'=>$crt?(float)$crt:0, 'x'=>date('Y-m-d', Helper::mysqlDate2JsTimestamp($oct->event->event_date)/1000)));
                }
                if ($oct->hasLeft()) {
                    $crt = $oct->{"left_sft"};
                    array_push($crt_data_list['left'], array('y'=>$crt?(float)$crt:0, 'x'=>date('Y-m-d', Helper::mysqlDate2JsTimestamp($oct->event->event_date)/1000)));
                }
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($crt_data_list[$side], array("EpisodeSummaryWidget","sortData"));
            foreach ($crt_data_list[$side] as $item) {
                $crt_plotly_list[$side]['x'][] = $item['x'];
                $crt_plotly_list[$side]['y'][] = $item['y'];
            }
        }
        return $crt_plotly_list;
    }

    public function getLossLetterMoreThan5(){
        $loss_letter_five_list = array('right' => array(), 'left' => array());
        foreach ($this->event_type->api->getEvents($this->patient, false) as $event) {
            $inj_mang = $event->getElementByClass('OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagementComplex');
            if ($inj_mang) {
                foreach (['left','right'] as $side) {
                    if ($inj_mang->hasEye($side)) {
                        foreach ($inj_mang->{$side.'_answers'} as $ra) {
                            if ($ra['question_id']==4) {
                                ${$side.'_answer'} = $ra['answer'];
                            } else {
                                ${$side.'_answer'} = null;
                            }
                        };
                        if (isset(${$side.'_answer'})&& ${$side.'_answer'}) {
                            array_push($loss_letter_five_list[$side], array( 'title'=>">5", 'info'=>">5", 'x'=>Helper::mysqlDate2JsTimestamp($inj_mang->created_date)));
                        }
                    }
                }
            }
        }
        foreach (['left', 'right'] as $side) {
            usort($loss_letter_five_list[$side], array("EpisodeSummaryWidget","sortData"));
        }
        return $loss_letter_five_list;
    }

    public function getInjectionsList(){
        $injection_list = array('right' => array(), 'left' => array());
        $injectionApi = Yii::app()->moduleAPI->get('OphTrIntravitrealinjection');
        if ($injectionApi) {
            foreach (['left','right'] as $eye_side) {
                foreach ($injectionApi->previousInjections($this->patient, $this->episode, $eye_side) as $injection) {
                    if (empty($injection_list[$eye_side])||!array_key_exists($injection[$eye_side.'_drug'], $injection_list[$eye_side])) {
                        $injection_list[$eye_side][$injection[$eye_side.'_drug']] = array();
                    }
                    array_push(
                        $injection_list[$eye_side][$injection[$eye_side.'_drug']],
                        array('title'=>$eye_side=='right'?'R':'L', 'info'=>'', 'x'=>Helper::mysqlDate2JsTimestamp($injection['date']))
                    );
                }
            }
        }
        foreach ( $injection_list as &$injection_side) {
            foreach ($injection_side as &$injection_type) {
                usort($injection_type, array("EpisodeSummaryWidget","sortData"));
                foreach ($injection_type as $i=>&$value) {
                    if ($i>0) {
                        $year = floor(($injection_type[$i]['x'] - $injection_type[$i-1]['x'])/1000/31556926);
                        $month = floor((($injection_type[$i]['x'] - $injection_type[$i-1]['x'])/1000 - 31556926*$year)/2629743);
                        $day = floor((($injection_type[$i]['x'] - $injection_type[$i-1]['x'])/1000 - 31556926*$year - 2629743*$month)/86400);
                        $value['info'] = $year.'Y, '.$month.'M, '.$day.'D ';
                    }
                }
            }
        }
        return $injection_list;
    }

    public function getDocument(){
        $MR_documents = array('right'=>array(), 'left'=>array());
        $event_type = EventType::model()->find('class_name=?', array('OphCoDocument'));
        $events = Event::model()->getEventsOfTypeForPatient($event_type, $this->patient);
        foreach ($events as $event) {
            $doc = $event->getElementByClass("Element_OphCoDocument_Document");
            if ($doc&&$doc->sub_type->name==='OCT') {
                $single_doc = $doc->single_document;
                $left_doc = $doc->left_document;
                $right_doc = $doc->right_document;
                $date = Helper::mysqlDate2JsTimestamp($event->event_date);
                if ($single_doc) {
                    array_push(
                        $MR_documents['right'],
                        array('doc_id'=>$single_doc->id, 'doc_name'=>$single_doc->name, 'date'=>$date)
                    );
                    array_push(
                        $MR_documents['left'],
                        array('doc_id'=>$single_doc->id, 'doc_name'=>$single_doc->name, 'date'=>$date)
                    );
                }
                if ($right_doc) {
                    array_push(
                        $MR_documents['right'],
                        array('doc_id'=>$right_doc->id, 'doc_name'=>$right_doc->name, 'date'=>$date)
                    );
                }
                if ($left_doc) {
                    array_push(
                        $MR_documents['left'],
                        array('doc_id'=>$left_doc->id, 'doc_name'=>$left_doc->name, 'date'=>$date)
                    );
                }
            }
        }
        return $MR_documents;
    }

    public function getDocDateMapID() {
        $id_date_map = array('right'=>array(), 'left'=>array());
        $event_type = EventType::model()->find('class_name=?', array('OphCoDocument'));
        $events = Event::model()->getEventsOfTypeForPatient($event_type, $this->patient);
        foreach ($events as $event) {
            $doc = $event->getElementByClass("Element_OphCoDocument_Document");
            if ($doc) {
                $single_doc = $doc->single_document;
                $left_doc = $doc->left_document;
                $right_doc = $doc->right_document;
                $date = date('Y-m-d', Helper::mysqlDate2JsTimestamp($event->created_date)/1000);
                if ($single_doc) {
                    $id_date_map['right'][$single_doc->id]=$date;
                    $id_date_map['left'][$single_doc->id]=$date;
                }
                if ($right_doc) {
                    $id_date_map['right'][$right_doc->id]=$date;
                }
                if ($left_doc) {
                    $id_date_map['left'][$left_doc->id]=$date;
                }
            }
        }
        return $id_date_map;
    }

    public function getVaData() {
        $va_data_list = parent::getVaData();
        $id_date_map = $this->getDocDateMapID();
        foreach (['left', 'right'] as $side) {
            foreach ($va_data_list[$side] as &$va) {
                $va_date = date('Y-m-d', $va['x']/1000);
                $id = array_search($va_date, $id_date_map[$side]);
                $va['oct'] = $id?:null;
                $va['side'] = $side;
            }
        }

        return $va_data_list;
    }

    public function getOctFly(){
        $id_date_map = array('right'=>array(), 'left'=>array());

        $event_type = EventType::model()->find('class_name=?', array('OphCoDocument'));
        $events = Event::model()->getEventsOfTypeForPatient($event_type, $this->patient);
        foreach ($events as $event) {
            $doc = $event->getElementByClass("Element_OphCoDocument_Document");
            if ($doc&&$doc->sub_type->name==='OCT') {
                $single_doc = $doc->single_document;
                $left_doc = $doc->left_document;
                $right_doc = $doc->right_document;
                $date = date('Y-m-d', Helper::mysqlDate2JsTimestamp($event->event_date)/1000);
                if ($single_doc) {
                    $id_date_map['right'][] = array('x'=>$date, 'id'=>$single_doc->id);
                    $id_date_map['left'][] = array('x'=>$date, 'id'=>$single_doc->id);
                }
                if ($right_doc) {
                    $id_date_map['right'][] = array('x'=>$date, 'id'=>$right_doc->id);
                }
                if ($left_doc) {
                    $id_date_map['left'][] = array('x'=>$date, 'id'=>$left_doc->id);
                }
            }
        }

        return $id_date_map;
    }
}
