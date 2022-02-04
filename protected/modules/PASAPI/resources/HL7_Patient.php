<?php

    namespace OEModule\PASAPI\resources;

class HL7_Patient extends BaseHL7_Section
{
    protected $prefix = "PID";

    public $patient_number;
    protected $assigning_authority = "PAS";
    protected $identifer_type_code = "PAS";
    public $nhs_number;
    protected $nhs_assigning_authority = "NHS";
    protected $nhs_identifier_type_code = "NHS";

    function setPatient($patient_id)
    {
        $patient = \Patient::model()->findByPk($patient_id);
        if ($patient) {
            $this->patient_number = $patient->getHos();
            $this->nhs_number = str_replace('None','',str_replace(' ','',$patient->getNhs()));
        }
    }

    function setPatientFromEvent($event_id)
    {
        $event = \Event::model()->findByPk($event_id);
        if ($event) {
            $episode = $event->episode;
            if ($episode) {
                $this->setPatient($episode->patient_id);
            }
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.2.1' => $this->patient_number ?? '',
            $this->prefix.'.2.4' => $this->assigning_authority ?? '',
            $this->prefix.'.2.5' => $this->identifer_type_code ?? '',
            $this->prefix.'.3.1' => $this->nhs_number ?? '',
            $this->prefix.'.3.4' => $this->nhs_assigning_authority ?? '',
            $this->prefix.'.3.5' => $this->nhs_identifier_type_code ?? ''
        );

        return $attributes;
    }
}
