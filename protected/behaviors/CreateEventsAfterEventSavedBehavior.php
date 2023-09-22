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
    public $determine_eye_from_element;

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
            $prescription_checkbox = $this->getPrescriptionCheckboxValue($suffix);
            $set_id = \Yii::app()->request->getParam("auto_generate_prescription_after_{$suffix}_set_id");

            if ($prescription_checkbox && !$set_id) {
                $this->addValidationError($event, "Generate prescription",
                    "Please select a standard set.",
                    "auto_generate_prescription_after_" . $suffix . "_set_id");
            }

            $save_as_draft_data = $this->getSaveAsDraftData();

            if (!is_null($save_as_draft_data)) {
                if ($this->validateCorrespondenceSignatureData($save_as_draft_data, $suffix)) {
                    $this->addMissingPinOrDraftUncheckedValidationError(
                        EventAutoGenerateEsign::CORRESPONDENCE, $event);
                }

                if ($this->validatePrescriptionSignatureData($save_as_draft_data, $suffix)) {
                    $this->addMissingPinOrDraftUncheckedValidationError(
                        EventAutoGenerateEsign::PRESCRIPTION, $event);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    private function getSaveAsDraftData(): mixed
    {
        return \Yii::app()->request->getParam(EventAutoGenerateCheckboxesWidget::SAVE_AS_DRAFT_INPUT_NAME);
    }

    private function getPrescriptionCheckboxValue($suffix)
    {
        $prescription_checkbox_name = "auto_generate_prescription_after_{$suffix}";
        return \Yii::app()->request->getParam($prescription_checkbox_name);
    }

    private function isMissingSignature($signature_class)
    {
        $missing_pin = false;

        $signature = $this->getSignature($signature_class);

        if (is_null($signature)) {
            $missing_pin = true;
        } elseif (!$signature->isSigned()) {
            $missing_pin = true;
        }

        return $missing_pin;
    }


    /**
     * Fronted errors are added like
     * $element->addError() and this method populates frontEndErrors array
     * (BaseEventTypeElement :: addError)
     * Now, here in beforeAction we cannot access the elements (open_elements not populated)
     * so somehow we need to scrollToElement and highlight the input field
     */
    private function addValidationError($event, $error_title, $error_text, $field_id)
    {
        $event->sender->external_errors[$error_title][] =
            '<a class="errorlink" onclick="scrollToElement($(\'#' . $field_id . '\'))">' . $error_text . '</a>';

        \Yii::app()->clientScript->registerScript($error_title . $field_id, "
                    if(typeof element_$field_id  === 'undefined') {
                        let element_$field_id = document.getElementById('$field_id');
                        if (element_$field_id) {
                            element_$field_id.classList.add('error');
                         }
                    }
                    ", \CClientScript::POS_END);
    }

    private function getSignature($signature_class)
    {
        $signature_data = \Yii::app()->request->getParam(EventAutoGenerateCheckboxesWidget::SIGNATURE_INPUT_NAME);

        $signature = null;

        if (isset($signature_data['proof']) && $signature_data['proof'] !== '') {
            $signature = new $signature_class();

            $signature->attributes = $signature_data;

            $signature->proof = $signature_data['proof'];
            $signature->setDataFromProof();
        }

        return $signature;
    }


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
    public function createPrescriptionEvent(int $set_id): array
    {
        $set = MedicationSet::model()->findByPk($set_id);
        $success = false;

        if ($set) {
            $signature = $this->getSignature(OphDrPrescription_Signature::class);

            $save_as_draft = $this->getSaveAsDraftData()[EventAutoGenerateEsign::PRESCRIPTION] ?? 0;

            $prescription_creator = new PrescriptionCreator($this->owner->event->episode, $signature, $save_as_draft);
            $prescription_creator->patient = $this->owner->patient;

            $element = $this->determine_eye_from_element::model()->findByAttributes(['event_id' => $this->owner->event->id]);

            if ($element) {
                $prescription_creator->addMedicationSet($set->id, $element->eye_id);
                $prescription_creator->elements['Element_OphDrPrescription_Details']->draft = !Yii::app()->user->checkAccess('OprnCreatePrescription');
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
    public function createCorrespondenceEvent($macro_name = null): array
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
                    'params' => array(':name' => "$name%")
                ));

                $letter_type = \LetterType::model()->find($criteria);
                $letter_type_id = $letter_type->id ?? null;

                $signature = $this->getSignature(OphCoCorrespondence_Signature::class);
                $save_as_draft = $this->getSaveAsDraftData()[EventAutoGenerateEsign::CORRESPONDENCE] ?? 0;

                $correspondence_creator = new CorrespondenceCreator($this->owner->event->episode, $macro,
                    $letter_type_id, $signature, $save_as_draft);
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

    /**
     * @param $save_as_draft_data
     * @param $event_type_string
     * @return bool
     */
    private function validateCorrespondenceSignatureData($save_as_draft_data, $event_type_string): bool
    {
        $correspondence_esign = new Element_OphCoCorrespondence_Esign();
        $pin_is_required_for_correspondence = $correspondence_esign->isPinRequired();

        $missing_pin_or_save_as_draft = false;


        if (isset($save_as_draft_data[EventAutoGenerateEsign::CORRESPONDENCE]) &&
            $save_as_draft_data[EventAutoGenerateEsign::CORRESPONDENCE] !== "1") {
            foreach (['auto_generate_gp_letter_after_', 'auto_generate_optom_letter_after_']
                     as $auto_generate_field) {
                $create_correspondence_checkbox = \Yii::app()->request->getParam($auto_generate_field . $event_type_string);

                if ($create_correspondence_checkbox && $pin_is_required_for_correspondence) {
                    if ($this->isMissingSignature(OphDrPrescription_Signature::class)) {
                        // check if missing draft
                        $missing_pin_or_save_as_draft = true;
                    }
                }
            }
        }

        return $missing_pin_or_save_as_draft;
    }

    /**
     * @param $save_as_draft_data
     * @param $event_type_string
     * @return bool
     */
    private function validatePrescriptionSignatureData($save_as_draft_data, $event_type_string): bool
    {
        $prescription_checkbox = $this->getPrescriptionCheckboxValue($event_type_string);
        $prescription_esign = new Element_OphDrPrescription_Esign();
        $pin_is_required_for_prescription = $prescription_esign->isPinRequired();
        $missing_pin_or_save_as_draft = false;

        if (isset($save_as_draft_data[EventAutoGenerateEsign::PRESCRIPTION]) &&
            $save_as_draft_data[EventAutoGenerateEsign::PRESCRIPTION] !== "1") {
            if ($prescription_checkbox && $pin_is_required_for_prescription) {
                if ($this->isMissingSignature(OphCoCorrespondence_Signature::class)) {
                    $missing_pin_or_save_as_draft = true;
                }
            }
        }

        return $missing_pin_or_save_as_draft;
    }

    /**
     * @param $event_type
     * @param CEvent $event
     */
    private function addMissingPinOrDraftUncheckedValidationError($event_type, CEvent $event): void
    {
        $this->addValidationError($event, ucfirst($event_type),
            "At least one signature must be provided to finalise this event or save draft must be checked.",
            "pin_EventAutoGenerateSignature");
    }
}
