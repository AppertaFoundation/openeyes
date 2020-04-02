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
    public $x_label;
    public $id_list;
    public $query_flags = array(); // Extra flags to use for determining how to retrieve the data.
    public $csv_mode = null; // Can be either 'BASIC' or 'ADVANCED'
    public $eye_cardinality = false;
    public $bin_size = 10;
    public $min_value = 0;

    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }

    public function getPrimaryDataPointName()
    {
        return $this->field_name;
    }
}