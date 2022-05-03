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
    public $dispense_condition;
    public $report_type;

    public function attributeLabels()
    {
        return array(
            'drugs' => 'Prescribed drugs',
            'start_date' => 'Date from',
            'start_end' => 'Date end',
            'all_ids' => "Patient's IDs",
        );
    }

    public function rules()
    {
        return array(
            array('start_date, end_date, drugs, user_id, dispense_condition,institution_id, report_type', 'safe'),
            array('drugs', 'requiredIfNoUser'),
        );
    }

    public function requiredIfNoUser($attributes, $params)
    {
        if (!$this->user_id && !$this->drugs && !$this->dispense_condition) {
            $this->addError('drugs', 'Either user, drugs or dispense condition must be selected.');
        }
    }

    public function run()
    {
        $user_id = Yii::app()->user->id;
        $this->setInstitutionAndSite($user_id);

        $command = Yii::app()->db->createCommand()
            ->select(
                '
                patient.id
                , contact.last_name
                , contact.first_name
                , patient.dob
                , IF(address.postcode IS NOT NULL, address.postcode, "N/A") as postcode
                , IF(d.authorised_date IS NOT NULL, d.authorised_date, d.last_modified_date) as created_date
                , IF(au.first_name IS NOT NULL, au.first_name, lu.first_name) as user_first_name
                , IF(au.last_name IS NOT NULL, au.last_name, lu.last_name) as user_last_name
                , IF(au.role IS NOT NULL, au.role, lu.role) as role
                , event.created_date as event_date
                , m.preferred_term
                , emu.dose
                , emu.dose_unit_term as dose_unit
                , mf.term as frequency
                , duration.name as duration
                , route.term as route
                , dc.name as dispense_condition
                , dl.name as dispense_location
                , IF(pgd.id IS NOT NULL, pgd.name, "N/A") as pgd_name
                , option.name as laterality
                , IF(preservative_free.id IS NOT NULL, 1, 0) as preservative_free
                '
            )
            ->from('episode')
            ->join('event', 'episode.id = event.episode_id AND event.deleted = 0')
            ->join('et_ophdrprescription_details d', 'event.id = d.event_id')
            ->join('event_medication_use emu', 'emu.event_id = d.event_id AND emu.usage_type = \'OphDrPrescription\'')
            ->join('medication m', 'emu.medication_id = m.id')
            ->leftJoin(
                "
                (
                    SELECT maa.id, maa.medication_id
                    FROM medication_attribute_assignment maa
                    JOIN medication_attribute_option mao ON maa.medication_attribute_option_id = mao.id
                    JOIN medication_attribute ma ON mao.medication_attribute_id = ma.id
                    where LOWER(ma.name) = 'preservative_free'
                ) preservative_free
                ",
                'preservative_free.medication_id = m.id'
            )
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('contact', 'patient.contact_id = contact.id')
            ->leftJoin('address', 'contact.id = address.contact_id')
            ->leftjoin('user au', 'd.authorised_by_user = au.id')
            ->leftJoin('ophdrpgdpsd_pgdpsd pgd', 'pgd.id = emu.pgdpsd_id')
            ->join('user lu', 'd.last_modified_user_id = lu.id')
            ->join('medication_frequency mf', 'mf.id = emu.frequency_id')
            ->join('medication_duration duration', 'duration.id = emu.duration_id')
            ->join('medication_route route', 'route.id = emu.route_id')
            ->join('ophdrprescription_dispense_condition dc', 'dc.id = emu.dispense_condition_id')
            ->join('ophdrprescription_dispense_location dl', 'dl.id = emu.dispense_location_id')
            ->leftJoin('medication_laterality option', 'option.id = emu.laterality');

        if ($this->drugs) {
            $command->andWhere(array('in', 'medication.id', $this->drugs));
        }

        if ($this->institution_id) {
            $command->andWhere('event.institution_id = :institution_id');
            $params[':institution_id'] = $this->institution_id;
        }

        $command->andWhere('event.created_date >= :start_date', array(':start_date' => date('Y-m-d', strtotime($this->start_date)) . ' 00:00:00'))
            ->andWhere('event.created_date <= :end_date', array(':end_date' => date('Y-m-d', strtotime($this->end_date)) . ' 23:59:59'))
            ->andWhere('episode.deleted = 0')
            // draft prescription event should not be considered
            ->andWhere('d.draft = 0');

        if (!Yii::app()->getAuthManager()->checkAccess('Report', $user_id)) {
            $this->user_id = $user_id;
        }

        if (is_numeric($this->user_id)) {
            $command->andWhere('d.created_user_id = :user_id', array(':user_id' => $this->user_id));
        }

        if (is_numeric($this->dispense_condition)) {
            $command->andWhere('emu.dispense_condition_id = :dispense_condition_id', array(':dispense_condition_id' => $this->dispense_condition));
        }
        switch ($this->report_type) {
            case '0':
                $command->andWhere('pgd.id IS NULL');
                break;
            case '1':
                $command->andWhere('pgd.id IS NOT NULL');
                break;
        }

        $this->items = $command->queryAll();
        $this->setPatientIdentifiers();
    }

    public function setPatientIdentifiers()
    {
        $items = [];
        foreach ($this->items as $item) {
            $item['identifier'] = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $item['id'], $this->user_institution_id, $this->user_selected_site_id));
            $item['all_ids'] = PatientIdentifierHelper::getAllPatientIdentifiersForReports($item['id']);
            $items[] = $item;
        }

        $this->items = $items;
    }

    public function toCSV()
    {
        $output = $this->getPatientIdentifierPrompt() .
            ",  Patient's Surname, Patient's First name,  Patient's DOB, Patient's Post code, Date of Prescription, Drug name, Drug dose,  Frequency, Duration, Route, Dispense Condition, Dispense Location, PGD Name, Laterality, Prescribed Clinician's name, Prescribed Clinician's Job-role, Prescription event date, Preservative Free, " .
            $this->getAttributeLabel('all_ids') .
            "\n";
        foreach ($this->items as $item) {
            $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $item['id'], $this->user_institution_id, $this->user_selected_site_id));

            $output .= $patient_identifier_value . ', ' . $item['last_name'] . ', ' . $item['first_name'] . ', ' . ($item['dob'] ? date('j M Y', strtotime($item['dob'])) : 'Unknown') . ', ' . $item['postcode'] . ', ';
            $output .= (date('j M Y', strtotime($item['created_date'])) . ' ' . (substr($item['created_date'], 11, 5))) . ', ' . $item['preferred_term'] . ', ';
            $output .= $item['dose'] . ' ' . $item['dose_unit'] . ', ' . $item['frequency'] . ', ' . $item['duration'] . ', ' . $item['route'] . ', ' . $item['dispense_condition'] . ', ' . $item['dispense_location'] . ', ' . $item['pgd_name'] . ', ' . $item['laterality'] . ', ';
            $output .= $item['user_first_name'] . ' ' . $item['user_last_name'] . ', ' . $item['role'] . ', ' . (date('j M Y', strtotime($item['event_date'])) . ' ' . (substr($item['event_date'], 11, 5))) . ', ';
            $output .= $item['preservative_free'] ? 'Yes' : 'No';
            $output .= ',"' . $item['all_ids'] . '"';
            $output .= "\n";
        }

        return $output;
    }
}
