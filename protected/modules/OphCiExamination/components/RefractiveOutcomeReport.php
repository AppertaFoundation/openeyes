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

    protected $plotlyConfig = array(
      'type' => 'bar',
      'showlegend' => false,
      'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
      'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
      'title' => '',
      'font' => array(
        'family' => 'Roboto,Helvetica,Arial,sans-serif',
      ),
      'xaxis' => array(
        'title' => 'POR - PPOR (Dioptres)',
        'ticks' => 'outside',
        'tickvals' => [],
        'ticktext' => [],
        'tickangle' => -45,
        'tickmode' => 'linear',
      ),
      'yaxis' => array(
        'title' => 'Number of eyes',
        'showline' => true,
        'showgrid' => true,
        'ticks' => 'outside',
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
        $iol_binds = array();
        // The variable below contains a prtial SQl command that searches for whether an IOL has been inserted during an operation by searching for keywords search as PCIOl in the eyedraw canvas - OE-9419
        $iol_classes = '('. implode(
            'OR',
            array_map(
                function ($iol_class) {
                    return " et_ophtroperationnote_cataract.eyedraw LIKE CONCAT('%', :".$iol_class.", '%') ";
                },
                \Yii::app()->params['eyedraw_iol_classes']
            )
        ) . ')';

        foreach (\Yii::app()->params['eyedraw_iol_classes'] as $iol_class) {
            $iol_binds[$iol_class] = $iol_class;
        }

        $this->command
            ->select('
                  post_examination.episode_id
                , note_event.episode_id
                , note_event.event_date as op_date
                , note_event.id
                , op_procedure.eye_id
                , MAX(post_examination.event_date) as post_exam_date
                , post_examination.id as post_id
                , patient.id as patient_id
                , left_refraction.sphere as left_sphere
                , right_refraction.sphere as right_sphere
                , left_refraction.cylinder as left_cylinder
                , right_refraction.cylinder as right_cylinder
                , predicted_refraction
                , note_event.id as event_id
            ')
            ->from('et_ophtroperationnote_surgeon')
            ->join('event note_event', 'note_event.id = et_ophtroperationnote_surgeon.event_id')
            ->join('et_ophtroperationnote_procedurelist op_procedure', 'op_procedure.event_id = note_event.id #And the operation notes procedures')
            ->join('episode', 'note_event.episode_id = episode.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join(
                'event post_examination',
                'post_examination.episode_id = note_event.episode_id
               AND post_examination.event_type_id = :examination
               AND post_examination.event_date >= note_event.event_date
               AND post_examination.created_date > note_event.created_date',
                array(
                    'examination' => $this->examinationEvent['id'],
                )
            )
            ->join('et_ophciexamination_refraction', 'post_examination.id = et_ophciexamination_refraction.event_id')
            ->join('ophciexamination_refraction_reading right_refraction', 'right_refraction.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = et_ophciexamination_refraction.id
                    AND single_reading.eye_id = 2
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )')
            ->join('ophciexamination_refraction_reading left_refraction', 'left_refraction.id = (
                    /* Need to ensure we only get one reading result, ordered by the priority of the type */
                    SELECT single_reading.id
                    FROM ophciexamination_refraction_reading single_reading
                    LEFT JOIN ophciexamination_refraction_type rt
                    ON single_reading.type_id = rt.id
                    WHERE element_id = et_ophciexamination_refraction.id
                    AND single_reading.eye_id = 1
                    ORDER BY -rt.priority DESC /* Null indicates an "other" type, which negative desc ordering will make last */
                    LIMIT 1
                )')
            ->join('et_ophtroperationnote_cataract', 'note_event.id = et_ophtroperationnote_cataract.event_id')
            ->where('post_examination.deleted <> 1 and note_event.deleted <> 1')
            ->andWhere($iol_classes, $iol_binds)
            ->group('note_event.id, op_procedure.eye_id')
            ->order('post_exam_date desc');

        if ($surgeon !== 'all') {
            $this->command->andWhere('surgeon_id = :surgeon', array('surgeon' => $surgeon));
        }

        if ($dateFrom) {
            $this->command->andWhere('note_event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('note_event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        if ($months) {
            $this->command->andWhere(
                'post_examination.event_date BETWEEN DATE_ADD(note_event.event_date, INTERVAL :monthsBefore MONTH) AND DATE_ADD(note_event.event_date, INTERVAL :monthsAfter MONTH)',
                array(
                    'monthsBefore' => ($months - 1),
                    'monthsAfter' => ($months + 1),
                )
            );
        }

        if ($procedures) {
            $this->command
                ->join('ophtroperationnote_procedurelist_procedure_assignment proc_ass', 'proc_ass.procedurelist_id = op_procedure.id')
                ->join('ophtroperationnote_procedure_element opnote', 'opnote.procedure_id = proc_ass.proc_id ')
                ->andWhere('proc_ass.proc_id in ('.implode(',', $procedures).')');
        }
        return $this->command->queryAll();
    }

    /**
     * @return array
     */

    public function dataset()
    {

        $ret = array();

        if ($this->allSurgeons) {
            $surgeon = 'all';
        } else {
            $surgeon = $this->surgeon;
        }

        $data = $this->queryData($surgeon, $this->from, $this->to, $this->months, $this->procedures);

        foreach ($data as $row) {
            $side = 'right';
            if ($row['eye_id'] === '1') {
                $side = 'left';
            }
            // calculate the difference between actual reading and predicted one
            $diff = ((float) $row[$side.'_sphere'] + ((float) $row[$side.'_cylinder'] / 2)) - (float) $row['predicted_refraction'];
            $diff = round($diff * 2) / 2;
            // there was some -0 value, the following line is just in case
            $diff = (float)$diff === (float)-0 ? 0 : $diff;
            
            $ret_ind = array_search($diff, array_column($ret, 'text'));

            if ($ret_ind === false) {
                array_push($ret, array(
                    'text' => (float)$diff,
                    'count' => 1,
                    'event_id' => array($row['event_id'])
                ));
            } else {
                $ret[$ret_ind]['count'] += 1;
                array_push($ret[$ret_ind]['event_id'], $row['event_id']);
            }
        }
        // this can be used to sort decimal / float number as defaultly usort will convert float to integer
        usort($ret, function ($a, $b) {
            $result = 0;
            $result = $a['text'] > $b['text'] ? 1 : -1;
            return $result;
        });
        // use the data to dynamically create ployly config
        $this->padPlotlyCategories($ret);
        $dataSet = array();
        foreach ($ret as $category => $val) {
            $rowTotal = array(
                'reading' => $val['text'],
                'rowTotal' => $val['count'],
                'eventList' => $val['event_id']
            );
            $dataSet[$category] = $rowTotal;
        }
        $this->setChartTitle($dataSet);
        return $dataSet;
    }

    protected function setChartTitle($data)
    {
        $totalEyes = 0;
        $plusOrMinusHalfPercent = 0;
        $plusOrMinusOnePercent = 0;
        $plusOrMinusOne = 0;
        $plusOrMinusHalf = 0;
        if ($data) {
            foreach ($data as $dataRow) {
                $totalEyes += (int) $dataRow['rowTotal'];
                $tickText = $dataRow['reading'];
                if ($tickText < -0.5 || $tickText > 0.5) {
                    $plusOrMinusHalf += (int) $dataRow['rowTotal'];
                }
    
                if ($tickText < -1 || $tickText > 1) {
                    $plusOrMinusOne += (int) $dataRow['rowTotal'];
                }
            }
            if ($plusOrMinusOne > 0) {
                $plusOrMinusOnePercent = number_format((($plusOrMinusOne / $totalEyes) * 100), 1, '.', '');
            }
    
            if ($plusOrMinusHalf > 0) {
                $plusOrMinusHalfPercent = number_format((($plusOrMinusHalf / $totalEyes) * 100), 1, '.', '');
            }
        }

        $this->plotlyConfig['title'] = 'Refractive Outcome: mean sphere (D)<br>'
        . '<sub>Total eyes: ' . $totalEyes
        . ', ±0.5D: ' .$plusOrMinusHalfPercent
        . '%, ±1D: '.$plusOrMinusOnePercent.'%</sub>';
    }
    /**
     * set plot xaxis range,  tick
     */
    protected function padPlotlyCategories($data = null)
    {
        // initial the value in case the $data is null
        $xaxis_max_val = 40;
        $step = 2.5;
        if ($data) {
            $xaxis_max_val = max(
                abs(
                    max(
                        array_column($data, 'text')
                    )
                ),
                abs(
                    min(
                        array_column($data, 'text')
                    )
                )
            );
            $step = $xaxis_max_val > 10 ? ceil(($xaxis_max_val * 2 / 40) * 2) / 2 : 0.5;
        }
        $this->plotlyConfig['xaxis']['range'] = [-$xaxis_max_val - $step, $xaxis_max_val + $step];
        $this->plotlyConfig['xaxis']['dtick'] = $step;
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

    public function tracesJson()
    {
        $dataset = $this->dataset();
        $trace1 = array(
          'name' => 'Refractive Outcome',
          'type' => 'bar',
          'marker' => array(
            'color' => '#7cb5ec',
          ),
          'x' => array_map(function ($item) {
            return $item['reading'];
          }, $dataset),
          'y'=> array_map(function ($item) {
            return $item['rowTotal'];
          }, $dataset),
            'customdata' => array_map(function ($item) {
                return $item['eventList'];
            }, $dataset),
          'hovertext' => array_map(function ($item) {
            return '<b>Refractive Outcome</b><br><i>Diff Post</i>: '
              . $item['reading']
              .'<br><i>Num Eyes:</i> '.$item['rowTotal'];
          }, $dataset),
          'hoverinfo' => 'text',
          'hoverlabel' => array(
            'bgcolor' => '#fff',
            'bordercolor' => '#7cb5ec',
            'font' => array(
              'color' => '#000',
            ),
          ),
        );

        $traces = array($trace1);
        return json_encode($traces);
    }
    /**
     * @return string
     */
    public function plotlyConfig()
    {
        return json_encode($this->plotlyConfig);
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
    public function renderSearch($analytics = false)
    {
        if ($analytics) {
            $this->searchTemplate = 'application.modules.OphCiExamination.views.reports.refractive_outcome_search_analytics';
        }
        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'procedures' => $this->cataractProcedures()));
    }
}
