<?php

    namespace OEModule\PASAPI\resources;

class HL7_Patient_Visit extends BaseHL7_Section
{
    protected $prefix = "PV1";

    protected $patient_class = "E";
    public $point_of_care;
    public $room;
    public $clinician;
    public $admit_source;
    public $visit_number;
    public $discharge_status;
    public $discharge_to_location;
    public $discharge_facility;
    public $discharge_datetime;

    public function setPatientVisitFromEvent($event_id)
    {
        $event = \Event::model()->findByPk($event_id);
        if ($event) {
            $clinician = \User::model()->findByPk($event->created_user_id);
            if ($clinician) {
                $this->clinician = $clinician->registration_code;
            }
            $worklist_patient = $event->worklist_patient;
            if ($worklist_patient) {
                $clinic_attribute = \WorklistAttribute::model()->find('name = ? and worklist_id = ?', ['Clinic', $worklist_patient->worklist_id]);
                $admin_source = \WorklistAttribute::model()->find('name = ? and worklist_id = ?', ['AdmitSource', $worklist_patient->worklist_id]);
                $visit_number = \WorklistAttribute::model()->find('name = ? and worklist_id = ?', ['VisitNumber', $worklist_patient->worklist_id]);

                $wla = $clinic_attribute
                    ? \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc', [$worklist_patient->id, $clinic_attribute->id])
                    : null;

                $this->point_of_care = ($wla ? $wla->attribute_value : '');

                $wla = $admin_source
                    ? \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc', [$worklist_patient->id, $admin_source->id])
                    : null;

                $this->admit_source = ($wla ? $wla->attribute_value : '');

                $wla = $visit_number
                    ? \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc', [$worklist_patient->id, $visit_number->id])
                    : null;
                $this->visit_number = ($wla ? $wla->attribute_value : '');
                $pathway = \Pathway::model()->find("worklist_patient_id = ?", array($worklist_patient->id));
                if ($pathway) {
                    $room_pathway_step = $pathway->peek(\PathwayStep::STEP_REQUESTED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                    if ($room_pathway_step) {
                        $prefix = "Waiting for ";
                    } else {
                        $room_pathway_step = $pathway->peek(\PathwayStep::STEP_STARTED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                        if ($room_pathway_step) {
                            $prefix = "With ";
                            if (substr($room_pathway_step->short_name,-6) == "Triage") { //Triage, RDCEC Triage, AES Triage
                                $prefix = "In ";
                            }
                        } else {
                            $room_pathway_step = $pathway->peek(\PathwayStep::STEP_COMPLETED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                            $prefix = "";
                        }
                    }
                    \Yii::log($this->point_of_care); // AES || RDCEC
                    \Yii::log($prefix.( $room_pathway_step->short_name ?? '')); // Waiting for RDCEC Nurse || With RDCEC Doctor || In Triage || ...
                    $this->room = $prefix.( $room_pathway_step->short_name ?? '');
                }
            }

            $discharge_status = \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()->find("name = 'Discharge'");
            $clinical_outcome = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::model()->find("event_id = ?", array($event->id));

            if ($clinical_outcome) {
                $clinical_outcome_entry = \OEModule\OphCiExamination\models\ClinicOutcomeEntry::model()->find("element_id = ? and status_id = ? ", array($clinical_outcome->id, $discharge_status->id));
                if ($clinical_outcome_entry) {
                    $this->discharge_status = $clinical_outcome_entry->discharge_status->ecds_code;
                    $this->discharge_to_location = $clinical_outcome_entry->discharge_destination->ecds_code;
                    $transfer_institution = $clinical_outcome_entry->transfer_to;
                    if ($transfer_institution) {
                            $this->discharge_facility = $transfer_institution->remote_id . '00';
                    }
                    $this->discharge_datetime = substr(str_replace('-', '', str_replace(':', '', str_replace(' ', '', $clinical_outcome_entry->created_date ?? ''))), 0, 14);
                }
            }
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.2' => $this->patient_class ?? '',
            $this->prefix.'.3.1' => $this->point_of_care ?? '',
            $this->prefix.'.3.2' => $this->room ?? '',
            $this->prefix.'.7.1' => $this->clinician ?? '',
            $this->prefix.'.14' => $this->admit_source ?? '',
            $this->prefix.'.19' => $this->visit_number ?? '',
            $this->prefix.'.36.1' => $this->discharge_status ?? '',
            $this->prefix.'.37.1' => $this->discharge_to_location ?? '',
            $this->prefix.'.42.4' => $this->discharge_facility ?? '',
            $this->prefix.'.45' => $this->discharge_datetime ?? ''
        );

        return $attributes;
    }
}
