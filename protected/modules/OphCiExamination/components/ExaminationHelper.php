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
                    $left_principals[] = array($patient_episode->diagnosis, $patient_episode->getSubspecialtyText());;
                }
            }
        }
        return array($right_principals, $left_principals);

    }
}