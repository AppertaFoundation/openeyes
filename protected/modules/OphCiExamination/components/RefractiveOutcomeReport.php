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

class RefractiveOutcomeReport extends \Report implements \ReportInterface
{
    /**
     * @var int
     */
    protected $months;

    /**
     * @var array
     */
    protected $procedures = array();

    /**
     * @var string
     */
    protected $searchTemplate = 'application.modules.OphCiExamination.views.reports.refractive_outcome_search';

    /**
     * @var array
     */
    protected $graphConfig = array(
        'chart' => array('renderTo' => '', 'type' => 'column'),
        'legend' => array('enabled' => false),
        'title' => array('text' => 'Refractive Outcome: mean sphere (D)'),
        'subtitle' => array('text' => 'Total eyes: {{eyes}}, ±0.5D: {{0.5}}%, ±1D: {{1}}%'),
        'xAxis' => array(
            'title' => array('text' => 'PPOR - POR (Dioptres)'),
        ),
        'yAxis' => array(
            'title' => array('text' => 'Number of eyes'),
        ),
        'tooltip' => array(
            'headerFormat' => '<b>Refractive Outcome</b><br>',
            'pointFormat' => '<i>Diff Post Op</i>: {point.category} <br /> <i>Num Eyes</i>: {point.y}',
        ),
    );

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->months = $app->getRequest()->getQuery('months', 0);
        $this->procedures = $app->getRequest()->getQuery('procedures', array());

        //if they selected all set to empty array to ignore procedure check in query
        if (in_array('all', $this->procedures)) {
            $this->procedures = array();
        }

