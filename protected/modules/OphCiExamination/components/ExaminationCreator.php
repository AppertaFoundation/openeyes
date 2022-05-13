<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\components;

use OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure;
use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments;
use OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications;
use OEModule\OphCiExamination\models\OphCiExamination_Instrument;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value;
use OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;
use OEModule\OphCoMessaging\components\MessageCreator;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

class ExaminationCreator
{
    protected $examinationEyeId;

    public function __construct()
    {
        echo \Yii::app()->params['optom_comment_alert'] . "\n";
    }

    /**
     * Create an examination event.
     *
     * @param $episodeId
     * @param $userId
     * @param $examination
     * @param $eventType
     * @param $eyeIds
     * @param $refractionType
     * @param $opNoteEventId
     *
     * @return \Event
     *
     * @throws \CDbException
     * @throws \Exception
     */
    public function save($episodeId, $userId, $examination, $eventType, $eyeIds, $refractionType, $opNoteEventId = null)
    {
        $episode = \Episode::model()->findByPk($episodeId);
        $opNoteEvent = $opNoteEventId ? \Event::model()->findByPk($opNoteEventId) : \Yii::app()->moduleAPI->get('OphTrOperationnote')->getLatestEvent($episode->patient);
        if (!$opNoteEvent) {
            throw new \Exception('Examination failed: no operation note event was found to copy institution and site from.');
        }

        $examination['institution_id'] = $opNoteEvent->institution_id;
        $examination['site_id'] = $opNoteEvent->site_id;

        $examinationEvent = $this->createExamination($episodeId, $userId, $examination, $eventType);
        $this->examinationEye($examination['patient']['eyes'], $eyeIds);

        if ($examinationEvent->save(true, null, true)) {
            $examinationEvent->refresh();
            $refraction = new Element_OphCiExamination_Refraction();
            $refraction->event_id = $examinationEvent->id;
            $refraction->created_user_id = $refraction->last_modified_user_id = $userId;
            $refraction->save();

            $iop = $this->createIop($userId, $examinationEvent);

            $complications = $this->createComplications($userId, $examinationEvent);

            $this->createComments($userId, $examination, $examinationEvent);

            $this->createMessage($episodeId, $userId, $examination, $examinationEvent, $opNoteEventId);

            if (count($examination['patient']['eyes'][0]['reading'][0]['visual_acuity'])) {
                $measure = $examination['patient']['eyes'][0]['reading'][0]['visual_acuity'][0]['measure'];
                $unit = OphCiExamination_VisualAcuityUnit::model()->find('name = :measure', array('measure' => $measure));
                $visualAcuity = $this->createVisualAcuity($userId, $examinationEvent);
            }

            if (count($examination['patient']['eyes'][0]['reading'][0]['near_visual_acuity'])) {
                $nearMeasure = $examination['patient']['eyes'][0]['reading'][0]['near_visual_acuity'][0]['measure'];
                $nearUnit = OphCiExamination_VisualAcuityUnit::model()->find('name = :measure', array('measure' => $nearMeasure));
                $nearVisualAcuity = $this->createVisualAcuity($userId, $examinationEvent, true);
            }

            foreach ($examination['patient']['eyes'] as $eye) {
                $eyeLabel = strtolower($eye['label']);
                $this->addRefractionReading($userId, $refraction, $refractionType, $eye['reading'][0]['refraction'], $eyeLabel);

                foreach ($eye['reading'][0]['visual_acuity'] as $vaData) {
                    $this->addVisualAcuityReading($userId, $visualAcuity, $unit, $vaData, $eyeLabel);
                }

                foreach ($eye['reading'][0]['near_visual_acuity'] as $vaData) {
                    $this->addVisualAcuityReading($userId, $nearVisualAcuity, $nearUnit, $vaData, $eyeLabel, true);
                }

                $this->addIop($eyeIds, $eye, $iop, $eyeLabel);
                $this->addComplication($userId, $eyeIds, $opNoteEventId, $eye, $complications, $eyeLabel);
            }

            $iop->left_comments = '';
            $iop->right_comments = '';
            $iop->save(false);

            $refraction->eye_id = $this->examinationEyeId;

            if (!$refraction->save(true, null, true)) {
                throw new \CDbException('Refraction failed: ' . print_r($refraction->getErrors(), true));
            }

            return $examinationEvent;
        } else {
            throw new \CDbException('Examination failed: ' . print_r($examinationEvent->getErrors(), true));
        }
    }

