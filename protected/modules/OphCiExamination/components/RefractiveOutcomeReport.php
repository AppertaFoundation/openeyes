<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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
            'categories' => array('-0.5', '0', '0.5'),
        ),
        'yAxis' => array(
            'title' => array('text' => 'Number of patients'),
        ),
        'tooltip' => array(
            'headerFormat' => '<b>Refractive Outcome</b><br>',
            'pointFormat' => '<i>Diff Post Op</i>: {point.category} <br /> <i>Num Eyes</i>: {point.y}'
        )
    );

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->months = $app->getRequest()->getQuery('months', 0);
        $this->procedures = $app->getRequest()->getQuery('procedures', array());

        //if they selected all set to empty array to ignore procedure check in query
        if(in_array('all', $this->procedures)){
            $this->procedures = array();
        }

        parent::__construct($app);
    }

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     * @param int $months
     * @param array $procedures
     * @return array|\CDbDataReader
     */
    protected function queryData($surgeon, $dateFrom, $dateTo, $months = 0, $procedures = array())
    {
        $this->getExaminationEvent();

        $this->command->select('post_examination.episode_id, note_event.episode_id, note_event.event_date as op_date, note_event.id, op_procedure.eye_id,
        post_examination.event_date as post_exam_date, post_examination.event_date as post_exam_date, post_examination.id as post_id,
        left_sphere, right_sphere, left_cylinder, right_cylinder, predicted_refraction')
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id #And the operation notes procedures')
            ->join('episode', 'note_event.episode_id = episode.id')
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
            ->andWhere('post_examination.deleted=0')
            ->andWhere('note_event.deleted=0')
            ->order('post_exam_date desc');

        if ($dateFrom) {
            $this->command->andWhere('note_event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('note_event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        if($months){
            $this->command->andWhere('post_examination.event_date BETWEEN DATE_ADD(note_event.event_date, INTERVAL :monthsBefore MONTH) AND DATE_ADD(note_event.event_date, INTERVAL :monthsAfter MONTH)',
                array(
                    'monthsBefore' => ($months - 1),
                    'monthsAfter' => ($months + 1)
                ));
        }

        if($procedures){
            $this->command
                ->join('ophtroperationnote_procedurelist_procedure_assignment proc_ass', 'proc_ass.procedurelist_id = op_procedure.id')
                ->join('ophtroperationnote_procedure_element opnote', 'opnote.procedure_id = proc_ass.proc_id and proc_ass.proc_id in (:procedures)', array('procedures' => join(',', $procedures)));
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

        foreach ($data as $row) {
            $side = 'right';
            if ($row['eye_id'] === '1') {
                $side = 'left';
            }
            $diff = (float)$row['predicted_refraction'] - ((float)$row[$side . '_sphere'] - ((float)$row[$side . '_cylinder'] / 2));
            $diff = number_format($diff, 1);
            if (!array_key_exists("$diff", $count)) {
                $count["$diff"] = 0;
                $this->graphConfig['xAxis']['categories'][] = $diff;
            }
            $count["$diff"]++;
        }

        sort($this->graphConfig['xAxis']['categories'], SORT_NUMERIC);
        $this->padCategories();
        $dataSet = array();
        foreach ($this->graphConfig['xAxis']['categories'] as $graphCategory) {
            $rowTotal = 0;
            foreach ($count as $category => $total) {
                if ($category == $graphCategory) {
                    $rowTotal = $total;
                }
            }
            $dataSet[] = $rowTotal;
        }

        return $dataSet;
    }

    /**
     *
     */
    protected function padCategories()
    {
        $top = array_pop($this->graphConfig['xAxis']['categories']);
        $bottom = array_shift($this->graphConfig['xAxis']['categories']);
        $bigger = $bottom;
        if(abs($top) > abs($bottom)){
            $bigger = $top;
        }

        $this->graphConfig['xAxis']['categories'] = array();
        $upperLimit = abs($bigger);
        $lowerLimit = 0 - $upperLimit;
        for($i = $lowerLimit; $i <= $upperLimit; $i += 0.5 ){
            $this->graphConfig['xAxis']['categories'][] = "$i";
        }
        
        $this->graphConfig['xAxis']['min'] = 0;
        $this->graphConfig['xAxis']['max'] = count($this->graphConfig['xAxis']['categories'])-1;
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
        if(!isset($this->series[0]['data'])){
            $data = $this->dataSet();
        } else {
            $data = $this->series[0]['data'];
        }

        $totalEyes = 0;
        $plusOrMinusOne = 0;
        $plusOrMinusHalf = 0;
        $plusOrMinusHalfPercent = 0;
        $plusOrMinusOnePercent = 0;

        foreach($data as $i => $category){
            $totalEyes += $category;
            $categoryText = $this->graphConfig['xAxis']['categories'][$i];
            
            $categoryFloat = number_format($categoryText, 2, '.', '');
            if($categoryFloat < -1 || $categoryFloat > 1){
                $plusOrMinusOne += $category;
            }
            
            if($categoryFloat < -0.5 || $categoryFloat > 0.5){
                $plusOrMinusHalf += $category;
            }
            
        }
     
        if($plusOrMinusOne > 0){
            $plusOrMinusOnePercent = number_format((($plusOrMinusOne / $totalEyes) * 100), 1, '.', '' );
        }
        
        if($plusOrMinusHalf > 0){
            $plusOrMinusHalfPercent = number_format((($plusOrMinusHalf / $totalEyes) * 100) ,1, '.', '');
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
        if($cataractElement){
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