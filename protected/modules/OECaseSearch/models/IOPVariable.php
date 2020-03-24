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
        SELECT iop.value iop
        FROM v_patient_iop iop
        WHERE iop.patient_id = p_outer.id
          AND iop.eye = '{$this->eye}'
          AND iop.event_id = (
              SELECT MAX(iop2.event_id)
              FROM v_patient_iop iop2
              WHERE iop2.patient_id = iop.patient_id
                AND iop2.eye = iop.eye
                AND (:start_date IS NULL OR iop2.event_date > :start_date)
                AND (:end_date IS NULL OR iop2.event_date < :end_date)
          )
        AND iop.reading_time = (
            SELECT MAX(iop3.reading_time)
            FROM v_patient_iop iop3
            WHERE iop3.patient_id = iop.patient_id
              AND iop3.eye = iop.eye
              AND iop3.event_id IN (
                SELECT MAX(iop4.event_id)
                FROM v_patient_iop iop4
                WHERE iop4.patient_id = iop3.patient_id
                  AND iop4.eye = iop3.eye
                  AND (:start_date IS NULL OR iop4.event_date > :start_date)
                  AND (:end_date IS NULL OR iop4.event_date < :end_date)
              )
        )
        GROUP BY iop.value";
                break;
            case 'ADVANCED':
                //break;
            default:
            return '
        SELECT value iop, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_iop
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND eye IS NOT NULL
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value';
                break;
        }
    }

    public function bindValues()
    {
        return array();
    }
}
