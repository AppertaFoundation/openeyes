<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class OphCiExamination_ReportReadyForSecondEyeUnbooked extends BaseReport
{
    /** @var array */
    public $items;

    public function attributeLabels()
    {
        return array(
            'event_date' => 'Event date',
            'hos_num' => 'Hospital Number',
        );
    }

    public function rules()
    {
        return array(
            array('event_date, hos_num', 'safe')
        );
    }

    public function run()
    {
        $cmd = Yii::app()->db->createCommand()
            ->select(array('event.event_date', 'patient.hos_num'))
            ->from('et_ophciexamination_optom_comments')
            ->join('event', 'event.id = et_ophciexamination_optom_comments.event_id')
            ->join('episode', 'episode.id = event.episode_id')
            ->join('patient', 'patient.id = episode.patient_id')
            ->where('et_ophciexamination_optom_comments.ready_for_second_eye = 1')
            ->andWhere('event.deleted = 0')
            ->andWhere("NOT EXISTS ( SELECT *
                    FROM   event e2
                    WHERE  e2.episode_id = episode.id
                      AND  e2.deleted = 0
                      AND  e2.event_type_id IN (SELECT id FROM event_type WHERE name IN ('Operation Note', 'Operation booking'))
                      AND  e2.event_date > event.event_date)")
            ->order(array('event.event_date', 'patient.hos_num'));

        $this->items = $cmd->queryAll();
    }

    public function toCSV()
    {
        $output = "Event date, Hospital Number\n";
        foreach ($this->items as $item) {
            $output.=date('j M Y', strtotime($item['event_date'])).', '.$item['hos_num']. "\n";
        }
        return $output;
    }
}