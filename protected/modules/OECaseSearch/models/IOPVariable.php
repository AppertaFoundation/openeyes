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
        // TODO: Return a query string here.
        return null;
    }

    public function bindValues()
    {
        // TODO: Return an array of  bind mappings here (if applicable)
        return array();
    }
}
