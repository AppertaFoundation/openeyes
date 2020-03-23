<?php

/**
 * Class CaseSearchVariable
 * @property $id_list array
 * @property $label string
 * @property $field_name string
 * @property $unit string
 * @property $csv_mode string|null
 */
abstract class CaseSearchVariable
{
    public $field_name;
    public $label;
    public $unit;
    public $id_list;
    public $csv_mode = null; // Can be either 'BASIC' or 'ADVANCED'

    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }

    abstract public function csvColumns($mode);
}