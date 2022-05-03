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
class ReportDiagnoses extends BaseReport
{
    const TYPE_PRINCIPAL_ONLY = 'PrincipalOnly';
    const TYPE_PRINCIPAL_ALL = 'Principal';
    const TYPE_SECONDARY_ONLY = 'SecondaryOnly';
    const TYPE_SECONDARY_ALL = 'Secondary';

    public $principal;
    public $secondary;
    public $all;
    public $condition_type;
    public $start_date;
    public $end_date;
    public $diagnoses;

    public function attributeNames()
    {
        return array(
            'principal',
            'secondary',
            'all',
            'condition_type',
            'start_date',
            'end_date',
        );
    }

    public function attributeLabels()
    {
        return array(
            'start_date' => 'Start date',
            'end_date' => 'End date',
            'condition_type' => 'Condition type',
            'all_ids' => 'Patient IDs'
        );
    }

    public function rules()
    {
        return array(
            array('principal, secondary, all, condition_type, start_date, end_date,institution_id', 'safe'),
            array('start_date, end_date, condition_type', 'required'),
        );
    }

    public function afterValidate()
    {
        if (empty($this->principal) && empty($this->secondary) && empty($this->all)) {
            $this->addError('principal', 'Please select at least one diagnosis');
        }

        return parent::afterValidate();
    }

    public function filterDiagnoses()
    {
        $all = array();

        foreach ($this->all as $disorder_id) {
            if (
                (empty($this->principal) || !in_array($disorder_id, $this->principal)) &&
                (empty($this->secondary) || !in_array($disorder_id, $this->secondary))
            ) {
                $all[] = $disorder_id;
            }
        }

        return $all;
    }

    public function getDbCommand()
    {
        return Yii::app()->db->createCommand()
            ->from('patient p')
            ->join('contact c', 'p.contact_id = c.id');
    }

    public function run()
    {
        $this->setInstitutionAndSite();

        if (!empty($this->all)) {
            $this->all = $this->filterDiagnoses();
        }

        $this->diagnoses = array();

        $select = 'p.id, c.first_name, c.last_name, p.dob';

        $query = $this->getDbCommand();

        $condition = '';
        $and_or_conditions = array();
        $whereParams = array();

        !empty($this->principal) && $this->joinDisorders('Principal', $this->principal, $select, $whereParams, $and_or_conditions, $query);
        !empty($this->secondary) && $this->joinDisorders('Secondary', $this->secondary, $select, $whereParams, $and_or_conditions, $query);
        !empty($this->all) && $this->joinDisorders('All', $this->all, $select, $whereParams, $and_or_conditions, $query);

        $query->select($select);

        if ($this->condition_type == 'or') {
            $condition = '( ' . implode(' or ', $and_or_conditions) . ' )';
            if ($this->institution_id) {
                $condition .= " AND ";
            }
        } elseif ($this->condition_type == 'and') {
            $condition = '( ' . implode(' and ', $and_or_conditions) . ' )';
            if ($this->institution_id) {
                $condition .= " AND ";
            }
        }

        if ($this->institution_id) {
            $condition .= "EXISTS( SELECT * FROM event ev1
            JOIN episode ep1 on ep1.id = ev1.episode_id
            WHERE ev1.institution_id = :institution_id AND ep1.patient_id = p.id
            )";

            $whereParams[':institution_id'] = $this->institution_id;
        }

        $query->where($condition, $whereParams);

