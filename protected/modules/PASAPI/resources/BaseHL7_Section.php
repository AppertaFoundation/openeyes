<?php

    namespace OEModule\PASAPI\resources;

class BaseHL7_Section implements HL7_Section_Interface
{
    protected $prefix = "";

    function __construct($data = array())
    {
        foreach ($data as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array();

        return $attributes;
    }
}