    /**
     * @return mixed|null
     *
     * @throws \Exception
     */
    public function getPortalUser()
    {
        $user = new \User();
        $portalUser = $user->portalUser();
        if (!$portalUser) {
            throw new \Exception('No User found for import');
        }

        return $portalUser->id;
    }

    /**
     * @return array
     */
    public function getEyes()
    {
        $eyes = \Eye::model()->findAll();
        $eyeIds = array();
        foreach ($eyes as $eye) {
            $eyeIds[strtolower($eye->name)] = $eye->id;
        }

        return $eyeIds;
    }

    /**
     * @param $userId
     * @param $examinationEvent
     * @param $near
     *
     * @return Element_OphCiExamination_VisualAcuity
     *
     * @throws \CDbException
     * @throws \Exception
     */
    protected function createVisualAcuity($userId, $examinationEvent, $near = false)
    {
        //Create visual acuity
        $visualAcuity = new Element_OphCiExamination_VisualAcuity();
        if ($near) {
            $visualAcuity = new Element_OphCiExamination_NearVisualAcuity();
        }
        $visualAcuity->event_id = $examinationEvent->id;
        $visualAcuity->created_user_id = $visualAcuity->last_modified_user_id = $userId;
        $visualAcuity->eye_id = $this->examinationEyeId;

        if (!$visualAcuity->save(false, null, true)) {
            throw new \CDbException('Visual Acuity failed: ' . print_r($visualAcuity->getErrors(), true));
        }
        $visualAcuity->refresh();

        return $visualAcuity;
    }

    /**
     * @param $userId
     * @param $visualAcuity
     * @param $unit
     * @param $vaData
     * @param $eyeLabel
     * @param $near
     *
     * @throws \CDbException
     * @throws \Exception
     */
    protected function addVisualAcuityReading($userId, $visualAcuity, $unit, $vaData, $eyeLabel, $near = false)
    {
        $vaReading = new OphCiExamination_VisualAcuity_Reading();
        if ($near) {
            $vaReading = new OphCiExamination_NearVisualAcuity_Reading();
        }
        $vaReading->element_id = $visualAcuity->id;
        $vaReading->unit_id = $unit->id;
        $baseValue = OphCiExamination_VisualAcuityUnitValue::model()->getBaseValue($unit->id, $vaData['reading']);
        $vaReading->value = $baseValue;
        $vaReading->method_id = OphCiExamination_VisualAcuity_Method::model()
            ->find('LOWER(name) = :name', ['name' => strtolower($vaData['method'])])
            ->id;
        if ($eyeLabel === 'left') {
            $vaReading->side = OphCiExamination_VisualAcuity_Reading::LEFT;
        } else {
            $vaReading->side = OphCiExamination_VisualAcuity_Reading::RIGHT;
        }
        $vaReading->created_user_id = $vaReading->last_modified_user_id = $userId;
        if (!$vaReading->save(true, null, true)) {
            throw new \CDbException('Visual Acuity Reading failed: ' . print_r($vaReading->getErrors(), true));
        }
    }

    protected function addRefractionReading($userId, $refractionElement, $refractionType, $refractionData, $eyeLabel)
    {
        $refractionReading = new OphCiExamination_Refraction_Reading();
        $refractionReading->created_user_id = $refractionReading->last_modified_user_id = $userId;
        if ($eyeLabel === 'left') {
            $refractionReading->eye_id = SidedData::LEFT;
        } else {
            $refractionReading->eye_id = SidedData::RIGHT;
        }

        $refractionReading->sphere = $refractionData['sphere'];
        $refractionReading->cylinder = $refractionData['cylinder'];
        $refractionReading->axis = $refractionData['axis'];
        $refractionReading->type_id = $refractionType->id;

        if ($refractionElement->{"{$eyeLabel}_readings"}) {
            $readings = $refractionElement->{"{$eyeLabel}_readings"};
            $readings[] = $refractionReading;
            $refractionElement->{"{$eyeLabel}_readings"} = $readings;
        } else {
            $refractionElement->{"{$eyeLabel}_readings"} = [$refractionReading];
        }
    }

