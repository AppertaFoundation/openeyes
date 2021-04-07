<?php

class IOPVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct(?array $id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'iop';
        $this->label = 'IOP';
        $this->x_label = 'IOP (mm Hg)';
        $this->eye_cardinality = true;
    }

    public function getPrimaryDataPointName()
    {
        return 'iop';
    }

    public function query()
    {
        $mode = $this->query_flags[0];
        $date_predicate = null;
        $time_predicate = null;

        if ($mode === 'first') {
            $date_predicate = 'SELECT MIN(event_date) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye';
            $time_predicate = 'SELECT MIN(reading_time) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye AND iop2.event_date = iop.event_date';
        } elseif ($mode === 'last') {
            $date_predicate = 'SELECT MAX(event_date) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye';
            $time_predicate = 'SELECT MAX(reading_time) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye AND iop2.event_date = iop.event_date';
        }

        switch ($this->csv_mode) {
            case 'BASIC':
                return "
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency
        FROM v_patient_iop iop
        WHERE iop.patient_id IN (" . implode(', ', $this->id_list) . ")
        AND iop.event_date = ({$date_predicate})
        AND iop.reading_time = ({$time_predicate})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY 10 * FLOOR(value/10)
        ORDER BY 1";
            case 'ADVANCED':
                return "
        SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = 'GLOBAL'
            ) nhs_num, iop.value iop, iop.side, iop.event_date, iop.reading_time
        FROM v_patient_iop iop
        JOIN patient p ON p.id = iop.patient_id
        WHERE iop.patient_id IN (" . implode(', ', $this->id_list) . ")
        AND iop.event_date = ({$date_predicate})
        AND iop.reading_time = ({$time_predicate})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4, 5";
            default:
                return "
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_iop iop
        WHERE iop.patient_id IN (" . implode(', ', $this->id_list) . ")
        AND iop.event_date = ({$date_predicate})
        AND iop.reading_time = ({$time_predicate})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY 10 * FLOOR(value/10)
        ORDER BY 1";
        }
    }

    public function bindValues()
    {
        return array();
    }
}
