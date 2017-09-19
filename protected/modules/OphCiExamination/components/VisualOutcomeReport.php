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

namespace OEModule\OphCiExamination\components;

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;

class VisualOutcomeReport extends \Report implements \ReportInterface
{
    /**
     * @var int
     */
    protected $months;

    /**
     * @var int
     */
    protected $method;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $searchTemplate = 'application.modules.OphCiExamination.views.reports.visual_acuity_search';

    /**
     * @var int
     */
    protected $totalEyes = 0;

    /**
     * @var array
     */
    protected $graphConfig = array(
        'chart' => array(
            'renderTo' => '',
            'type' => 'bubble',
        ),
        'tooltip' => array(
                'useHTML' => true,
                'headerFormat' => '<b>{series.name}</b><br>',
                'pointFormat' => 'Number of eyes: {point.z}',
        ),
        'legend' => array('enabled' => false),
        'title' => array('text' => 'Visual Acuity (Distance)'),
        'subtitle' => array('text' => 'Total Eyes: 0'),
        'xAxis' => array(
            'title' => array('text' => 'Visual acuity at surgery (LogMAR)'),
            'categories' => array('>1.20', '>0.90-1.20', '>0.60-0.90', '>0.30-0.60', '>0.00-0.30', '<= 0.00'),
            'min' => 0,
            'max' => 5,
        ),
        'yAxis' => array(
            'title' => array('text' => 'Visual acuity 4 months after surgery (LogMAR)'),
            'categories' => array('>1.20', '>0.90-1.20', '>0.60-0.90', '>0.30-0.60', '>0.00-0.30', '<= 0.00'),
            'min' => 0,
            'max' => 5,
            'gridLineWidth' => 0,
            'minorGridLineWidth' => 0,
        ),
        'plotOptions' => array(
            'scatter' => array(
                'tooltip' => array(
                    'headerFormat' => '<b>Visual Acuity</b><br>',
                    'pointFormat' => '<i>Before Surgery: {point.x}</i><br /> <i>After surgery:<i/> {point.y}',
                ),
            ),
            'bubble' => array(
                'minSize' => '3%',
                'maxSize' => '17%',
            ),
        ),
    );

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->months = $app->getRequest()->getQuery('months', 4);
        $this->method = $app->getRequest()->getQuery('method', 'best');
        $this->type = $app->getRequest()->getQuery('type', 'distance');

