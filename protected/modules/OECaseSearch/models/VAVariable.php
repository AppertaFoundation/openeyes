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
        return 'SELECT value va, COUNT(*) frequency
        FROM v_patient_va
        WHERE patient_id IN (' . implode(',', $this->id_list) . ')
        GROUP BY value';
    }

    public function bindValues()
    {
        return array();
    }
}
