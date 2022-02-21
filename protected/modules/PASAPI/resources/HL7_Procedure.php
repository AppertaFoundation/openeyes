<?php

    namespace OEModule\PASAPI\resources;

class HL7_Procedure extends BaseHL7_Section
{
    protected $prefix = "PR1";

    public $identifier;
    public $procedure_coding_method = "SCT";
    public $procedure_code_identifier;
    public $procedure_code_text;
    public $procedure_code_name_of_coding_system;
    public $procedure_date_time;
    public $procedure_functional_type;
    public $investigation_or_treatment_clinician;
    public $clinician_id_number;
    public $clinician_family_name;
    public $clinician_given_name;
    public $clinician_prefix;
    public $clinician_degree;

    public function setClinicianData($user_id)
    {
        $clinician = \User::model()->findByPk($user_id);
        if ($clinician) {
            $this->clinician_id_number = $clinician->registration_code;
            $this->clinician_family_name = $clinician->last_name;
            $this->clinician_given_name = $clinician->first_name;
            $this->clinician_prefix = $clinician->title;
            $this->clinician_degree = $clinician->qualifications;
        }
    }

    /***
     * @params \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry $entry
     */
    public function setProceduresFromInvestigation($entry, $comment)
    {
        $this->procedure_code_identifier = $entry->investigationCode->ecds_code;
        $this->procedure_code_text = $entry->investigationCode->name;
        if (!empty($entry->investigationCode->ecds_code)) {
            $this->procedure_code_name_of_coding_system = "ECDS";
        } else {
            $this->procedure_code_name_of_coding_system = "SNOMED";
        }
        $this->procedure_date_time = $entry->date." ".$entry->time;
        $this->procedure_functional_type = "D";

        $this->setClinicianData($entry->created_user_id);
    }

    /***
     * @params \OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedures_Entry $entry
     */
    public function setProceduresFromClinicalProcedure($entry)
    {
        $this->procedure_code_identifier = $entry->procedure->ecds_code;
        $this->procedure_code_text = $entry->procedure->term;
        if (!empty($entry->procedure)) {
            $this->procedure_code_name_of_coding_system = "ECDS";
        } else {
            $this->procedure_code_name_of_coding_system = "SNOMED";
        }
        $this->procedure_date_time = substr($entry->date, 0, 10)." ".$entry->outcome_time;
        $this->procedure_functional_type = "P";

        $this->setClinicianData($entry->created_user_id);
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.1' => $this->identifier ?? '',
            $this->prefix.'.2' => $this->procedure_coding_method ?? '',
            $this->prefix.'.3.1' => $this->procedure_code_identifier ?? '',
            $this->prefix.'.3.2' => $this->procedure_code_text ?? '',
            $this->prefix.'.3.3' => $this->procedure_code_name_of_coding_system ?? '',
            $this->prefix.'.5' => substr(str_replace('-', '', str_replace(':', '', str_replace(' ', '', $this->procedure_date_time ?? ''))), 0, 14),
            $this->prefix.'.6' => $this->procedure_functional_type ?? '',
            $this->prefix.'.11.1' => $this->clinician_id_number ?? '',
            $this->prefix.'.11.2' => $this->clinician_family_name ?? '',
            $this->prefix.'.11.3' => $this->clinician_given_name ?? '',
            $this->prefix.'.11.6' => $this->clinician_prefix ?? '',
            $this->prefix.'.11.7' => $this->clinician_degree ?? ''
        );

        return $attributes;
    }
}