        parent::__construct($app);
    }

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     * @param int        $months
     * @param int|string $method
     * @param string     $type
     *
     * @return array|\CDbDataReader
     */
    protected function queryData($surgeon, $dateFrom, $dateTo, $months = 4, $method = 'best', $type = 'distance')
    {
        $table = 'ophciexamination_visualacuity_reading';
        if ($type !== 'distance') {
            $table = 'ophciexamination_nearvisualacuity_reading';
        }

        $this->getExaminationEvent();

        $this->command->select('pre_examination.episode_id, note_event.episode_id, note_event.event_date as op_date, note_event.id, op_procedure.eye_id, pre_reading.method_id,
        pre_examination.event_date as pre_exam_date, post_examination.event_date as post_exam_date, pre_examination.id as pre_id, post_examination.id as post_id,
        pre_reading.value as pre_value, post_reading.value as post_value')
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id #And the operation notes procedures')
            ->join('episode', 'note_event.episode_id = episode.id')
            ->join('event  pre_examination',
                'pre_examination.episode_id = note_event.episode_id AND pre_examination.event_type_id = :examination
                AND pre_examination.event_date <= note_event.event_date',
                array('examination' => $this->examinationEvent['id'])
            )->join('event post_examination', 'post_examination.episode_id = note_event.episode_id
               AND post_examination.event_type_id = :examination
               AND post_examination.event_date >= note_event.event_date
               AND post_examination.created_date > note_event.created_date
               AND post_examination.event_date BETWEEN DATE_ADD(note_event.event_date, INTERVAL :monthsBefore MONTH) AND DATE_ADD(note_event.event_date, INTERVAL :monthsAfter MONTH)',
                array(
                    'examination' => $this->examinationEvent['id'],
                    'monthsBefore' => ($months - 1),
                    'monthsAfter' => ($months + 1),
                )
            )->join('et_ophciexamination_visualacuity pre_acuity',
                'pre_examination.id = pre_acuity.event_id
                AND (pre_acuity.eye_id = op_procedure.eye_id
                OR pre_acuity.eye_id = 3)'
            )->join('et_ophciexamination_visualacuity post_acuity',
                'post_examination.id = post_acuity.event_id
                AND (post_acuity.eye_id = op_procedure.eye_id
                OR post_acuity.eye_id = 3)'
            )->join($table.' pre_reading',
                'pre_acuity.id = pre_reading.element_id
                AND IF(op_procedure.eye_id = 1, pre_reading.side = 1, IF(op_procedure.eye_id = 2,
                                                                           pre_reading.side = 0,
                                                                           pre_reading.side IS NOT NULL))'
            )->join($table.' post_reading', 'post_acuity.id = post_reading.element_id
               AND post_reading.side = pre_reading.side
               AND post_reading.method_id = pre_reading.method_id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('pre_examination.deleted <> 1 and post_examination.deleted <> 1 and note_event.deleted <> 1')
            ->order('pre_exam_date asc, post_exam_date desc');

        if ($dateFrom) {
            $this->command->andWhere('note_event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('note_event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        if ($method) {
            if (is_numeric($method)) {
                $this->command->andWhere('pre_reading.method_id = :method', array('method' => $method));
            } else {
                $this->command
                    ->join('ophciexamination_visualacuity_method', 'ophciexamination_visualacuity_method.id = pre_reading.method_id')
                    ->andWhere('ophciexamination_visualacuity_method.name = "Glasses"
                                OR ophciexamination_visualacuity_method.name = "Contact lens"
                                OR ophciexamination_visualacuity_method.name = "Unaided"');
            }
        }

        return $this->command->queryAll();
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $data = $this->queryData($this->surgeon, $this->from, $this->to, $this->months, $this->method, $this->type);

        $dataCheck = array();
        $dataSet = array();
        $eyeDiffs = array();
        $bestValues = array();
        foreach ($data as $row) {
            if (!isset($dataCheck[$row['id']])) {
                //Do we have data for this operation?
                $dataCheck[$row['id']] = array();
            }
            if (!isset($dataCheck[$row['id']][$row['eye_id']])) { //and specifically for this eye in the op
                $dataCheck[$row['id']][$row['eye_id']] = array();
            }
            if (!isset($dataCheck[$row['id']][$row['eye_id']][$row['method_id']]) || $this->method === 'best') { //and then for this method
                $dataCheck[$row['id']][$row['eye_id']][$row['method_id']] = true;
                if ($this->method === 'best') {
                    $diffForEye = $row['pre_value'] - $row['post_value'];
                    if (!isset($eyeDiffs[$row['id'].'_'.$row['eye_id']])) {
                        $eyeDiffs[$row['id'].'_'.$row['eye_id']] = $diffForEye;
                        $bestValues[$row['id'].'_'.$row['eye_id']] = array(
                            $this->convertVisualAcuity($row['pre_value']),
                            $this->convertVisualAcuity($row['post_value']),
                        );
                    } elseif ($diffForEye > $eyeDiffs[$row['id'].'_'.$row['eye_id']]) {
                        $eyeDiffs[$row['id'].'_'.$row['eye_id']] = $diffForEye;
                        $bestValues[$row['id'].'_'.$row['eye_id']] = array(
                            $this->convertVisualAcuity($row['pre_value']),
                            $this->convertVisualAcuity($row['post_value']),
                        );
                    }
                } else {

                    //get the pre/post values now. Only the first time, order in SQL query means the first one we come
                    //across is the one closest to the op pre and post.
                    $dataSet[] = array(
                        $this->convertVisualAcuity($row['pre_value']),
                        $this->convertVisualAcuity($row['post_value']),
                    );
                }
            }
        }

        $counts = array();
        $dataSet = array_merge($dataSet, array_values($bestValues));
        foreach ($dataSet as $data) {
            if (!array_key_exists($data[0].'_'.$data[1], $counts)) {
                $counts[$data[0].'_'.$data[1]] = 0;
            }
            ++$counts[$data[0].'_'.$data[1]];
        }

        $matrix = array();

        foreach ($counts as $key => $count) {
            $xAxsis = null;
            $yAxsis = null;

            $points = explode('_', $key);

            $xPoint = (float) $points[0];
            $yPoint = (float) $points[1];

            if ($xPoint <= 0) {
                $xAxsis = 5;
            }

            if ($xPoint >= 0 && $xPoint <= 0.30) {
                $xAxsis = 4;
            }

            if ($xPoint > 0.30 && $xPoint <= 0.60) {
                $xAxsis = 3;
            }

            if ($xPoint > 0.60 && $xPoint <= 0.90) {
                $xAxsis = 2;
            }

            if ($xPoint > 0.90 && $xPoint <= 1.2) {
                $xAxsis = 1;
            }

            if ($xPoint > 1.2) {
                $xAxsis = 0;
            }

            // yAxsis

            if ($yPoint <= 0) {
                $yAxsis = 5;
            }

            if ($yPoint >= 0 && $yPoint <= 0.30) {
                $yAxsis = 4;
            }

            if ($yPoint > 0.30 && $yPoint <= 0.60) {
                $yAxsis = 3;
            }

            if ($yPoint > 0.60 && $yPoint <= 0.90) {
                $yAxsis = 2;
            }

            if ($yPoint > 0.90 && $yPoint <= 1.2) {
                $yAxsis = 1;
            }

            if ($yPoint > 1.2) {
                $yAxsis = 0;
            }

            if (isset($matrix[$xAxsis][$yAxsis])) {
                $matrix[$xAxsis][$yAxsis] += $count;
            } else {
                $matrix[$xAxsis][$yAxsis] = $count;
            }
        }

        $returnData = array();

        foreach ($matrix as $xCoord => $bubbleX) {
            foreach ($bubbleX as $yCoord => $value) {
                $returnData[] = array(
                    $xCoord,
                    $yCoord,
                    $value,
                );

                $this->totalEyes += $value;
            }
        }

        return $returnData;
    }

    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series = array(
            array(
                'data' => $this->dataSet(),
                'name' => 'Visual Outcome',
                'dataLabels' => array(
                    'enabled' => true,
                    'y' => -10, // -10 pixels down from the top
                    'style' => array(
                        'fontSize' => '13px',
                        'fontFamily' => 'Verdana, sans-serif',
                    ),
                ),
            ),
            array(
                'type' => 'line',
                'data' => array(
                    array(-1, -1),
                    array(6, 6),
                ),
                'dashStyle' => 'longdash',
                'marker' => array('enabled' => false),
                'enableMouseTracking' => false,
            ),
        );

        return json_encode($this->series);
    }

    /**
     * @return string
     */
    public function graphConfig()
    {
        $this->graphConfig['chart']['renderTo'] = $this->graphId();
        $this->graphConfig['subtitle']['text'] = 'Total Eyes: '.$this->totalEyes;

        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

    /**
     * @param $baseValue
     *
     * @return float
     */
    protected function convertVisualAcuity($baseValue)
    {
        $logMar = OphCiExamination_VisualAcuityUnit::model()->find('name = "logMAR"');
        $reading = new OphCiExamination_VisualAcuity_Reading();

        return (float) $reading->convertTo($baseValue, $logMar['id']);
    }

    /**
     * @return mixed|string
     */
    public function renderSearch()
    {
        $visualAcuityMethods = OphCiExamination_VisualAcuity_Method::model()->findAll();

        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'methods' => $visualAcuityMethods));
    }
}
