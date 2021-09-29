<?php

class CRTVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct(?array $id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'crt';
        $this->label = 'CRT';
        $this->x_label = 'CRT (microns)';
        $this->eye_cardinality = true;
    }

    public function query(): string
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
            case 'ADVANCED':
                return '
        SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = \'GLOBAL\'
            ) nhs_num, crt.value, crt.side, crt.event_date, null
        FROM v_patient_crt crt
        JOIN patient p ON p.id = crt.patient_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4';
            default:
                return '
        SELECT 10 * FLOOR(value/10) crt, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_crt
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1';
        }
    }

    public function bindValues(): array
    {
        return array();
    }
}
