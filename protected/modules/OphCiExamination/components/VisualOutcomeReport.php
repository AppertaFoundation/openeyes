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


class VisualOutcomeReport extends \Report implements \ReportInterface
{
    protected $graphConfig = array(
        'chart' => array('renderTo' => ''),
        'title' => array('text' => 'Visual Acuity'),
        'xAxis' => array(
            'title' => array('text' => 'At Surgery'),
        ),
        'yAxis' => array(
            'title' => array('text' => 'Months after Surgery'),
        ),
        'plotOptions' => array('scatter' => array(
            'tooltip' => array(
                'headerFormat' => '<b>Visual Acuity</b><br>',
                'pointFormat' => 'Before Surgery {point.x}, And after {point.y}'
            )
        ))
    );

    protected function queryData($surgeon, $dateFrom, $dateTo, $months)
    {
        $this->command->select('SELECT
   pre_examination.episode_id,
   note_event.episode_id,
   note_event.id,
   note_event.event_date op_date,
   note_event.id,
   op_procedure.eye_id,
   pre_examination.event_date as pre_exam_date,
   post_examination.event_date post_exam_date,
   pre_examination.id,
   pre_reading.value as pre_value,
   post_reading.value as post_value
FROM
   openeyes.et_ophtroperationnote_surgeon
       JOIN
   `event` AS note_event ON note_event.id = et_ophtroperationnote_surgeon.event_id #Get the operation note
       JOIN
   et_ophtroperationnote_procedurelist AS op_procedure ON op_procedure.event_id = note_event.id #And the operation notes procedures
       JOIN
   episode ON note_event.episode_id = episode.id #Get the episode to find the examinations
       JOIN
   `event` AS pre_examination ON pre_examination.episode_id = note_event.episode_id #Then get the examinations previous to the op not
       AND pre_examination.event_type_id = :examination
       AND pre_examination.event_date <= note_event.event_date
       JOIN
   `event` AS post_examination ON post_examination.episode_id = note_event.episode_id
       AND post_examination.event_type_id = :examination
       AND post_examination.event_date >= DATE_ADD(note_event.event_date, INTERVAL :months MONTH) #and the examinations n months after examination
       JOIN
   et_ophciexamination_visualacuity AS pre_acuity ON pre_examination.id = pre_acuity.event_id #Find the visual acuity for that eye (or both) from the examination
       AND (pre_acuity.eye_id = op_procedure.eye_id
       OR pre_acuity.eye_id = 3)
       JOIN
   et_ophciexamination_visualacuity AS post_acuity ON post_examination.id = post_acuity.event_id #And again for after the op note
       AND (post_acuity.eye_id = op_procedure.eye_id
       OR post_acuity.eye_id = 3)
       JOIN
   ophciexamination_visualacuity_reading AS pre_reading ON pre_acuity.id = pre_reading.element_id #Then get the values of the reading
       AND IF(op_procedure.eye_id = 1, pre_reading.side = 1, IF(op_procedure.eye_id = 2,
                               pre_reading.side = 0,
                               pre_reading.side IS NOT NULL)) #procedures have an eye_id  (1,2, or 3, left, right or both). Visual Acuity has a side (0 or 1, right or left).
       JOIN
   ophciexamination_visualacuity_reading AS post_reading ON post_acuity.id = post_reading.element_id
       AND post_reading.side = pre_reading.side
       AND post_reading.method_id = pre_reading.method_id #Get the post acuity reading that is the same method and side as pre
WHERE
   surgeon_id = :surgeon
   order by pre_examination.event_date desc, post_examination.event_date asc;', array(
            'examination' => $this->examinationEvent['id'],
            'months' => $months,
            'surgeon' => $surgeon
        ));

        $this->command->select('pre_examination.episode_id, note_event.episode_id, note_event.id, note_event.event_date as op_date, note_event.id, op_procedure.eye_id,
        pre_examination.event_date as pre_exam_date, post_examination.event_date as post_exam_date, pre_examination.id, pre_reading.value as pre_value, post_reading.value as post_value')
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
               AND post_examination.event_date BETWEEN DATE_SUB(note_event.event_date, INTERVAL :monthsBefore MONTH) AND DATE_ADD(note_event.event_date, INTERVAL :monthsAfter MONTH)',
                array(
                    'examination' => $this->examinationEvent['id'],
                    'monthsBefore' => ($months - 1),
                    'monthsAfter' => ($months + 1)
                )
            )->join('et_ophciexamination_visualacuity pre_acuity',
                'pre_examination.id = pre_acuity.event_id
                AND (pre_acuity.eye_id = op_procedure.eye_id
                OR pre_acuity.eye_id = 3)'
            )->join('et_ophciexamination_visualacuity post_acuity',
                'post_examination.id = post_acuity.event_id
                AND (post_acuity.eye_id = op_procedure.eye_id
                OR post_acuity.eye_id = 3)'
            )->join('ophciexamination_visualacuity_reading pre_reading',
                'pre_acuity.id = pre_reading.element_id
                AND IF(op_procedure.eye_id = 1, pre_reading.side = 1, IF(op_procedure.eye_id = 2,
                                                                           pre_reading.side = 0,
                                                                           pre_reading.side IS NOT NULL))'
            )->join('ophciexamination_visualacuity_reading post_reading', 'post_acuity.id = post_reading.element_id
               AND post_reading.side = pre_reading.side
               AND post_reading.method_id = pre_reading.method_id')
            ->where('surgeon = :surgeon', array('surgeon' => $surgeon));

        if($dateFrom){
            $this->command->andWhere('event.event_date > :dateFrom', array('dateFrom' => $dateFrom));
        }

        if($dateTo){
            $this->command->andWhere('event.event_date < :dateTo', array('dateFrom' => $dateTo));
        }

        return $this->command->queryAll();
    }

    public function dataSet()
    {
        // TODO: Implement dataSet() method.
    }

    public function seriesJson()
    {
        // TODO: Implement seriesJson() method.
    }

    public function graphConfig()
    {
        $this->graphConfig['chart']['renderTo'] = $this->graphId();

        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

}