<?php

    namespace OEModule\PASAPI\resources;

    use OEModule\PASAPI\resources\BaseHL7;
    use OEModule\PASAPI\resources\HL7_Patient_Visit;
    use OEModule\PASAPI\resources\HL7_Patient_Visit_Additional;
    use OEModule\PASAPI\resources\HL7_Diagnosis;
    use OEModule\PASAPI\resources\HL7_Procedure;
    use OEModule\PASAPI\resources\HL7_UK_Additional;

class HL7_A08 extends BaseHl7
{
    protected const event_type = "A08";

    //Patient is inherited

    //Patient Visit
    protected $patient_visit;

    //Patient Visit additional
    protected $patient_visit_additional;

    //Diagnosis
    protected $diagnosis = array();

    //Procedures
    protected $procedures = array();

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

            $hl7_patient_visit_additional = new HL7_Patient_Visit_Additional();
            $hl7_patient_visit_additional->setPatientVisitAdditionalFromEvent($event_id);
            $this->setPatientVisitAdditional($hl7_patient_visit_additional);

            $diagnosis_element = \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::model()->find("event_id = ?", array($event_id));
            if ($diagnosis_element) {
                $diagnosis_data = $diagnosis_element->diagnoses;
                $counter = 2;
                foreach ($diagnosis_data as $diag) {
                    $hl7_diagnosis = new HL7_Diagnosis();
                    $hl7_diagnosis->setDiagnosisFromData($diag);
                    if ($diag->principal === "1") {
                        $hl7_diagnosis->identifier = 1;
                    } else {
                        $hl7_diagnosis->identifier = $counter;
                        $counter++;
                    }
                    $this->addDiagnosis($hl7_diagnosis);
                }
            }

            $counter = 1;

            $investigation_element = \OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation::model()->find("event_id = ?", array($event_id));
            if ($investigation_element) {
                $investigation_data = $investigation_element->entries;
                foreach ($investigation_data as $invest) {
                    $hl7_procedures = new HL7_Procedure();
                    $hl7_procedures->setProceduresFromInvestigation($invest, $investigation_element->description);
                    $hl7_procedures->identifier = $counter;
                    $counter++;
                    $this->addProcedure($hl7_procedures);
                }
            }
            $clinic_procedures_element = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicProcedures::model()->find("event_id = ?", array($event_id));
            if ($clinic_procedures_element) {
                $clinic_procedures_data = $clinic_procedures_element->entries;
                foreach ($clinic_procedures_data as $proc) {
                    $hl7_procedures = new HL7_Procedure();
                    $hl7_procedures->setProceduresFromClinicalProcedure($proc);
                    $hl7_procedures->identifier = $counter;
                    $counter++;
                    $this->addProcedure($hl7_procedures);
                }
            }

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
            'PV1' => $this->patient_visit,
            'PV2' => $this->patient_visit_additional,
            'DG1' => $this->diagnosis,
            'PR1' => $this->procedures,
            'ZU1' => $this->uk_additional
        );

        return $attributes;
    }
}
