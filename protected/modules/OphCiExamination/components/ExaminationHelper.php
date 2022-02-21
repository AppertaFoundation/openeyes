<?php
/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

namespace OEModule\OphCiExamination\components;


class ExaminationHelper
{
    /**
     * @param \Episode $episode
     * @return array
     */
    public static function getOtherPrincipalDiagnoses(\Episode $episode)
    {
        $right_principals = array();
        $left_principals = array();
        foreach ($episode->patient->episodes as $patient_episode) {
            if ($patient_episode->id != $episode->id && $patient_episode->diagnosis) {
                if (in_array($patient_episode->eye_id, array(\Eye::RIGHT, \Eye::BOTH))) {
                    $right_principals[] = array($patient_episode->diagnosis, $patient_episode->getSubspecialtyText());
                }
                if (in_array($patient_episode->eye_id, array(\Eye::LEFT, \Eye::BOTH))) {
                    $left_principals[] = array($patient_episode->diagnosis, $patient_episode->getSubspecialtyText());
                }
            }
        }
        return array($right_principals, $left_principals);
    }

    /**
     * A list of deprecated or optional elements to filter out from the available elements for Examination
     * @return array
     */
    public static function elementFilterList()
    {
        if (\Yii::app()->hasModule('OphCoTherapyapplication')) {
            $remove = array('OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagement');
        } else {
            $remove = array('OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagementComplex');
        }

        // Deprecated elements that we keep in place for backward compatibility with rendering
        return array_merge($remove, array(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Conclusion',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Comorbidities',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Risks',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_PupillaryAbnormalities',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement_Archive',
            'OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation',
        ));

    }
}
