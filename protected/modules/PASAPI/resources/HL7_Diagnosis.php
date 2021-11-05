<?php

    namespace OEModule\PASAPI\resources;

class HL7_Diagnosis extends BaseHL7_Section
{
    protected $prefix = "DG1";

    public $identifier;
    public $diagnosis_coding_method = "SCT";
    public $diagnosis_code_identifier;
    public $diagnosis_code_text = "SCT";
    public $diagnosis_code_name_of_coding_system = "ECDS";
    public $diagnosis_description;
    public $diagnosis_date_time;
    public $diagnosis_type;
    public $clinician_id_number;
    public $clinician_family_name;
    public $clinician_given_name;
    public $clinician_prefix;
    public $clinician_degree;

    /***
     * @param \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis $diag
     */
    public function setDiagnosisFromData($diag)
    {
        $this->diagnosis_code_identifier = $diag->disorder->ecds_code;

        $this->diagnosis_description = $diag->disorder->term; //or fully_specified_name
        $this->diagnosis_date_time = $diag->date." ".$diag->time;
        $this->diagnosis_type = "410605003"; //Confirmed diagnosis
        $clinician = \User::model()->findByPk($diag->created_user_id);
        if ($clinician) {
            $this->clinician_id_number = $clinician->registration_code;
            $this->clinician_family_name = $clinician->last_name;
            $this->clinician_given_name = $clinician->first_name;
            $this->clinician_prefix = $clinician->title;
            $this->clinician_degree = $clinician->qualifications;
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.1' => $this->identifier ?? '',
            $this->prefix.'.2' => $this->diagnosis_coding_method ?? '',
            $this->prefix.'.3.1' => $this->diagnosis_code_identifier ?? '',
            $this->prefix.'.3.2' => $this->diagnosis_code_text ?? '',
            $this->prefix.'.3.3' => $this->diagnosis_code_name_of_coding_system ?? '',
            $this->prefix.'.4' => $this->diagnosis_description ?? '',
            $this->prefix.'.5' => substr(str_replace('-', '', str_replace(':', '', str_replace(' ', '', $this->diagnosis_date_time ?? ''))), 0, 14),
            $this->prefix.'.6' => $this->diagnosis_type ?? '',
            $this->prefix.'.16.1' => $this->clinician_id_number ?? '',
            $this->prefix.'.16.2' => $this->clinician_family_name ?? '',
            $this->prefix.'.16.3' => $this->clinician_given_name ?? '',
            $this->prefix.'.16.6' => $this->clinician_prefix ?? '',
            $this->prefix.'.16.7' => $this->clinician_degree ?? ''
        );

        return $attributes;
    }
}
