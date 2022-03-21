<?php

/**
 * Base class for all selectable variables in Advanced Search.
 */
abstract class CaseSearchVariable
{
    /**
     * @var string $field_name Internal field name
     */
    public string $field_name;

    /**
     * @var string $label Human-readable label for display.
     */
    public string $label;

    /**
     * @var string $x_label X-Axis plot label
     */
    public string $x_label;

    /**
     * @var int[] $id_list List of patient IDs to be plotted against.
     */
    public ?array $id_list;

    /**
     * @var array $query_flags List of extra flags to attach to the plot queries.
     */
    public array $query_flags = array(); // Extra flags to use for determining how to retrieve the data.

    /**
     * @var string|null $csv_mode CSV display mode. Either 'BASIC' or 'ADVANCED'.
     */
    public ?string $csv_mode = null; // Can be either 'BASIC' or 'ADVANCED'

    /**
     * @var bool $eye_cardinality Specifies whether the plotting queries should apply to both eyes.
     */
    public bool $eye_cardinality = false;

    /**
     * @var int $bin_size X-Axis bin size.
     */
    public int $bin_size = 10;

    /**
     * @var int $min_value Minimum X-axis value
     */
    public int $min_value = 0;

    /**
     * CaseSearchVariable constructor.
     * @param $id_list int[] List of Patient IDs
     */
    public function __construct(?array $id_list)
    {
        $this->id_list = $id_list;
    }

    /**
     * Get the name of the primary datapoint used in the plotting queries.
     * @return string Name of variable
     */
    public function getPrimaryDataPointName(): string
    {
        return $this->field_name;
    }
}
