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
