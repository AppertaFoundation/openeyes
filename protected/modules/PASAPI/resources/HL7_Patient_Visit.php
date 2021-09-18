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

    public function setPatientVisitFromEvent($event_id)
    {
        $event = \Event::model()->findByPk($event_id);
        if ($event) {
            $worklist_patient = $event->worklist_patient;
            if ($worklist_patient) {
                $pathway = \Pathway::model()->find("worklist_patient_id = ?", array($worklist_patient->id));
                if ($pathway) {
                    $room_pathway_step = $pathway->peek(\PathwayStep::STEP_STARTED, array_column(\PathwayStepType::model()->findAll(), 'id'));
                    if ($room_pathway_step) {
                        $this->room = $room_pathway_step->long_name;
                    }
                }

                $this->point_of_care = \WorklistPatientAttribute::model()->findAll('worklist_patient_id = ? and worklist_attribute_id = ? ', array($worklist_patient->id, \WorklistAttribute::model()->find('name = ?', array('Clinic'))->id));
                $this->admit_source = \WorklistPatientAttribute::model()->findAll('worklist_patient_id = ? and worklist_attribute_id = ? ', array($worklist_patient->id, \WorklistAttribute::model()->find('name = ?', array('AdmitSource'))->id));
                $this->visit_number = \WorklistPatientAttribute::model()->findAll('worklist_patient_id = ? and worklist_attribute_id = ? ', array($worklist_patient->id, \WorklistAttribute::model()->find('name = ?', array('VisitNumber'))->id));
            }
        }
    }

    /**
     * @return array $attributes
     */
    function getHL7attributes()
    {
        $attributes = array(
            $this->prefix.'.2' => $this->patient_class,
            $this->prefix.'.3.1' => $this->point_of_care,
            $this->prefix.'.3.2' => $this->room,
            $this->prefix.'.14' => $this->admit_source,
            $this->prefix.'.19' => $this->visit_number
        );

        return $attributes;
    }
}
