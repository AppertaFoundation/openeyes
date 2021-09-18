<?php

    namespace OEModule\PASAPI\resources;

class HL7_Patient_Visit_Additional extends BaseHL7_Section
{
    protected $prefix = "PV2";

    public $chief_complaint_code;
    public $chief_complaint_description;
    public $chief_alternative_identifier;
    protected $name_of_alternative_coding_system = "ECDS";
    public $referral_source_code;
    public $patient_status_code;
    public $admission_level_acuity_identifier;
    public $admission_level_text;
    public $admission_level_name_of_coding_system = "PCS";
    public $admission_level_alternative_identifier;
    public $admission_level_name_of_alternative_coding_system = "ECDS";

    public function setPatientVisitAdditionalFromEvent($event_id)
    {
        $triage_element = \OEModule\OphCiExamination\models\Element_OphCiExamination_Triage::model()->find("event_id = ?", array($event_id));
        if ($triage_element) {
            $triage_data = \OEModule\OphCiExamination\models\OphCiExamination_Triage::model()->find("element_id = ?", array($triage_element->id));
            if ($triage_data) {
                $chief_complaint = \OEModule\OphCiExamination\models\OphCiExamination_Triage_ChiefComplaint::model()->findByPk($triage_data->chief_complaint_id);
                if ($chief_complaint) {
                    $this->chief_complaint_code = $chief_complaint->id;
                    $this->chief_complaint_description = $chief_complaint->description;
                    $this->chief_alternative_identifier = $chief_complaint->snomed_code;
                }
            }
        }
        /*
        "This field is optional and linked to the Admit Source PV1:14 field.

        Acceptable Values will be National Site Code or Local Site Code from the 4 below

        1. Barts And London NHS Trust - Local Site Code ""BARTS"" - National Site Code ""RNJ00""
        2. Chelsea And Westminster- Local Site Code ""CW"" - National Site Code ""RQM00""
        3. Great Ormond Street Hospital - Local Site Code ""GOSH"" - National Site Code ""RP400""
        4. University College Hospital - Local Site Code ""UCLH"" - National Site Code ""RRV00"""
        */
        $this->referral_source_code = ""; //Has to be clarified the source of referral_source_code
        /*
        "Patient Status Code
        WA - Waiting For Assessment
        TIP - Treatment in Progress
        TC - Treatment Complete"
        */
        $this->patient_status_code = ""; //Has to be clarified the source of patient_status_code

        $priority =  \OEModule\OphCiExamination\models\OphCiExamination_Triage_Priority::model()->findByPk($triage_data->priority_id);
        if ($priority) {
            $this->admission_level_acuity_identifier = $priority->id;
            $this->admission_level_text = $priority->description;
            $this->admission_level_alternative_identifier = $priority->snomed_code;
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.3.1' => $this->chief_complaint_code,
            $this->prefix.'.3.2' => $this->chief_complaint_description,
            $this->prefix.'.3.4' => $this->chief_alternative_identifier,
            $this->prefix.'.3.6' => $this->name_of_alternative_coding_system,
            $this->prefix.'.13' => $this->referral_source_code,
            $this->prefix.'.24' => $this->patient_status_code,
            $this->prefix.'.40.1' => $this->admission_level_acuity_identifier,
            $this->prefix.'.40.2' => $this->admission_level_text,
            $this->prefix.'.40.3' => $this->admission_level_name_of_coding_system,
            $this->prefix.'.40.4' => $this->admission_level_alternative_identifier,
            $this->prefix.'.40.5' => $this->name_of_alternative_coding_system
        );

        return $attributes;
    }
}
