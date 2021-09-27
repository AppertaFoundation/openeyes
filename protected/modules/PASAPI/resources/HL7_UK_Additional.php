<?php

    namespace OEModule\PASAPI\resources;

class HL7_UK_Additional extends BaseHL7_Section
{
    protected $prefix = "ZU1";

    public $identifier;
    public $text;
    public $name_of_coding_system;

    public function setUKAdditionalDataFromEvent($event_id)
    {
        $this->identifier = "";
        $this->text = "";
        $this->name_of_coding_system = "";
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.13.1' => $this->identifier ? $this->identifier : '',
            $this->prefix.'.13.2' => $this->text ? $this->text : '',
            $this->prefix.'.13.3' => $this->name_of_coding_system ? $this->name_of_coding_system : ''
        );

        return $attributes;
    }
}
