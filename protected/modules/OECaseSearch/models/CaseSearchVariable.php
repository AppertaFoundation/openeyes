<?php

/**
 * Class CaseSearchVariable
 * @property $id_list array
 */
abstract class CaseSearchVariable
{
    protected $field_name;
    protected $label;
    private $_id_list;

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if ($name === 'id_list') {
            return $this->_id_list;
        }
        return $this->$name;
    }

    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }
}