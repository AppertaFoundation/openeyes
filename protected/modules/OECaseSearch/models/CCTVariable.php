<?php
class CCTVariable extends CaseSearchVariable implements DBProviderInterface
{
    /**
     * CCTVariable constructor.
     * @param $id_list int[] Patient ID list
     */
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'cct';
        $this->label = 'CCT';
        $this->x_label = 'CCT (microns)';
        $this->eye_cardinality = true;
    }

    /**
     * Get the SQL query string.
     * @return string Query
     */
    public function query()
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return '
        SELECT 10 * FLOOR(value/10) cct, COUNT(*) frequency
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1';
                break;
            case 'ADVANCED':
                return '
        SELECT p.nhs_num, cct.value, cct.side, cct.event_date, null
        FROM v_patient_cct cct
        JOIN patient p ON p.id = cct.patient_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4';
                break;
            default:
                return '
        SELECT 10 * FLOOR(value/10) cct, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1';
                break;
        }
    }

    /**
     * Get list of bind values
     * @return array List of bind variables.
     */
    public function bindValues()
    {
        return array();
    }
}
