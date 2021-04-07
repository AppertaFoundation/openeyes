<?php

class RefractionVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct(?array $id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'refraction';
        $this->label = 'Refraction';
        $this->x_label = 'Refraction (mean sph)';
        $this->eye_cardinality = true;
    }

    public function query()
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return '
        SELECT FLOOR(value) refraction, COUNT(*) frequency
        FROM v_patient_refraction
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value)
        ORDER BY 1';
            case 'ADVANCED':
                return 'SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = \'GLOBAL\'
            ) nhs_num, r.value, r.side, r.event_date, null
        FROM v_patient_refraction r
        JOIN patient p ON p.id = r.patient_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4';
            default:
                return '
        SELECT FLOOR(value) refraction, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_refraction
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value)
        ORDER BY 1';
        }
    }

    public function bindValues()
    {
        return array();
    }
}
