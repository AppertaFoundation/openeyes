<?php
class CCTVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'cct';
        $this->label = 'CCT';
        $this->unit = 'microns';
        $this->eye_cardinality = true;
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "
        SELECT value cct
        FROM v_patient_cct cct
        WHERE patient_id = p_outer.id
          AND eye = '{$this->eye}'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)";
                break;
            case 'ADVANCED':
                return "
        SELECT value cct
        FROM v_patient_cct cct
        WHERE patient_id = p_outer.id
          AND eye = '{$this->eye}'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)";
                break;
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

    public function bindValues()
    {
        return array();
    }
}
