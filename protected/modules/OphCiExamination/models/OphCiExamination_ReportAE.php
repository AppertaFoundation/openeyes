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

class OphCiExamination_ReportAE extends BaseReport
{
    public array $items;

    public $start_date;
    public $end_date;
    public $clinician;

    public function rules()
    {
        return array(
            array('start_date, end_date', 'required'),
            array('start_date', 'OEDateCompareValidator', 'compareAttribute' => 'end_date', 'allowEmpty' => true,
                'operator' => '<=', 'message' => '{attribute} must be on or before {compareAttribute}', ),
            array('start_date, end_date', 'safe')
        );
    }

    public function run()
    {
        $this->setInstitutionAndSite();

        $ae_subspecialty = \Subspecialty::model()->findByAttributes(['ref_spec' => 'AE']);

        $cmd = Yii::app()->db->createCommand()
            ->select('e.id,
            p.id as patient_id,
            e.event_date, 
            NULL as hos_num,
            p.dob,
            CONCAT(c.first_name, \' \', c.last_name) as name,
            CONCAT(u.first_name, \' \', u.last_name) as clinician,
            u.role,
            IFNULL(priority.description, \'N/A\') as priority,
            fs.name followup_status,
            d_status.name discharge_status,
            d_destination.name discharge_destination')
            ->from('episode ep')
            ->join('firm f', 'f.id = ep.firm_id')
            ->join('service_subspecialty_assignment ssa', 'ssa.id = f.service_subspecialty_assignment_id')
            ->join('event e', 'e.episode_id = ep.id') // This should be the latest examination event
            ->join('event_type et', 'et.id = e.event_type_id')
            ->leftJoin('et_ophciexamination_triage triage', 'triage.event_id = e.id')
            ->leftJoin('ophciexamination_triage triage_entry', 'triage_entry.element_id = triage.id')
            ->leftJoin('ophciexamination_triage_priority priority', 'priority.id = triage_entry.priority_id')
            ->leftJoin('et_ophciexamination_clinicoutcome followup', 'followup.event_id = e.id')
            ->leftJoin('ophciexamination_clinicoutcome_entry followup_entry', 'followup_entry.element_id = followup.id')
            ->leftJoin('ophciexamination_clinicoutcome_status fs', 'fs.id = followup_entry.status_id')
            ->leftJoin('ophciexamination_discharge_status d_status', 'd_status.id = followup_entry.discharge_status_id')
            ->leftJoin('ophciexamination_discharge_destination d_destination', 'd_destination.id = followup_entry.discharge_destination_id')
            ->join('patient p', 'p.id = ep.patient_id')
            ->join('contact c', 'c.id = p.contact_id')
            ->join('user u', 'u.id = e.last_modified_user_id')
            ->where('e.deleted = 0')
            ->andWhere('f.institution_id = :institution_id', [':institution_id' => $this->user_institution_id])
            ->andWhere('et.class_name = \'OphCiExamination\'')
            ->andWhere('ssa.subspecialty_id = :subspecialty_id', [':subspecialty_id' => $ae_subspecialty->id])
            ->andWhere(':clinician IS NULL OR u.id = :clinician', [':clinician' => $this->clinician])
            ->andWhere('date(e.event_date) <= date(:end_date)', [':end_date' => $this->end_date])
            ->andWhere('date(e.event_date) >= date(:start_date)', [':start_date' => $this->start_date])
            ->order(array('e.event_date'));

        $this->items = $cmd->queryAll();
        $this->setPatientIdentifiers();
    }

    public function setPatientIdentifiers()
    {
        $items = [];
        foreach ($this->items as $item) {
            $item['hos_num'] = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $item['patient_id'], $this->user_institution_id, $this->user_selected_site_id));
            $items[] = $item;
        }

        $this->items = $items;
    }

    public function toCSV()
    {
        $output = 'Date,'
            . $this->getPatientIdentifierPrompt()
            . ','
            . 'DOB,"Patient\'s Name","Clinician\'s Name","Job role",Priority,Outcome'
            . "\n";
        foreach ($this->items as $item) {
            $event_date = date('j M Y', strtotime($item['event_date']));
            $dob = date('j M Y', strtotime($item['dob']));
            $outcome = $item['followup_status']
                . ($item['discharge_status'] ? (' - ' . $item['discharge_status']) : '')
                . ($item['discharge_destination'] ? (' - ' . $item['discharge_destination']) : '');
            $output .= "\"$event_date\",\"{$item['hos_num']}\",\"$dob\",\"{$item['name']}\",\"{$item['clinician']}\",\"{$item['role']}\",\"{$item['priority']}\",\"$outcome\"\n";
        }
        return $output;
    }
}
