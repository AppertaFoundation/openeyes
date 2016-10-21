<?php

/**
 * Class Crud
 *
 * Allows generic crud options for models
 */
class Crud extends Admin
{
    /**
     * @param $type
     *
     * @throws Exception
     */
    protected function audit($type, $data = null)
    {
        Audit::add('crud-'.$this->modelName, $type, $data);
    }
}