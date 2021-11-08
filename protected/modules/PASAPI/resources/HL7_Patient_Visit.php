<?php

    namespace OEModule\PASAPI\resources;

class HL7_Patient_Visit extends BaseHL7_Section
{
    protected $prefix = "PV1";

    protected $patient_class = "E";
    public $point_of_care;
    public $room;
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
            $worklist_patient = $event->worklist_patient;
            if ($worklist_patient) {
                $pathway = \Pathway::model()->find("worklist_patient_id = ?", array($worklist_patient->id));
                if ($pathway) {
                    $room_pathway_step = $pathway->peek(\PathwayStep::STEP_REQUESTED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                    if ($room_pathway_step) {
                        $this->room = substr( "WF". ( $room_pathway_step->short_name ?? ''), 0, 3) ;
                    } else {
                        $room_pathway_step = $pathway->peek(\PathwayStep::STEP_STARTED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                        if ($room_pathway_step) {
                            $prefix = "With ";
                            if ($room_pathway_step->short_name == "Triage") {
                                $prefix = "In ";
                            }
                            $this->room = $prefix. ( $room_pathway_step->short_name ?? '') ;
                        } else {
                            $room_pathway_step = $pathway->peek(\PathwayStep::STEP_COMPLETED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                            if ($room_pathway_step) {
                                $this->room = ( $room_pathway_step->short_name ?? '') ;
                            }
                        }
                    }
                }

                $wla = \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc',
                        array($worklist_patient->id, \WorklistAttribute::model()->find('name = ? and worklist_id = ?', array('Clinic', $worklist_patient->worklist_id))->id));
                $this->point_of_care = ($wla ? $wla->attribute_value : '');
                $wla = \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc',
                        array($worklist_patient->id, \WorklistAttribute::model()->find('name = ? and worklist_id = ?', array('AdmitSource', $worklist_patient->worklist_id))->id));
                $this->admit_source = ($wla ? $wla->attribute_value : '');
                $wla = \WorklistPatientAttribute::model()->find('worklist_patient_id = ? and worklist_attribute_id = ? order by id desc',
                        array($worklist_patient->id, \WorklistAttribute::model()->find('name = ? and worklist_id = ?', array('VisitNumber', $worklist_patient->worklist_id))->id));
                $this->visit_number = ($wla ? $wla->attribute_value : '');
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
                            $this->discharge_facility = $transfer_institution->pas_key;
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
