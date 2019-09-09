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
class OphDrPrescription_ReportPrescribedDrugs extends BaseReport
{
    public $drugs;
    public $start_date;
    public $end_date;
    public $items;
    public $user_id;

    public function attributeLabels()
    {
        return array(
            'drugs' => 'Prescribed drugs',
            'start_date' => 'Date from',
            'start_end' => 'Date end',
        );
    }

    public function rules()
    {
        return array(
            array('start_date, end_date, drugs, user_id', 'safe'),
            array('drugs', 'requiredIfNoUser'),
        );
    }

    public function requiredIfNoUser($attributes, $params)
    {
        if (!$this->user_id && !$this->drugs) {
            $this->addError('drugs', 'Either user or drugs must be selected.');
        }
    }

    public function run()
    {
        $tag_id = Yii::app()->params['preservative_free_tag_id'];

        $command = Yii::app()->db->createCommand()
            ->select(
                'patient.hos_num, contact.last_name, contact.first_name, patient.dob, address.postcode, d.created_date, medication.preferred_term, 
                 IF(medication.id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id = (SELECT id FROM medication_set WHERE name = \'Preservative free\')),1,0) AS preservative_free,
                user.first_name as user_first_name, user.last_name as user_last_name, user.role, event.created_date as event_date'
            )
            ->from('episode')
            ->join('event', 'episode.id = event.episode_id AND event.deleted = 0')
            ->join('et_ophdrprescription_details d', 'event.id = d.event_id')
            ->join('event_medication_use emu', 'emu.event_id = d.event_id AND emu.usage_type = \'OphDrPrescription\'')
            ->join('medication', 'emu.medication_id = medication.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('contact', 'patient.contact_id = contact.id')
            ->join('address', 'contact.id = address.contact_id')
            ->join('user', 'd.created_user_id = user.id')
            ->where('1=1');

        if ($this->drugs) {
            $command->andWhere(array('in', 'medication.id', $this->drugs));
        }

        $command->andWhere('event.created_date >= :start_date', array(':start_date' => date('Y-m-d', strtotime($this->start_date)).' 00:00:00'))
            ->andWhere('event.created_date <= :end_date', array(':end_date' => date('Y-m-d', strtotime($this->end_date)).' 23:59:59'))
            ->andWhere('episode.deleted = 0');

        if ( !Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id) ) {
            $this->user_id = Yii::app()->user->id;
        }

        if (is_numeric($this->user_id)) {
            $command->andWhere('d.created_user_id = :user_id', array(':user_id' => $this->user_id));
        }

        $this->items = $command->queryAll();
    }

    public function toCSV()
    {
        $output = "Patient's no,  Patient's Surname, Patient's First name,  Patient's DOB, Patient's Post code, Date of Prescription, Drug name, Prescribed Clinician's name, Prescribed Clinician's Job-role, Prescription event date, Preservative Free\n";
        foreach ($this->items as $item) {
         
            $output .= $item['hos_num'].', '.$item['last_name'].', '.$item['first_name'].', '.($item['dob'] ? date('j M Y', strtotime($item['dob'])) : 'Unknown').', '.$item['postcode'].', ';
            $output .= (date('j M Y', strtotime($item['created_date'])).' '.(substr($item['created_date'], 11, 5))).', '.$item['preferred_term'].', ';
            $output .= $item['user_first_name'].' '.$item['user_last_name'].', '.$item['role'].', '.(date('j M Y', strtotime($item['event_date'])).' '.(substr($item['event_date'], 11, 5))) . ', ';
            $output .= $item['preservative_free'] ? 'Yes' : 'No';
            $output .= "\n";
        }

        return $output;
    }
}
