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
        SELECT value cct, COUNT(*) frequency
        FROM v_patient_cct
        WHERE patient_id IN (' . implode(', ', $this->id_list) .')
        GROUP BY value';
    }

    public function bindValues()
    {
        return array();
    }
}
