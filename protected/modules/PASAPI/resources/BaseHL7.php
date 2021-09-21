<?php

    namespace OEModule\PASAPI\resources;

    use OEModule\PASAPI\resources\HL7_Patient;

class BaseHL7
{
    protected const event_type = "";

    //patient
    protected $patient;

    /***
     * Set Patient data from HL7_Patient object
     * @param HL7_Patient $patient
     */
    public function setPatient(HL7_Patient $patient)
    {
        $this->patient = $patient->getHL7attributes();
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            'event_type' => $this::event_type,
            'patient' => $this->patient
        );

        return $attributes;
    }
}
