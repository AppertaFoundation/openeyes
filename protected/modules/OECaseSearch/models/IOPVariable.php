<?php
class IOPVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'iop';
        $this->label = 'IOP';
        $this->unit = 'mm Hg';
        $this->eye_cardinality = true;
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return "
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency
        FROM v_patient_iop
        WHERE patient_id IN (" . implode(', ', $this->id_list) . ")
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)";
                break;
            case 'ADVANCED':
                return "
        SELECT p.nhs_num, iop.value iop, iop.side, iop.event_date, iop.reading_time
        FROM v_patient_iop iop
        JOIN patient p ON p.id = iop.patient_id
        WHERE iop.patient_id IN (" . implode(', ', $this->id_list) . ")
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4, 5";
                break;
            default:
            return '
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_iop
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
