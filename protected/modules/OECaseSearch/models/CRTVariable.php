<?php
class CRTVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'crt';
        $this->label = 'CRT';
        $this->x_label = 'CRT (microns)';
        $this->eye_cardinality = true;
    }

    public function query()
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return '
        SELECT 10 * FLOOR(value/10) crt, COUNT(*) frequency
        FROM v_patient_crt
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1';
                break;
            case 'ADVANCED':
                return '
        SELECT p.nhs_num, crt.value, crt.side, crt.event_date, null
        FROM v_patient_crt crt
        JOIN patient p ON p.id = crt.patient_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4';
                break;
            default:
                return '
        SELECT 10 * FLOOR(value/10) crt, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_crt
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
