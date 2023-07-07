<?php
use OEModule\OphDrPGDPSD\models\Element_DrugAdministration;

class DrugAdministrationCreator extends \EventCreator
{
    private $entries = array();

    public function __construct($episode)
    {
        $da_event_type = \EventType::model()->find('name = "Drug Administration"');
        parent::__construct($episode, $da_event_type->id);

        $this->elements[Element_DrugAdministration::class] = new Element_DrugAdministration();
    }

    public function setEntriesAndWorklistPatient($assignment, $worklist_patient_id, $firm_id)
    {
        $element = $this->elements['Element_DrugAdministration'];
        $element->assignments = array($assignment);
        $this->event->worklist_patient_id = $worklist_patient_id;
        $this->event->firm_id = $firm_id;
        $this->event->automated_source = 'Generate automated Drug Administration Event through worklist';
    }
    protected function saveElements($event_id)
    {
        foreach ($this->elements as $element) {
            $element->event_id = $event_id;
            if (!$element->save()) {
                $this->addErrors($element->getErrors());
                \OELog::log("Element_DrugAdministration:" . print_r($element->getErrors(), true));
                return false;
            }
        }
        return true;
    }
}
