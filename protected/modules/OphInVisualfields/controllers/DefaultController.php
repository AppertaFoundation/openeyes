<?php

class DefaultController extends BaseEventTypeController
{

    protected function setComplexAttributes_Element_OphInVisualfields_Condition($element, $data, $index)
    {
        $abilities = array();

        if (!empty($data['MultiSelect_ability'])) {
            foreach ($data['MultiSelect_ability'] as $ability_id) {
                $assignment = new Element_OphInVisualfields_Condition_Ability_Assignment();
                $assignment->id = $ability_id;

                $abilities[] = OphInVisualfields_Condition_Ability::model()->findByPk($ability_id);
            }
        }

        $element->abilitys = $abilities;
    }

    protected function saveComplexAttributes_Element_OphInVisualfields_Condition($element, $data, $index)
    {
        $element->updateMultiSelectData('Element_OphInVisualfields_Condition_Ability_Assignment', empty($data['MultiSelect_ability']) ? array() : $data['MultiSelect_ability'], 'ophinvisualfields_condition_ability_id');
    }

    public function getophinvisualfields_condition_ability_defaults()
    {
        $ids = array();
        foreach (OphInVisualfields_Condition_Ability::model()->findAll('`default` = ?', array(1)) as $item) {
            $ids[] = $item->id;
        }

        return $ids;
    }

    protected function setComplexAttributes_Element_OphInVisualfields_Result($element, $data, $index)
    {
        $assessments = array();

        if (!empty($data['MultiSelect_assessment'])) {
            foreach ($data['MultiSelect_assessment'] as $assessment_id) {
                $assignment = new Element_OphInVisualfields_Result_Assessment_Assignment();
                $assignment->id = $assessment_id;

                $assessments[] = OphInVisualfields_Result_Assessment::model()->findByPk($assessment_id);
            }
        }

        $element->assessment = $assessments;
    }

    protected function saveComplexAttributes_Element_OphInVisualfields_Result($element, $data, $index)
    {
        $element->updateMultiSelectData('Element_OphInVisualfields_Result_Assessment_Assignment', empty($data['MultiSelect_assessment']) ? array() : $data['MultiSelect_assessment'], 'ophinvisualfields_result_assessment_id');
    }
}
