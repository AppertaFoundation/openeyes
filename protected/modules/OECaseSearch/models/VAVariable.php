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

    public function query()
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "SELECT snellen_value va, COUNT(*) frequency
            FROM v_patient_va_converted
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY snellen_value
            ORDER BY snellen_value";
                break;
            case 'ADVANCED':
                return "SELECT p.nhs_num, snellen_value va, side, va.reading_date, null
            FROM v_patient_va_converted va
            JOIN patient p ON p.id = va.patient_id
            WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            ORDER BY 1, 2, 3, 4";
                break;
            default:
                return 'SELECT snellen_value va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY snellen_value
            ORDER BY snellen_value';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
