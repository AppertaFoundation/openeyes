<?php

use OEModule\OphGeneric\models\Assessment;
use OEModule\OphGeneric\models\AssessmentEntry;

class DefaultController extends BaseEventTypeController
{
    protected $show_element_sidebar = false;

    /**
     * Abstraction of getting the elements for the event being controlled to allow more complex overrides (such as workflow)
     * where required.
     *
     * This should be overridden if the standard elements for the event are affected by the controller state.
     *
     * @return BaseEventTypeElement[]
     */
    protected function getEventElements()
    {
        if ($this->event && !$this->event->isNewRecord) {
            $elements = $this->event->getElements();
        } else {
            $elements = $this->event_type->getDefaultElements();
        }
        return $elements;
    }

    /**
     * Custom validation for elements.
     *
     * @param array $data
     * @return array|mixed
     * @throws \Exception
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);

        if (isset($data['OEModule_OphGeneric_models_Assessment'])) {
            $errors = $this->setAndValidateAssessmentFromData($data, $errors);
        }

        return $errors;
    }


    /**
     * Custom validation for OCT element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateAssessmentFromData($data, $errors)
    {
        $et_name = Assessment::model()->getElementTypeName();
        $assessment_element = $this->getOpenElementByClassName('OEModule_OphGeneric_models_Assessment');
        $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

        //TODO: make validation error link to Assessment work:
        // currently, "addError()" is not called so function "scrolToElement()" is not called

        foreach ($entries as $assessment_id => $assessment_entry_sides) {
            foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                $assessment_entry->attributes = $assessment_entry_data;

                if (!$assessment_entry->validate()) {
                    $assessmentErrors = $assessment_entry->getErrors();
                    foreach ($assessmentErrors as $assessmentErrorsAttributeName => $assessmentErrorsMessages) {
                        foreach ($assessmentErrorsMessages as $assessmentErrorMessage) {
                            $assessment_element->setFrontEndError('OEModule_OphGeneric_models_Assessment_entries_' . $assessment_id . '_' . $eye_side . '_' . $assessmentErrorsAttributeName);
                            $errors[$et_name][] = $assessmentErrorMessage;
                        }
                    }
                }
            }
        }

        return $errors;
    }


    protected function saveComplexAttributes_Assessment($element, $data, $index)
    {
        $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

        foreach ($entries as $assessment_id => $assessment_entry_sides) {
            foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                $assessment_entry->attributes = $assessment_entry_data;
                $assessment_entry->save(false);
            }
        }
    }
}
