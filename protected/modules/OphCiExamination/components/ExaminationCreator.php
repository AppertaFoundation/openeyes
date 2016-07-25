<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\components;


class ExaminationCreator
{
    /**
     * Create an examination event
     *
     * @param $episodeId
     * @param $portalUserId
     * @param $examination
     * @param $eventType
     * @param $eyeIds
     * @param $refractionType
     * @param $opNoteEventId
     * @return Event
     * @throws CDbException
     * @throws Exception
     */
    public function saveExamination($episodeId, $portalUserId, $examination, $eventType, $eyeIds, $refractionType, $opNoteEventId = null)
    {
        //Create main examination event
        $examinationEvent = new Event();
        $examinationEvent->episode_id = $episodeId;
        $examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = $portalUserId;

        $examinationEvent->event_date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $examination['examination_date'])->format('Y-m-d');
        $examinationEvent->event_type_id = $eventType['id'];
        $examinationEvent->is_automated = 1;
        $examinationEvent->automated_source = json_encode($examination['op_tom']);
        if ($examinationEvent->save(true, null, true)) {
            $examinationEvent->refresh();
            $refraction = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction();
            $refraction->event_id = $examinationEvent->id;
            $refraction->created_user_id = $refraction->last_modified_user_id = $portalUserId;

            $iop = new \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure();
            $iop->event_id = $examinationEvent->id;
            $iop->created_user_id = $iop->last_modified_user_id = $portalUserId;
            $iop->eye_id = $eyeIds['both'];
            $iop->left_comments = 'Portal Add';
            $iop->right_comments = 'Portal Add';
            if (!$iop->save(true, null, true)) {
                throw new CDbException('iop failed: ' . print_r($iop->getErrors(), true));
            }
            $iop->refresh();

            $complications = new \OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications();
            $complications->event_id = $examinationEvent->id;
            $complications->created_user_id = $complications->last_modified_user_id = $portalUserId;
            $complications->eye_id = $eyeIds['both'];
            if (!$complications->save(true, null, true)) {
                throw new CDbException('Complications failed: ' . print_r($complications->getErrors(), true));
            }
            $complications->refresh();
            if (count($examination['patient']['eyes'][0]['reading'][0]['visual_acuity'])) {
                //create VisualFunction, required for visual acuity to show.
                $visualFunction = new \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction();
                $visualFunction->event_id = $examinationEvent->id;
                $visualFunction->eye_id = $eyeIds['both'];
                $visualFunction->left_rapd = 0;
                $visualFunction->right_rapd = 0;
                $visualFunction->created_user_id = $visualFunction->last_modified_user_id = $portalUserId;
                if (!$visualFunction->save(true, null, true)) {
                    throw new CDbException('Visual Function failed: ' . print_r($visualFunction->getErrors(), true));
                }

                $measure = $examination['patient']['eyes'][0]['reading'][0]['visual_acuity'][0]['measure'];
                $unit = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->find('name = :measure', array('measure' => $measure));
                //Create visual acuity
                $visualAcuity = new \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity();
                $visualAcuity->event_id = $examinationEvent->id;
                $visualAcuity->created_user_id = $visualAcuity->last_modified_user_id = $portalUserId;
                $visualAcuity->eye_id = $eyeIds['both'];
                $visualAcuity->unit_id = $unit->id;
                if (!$visualAcuity->save(false, null, true)) {
                    throw new CDbException('Visual Acuity failed: ' . print_r($visualAcuity->getErrors(), true));
                }
                $visualAcuity->refresh();
            }

            foreach ($examination['patient']['eyes'] as $eye) {
                $eyeLabel = strtolower($eye['label']);
                $refractionReading = $eye['reading'][0]['refraction'];
                $typeSide = $eyeLabel . '_type_id';
                $sphereSide = $eyeLabel . '_sphere';
                $cylinderSide = $eyeLabel . '_cylinder';
                $axisSide = $eyeLabel . '_axis';
                $refraction->$typeSide = $refractionType['id'];
                $refraction->$sphereSide = $refractionReading['sphere'];
                $refraction->$cylinderSide = $refractionReading['cylinder'];
                $refraction->$axisSide = $refractionReading['axis'];

                foreach ($eye['reading'][0]['visual_acuity'] as $vaData) {
                    $vaReading = new \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading();
                    $vaReading->element_id = $visualAcuity->id;
                    $baseValue = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue::model()->getBaseValue($unit->id, $vaData['reading']);
                    $vaReading->value = $baseValue;
                    $vaReading->method_id = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->find('name = :name', array('name' => $vaData['method']))->id;
                    $vaReading->side = ($eyeLabel === 'left') ? \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::LEFT : \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::RIGHT;
                    $vaReading->created_user_id = $vaReading->last_modified_user_id = $portalUserId;
                    if (!$vaReading->save(true, null, true)) {
                        throw new CDbException('Visual Acuity Reading failed: ' . print_r($vaReading->getErrors(), true));
                    }
                }

                $iopReading = $eye['reading'][0]['iop'];
                $iopValue = new \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value();
                $iopValue->element_id = $iop->id;
                $iopValue->eye_id = $eyeIds[$eyeLabel];
                $iopReadingValue = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading::model()->find('value = ?', array($iopReading['mm_hg']));
                $instrument = \OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->find('name = ?', array($iopReading['instrument']));
                $iopValue->reading_id = $iopReadingValue['id'];
                $iopValue->instrument_id = $instrument['id'];
                if (!$iopValue->save(true, null, true)) {
                    throw new CDbException('iop value failed: ' . print_r($iop->getErrors(), true));
                }
                if (array_key_exists('complications', $eye)) {
                    if (count($eye['complications'])) {
                        foreach ($eye['complications'] as $complicationArray) {
                            $eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
                            $eyeComplication->element_id = $complications->id;
                            $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "' . $complicationArray['complication'] . '"');
                            $eyeComplication->complication_id = $complicationToAdd->id;
                            $eyeComplication->operation_note_id = $opNoteEventId;
                            $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                            $eyeComplication->created_user_id = $eyeComplication->last_modified_user_id = $portalUserId;
                            $eyeComplication->save(true, null, true);
                        }
                    } else {
                        $eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
                        $eyeComplication->element_id = $complications->id;
                        $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "none"');
                        $eyeComplication->complication_id = $complicationToAdd->id;
                        $eyeComplication->operation_note_id = $opNoteEventId;
                        $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                        $eyeComplication->created_user_id = $eyeComplication->last_modified_user_id = $portalUserId;
                        $eyeComplication->save(true, null, true);
                    }
                }
            }

            $refraction->eye_id = $eyeIds['both'];
            if (!$refraction->save(true, null, true)) {
                throw new CDbException('Refraction failed: ' . print_r($iop->getErrors(), true));
            }
            
            return $examinationEvent;
        } else {
            throw new CDbException('Examination failed: ' . print_r($examinationEvent->getErrors(), true));
        }
    }

    /**
     * @return mixed|null
     * @throws Exception
     */
    public function getPortalUser()
    {
        $user = new User();
        $portalUser = $user->portalUser();
        if (!$portalUser) {
            throw new Exception('No User found for import');
        }

        return $portalUser->id;
    }

    /**
     * @return array
     */
    public function getEyes()
    {
        $eyes = Eye::model()->findAll();
        $eyeIds = array();
        foreach ($eyes as $eye) {
            $eyeIds[strtolower($eye->name)] = $eye->id;
        }

        return $eyeIds;
    }
}