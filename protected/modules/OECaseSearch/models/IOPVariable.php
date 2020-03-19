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
        return '
        SELECT value iop, COUNT(*) frequency
        FROM v_patient_iop
        WHERE patient_id IN (' . implode(', ', $this->id_list) . ')
        AND eye IS NOT NULL
        GROUP BY value';
    }

    public function bindValues()
    {
        return array();
    }
}