    /**
     * @param $userId
     * @param $examination
     * @param $examinationEvent
     * @throws \CDbException
     * @throws \Exception
     */
    protected function createComments($userId, $examination, $examinationEvent)
    {
        $comments = new Element_OphCiExamination_OptomComments();
        $comments->event_id = $examinationEvent->id;
        $comments->created_user_id = $comments->last_modified_user_id = $userId;
        $comments->ready_for_second_eye = $examination['patient']['ready_for_second_eye'];
        $comments->comment = $examination['patient']['comments'];
        if (!$comments->save(true, null, true)) {
            throw new \CDbException('Complications failed: ' . print_r($comments->getErrors(), true));
        }
    }

    /**
     * @param $userId
     * @param $examinationEvent
     * @return Element_OphCiExamination_PostOpComplications
     * @throws \CDbException
     * @throws \Exception
     */
    protected function createComplications($userId, $examinationEvent)
    {
        $complications = new Element_OphCiExamination_PostOpComplications();
        $complications->event_id = $examinationEvent->id;
        $complications->created_user_id = $complications->last_modified_user_id = $userId;
        $complications->eye_id = $this->examinationEyeId;
        if (!$complications->save(true, null, true)) {
            throw new \CDbException('Complications failed: ' . print_r($complications->getErrors(), true));
        }
        $complications->refresh();

        return $complications;
    }

    /**
     * @param $episodeId
     * @param $userId
     * @param $examination
     * @param $examinationEvent
     * @throws \CDbException
     */
    protected function createMessage($episodeId, $userId, $examination, $examinationEvent, $opNoteEventId = null)
    {
        if (isset(\Yii::app()->modules['OphCoMessaging'])) {
            $episode = \Episode::model()->findByPk($episodeId);
            //$recipient = \User::model()->findByPk($episode->firm->consultant_id);
            $recipient = null;
            if ($opNoteEventId !== null) {
                $surgeon = \Element_OphTrOperationnote_Surgeon::model()->findByAttributes(array('event_id' => $opNoteEventId ));
                $recipient = $surgeon->surgeon;
            }

            if ($recipient) {
                $sender = \User::model()->findByPk($userId);
                $type = OphCoMessaging_Message_MessageType::model()->findByAttributes(array('name' => 'General'));
                if ($examination['patient']['ready_for_second_eye'] === false) {
                    $ready = 'No';
                } elseif ($examination['patient']['ready_for_second_eye'] === true) {
                    $ready = 'Yes';
                } else {
                    $ready = 'Not Applicable';
                }

                $messageCreator = new MessageCreator($episode, $sender, $recipient, $type, $examination['institution_id'], $examination['site_id']);
                $messageCreator->setMessageTemplate('application.modules.OphCoMessaging.views.templates.optom');
                $messageCreator->setMessageData(array(
                    'optom' => $examination['op_tom']['name'] . ' (' . $examination['op_tom']['goc_number'] . ')',
                    'optom_address' => $examination['op_tom']['address'],
                    'ready' => $ready,
                    'comments' => ($examination['patient']['comments']) ? $examination['patient']['comments'] : 'No Comments',
                    'patient' => $episode->patient,
                ));
                $message = $messageCreator->save('', array('event' => $examinationEvent->id));
                $emailSetting = \SettingInstallation::model()->find('`key` = "optom_comment_alert"');
                if ($emailSetting && $emailSetting->value) {
                    $recipients = explode(',', $emailSetting->value);
                    $messageCreator->emailAlert($recipients, 'New Optom Comment', $message->message_text);
                }
            }
        }
    }

    /**
     * @param $episodeId
     * @param $userId
     * @param $examination
     * @param $eventType
     * @return \Event
     */
    protected function createExamination($episodeId, $userId, $examination, $eventType)
    {
        //Create main examination event
        $examinationEvent = new \Event("automatic");
        $examinationEvent->institution_id = $examination['institution_id'];
        $examinationEvent->site_id = $examination['site_id'];
        $examinationEvent->episode_id = $episodeId;
        $examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = $userId;
        $examinationEvent->event_date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $examination['examination_date'])->format('Y-m-d');
        $examinationEvent->event_type_id = $eventType['id'];
        $examinationEvent->is_automated = 1;
        $examinationEvent->automated_source = json_encode($examination['op_tom']);

