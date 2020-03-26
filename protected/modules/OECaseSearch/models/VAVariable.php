<?php
class VAVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'va';
        $this->label = 'VA';
        $this->unit = null;
        $this->eye_cardinality = true;
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "SELECT 10 * FLOOR(base_value/10) va, COUNT(*) frequency
            FROM v_patient_va_converted
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY FLOOR(base_value/10)
            ORDER BY 1";
                break;
            case 'ADVANCED':
                return "SELECT p.nhs_num, base_value va, side, va.reading_date, null
            FROM v_patient_va_converted va
            JOIN patient p ON p.id = va.patient_id
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            ORDER BY 1, 2, 3, 4"; // Query is scalar, so the GROUP BY at the end will ensure that only one value is returned.
                break;
            default:
                return 'SELECT 10 * FLOOR(base_value/10) va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY FLOOR(base_value/10)';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