        parent::__construct($app);
    }

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     * @param int   $months
     * @param array $procedures
     *
     * @return array|\CDbDataReader
     */
    protected function queryData($surgeon, $dateFrom, $dateTo, $months = 0, $procedures = array())
    {
        $this->getExaminationEvent();

        $this->command->select('post_examination.episode_id, note_event.episode_id, note_event.event_date as op_date, note_event.id, op_procedure.eye_id,
        post_examination.event_date as post_exam_date, post_examination.event_date as post_exam_date, post_examination.id as post_id, patient.id as patient_id,
        left_sphere, right_sphere, left_cylinder, right_cylinder, predicted_refraction')
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id #And the operation notes procedures')
            ->join('episode', 'note_event.episode_id = episode.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('event post_examination', 'post_examination.episode_id = note_event.episode_id
               AND post_examination.event_type_id = :examination
               AND post_examination.event_date >= note_event.event_date
               AND post_examination.created_date > note_event.created_date',
                array(
                    'examination' => $this->examinationEvent['id'],
                    )
            )
            ->join('et_ophciexamination_refraction', 'post_examination.id = et_ophciexamination_refraction.event_id')
            ->join('et_ophtroperationnote_cataract', 'note_event.id = et_ophtroperationnote_cataract.event_id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('post_examination.deleted <> 1 and note_event.deleted <> 1')
            ->order('post_exam_date desc');

        if ($dateFrom) {
            $this->command->andWhere('note_event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('note_event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        if ($months) {
            $this->command->andWhere('post_examination.event_date BETWEEN DATE_ADD(note_event.event_date, INTERVAL :monthsBefore MONTH) AND DATE_ADD(note_event.event_date, INTERVAL :monthsAfter MONTH)',
                array(
                    'monthsBefore' => ($months - 1),
                    'monthsAfter' => ($months + 1),
                ));
        }

        if ($procedures) {
            $this->command
                ->join('ophtroperationnote_procedurelist_procedure_assignment proc_ass', 'proc_ass.procedurelist_id = op_procedure.id')
                ->join('ophtroperationnote_procedure_element opnote', 'opnote.procedure_id = proc_ass.proc_id and proc_ass.proc_id in (:procedures)', array('procedures' => implode(',', $procedures)));
        }

        return $this->command->queryAll();
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $data = $this->queryData($this->surgeon, $this->from, $this->to, $this->months, $this->procedures);
        $count = array();

        $this->padCategories();

        // fill up the array with 0, have to send 0 to highcharts if there is no data
        foreach ($this->graphConfig['xAxis']['categories'] as $xCat) {
            $count[] = 0;
        }
        $bestvalues = array();

        foreach ($data as $row) {
            $side = 'right';
            if ($row['eye_id'] === '1') {
                $side = 'left';
            }
            $diff = (float) $row['predicted_refraction'] - ((float) $row[$side.'_sphere'] + ((float) $row[$side.'_cylinder'] / 2));

            $diff = round($diff * 2) / 2;

            $diff_index = array_search($diff, $this->graphConfig['xAxis']['categories']);

            if ($diff_index >= 0 && $diff_index <= (count($this->graphConfig['xAxis']['categories']) - 1)) {
                if (!array_key_exists($row['patient_id'].$side, $bestvalues)) {
                    $bestvalues[$row['patient_id'].$side] = $diff_index;
                } elseif (abs($this->graphConfig['xAxis']['categories'][$diff_index]) < abs($this->graphConfig['xAxis']['categories'][$bestvalues[$row['patient_id'].$side]])) {
                    $bestvalues[$row['patient_id'].$side] = $diff_index;
                }
            }
        }

        foreach ($bestvalues as $key => $diff) {
            if (!array_key_exists("$diff", $count)) {
                $count["$diff"] = 0;
            }
            ++$count["$diff"];
        }

        ksort($count, SORT_NUMERIC);

        $dataSet = array();
        foreach ($count as $category => $total) {
            $rowTotal = array((float) $category, $total);
            $dataSet[] = $rowTotal;
        }

        return $dataSet;
    }

    /**
     *
     */
    protected function padCategories()
    {
        for ($i = -10; $i <= 10; $i += 0.5) {
            $this->graphConfig['xAxis']['categories'][] = $i;
        }

        $this->graphConfig['xAxis']['min'] = 0;
        $this->graphConfig['xAxis']['max'] = count($this->graphConfig['xAxis']['categories']) - 1;
    }

    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series = array(
            array(
                'data' => $this->dataSet(),
                'name' => 'Refractive Outcome',
            ),
        );

        return json_encode($this->series);
    }

    /**
     * @return string
     */
    public function graphConfig()
    {
        if (!isset($this->series[0]['data'])) {
            $data = $this->dataSet();
        } else {
            $data = $this->series[0]['data'];
        }

        $totalEyes = 0;
        $plusOrMinusOne = 0;
        $plusOrMinusHalf = 0;
        $plusOrMinusHalfPercent = 0;
        $plusOrMinusOnePercent = 0;

        foreach ($data as $dataRow) {
            $totalEyes += (int) $dataRow[1];

            // 19 and 21 are the indexes of the -0.5 and +0.5 columns
            if ($dataRow[0] < 19 || $dataRow[0] > 21) {
                $plusOrMinusHalf += (int) $dataRow[1];
            }

            // 18 and 22 are the indexes of the -1 and +1 columns
            if ($dataRow[0] < 18 || $dataRow[0] > 22) {
                $plusOrMinusOne += (int) $dataRow[1];
            }
        }
        if ($plusOrMinusOne > 0) {
            $plusOrMinusOnePercent = number_format((($plusOrMinusOne / $totalEyes) * 100), 1, '.', '');
        }

        if ($plusOrMinusHalf > 0) {
            $plusOrMinusHalfPercent = number_format((($plusOrMinusHalf / $totalEyes) * 100), 1, '.', '');
        }

        $this->graphConfig['subtitle']['text'] = str_replace(
            array('{{eyes}}', '{{0.5}}', '{{1}}'),
            array($totalEyes, $plusOrMinusHalfPercent, $plusOrMinusOnePercent),
            $this->graphConfig['subtitle']['text']
        );

        $this->graphConfig['chart']['renderTo'] = $this->graphId();

        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

    /**
     * @return array
     */
    protected function cataractProcedures()
    {
        $cataractProcedures = array();
        $cataractElement = \ElementType::model()->findByAttributes(array('name' => 'Cataract'));
        if ($cataractElement) {
            $procedure = new \Procedure();
            $cataractProcedures = $procedure->getProceduresByOpNote($cataractElement['id']);
        }

        return $cataractProcedures;
    }

    /**
     * @return mixed|string
     */
    public function renderSearch()
    {
        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'procedures' => $this->cataractProcedures()));
    }
}