        foreach ($query->queryAll() as $item) {
            $this->addDiagnosesResultItem($item);
        }
    }

    public function joinDisorders($type, $list, &$select, &$whereParams, &$conditions, &$query)
    {
        $i = 0;

        if ($type === 'Principal') {
            foreach ($list as $disorder_id) {
                $conditions[] = $this->joinDisorder(self::TYPE_PRINCIPAL_ONLY, $i, $disorder_id, $select, $whereParams, $query);

                ++$i;
            }
        } elseif ($type === 'Secondary') {
            foreach ($list as $disorder_id) {
                $conditions[] = $this->joinDisorder(self::TYPE_SECONDARY_ONLY, $i, $disorder_id, $select, $whereParams, $query);

                ++$i;
            }
        } else {
            foreach ($list as $disorder_id) {
                $condition_p = $this->joinDisorder(self::TYPE_PRINCIPAL_ALL, $i, $disorder_id, $select, $whereParams, $query);
                $condition_s = $this->joinDisorder(self::TYPE_SECONDARY_ALL, $i, $disorder_id, $select, $whereParams, $query);

                $conditions[] = '(' . $condition_p . ' or ' . $condition_s . ')';

                ++$i;
            }
        }
    }

    public function addDiagnosesResultItem($item)
    {
        $patient_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $item['id'], $this->user_institution_id, $this->user_selected_site_id));
        $patient_identifiers = PatientIdentifierHelper::getAllPatientIdentifiersForReports($item['id']);
        $diagnoses = array();

        !empty($this->principal) && $diagnoses = $this->getDiagnosesForRow('Principal', $item, $this->principal, $diagnoses);
        !empty($this->secondary) && $diagnoses = $this->getDiagnosesForRow('Secondary', $item, $this->secondary, $diagnoses);
        !empty($this->all) && $diagnoses = $this->getDiagnosesForRow('All', $item, $this->all, $diagnoses);

        ksort($diagnoses);
        reset($diagnoses);

        $ts = key($diagnoses);

        while (isset($this->diagnoses[$ts])) {
            ++$ts;
        }

        $this->diagnoses[$ts] = array(
            'identifier' => $patient_identifier_value,
            'dob' => $item['dob'],
            'first_name' => $item['first_name'],
            'last_name' => $item['last_name'],
            'diagnoses' => $diagnoses,
            'all_ids' => $patient_identifiers,
        );
    }

    public function getDiagnosesForRow($type, $item, $list, $diagnoses)
    {
        $eyes = CHtml::listData(Eye::model()->findAll(), 'id', 'name');

        if ($type === 'Principal') {
            for ($i = 0; $i < count($list); ++$i) {
                $this->insertReportItem(self::TYPE_PRINCIPAL_ONLY, $i, $eyes, $diagnoses, $item);
            }
        } elseif ($type === 'Secondary') {
            for ($i = 0; $i < count($list); ++$i) {
                $this->insertReportItem(self::TYPE_SECONDARY_ONLY, $i, $eyes, $diagnoses, $item);
            }
        } elseif ($type === 'All') {
            for ($i = 0; $i < count($list); ++$i) {
                $this->insertReportItem(self::TYPE_PRINCIPAL_ALL, $i, $eyes, $diagnoses, $item);
                $this->insertReportItem(self::TYPE_SECONDARY_ALL, $i, $eyes, $diagnoses, $item);
            }
        }

        return $diagnoses;
    }

    public function getFreeTimestampIndex($date, $list)
    {
        $ts = strtotime($date);

        while (isset($list[$ts])) {
            ++$ts;
        }

        return $ts;
    }

    public function description()
    {
        $description = 'Patients with ' . ($this->condition_type == 'or' ? 'any' : 'all') . " of these diagnoses:\n";

        if (!empty($this->principal)) {
            foreach ($this->principal as $disorder_id) {
                $description .= Disorder::model()->findByPk($disorder_id)->term . " (Principal)\n";
            }
        }

        if (!empty($this->secondary)) {
            foreach ($this->secondary as $disorder_id) {
                $description .= Disorder::model()->findByPk($disorder_id)->term . " (Secondary)\n";
            }
        }

        if (!empty($this->all)) {
            foreach ($this->all as $disorder_id) {
                $description .= Disorder::model()->findByPk($disorder_id)->term . " (Principal or Secondary)\n";
            }
        }

        return $description . 'Between ' . $this->start_date . ' and ' . $this->end_date;
    }

    /**
     * Output the report in CSV format.
     *
     * @return string
     */
    public function toCSV()
    {
        $output = $this->description() . "\n\n";

        $output .= $this->getPatientIdentifierPrompt() . ',' . Patient::model()->getAttributeLabel('dob') . ',' . Patient::model()->getAttributeLabel('first_name') . ',' . Patient::model()->getAttributeLabel('last_name') . ",Date,Diagnoses," . $this->getAttributeLabel('all_ids') . "\n";

        foreach ($this->diagnoses as $ts => $diagnosis) {
            foreach ($diagnosis['diagnoses'] as $_diagnosis) {
                $output .= "\"{$diagnosis['identifier']}\",\"" .
                    ($diagnosis['dob'] ? date('j M Y', strtotime($diagnosis['dob'])) : 'Unknown') .
                    "\",\"{$diagnosis['first_name']}\",\"{$diagnosis['last_name']}\",\"" .
                    (isset($_diagnosis['date']) ? date('j M Y', strtotime($_diagnosis['date'])) : date('j M Y', $ts)) .
                    '","';
                $output .= $_diagnosis['eye'] . ' ' . $_diagnosis['disorder'] . ' (' . $_diagnosis['type'] . ")\",\"{$diagnosis['all_ids']}\"\n";
            }
        }

        return $output;
    }

    /**
     * Get the field/column prefix to be used in both the query and the result
     * parsing functions
     *
     * @param string $type The diagnosis item type, either TYPE_PRINCIPAL_ONLY, TYPE_PRINCIPAL_ALL, TYPE_SECONDARY_ONLY or TYPE_SECONDARY_ALL
     *
     * @return string A prefix specific to the supplied item type
     */
    private function getFieldPrefix($type)
    {
        if ($type === self::TYPE_PRINCIPAL_ONLY) {
            return 'pdis';
        } elseif ($type === self::TYPE_PRINCIPAL_ALL) {
            return 'padis';
        } elseif ($type === self::TYPE_SECONDARY_ONLY) {
            return 'sdis';
        } else {
            return 'sadis';
        }
    }

    /**
     * Get the column name to be used in the query for the date field,
     * which differs between the episode and secondary_diagnosis table
     *
     * @param string $type The diagnosis item type, either TYPE_PRINCIPAL_ONLY, TYPE_PRINCIPAL_ALL, TYPE_SECONDARY_ONLY or TYPE_SECONDARY_ALL
     *
     * @return string The date column for the table associated with the supplied item type
     */
    private function getDateColumnName($type)
    {
        if (
            $type === self::TYPE_PRINCIPAL_ONLY
            || $type === self::TYPE_PRINCIPAL_ALL
        ) {
            return 'created_date';
        } else {
            return 'date';
        }
    }

    /**
     * Get the table name and a suitable alias for use in the query
     *
     * @param string $type The diagnosis item type, either TYPE_PRINCIPAL_ONLY, TYPE_PRINCIPAL_ALL, TYPE_SECONDARY_ONLY or TYPE_SECONDARY_ALL
     *
     * @return array An array of the principal or secondary table name and an alias specific to the supplied item type
     */
    private function getJoinTableNames($type)
    {
        if ($type === self::TYPE_PRINCIPAL_ONLY) {
            return array('episode', 'e');
        } elseif ($type === self::TYPE_PRINCIPAL_ALL) {
            return array('episode', 'ea');
        } elseif ($type === self::TYPE_SECONDARY_ONLY) {
            return array('secondary_diagnosis', 'sd');
        } else {
            return array('secondary_diagnosis', 'sda');
        }
    }

    /**
     * Get the name to put down on the actual report,
     * either 'Principal' or 'Secondary'
     *
     * @param string $type The diagnosis item type, either TYPE_PRINCIPAL_ONLY, TYPE_PRINCIPAL_ALL, TYPE_SECONDARY_ONLY or TYPE_SECONDARY_ALL
     *
     * @return string Either 'Principal' or 'Secondary'
     */
    private function getTypeReportName($type)
    {
        if (
            $type === self::TYPE_PRINCIPAL_ONLY
            || $type === self::TYPE_PRINCIPAL_ALL
        ) {
            return 'Principal';
        } else {
            return 'Secondary';
        }
    }

    /**
     * Insert an item into the list of diagnoses if there is a record set for it
     *
     * @param string $type The diagnosis item type, either TYPE_PRINCIPAL_ONLY, TYPE_PRINCIPAL_ALL, TYPE_SECONDARY_ONLY or TYPE_SECONDARY_ALL
     * @param int $i The index of the diagnosis in the query
     * @param array $eyes The list of eye model ids and names, to be matched with the item
     * @param array &$diagnoses A reference to the list of diagnoses to insert the item into
     * @param mixed $item A row returned from the query to be processed
     *
     * @return bool If the item had a record set and was inserted, or not
     */
    private function insertReportItem($type, $i, $eyes, &$diagnoses, $item)
    {
        $field_prefix = $this->getFieldPrefix($type);

        if ($item["{$field_prefix}{$i}_date"]) {
            $ts = $this->getFreeTimestampIndex($item["{$field_prefix}{$i}_date"], $diagnoses);

            $diagnoses[$ts] = array(
                'type' => $this->getTypeReportName($type),
                'disorder' => $item["{$field_prefix}{$i}_fully_specified_name"],
                'date' => $item["{$field_prefix}{$i}_date"],
                'eye' => isset($item["{$field_prefix}{$i}_eye"]) ? $eyes[$item["{$field_prefix}{$i}_eye"]] : null,
            );

            return true;
        }

        return false;
    }

    private function joinDisorder($type, $i, $disorder_id, &$select, &$whereParams, &$query)
    {
        $join_table = $this->getJoinTableNames($type);
        $date_field = $this->getDateColumnName($type);
        $select_prefix = $this->getFieldPrefix($type);

        $select .= ", {$join_table[1]}$i.$date_field as {$select_prefix}{$i}_date, {$select_prefix}{$i}.fully_specified_name as {$select_prefix}{$i}_fully_specified_name, {$join_table[1]}{$i}.eye_id as {$select_prefix}{$i}_eye";

        $whereParams[":{$select_prefix}$i"] = $disorder_id;

        $join_condition = "{$join_table[1]}$i.patient_id = p.id and {$join_table[1]}$i.disorder_id = :{$select_prefix}$i";

        if ($this->start_date) {
            $join_condition .= " and {$join_table[1]}$i.$date_field >= :start_date";
            $whereParams[':start_date'] = date('Y-m-d', strtotime($this->start_date));
        }
        if ($this->end_date) {
            $join_condition .= " and {$join_table[1]}$i.$date_field <= :end_date";
            $whereParams[':end_date'] = date('Y-m-d', strtotime($this->end_date)) . ' 23:59:59';
        }

        $query->leftJoin("{$join_table[0]} {$join_table[1]}$i", $join_condition);
        $query->leftJoin("disorder {$select_prefix}$i", "{$select_prefix}$i.id = {$join_table[1]}$i.disorder_id");

        return "{$select_prefix}$i.id is not null";
    }
}
