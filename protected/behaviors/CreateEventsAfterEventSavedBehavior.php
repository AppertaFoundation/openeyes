<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class CreateEventsAfterEventSavedBehavior extends CBehavior
{
    public function events()
    {
        return array_merge(parent::events(), [
            'onBeforeAction' => 'beforeAction',
        ]);
    }

    public function beforeAction(\CEvent $event)
    {
        $action = isset($event->params['action']) ? $event->params['action'] : null;
        $is_post = \Yii::app()->request->isPostRequest;

        if ($action && ($action->id === 'create') && $is_post) {
            $suffix = strtolower($this->owner->event->eventType->class_name);
            $prescription_checkbox_name = "auto_generate_prescription_after_{$suffix}";
            $prescription_checkbox = \Yii::app()->request->getParam($prescription_checkbox_name);
            $set_id = \Yii::app()->request->getParam("auto_generate_prescription_after_{$suffix}_set_id");

            if ($prescription_checkbox && !$set_id) {
                /**
                 * Fronted errors are added like
                 * $element->addError() and this method populates frontEndErrors array
                 * (BaseEventTypeElement :: addError)
                 * Now, here in beforeAction we cannot access the elements (open_elements not populated)
                 * so somehow we need to scrollToElement and highlight the input field
                 */
                $event->sender->external_errors["Generate prescription"][] =
                    '<a class="errorlink" onclick="scrollToElement($(\'#auto_generate_prescription_after_' . $suffix  . '_set_id\'))">Please select a standard set</a>';

                \Yii::app()->clientScript->registerScript('standard_set_error', "
                    const element = document.getElementById('auto_generate_prescription_after_{$suffix}_set_id');
                    if (element) {
                        element.classList.add('error');
                    }
                    ", \CClientScript::POS_END);
            }
        }
    }

    public $determine_eye_from_element;

    /**
     * Creates prescription event if required
     */
    public function checkAndCreatePrescriptionEvent()
    {
        $create_prescription = \Yii::app()->request->getParam('auto_generate_prescription_after_' . strtolower($this->owner->event->eventType->class_name));
        $set_id = \Yii::app()->request->getParam('auto_generate_prescription_after_' . strtolower($this->owner->event->eventType->class_name) . "_set_id");

        if ($create_prescription) {
            $transaction = Yii::app()->db->beginTransaction();

            $result = $this->createPrescriptionEvent($set_id);
            if ($result['success'] === true) {
                $transaction->commit();
            } else {
                $transaction->rollback();
                $this->logEventCreationFail($result['errors'], 'OphDrPrescription', 'Element_OphDrPrescription_Details');
            }
        }
    }

    /**
     * Creates correspondence if required
     */
    public function checkAndCreateCorrespondenceEvent()
    {
        $event_type_string = strtolower($this->owner->event->eventType->class_name);
        $create_correspondence = \Yii::app()->request->getParam('auto_generate_gp_letter_after_' . $event_type_string);

        if ($create_correspondence) {
            if ($this->owner->patient->gp_id && $this->owner->patient->practice_id) {
                $macro_name = \SettingMetadata::model()->getSetting('default_letter_' . $event_type_string);
                $transaction = Yii::app()->db->beginTransaction();

                $result = $this->createCorrespondenceEvent($macro_name);
                if ($result['success'] === true) {
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                    $this->logEventCreationFail($result['errors'], 'OphCoCorrespondence', 'ElementLetter');
                }
            } else {
                \Yii::app()->user->setFlash('issue', "GP letter could not be created because the patient has no GP");
                $this->logEventCreationFail(['Error Message' => 'GP letter could not be created because the patient has no GP', 'gp_id' => $this->owner->patient->gp_id, 'practice_id' => $this->owner->patient->practice_id], 'OphCoCorrespondence', 'Patient');
            }
        }
    }

    /**
     * Creates Optom correspondence if required
     */
    public function checkAndCreateOptomCorrespondenceEvent()
    {
        $event_type_string = strtolower($this->owner->event->eventType->class_name);
        $create_optom_correspondence = \Yii::app()->request->getParam('auto_generate_optom_letter_after_' . $event_type_string);

        if ($create_optom_correspondence) {
            $macro_name = \SettingMetadata::model()->getSetting('default_optom_letter_' . $event_type_string);
            $transaction = Yii::app()->db->beginTransaction();

            $result = $this->createCorrespondenceEvent($macro_name);
            if ($result['success'] === true) {
                $transaction->commit();
            } else {
                $transaction->rollback();
                $this->logEventCreationFail($result['errors'], 'OphCoCorrespondence', 'ElementLetter');
            }
        }
    }

    /**
     * Create prescription event
     * @param int $set_id
     * @return array
     */
    public function createPrescriptionEvent(int $set_id) : array
    {
        $set = MedicationSet::model()->findByPk($set_id);
        $success = false;

        if ($set) {
            $prescription_creator = new PrescriptionCreator($this->owner->event->episode);
            $prescription_creator->patient = $this->owner->patient;

            $element = $this->determine_eye_from_element::model()->findByAttributes(['event_id' => $this->owner->event->id]);

            if ($element) {
                $prescription_creator->addMedicationSet($set->id, $element->eye_id);

                $prescription_creator->save();

                $success = !$prescription_creator->hasErrors();
                $errors = $prescription_creator->getErrors();

                if (!empty($errors)) {
                    $msg = "Automatic Prescription creation is not possible - please create prescription manually. The selected Medication Set has missing mandatory prescribing values. Please ask your system administrator to update the Medication Set to enable Automatic Prescriptions.";
                    \Yii::app()->user->setFlash('issue.prescription', $msg);
                }
            } else {
                $msg = "Unable to create default Prescription because: Can't determinate side based on Treatement element";
                $errors[] = [$msg];
                $errors[] = ['set_id' => $set_id]; // these are only going to the logs and audit, not displayed to the user

                \Yii::app()->user->setFlash('issue.prescription', $msg);
            }
        } else {
            $msg = "Unable to create default Prescription because: No drug set was found";
            $errors[] = [$msg];
            $errors[] = ['set_id' => $set_id]; // these are only going to the logs and audit, not displayed to the user

            \Yii::app()->user->setFlash('issue.prescription', $msg);
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    /**
     * Create Correspondence event
     *
     * @param null $macro_name
     * @return array
     */
    public function createCorrespondenceEvent($macro_name = null) : array
    {
        $event_type_string = strtolower($this->owner->event->eventType->class_name);

        $correspondence_api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        if (empty($macro_name)) {
            $macro_name = \SettingMetadata::model()->getSetting("default_letter" . $event_type_string);
        }
        $macro = $correspondence_api->getDefaultMacroByEpisodeStatus($this->owner->event->episode, $firm, \Yii::app()->session['selected_site_id'], $macro_name);

        $success = false;

        if ($macro) {
            //check if macro has recipient
            if ($macro->recipient_id) {
                $name = addcslashes($this->owner->event->episode->status->name, '%_'); // escape LIKE's special characters
                $criteria = new CDbCriteria(array(
                    'condition' => "name LIKE :name",
                    'params'    => array(':name' => "$name%")
                ));

                $letter_type = \LetterType::model()->find($criteria);
                $letter_type_id = $letter_type->id ?? null;

                $correspondence_creator = new CorrespondenceCreator($this->owner->event->episode, $macro, $letter_type_id);
                $correspondence_creator->save();

                $success = !$correspondence_creator->hasErrors();
                $errors = $correspondence_creator->getErrors();
            } else {
                $msg = "Unable to create default Letter because: macro '{$macro_name}' does not have any target.";
                $errors[] = [$msg];

                \Yii::app()->user->setFlash('issue.correspondence', $msg);
            }
        } else {
            $msg = "Unable to create default Letter because: No macro named '{$macro_name}' was found";
            $errors[] = [$msg];

            \Yii::app()->user->setFlash('issue.correspondence', $msg);
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    protected function logEventCreationFail($errors, $module, $model)
    {
        $log = print_r($errors, true);
        \Audit::add('event', 'create-failed', 'Automatic Event creation Failed<pre>' . $log . '</pre>', $log, [
            'module' => $module,
            'episode_id' => $this->owner->event->episode->id,
            'patient_id' => $this->owner->patient->id,
            'model' => $model
        ]);
    }
}