        return $examinationEvent;
    }

    /**
     * @param $userId
     * @param $eyeIds
     * @param $opNoteEventId
     * @param $eye
     * @param $complications
     * @param $eyeLabel
     * @throws \Exception
     */
    protected function addComplication($userId, $eyeIds, $opNoteEventId, $eye, $complications, $eyeLabel)
    {
        if (array_key_exists('complications', $eye)) {
            if (count($eye['complications'])) {
                foreach ($eye['complications'] as $complicationArray) {
                    $eyeComplication = new OphCiExamination_Et_PostOpComplications();
                    $eyeComplication->element_id = $complications->id;
                    $complicationToAdd = OphCiExamination_PostOpComplications::model()->find('LOWER(name) = "' . strtolower($complicationArray['complication']) . '"');
                    $eyeComplication->complication_id = $complicationToAdd->id;
                    $eyeComplication->operation_note_id = $opNoteEventId;
                    $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                    $eyeComplication->created_user_id = $eyeComplication->last_modified_user_id = $userId;
                    $eyeComplication->save(true, null, true);
                }
            } else {
                $eyeComplication = new OphCiExamination_Et_PostOpComplications();
                $eyeComplication->element_id = $complications->id;
                $complicationToAdd = OphCiExamination_PostOpComplications::model()->find('name = "none"');
                $eyeComplication->complication_id = $complicationToAdd->id;
                $eyeComplication->operation_note_id = $opNoteEventId;
                $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                $eyeComplication->created_user_id = $eyeComplication->last_modified_user_id = $userId;
                $eyeComplication->save(true, null, true);
            }
        }
    }

    /**
     * @param $eyeIds
     * @param $eye
     * @param $iop
     * @param $eyeLabel
     * @throws \CDbException
     * @throws \Exception
     */
    protected function addIop($eyeIds, $eye, $iop, $eyeLabel)
    {
        $iopReading = $eye['reading'][0]['iop'];
        $iopValue = new OphCiExamination_IntraocularPressure_Value('exam_creator');
        $iopValue->element_id = $iop->id;
        $iopValue->eye_id = $eyeIds[$eyeLabel];
        $iopReadingValue = OphCiExamination_IntraocularPressure_Reading::model()
            ->find('value = ?', [$iopReading['mm_hg']]);
        $instrument = OphCiExamination_Instrument::model()
            ->find('LOWER(name) = ?', [strtolower($iopReading['instrument'])]);

        if ($instrument['scale_id']) {
            $iopValue->qualitative_reading_id = $instrument['scale_id'];
        }

        $iopValue->reading_id = $iopReadingValue['id'];
        $iopValue->instrument_id = $instrument['id'];

        if (!$iopValue->save(true, null, true)) {
            throw new \CDbException('iop value failed: ' . print_r($iop->getErrors(), true));
        }
    }

    /**
     * @param $userId
     * @param $examinationEvent
     * @return Element_OphCiExamination_IntraocularPressure
     * @throws \CDbException
     * @throws \Exception
     */
    protected function createIop($userId, $examinationEvent)
    {
        $iop = new Element_OphCiExamination_IntraocularPressure();
        $iop->event_id = $examinationEvent->id;
        $iop->created_user_id = $iop->last_modified_user_id = $userId;
        $iop->eye_id = $this->examinationEyeId;
        $iop->left_comments = 'Portal Add';
        $iop->right_comments = 'Portal Add';

        if (!$iop->save(true, null, true)) {
            throw new \CDbException('iop failed: ' . print_r($iop->getErrors(), true));
        }
        $iop->refresh();

        return $iop;
    }

    protected function examinationEye(array $eyes, array $eyeIds)
    {
        if (count($eyes) === 2) {
            $this->examinationEyeId = $eyeIds['both'];
        } else {
            $this->examinationEyeId = $eyeIds[strtolower($eyes[0]['label'])];
        }
    }
}
