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
class OphCoCorrespondence_ReportLetters extends BaseReport
{
    public $match_correspondence;
    public $match_legacy_letters;
    public $phrases;
    public $condition_type;
    public $start_date;
    public $end_date;
    public $author_id;
    public $letters;
    public $site_id;
    public $statuses;
    public $contact_type;
    public $recipient_type;

    public function attributeNames()
    {
        return array(
            'match_correspondence',
            'match_legacy_letters',
            'phrases',
            'condition_type',
            'start_date',
            'end_date',
            'author_id',
            'site_id',
            'statuses',
            'recipient_type',
            'contact_type',
        );
    }

    public function attributeLabels()
    {
        return array(
            'match_correspondence' => 'Match correspondence',
            'match_legacy_letters' => 'Match legacy letters',
            'phrases' => 'Phrases',
            'condition_type' => 'Search method',
            'start_date' => 'Date from',
            'start_end' => 'Date end',
            'author_id' => 'Author',
            'site_id' => 'Site',
            'statuses' => 'Status',
            'recipient_type' => 'Recipient Type',
            'contact_type' => 'Contact Type',
            'all_ids' => 'Patient IDs',
        );
    }

    public function rules()
    {
        return array(
            array('match_correspondence, match_legacy_letters, phrases, condition_type, start_date,
             end_date, site_id, statuses, author_id, recipient_type, contact_type, institution_id', 'safe'),
            array('condition_type', 'required'),
        );
    }

    public function afterValidate()
    {
        if (!empty($this->phrases)) {
            $has_phrases = false;

            foreach ($this->phrases as $phrase) {
                $phrase && $has_phrases = true;
            }

            if (!$has_phrases) {
                $this->phrases = array();
            }
        }

        if (!$this->match_correspondence && !$this->match_legacy_letters) {
            $this->addError('match_correspondence', 'Please select which type of letters you want to search');
        }

        return parent::afterValidate();
    }

    public function run()
    {
        $this->setInstitutionAndSite();
        $where_clauses = array();
        $where_params = array();
        $where_operator = ' ' . ($this->condition_type == 'and' ? 'and' : 'or') . ' ';

        $select = array(
            'c.first_name', 'c.last_name', 'p.dob', 'p.gender', 'cons.first_name as cons_first_name',
            'cons.last_name as cons_last_name', 'e.created_date', 'ep.patient_id',
            'IF(output_status IS NULL,IF(l.draft=0," - ","Draft"), LOWER(output_status)) as status',
            'document_output.output_type as output_type');

        $data = $this->getDbCommand();

        if ($this->match_correspondence) {
            $this->joinLetters('Correspondence', $data, $select, $where_clauses, $where_params, $where_operator);
        }

        if ($this->match_legacy_letters) {
            $this->joinLetters('Legacy', $data, $select, $where_clauses, $where_params, $where_operator);
        }

        $where = ' ( ' . implode(' or ', $where_clauses) . ' ) ';

        if ($this->start_date) {
            $this->applyStartDate($where, $where_params);
        }

        if ($this->end_date) {
            $this->applyEndDate($where, $where_params);
        }

        $this->letters = array();
        $data->where($where, $where_params);

        if ($this->match_correspondence) {
            $data->leftJoin('site', 'l.site_id = site.id');
            $select[] = 'site.name';

            if ($this->site_id) {
                $data->andWhere('site.id = :site_id', array(':site_id' => $this->site_id));
            }

            if ($this->institution_id) {
                $data->leftJoin('institution', 'institution.id = site.institution_id');
                $data->andWhere('institution.id = :institution_id', [':institution_id' => $this->institution_id]);
            }
        }

        $data->leftJoin("document_instance", "document_instance.correspondence_event_id = l.event_id");
        $data->leftJoin("document_target", "document_instance.id = document_target.document_instance_id");
        $data->leftJoin("document_output", "document_target.id = document_output.document_target_id");

        if ($this->statuses) {
            $data->andWhere(['in', 'document_output.output_status', $this->statuses]);
        }

        if ($this->recipient_type) {
            $data->andWhere(['in', 'document_target.ToCc', $this->recipient_type]);
        }

        if ($this->contact_type) {
            $data->andWhere(['in', 'document_target.contact_type', $this->contact_type]);
        }

        $data->select(implode(',', $select));
        $data->andWhere('e.deleted = 0');
        $data->andWhere('p.is_deceased = 0');

        $this->executeQuery($data);
    }

    private function mapOutputType($type)
    {
        $output_type_map = [
            \DocumentOutput::TYPE_PRINT => 'Print',
            \DocumentOutput::TYPE_DOCMAN => 'Docman',
            \DocumentOutput::TYPE_INTERNAL_REFFERAL => 'Internal Referral',
        ];
        return array_key_exists($type, $output_type_map)
            ? $output_type_map[$type]
            : 'No output';
    }

    public function executeQuery($data)
    {
        foreach ($data->queryAll() as $i => $row) {
            if (@$row['lid']) {
                $row['type'] = "Correspondence ({$this->mapOutputType($row['output_type'])})";
                $row['link'] = Yii::app()->createURL('/OphCoCorrespondence/default/view/' . $row['event_id']);
            } else {
                $row['type'] = 'Legacy letter';
                $row['link'] = Yii::app()->createURL('/OphLeEpatientletter/default/view/' . $row['l2_event_id']);
            }

            $row['identifier'] = $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $row['patient_id'], $this->user_institution_id, $this->user_selected_site_id));
            $row['all_ids'] = PatientIdentifierHelper::getAllPatientIdentifiersForReports($row['patient_id']);

            $this->letters[] = $row;
        }
    }

    public function getDbCommand()
    {
        return Yii::app()->db->createCommand()
            ->from('event e')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('contact c', 'p.contact_id = c.id')
            ->join('user', 'e.created_user_id = user.id')
            ->join('contact cons', 'user.contact_id = cons.id AND user.contact_id = cons.id')
            ->order('e.created_date asc');
    }

    public function joinLetters($type, $data, &$select, &$where_clauses, &$where_params, $where_operator)
    {
        $et = ($type == 'Correspondence')
            ? EventType::model()->find('class_name=?', array('OphCoCorrespondence'))
            : EventType::model()->find('class_name=?', array('OphLeEpatientletter'));

        $letter_table = ($type == 'Correspondence')
            ? array('et_ophcocorrespondence_letter', 'l')
            : array('et_ophleepatientletter_epatientletter', 'l2');

        $text_field = ($type == 'Correspondence') ? 'body' : 'letter_html';

        $data->leftJoin("{$letter_table[0]} {$letter_table[1]}", "{$letter_table[1]}.event_id = e.id");

        if ($type == 'Correspondence') {
            $clause = "({$letter_table[1]}.id is not null and e.event_type_id = :et_{$letter_table[1]}_id ";
            $where_params[":et_{$letter_table[1]}_id"] = $et->id;
        } else {
            $clause = "({$letter_table[1]}.id is not null";
        }

        if ($this->phrases) {
            $clause .= ' and (';
            foreach ($this->phrases as $i => $phrase) {
                $where_params[":body{$letter_table[1]}" . $i] = '%' . strtolower($phrase) . '%';
                if ($i > 0) {
                    $clause .= $where_operator;
                }
                $clause .= " lower({$letter_table[1]}.$text_field) like :body{$letter_table[1]}$i";
            }

            $clause .= ' )';
        }

        //If user does NOT have the RBAC role 'Report' then select the current user
        if (!Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) {
            $this->author_id = Yii::app()->user->id;
        }

        if ($this->author_id) {
            if (!$author = User::model()->findByPk($this->author_id)) {
                throw new Exception("User not found: $this->author_id");
            }

            if ($type == 'Correspondence') {
                $clause .= " AND ( {$letter_table[1]}.created_user_id = :authorID";
                $where_params[':authorID'] = $this->author_id;

                $clause .= " OR lower({$letter_table[1]}.footer) LIKE :authorName )";
                $where_params[':authorName'] = '%' . strtolower($author->fullName) . '%';
            } else {
                $clause .= " and lower({$letter_table[1]}.$text_field) like :authorName";
                $where_params[':authorName'] = '%' . strtolower($author->fullName) . '%';
            }
        }

        $where_clauses[] = $clause . ' )';
        $select[] = "{$letter_table[1]}.id as {$letter_table[1]}id";

        if ($type == 'Correspondence') {
            $select[] = "{$letter_table[1]}.event_id";
        } else {
            $select[] = "{$letter_table[1]}.event_id as l2_event_id";
        }
    }

    public function applyStartDate(&$where, &$where_params)
    {
        $where .= ' and e.created_date >= :dateFrom';
        $where_params[':dateFrom'] = date('Y-m-d', strtotime($this->start_date)) . ' 00:00:00';
    }

    public function applyEndDate(&$where, &$where_params)
    {
        $where .= ' and e.created_date <= :dateTo';
        $where_params[':dateTo'] = date('Y-m-d', strtotime($this->end_date)) . ' 23:59:59';
    }

    public function description()
    {
        if ($this->match_correspondence) {
            $description = 'Correspondence';
        }

        if ($this->match_legacy_letters) {
            if (@$description) {
                $description .= ' and legacy letters';
            } else {
                $description = 'Legacy letters';
            }
        }

        if ($this->phrases) {
            $description .= ' containing ' . ($this->condition_type == 'and' ? 'all' : 'any') . " of these phrases:\n";

            foreach ($this->phrases as $phrase) {
                if ($phrase) {
                    $description .= $phrase . "\n";
                }
            }
        }

        if ($this->start_date || $this->end_date || $this->author_id) {
            $description .= 'written';

            if ($this->start_date && $this->end_date) {
                $description .= ' between ' . $this->start_date . ' and ' . $this->end_date;
            } elseif ($this->start_date) {
                $description .= ' after ' . $this->start_date;
            } elseif ($this->end_date) {
                $description .= ' before ' . $this->end_date;
            }

            if ($this->author_id) {
                $description .= ' by ' . User::model()->findByPk($this->author_id)->fullName;
            }
        }

        return $description;
    }

    /**
     * Output the report in CSV format.
     *
     * @return string
     */
    public function toCSV()
    {
        $output = $this->description() . "\n\n";

        $output .= $this->getPatientIdentifierPrompt() . ',' . Patient::model()->getAttributeLabel('dob') . ',' . Patient::model()->getAttributeLabel('first_name') . ',' . Patient::model()->getAttributeLabel('last_name') . ',' . Patient::model()->getAttributeLabel('gender') . ",Consultant's name,Site,Date,Type,Status,Link," . $this->getAttributeLabel('all_ids') . "\n";

        foreach ($this->letters as $letter) {
            $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $letter['patient_id'], $this->user_institution_id, $this->user_selected_site_id));
            $output .= "\"{$patient_identifier_value}\",\"" . ($letter['dob'] ? date('j M Y', strtotime($letter['dob'])) : 'Unknown') . "\",\"{$letter['first_name']}\",\"{$letter['last_name']}\",\"{$letter['gender']}\",\"{$letter['cons_first_name']} {$letter['cons_last_name']}\",\"" . (isset($letter['name']) ? $letter['name'] : 'N/A') . '","' . date('j M Y', strtotime($letter['created_date'])) . '","' . $letter['type'] . '","' . ucfirst($letter['status']) . '","' . $letter['link'] . '","' . $letter['all_ids'] . "\"\n";
        }

        return $output;
    }
}
