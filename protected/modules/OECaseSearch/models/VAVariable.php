<?php
class VAVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'va';
        $this->label = 'VA';
        $this->unit = 'logMAR';
        $this->eye_cardinality = true;
        $this->bin_size = 0.3;
        $this->min_value = -0.9;
    }

    public function query()
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "SELECT 0.3 * FLOOR(LogMAR_value / 0.3) va, COUNT(*) frequency
            FROM v_patient_va_converted
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            AND logMAR_value REGEXP '[0-9]+(\.[0-9]*)?'
            GROUP BY FLOOR(LogMAR_value / 0.3)
            ORDER BY 1";
                break;
            case 'ADVANCED':
                return "SELECT p.nhs_num, LogMAR_value va, side, va.reading_date, null
            FROM v_patient_va_converted va
            JOIN patient p ON p.id = va.patient_id
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            ORDER BY 1, 2, 3, 4";
                break;
            default:
                return 'SELECT 0.3 * FLOOR(LogMAR_value / 0.3) va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            AND logMAR_value REGEXP \'[0-9]+\.?[0-9]*\'
            GROUP BY FLOOR(LogMAR_value / 0.3)
            ORDER BY 1';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
