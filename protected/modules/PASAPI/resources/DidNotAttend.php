<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\resources;

use PatientIdentifierType;

/**
 * Class DidNotAttend.
 *
 * @property string $HospitalNumber
 * @property date $Date
 * @property string $Comments
 */
class DidNotAttend extends BaseResource
{
    const DEFAULT_AUTOGEN_MESSAGE = "Entry automatically created by the PAS.";
    protected static $resource_type = 'DidNotAttend';
    public $HospitalNumber;
    public $IdentifierType;
    public $Date;
    public $Comments;
    private $patient;
    private $episode;

    /**
     * @return bool
     */
    public function shouldValidateRequired()
    {
        // DidNotAttend will always be new record
        return true;
    }

    public function validate()
    {
        if (!$this->patient) {
            $this->addError('Patient not found');
        } elseif (!$this->episode) {
            // Only need to check if patient exists as if it doesn't
            // the episode definitely won't exist.
            $this->addError('Episode could not be created');
        }
        return parent::validate();
    }

    public function isEnabled()
    {
        return \SettingMetadata::model()->checkSetting('DNA_autogen_enabled', 'on');
    }

    public function getSource()
    {
        $DNA_autogen_message = \SettingMetadata::model()->getSetting('DNA_autogen_message');
        return $DNA_autogen_message ?: self::DEFAULT_AUTOGEN_MESSAGE;
    }

    private function getPatient()
    {
        return \PatientIdentifierHelper::getPatientByPatientIdentifier($this->HospitalNumber, $this->IdentifierType);
    }

    private function getEpisode($firm = null)
    {
        $this->patient = $this->getPatient();
        if ($this->patient) {
            if ($firm) {
                return $this->patient->getOrCreateEpisodeForFirm($firm);
            } else {
                $firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
                return $this->patient->getOrCreateEpisodeForFirm($firm);
            }
        } else {
            return null;
        }
    }

    public function save()
    {
        $transaction = $this->startTransaction();

        try {
            $this->episode = $this->getEpisode();
            if (!$this->validate()) {
                return;
            }

            $DNA_type_id = \Yii::app()->db->createCommand('SELECT id FROM event_type WHERE name = "Did Not Attend"')->queryScalar();
            if (!$DNA_type_id) {
                $this->addError("Could not find 'Did not attend' event type");
                return;
            }

            $patient_identifier_type = PatientIdentifierType::model()->findByAttributes([
                'unique_row_string' => $this->IdentifierType
            ]);

            $did_not_attend_creator = new \OEModule\OphCiDidNotAttend\components\DidNotAttendCreator($this->episode, $DNA_type_id);
            $did_not_attend_creator->event->institution_id = $patient_identifier_type->institution_id;
            $did_not_attend_creator->setSource($this->getSource());
            $did_not_attend_creator->setDate($this->Date);
            $did_not_attend_creator->setComments($this->Comments);
            if (!$id = $did_not_attend_creator->save()) {
                $transaction->rollback();
                foreach ($did_not_attend_creator->event->getErrors() as $error) {
                    $this->addError($error);
                }
                return;
            }
            $transaction->commit();
            return $id;
        } catch (\Exception $e) {
            $transaction->rollback();
            $this->addError($e);
        }
    }
}
