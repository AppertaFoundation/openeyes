<?php

namespace OEModule\OphTrConsent\models;

class Element_OphTrConsent_PatientSignature extends \Element_PatientSignature
{
    use \ConsentTypeAware;

    public function tableName()
    {
        return "et_ophtrconsent_patient_signature";
    }

    public function getSignatoryPersonOptions()
    {
        return array_intersect_key($this->getSignatoryPersonLabels(), array_flip([
           self::SIGNATORY_PERSON_PATIENT,
        ]));
    }
}