<?php
class VAVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'va';
        $this->label = 'VA';
        $this->unit = 'ETDRS Letters';
    }

    public function query($searchProvider)
    {
        return 'SELECT ETDRS_value va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_va_converted
        WHERE patient_id IN (' . implode(',', $this->id_list) . ')
        AND eye IS NOT NULL
        AND (:start_date IS NULL OR reading_date > :start_date)
        AND (:end_date IS NULL OR reading_date < :end_date)
        GROUP BY ETDRS_value';
    }

    public function bindValues()
    {
        return array();
    }
}
