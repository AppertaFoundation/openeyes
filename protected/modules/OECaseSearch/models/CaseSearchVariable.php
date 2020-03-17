<?php

/**
 * Class CaseSearchVariable
 * @property $id_list array
 * @property $label string
 * @property $field_name string
 */
abstract class CaseSearchVariable
{
    protected $_field_name;
    protected $_label;
    private $_id_list;

    public function __get($name)
    {
        // TODO: Implement __get() method.
        switch ($name) {
            case 'id_list':
                return $this->_id_list;
                break;
            case 'field_name':
                return $this->_field_name;
                break;
            case 'label':
                return $this->_label;
                break;
            default:
                return $this->$name;
        }
    }

    public function __construct($id_list)
    {
        $this->id_list = $id_list;
    }
}