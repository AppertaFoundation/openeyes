<?php
class CCTVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'cct';
        $this->label = 'CCT';
        $this->unit = 'microns';
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return '
        SELECT c.first_name, c.last_name, value cct
        FROM v_patient_cct cct
        JOIN patient p ON p.id = cct.patient_id
        JOIN contact c ON c.id = p.contact_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)';
                break;
            case 'ADVANCED':
                /*return '
        SELECT value cct, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value';
                break;*/
            default:
                return '
        SELECT value cct, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value';
                break;
        }

    }

    public function csvColumns($mode)
    {
        if ($mode === 'BASIC') {
            return array(
                'First name',
                'Surname',
                'CCT (microns)'
            );
        } else {
            return array();
        }
    }

    public function bindValues()
    {
        return array();
    }
}
