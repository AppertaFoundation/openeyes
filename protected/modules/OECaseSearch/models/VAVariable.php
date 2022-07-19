<?php

class VAVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct(?array $id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'va';
        $this->label = 'VA (best)';
        $this->x_label = 'VA (LogMAR 1dp)';
        $this->eye_cardinality = true;
        $this->bin_size = 0.3;
        $this->min_value = -0.9;
    }

    public function query(): string
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "SELECT 0.3 * FLOOR(va1.LogMAR_value / 0.3) va, COUNT(*) frequency
            FROM v_patient_va_converted va1
            WHERE va1.patient_id IN (" . implode(', ', $this->id_list) . ")
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            AND va1.logMAR_value REGEXP '[0-9]+(\.[0-9]*)?'
            GROUP BY 0.3 * FLOOR(LogMAR_value / 0.3)
            ORDER BY 1";
            case 'ADVANCED':
                return "SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = 'GLOBAL'
            ) nhs_num,
            va1.LogMAR_value va,
            va1.side, DATE_FORMAT(MAX(va1.reading_date), '%d-%m-%Y'),
            DATE_FORMAT(MAX(va1.reading_date), '%H:%i:%s')
            FROM v_patient_va_converted va1
            JOIN patient p ON p.id = va1.patient_id
            WHERE va1.patient_id IN (" . implode(', ', $this->id_list) . ")
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            GROUP BY p.nhs_num, va1.LogMAR_value, va1.side
            ORDER BY p.nhs_num, va1.LogMAR_value, va1.side";
            default:
                return 'SELECT
                0.3 * FLOOR(va1.LogMAR_value / 0.3) va,
                COUNT(*) frequency,
                GROUP_CONCAT(DISTINCT va1.patient_id) patient_id_list
            FROM v_patient_va_converted va1
            WHERE va1.patient_id IN (' . implode(', ', $this->id_list) . ')
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            AND va1.logMAR_value REGEXP \'[0-9]+\.?[0-9]*\'
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            GROUP BY 0.3 * FLOOR(LogMAR_value / 0.3)
            ORDER BY 1';
        }
    }

    public function bindValues(): array
    {
        return array();
    }
}
