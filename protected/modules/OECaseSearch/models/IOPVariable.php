<?php
class IOPVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'iop';
        $this->label = 'IOP';
        $this->unit = 'mm Hg';
    }

    public function query($searchProvider)
    {
        switch ($this->csv_mode) {
            case 'BASIC':
                return '
        SELECT c.first_name, c.last_name, value iop
        FROM v_patient_iop iop
        JOIN patient p ON p.id = iop.patient_id
        JOIN contact c ON c.id = p.contact_id
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND eye IS NOT NULL
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)';
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

    public function csvColumns($mode)
    {
        if ($mode === 'BASIC') {
            return array(
                'First name',
                'Surname',
                'IOP (mm Hg)'
            );
        } else {
            return array();
        }
    }

    public function bindValues()
    {
        return array();
    }
}
