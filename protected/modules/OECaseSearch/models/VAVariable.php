<?php
class VAVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'va';
        $this->label = 'VA';
        $this->unit = 'ETDRS Letters';
        $this->eye_cardinality = true;
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "SELECT va_converted.ETDRS_value va
            FROM v_patient_va_converted va_converted
            WHERE va_converted.patient_id = p_outer.id
              AND va_converted.eye = '{$this->eye}'
              AND va_converted.base_value = (
                SELECT MAX(va2.base_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va_converted.patient_id
                  AND va2.eye = va_converted.eye
                  AND (:start_date IS NULL OR reading_date > :start_date)
                  AND (:end_date IS NULL OR reading_date < :end_date)
                )
              AND (:start_date IS NULL OR reading_date > :start_date)
              AND (:end_date IS NULL OR reading_date < :end_date)
              GROUP BY va_converted.ETDRS_value"; // Query is scalar, so the GROUP BY at the end will ensure that only one value is returned.
                break;
            case 'ADVANCED':
                return "SELECT va_converted.ETDRS_value va
            FROM v_patient_va_converted va_converted
            WHERE va_converted.patient_id = p_outer.id
              AND va_converted.eye = '{$this->eye}'
              AND va_converted.base_value = (
                SELECT MAX(va2.base_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va_converted.patient_id
                  AND va2.eye = va_converted.eye
                  AND (:start_date IS NULL OR reading_date > :start_date)
                  AND (:end_date IS NULL OR reading_date < :end_date)
                )
              AND (:start_date IS NULL OR reading_date > :start_date)
              AND (:end_date IS NULL OR reading_date < :end_date)
              GROUP BY va_converted.ETDRS_value"; // Query is scalar, so the GROUP BY at the end will ensure that only one value is returned.
                break;
            default:
                return 'SELECT ETDRS_value va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
            AND eye = \'' . $this->eye . '\'
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY ETDRS_value';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
