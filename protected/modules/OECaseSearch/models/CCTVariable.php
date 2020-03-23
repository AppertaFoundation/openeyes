<?php
class CCTVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'cct';
        $this->label = 'CCT';
        $this->unit = 'microns';
    }

    public function query($searchProvider)
    {
        return '
        SELECT value cct, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value';
    }

    public function bindValues()
    {
        return array();
    }
}
