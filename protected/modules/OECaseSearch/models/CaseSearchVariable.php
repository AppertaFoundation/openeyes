<?php

/**
 * Class CaseSearchVariable
 */
abstract class CaseSearchVariable
{
    /**
     * @var string $field_name
     */
    public $field_name;

    /**
     * @var string $label
     */
    public $label;

    /**
     * @var string $x_label
     */
    public $x_label;

    /**
     * @var int[] $id_list
     */
    public $id_list;

    /**
     * @var array $query_flags
     */
    public $query_flags = array(); // Extra flags to use for determining how to retrieve the data.

    /**
     * @var string|null $csv_mode
     */
    public $csv_mode = null; // Can be either 'BASIC' or 'ADVANCED'

    /**
     * @var bool $eye_cardinality
     */
    public $eye_cardinality = false;

    /**
     * @var int $bin_size
     */
    public $bin_size = 10;

    /**
     * @var int $min_value
     */
    public $min_value = 0;

    /**
     * CaseSearchVariable constructor.
     * @param $id_list
     */
    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }

    /**
     * @return string Name of variable
     */
    public function getPrimaryDataPointName()
    {
        return $this->field_name;
    }
}