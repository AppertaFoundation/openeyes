<?php

    namespace OEModule\PASAPI\resources;

    use OEModule\PASAPI\resources\BaseHL7;
    use OEModule\PASAPI\resources\HL7_Patient_Visit;
    use OEModule\PASAPI\resources\HL7_Patient_Visit_Additional;
    use OEModule\PASAPI\resources\HL7_Diagnosis;
    use OEModule\PASAPI\resources\HL7_Procedure;
    use OEModule\PASAPI\resources\HL7_UK_Additional;

class HL7_A13 extends BaseHl7
{
    protected const event_type = "A13";

    //Patient is inherited

    //Patient Visit
    protected $patient_visit;

    //UK Additional data
    protected $uk_additional;

    function setDataFromEvent($event_id)
    {
        $event = \Event::model()->findByPk($event_id);

        if ($event) {
            $hl7_patient = new HL7_Patient();
            $hl7_patient->setPatientFromEvent($event_id);
            $this->setPatient($hl7_patient);

            $hl7_patient_visit = new HL7_Patient_Visit();
            $hl7_patient_visit->setPatientVisitFromEvent($event_id);
            $this->setPatientVisit($hl7_patient_visit);

            $hl7_uk_additional = new HL7_UK_Additional();
            $hl7_uk_additional->setUKAdditionalDataFromEvent($event_id);
            $this->setUKAdditionalData($hl7_uk_additional);
        }
    }

    /***
     * Set Patient Visit from HL7_Patient_Visit object
     * @param HL7_Patient_Visit $patient_visit
     */
    function setPatientVisit(HL7_Patient_Visit $patient_visit)
    {
        $this->patient_visit = $patient_visit->getHL7attributes();
    }

    /***
     * Set Patient Visit Additional from HL7_Patient_Visit_Additional object
     * @param HL7_Patient_Visit_Additional $patient_visit_additional
     */
    function setPatientVisitAdditional(HL7_Patient_Visit_Additional $patient_visit_additional)
    {
        $this->patient_visit_additional = $patient_visit_additional->getHL7attributes();
    }

    /***
     * Add a new Diagnosis record from HL7_Diagnosis object
     * @param HL7_Diagnosis $diagnosis
     */
    function addDiagnosis(HL7_Diagnosis $diagnosis)
    {
        $this->diagnosis[] = $diagnosis->getHL7attributes();
    }

    /***
     * Add a new Procedure record from HL7_Diagnosis object
     * @param HL7_Procedure $procedure
     */
    function addProcedure(HL7_Procedure $procedure)
    {
        $this->procedures[] = $procedure->getHL7attributes();
    }

    /***
     * Set UK Additional Data from HL7_UK_Additional object
     * @param HL7_UK_Additional $uk_additional
     */
    function setUKAdditionalData(HL7_UK_Additional $uk_additional)
    {
        $this->uk_additional = $uk_additional->getHL7attributes();
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            'event_type' => $this::event_type,
            'PID' => $this->patient,
            'PV1' => $this->patient_visit
        );

        return $attributes;
    }
}
