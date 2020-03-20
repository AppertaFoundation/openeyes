<?php

/**
 * Class CaseSearchVariable
 * @property $id_list array
 * @property $label string
 * @property $field_name string
 * @property $unit string
 */
abstract class CaseSearchVariable
{
    public $field_name;
    public $label;
    public $unit;
    public $id_list;

    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }
}