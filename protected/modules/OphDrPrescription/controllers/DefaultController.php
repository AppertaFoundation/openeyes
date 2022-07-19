<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DefaultController extends BaseEventTypeController
{
    const FP10_PRINT_MODE = 2;
    const WP10_PRINT_MODE = 3;
    const NORMAL_PRINT_MODE = 1;

    protected static $action_types = array(
        'drugList' => self::ACTION_TYPE_FORM,
        'repeatForm' => self::ACTION_TYPE_FORM,
        'routeOptions' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
        'printCopy'    => self::ACTION_TYPE_PRINT,
        'finalize' => self::ACTION_TYPE_FORM,
        'finalizeWithSignatures' => self::ACTION_TYPE_FORM,
        'getSignatureByPin' => self::ACTION_TYPE_FORM,
        'getSignatureByUsernameAndPin' => self::ACTION_TYPE_FORM
    );

    private function userIsAdmin()
    {
        $user = Yii::app()->session['user'];

        if (Yii::app()->authManager->checkAccess('admin', $user->id)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        return [
            'getSignatureByPin' => [
                'class' => GetSignatureByPinAction::class
            ],
            'getSignatureByUsernameAndPin' => [
                'class' => GetSignatureByUsernameAndPinAction::class
            ]
        ];
    }

    public function actionView($id)
    {
        $model = Element_OphDrPrescription_Details::model()
            ->findBySql('SELECT * FROM et_ophdrprescription_details WHERE event_id = :id', [':id' => $id]);

        $this->showAllergyWarning();

        $this->editable = $model->isEditableByMedication();
        if ($this->editable == true) {
            $this->editable = $this->userIsAdmin() || $model->draft
            || (SettingMetadata::model()->findByAttributes(array('key' => 'enable_prescriptions_edit'))->getSettingName() === 'On');
        }

        if ($this->event->delete_pending) {
            Yii::app()->user->setFlash('patient.delete_pending', 'This event is pending deletion and has been locked.');
        }

        if ($model->edit_reason_id) {
            $this->showReasonForEdit($model->edit_reason_id, $model->edit_reason_other);
        }

        if (!$model->isEditableByMedication()) {
            Yii::app()->user->setFlash('alert.meds_management', 'This prescription was created from Medication Management in an Examination event. To make changes or remove, please edit the original Examination');
        }
        if ($model->draft) {
            Yii::app()->user->setFlash('alert.draft', 'This prescription is a draft and can still be edited');
        }

        return parent::actionView($id);
    }

    /**
     * Defines JS data structure for common drug lookup in prescription.
     */
    protected function setCommonDrugMetadata()
    {
        $this->jsVars['common_drug_metadata'] = array();
        foreach (Element_OphDrPrescription_Details::model()->commonDrugs() as $medication) {
            $this->jsVars['common_drug_metadata'][$medication->id] = array(
                    'medication_set_id' => array_map(function ($e) {
                        return $e->id;
                    }, $medication->getTypes()),
                    'preservative_free' => (int)$medication->isPreservativeFree(),
            );
        }
    }

    /**
     * @return bool
     */
    protected function initEdit($action)
    {
        $method_name = 'check' . ucfirst($action) . 'Access';
        if (!$this->$method_name()) {
            return false;
        }

        $assetManager = Yii::app()->getAssetManager();
        $baseAssetsPath = Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath, true);
        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath, true) . '/OpenEyes.UI.InputFieldValidation.js', CClientScript::POS_END);

        $this->showAllergyWarning();

        if (isset($_POST['saveprintform'])) {
            // Save and print FP10 clicked, stash print form flag
            $form_format = strtolower(SettingMetadata::model()->getSetting('prescription_form_format'));
            Yii::app()->session["print_prescription_$form_format"] = true;
        } elseif (isset($_POST['saveprint'])) {
            // Save and print clicked, stash print flag
            Yii::app()->session['print_prescription'] = true;
        }

        if ($api = \Yii::app()->moduleAPI->get("OphInCocoa")) {
            /** @var \OEModule\OphInCocoa\components\OphInCocoa_API $api */
            $api->displayPrescriptionWarning(\Yii::app()->session['selected_site_id']);
        }

        return true;
    }

    public function printActions()
    {
        return array('print', 'printFpTen', 'markPrinted', 'doPrint');
    }

    /**
     * Initialisation function for specific patient id
     * Used for ajax actions.
     *
     * @param $patient_id
     *
     * @throws CHttpException
     */
    protected function initForPatient($patient_id)
    {
        if (!$this->patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(403, 'Invalid patient_id.');
        }

        if (!$this->episode = $this->getEpisode($this->firm, $patient_id)) {
            throw new CHttpException(403, 'Invalid request for this patient.');
        }
    }

    /**
     * Some additional initialisation for create.
     * @throws CHttpException
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();
        $this->initEdit('create');
    }

    /**
     * Some additional initialisation for create.
     * @throws CHttpException
     */
    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit('edit');
    }

    /**
     * Some additional initialisation for view.
     */
    protected function initActionView()
    {
        parent::initActionView();

        // Clear any stale warning
        Yii::app()->user->getFlash('warning.prescription_allergy');

        // set required js variables
        $cs = Yii::app()->getClientScript();
        $cs->registerScript(
            'scr_prescription_view',
            "prescription_print_url = '"
            . Yii::app()->createUrl('/OphDrPrescription/default/print/' . $this->event->id)
            . "';\n",
            CClientScript::POS_READY
        );

        // Get prescription details element
        $element = Element_OphDrPrescription_Details::model()->findByAttributes(array('event_id' => $this->event->id));

        foreach ($element->items as $item) {
            if ($this->patient->hasDrugAllergy($item->medication_id)) {
                $this->showAllergyWarning();
                break;
            }
        }
    }

    /**
     * marks the prescription as printed. So this is the 3rd place this can happen, but not sure
     * if this is every called.
     *
     * @param int $id
     *
     * @throws Exception
     */
    public function printInit($id)
    {
        parent::printInit($id);
        $this->site = $this->event->site;
        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($id))) {
            throw new Exception('Prescription not found: ' . $id);
        }
        $prescription->printed = 1;
        if (!$prescription->update(['printed'])) {
            throw new Exception('Unable to save prescription: ' . print_r($prescription->getErrors(), true));
        }
        $this->event->info = $prescription->infotext;
        if (!$this->event->update(["info"])) {
            throw new Exception('Unable to save event: ' . print_r($this->event->getErrors(), true));
        }
    }

    /**
     * Set flash message for patient allergies.
     */
    protected function showAllergyWarning()
    {
        if ($this->patient->no_allergies_date) {
            Yii::app()->user->setFlash('info.prescription_allergy', $this->patient->getAllergiesString());
        } else {
            Yii::app()->user->setFlash('patient.prescription_allergy', $this->patient->getAllergiesString());
        }
    }

    /*
     * Set flash message reason for edit
     * @param $reason_id
     * @param $reason_text
     */
    protected function showReasonForEdit($reason_id, $reason_text)
    {
        $edit_reason = OphDrPrescriptionEditReasons::model()->findByPk($reason_id);
        if ($edit_reason != null) {
            if ($reason_id > 1) {
                Yii::app()->user->setFlash('alert.edit_reason', 'Edit reason: ' . $edit_reason->caption);
            } else {
                Yii::app()->user->setFlash('alert.edit_reason', 'Edit reason: ' . $reason_text);
            }
        }
    }

    /**
     * Ajax action to search for drugs.
     */
    public function actionDrugList()
    {
        if (Yii::app()->request->getIsAjaxRequest()) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('deleted_date IS NULL');

            $params = [];
            $return = array();

            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $criteria->addCondition("LOWER(t.preferred_term) LIKE :term OR LOWER(medication_search_index.alternative_term) LIKE :term");
                $params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
                $criteria->join = 'LEFT JOIN medication_search_index ON t.id = medication_search_index.medication_id';
            }

            if (isset($_GET['type_id']) && $type_id = $_GET['type_id']) {
                $criteria->addCondition("id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id = :type_id)");
                $params[':type_id'] = $type_id;
            }

            $preservative_free = \Yii::app()->request->getParam('preservative_free');
            if ($preservative_free) {
                $criteria->addCondition("id IN (SELECT medication_id FROM medication_set_item WHERE
                                                medication_set_id = (SELECT id FROM medication_set WHERE name = 'Preservative free'))");
            }

            if (!empty($criteria->condition)) {
                $criteria->order = 't.preferred_term';
                $criteria->limit = 50;
                $criteria->select = 't.id, t.preferred_term';
                $criteria->params = $params;

                $drugs = Medication::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, $criteria);

                foreach ($drugs as $drug) {
                    $infoBox = new MedicationInfoBox();
                    $infoBox->medication_id = $drug->id;
                    $infoBox->init();
                    $tooltip = $infoBox->getHTML();

                    $return[] = array(
                        'label' => $drug->preferred_term,
                        'value' => $drug->preferred_term,
                        'id' => $drug->id,
                        'prepended_markup' => $tooltip,
                        'allergies' => array_map(function ($e) {
                            return $e->id;
                        }, $drug->allergies),
                    );
                }
            }

            $this->renderJSON($return);
        }
    }

    /**
     * Get a repeat prescription form for the patient (will ignore prescriptions from the
     * event defined by $current_id if given.
     *
     * @param $key
     * @param $patient_id
     * @param null $current_id
     */
    public function actionRepeatForm($key, $patient_id, $current_id = null)
    {
        $this->initForPatient($patient_id);

        if ($prescription = $this->getPreviousPrescription($current_id)) {
            foreach ($prescription->items as $item) {
                $this->renderPrescriptionItem($key, $item);
                ++$key;
            }
        }
    }

    /**
     * Get the most recent prescription for the current patient, not including
     * those from the given event id $current_id.
     *
     * @param int $current_id - event id to ignore
     *
     * @return Element_OphDrPrescription_Details
     */
    public function getPreviousPrescription($current_id = null)
    {
        if ($this->episode) {
            $condition = 'episode_id = :episode_id';
            $params = array(':episode_id' => $this->episode->id);
            if ($current_id) {
                $condition .= ' AND t.id != :current_id';
                $params[':current_id'] = $current_id;
            }
            $condition .= ' AND event.deleted = 0';

            return Element_OphDrPrescription_Details::model()->find(array(
                    'condition' => $condition,
                    'join' => 'JOIN event ON event.id = t.event_id AND event.deleted = false',
                    'order' => 'created_date DESC',
                    'params' => $params,
            ));
        }
    }

    /**
     * Ajax method to get html dropdown of the options for the given route.
     *
     * @param $key
     * @param $route_id
     */
    public function actionRouteOptions($key, $route_id)
    {
        $route = MedicationRoute::model()->findByPk($route_id);
        if ($route->has_laterality) {
            $options = MedicationLaterality::model()->findAll('deleted_date IS NULL');
            echo CHtml::dropDownList('Element_OphDrPrescription_Details[items][' . $key . '][laterality]', null, CHtml::listData($options, 'id', 'name'), array('empty' => '-- Select --'));
        } else {
            echo '-';
        }
    }

    /**
     * Set the prescription items.
     *
     * @param BaseEventTypeElement $element
     * @param array                $data
     * @param int                  $index
     */
    protected function setElementComplexAttributesFromData($element, $data, $index = null)
    {
        if (get_class($element) == 'Element_OphDrPrescription_Details' && isset($data['Element_OphDrPrescription_Details']['items']) && $data['Element_OphDrPrescription_Details']['items']) {
            // Form has been posted, so we should return the submitted values instead
            $items = array();
            foreach ($data['Element_OphDrPrescription_Details']['items'] as $item) {
                if (isset($item['id'])) {
                    $item_model = OphDrPrescription_Item::model()->findByPk($item['id']);
                } else {
                    $item_model = new OphDrPrescription_Item();
                }

                $item_model->attributes = $item;
                if (!$item_model->start_date) {
                    $item_model->start_date = substr($this->event->event_date, 0, 10);
                }
                if (isset($item['taper'])) {
                    $tapers = array();
                    foreach ($item['taper'] as $taper) {
                        if (isset($taper['id'])) {
                            $taper_model = OphDrPrescription_ItemTaper::model()->findByPk($taper['id']);
                        } else {
                            $taper_model = new OphDrPrescription_ItemTaper();
                        }

                        $taper_model->attributes = $taper;
                        $tapers[] = $taper_model;
                    }
                    $item_model->tapers = $tapers;
                }
                $items[] = $item_model;
            }

            $element->items = $items;
        }
    }

    /**
     * Actually save the prescription items against the details object.
     *
     * @param $data
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        foreach ($this->open_elements as $element) {
            if (get_class($element) === 'Element_OphDrPrescription_Details') {
                $items = [];
                if (isset($data['Element_OphDrPrescription_Details']['items'])) {
                    foreach ($data['Element_OphDrPrescription_Details']['items'] as $item) {
                        if (isset($item['id']) && isset($existing_item_ids[$item['id']])) {
                            $item_model = OphDrPrescription_Item::model()->findByPk($item['id']);
                        } else {
                            $item_model = new OphDrPrescription_Item();
                            $item_model->event_id = $this->event->id;
                            $item_model->medication_id = $item['medication_id'];
                            $item_model->start_date = substr($this->event->event_date, 0, 10);
                        }

                        $item_model->setAttributes($item);
                    }

                    $new_tapers = (isset($item['taper'])) ? $item['taper'] : [];
                    $taper_relation = [];
                    foreach ($new_tapers as $taper) {
                        if (isset($taper['id']) && isset($existing_taper_ids[$taper['id']])) {
                            // Taper is being updated
                            $taper_model = OphDrPrescription_ItemTaper::model()->findByPk($taper['id']);
                        } else {
                            // Taper is new
                            $taper_model = new OphDrPrescription_ItemTaper();
                        }

                        $taper_model->dose = $taper['dose'];
                        $taper_model->frequency_id = $taper['frequency_id'];
                        $taper_model->duration_id = $taper['duration_id'];

                        $taper_relation[] = $taper_model;
                    }

                    $item_model->tapers = $taper_relation;

                    $items[] = $item_model;
                }

                $element->updateItems($items);
            }
        }
    }

    public function actionPrint($id)
    {
        $print_mode = Yii::app()->request->getParam('print_mode', null);

        $user = User::model()->findByPk(Yii::app()->user->id);

        $this->printInit($id);
        $this->layout = '//layouts/print';

        $pdf_documents = (int)Yii::app()->request->getParam('pdf_documents');

        if ($print_mode === 'WP10' || $print_mode === 'FP10') {
            Yii::app()->puppeteer->leftMargin = '0mm';
            Yii::app()->puppeteer->rightMargin = '0mm';
            Yii::app()->puppeteer->topMargin = '6mm';
            Yii::app()->puppeteer->bottomMargin = '0mm';
            Yii::app()->puppeteer->scale = 0.998;

            $this->render('print_fpten', array(
                'user' => $user,
                'print_mode' => $print_mode
            ));
        } elseif ($pdf_documents === 1) {
            Yii::app()->puppeteer->leftMargin = '8mm';
            Yii::app()->puppeteer->rightMargin = '8mm';
            $this->render('print');
        } else {
            Yii::app()->puppeteer->leftMargin = '8mm';
            Yii::app()->puppeteer->rightMargin = '8mm';
            $this->render('print');
            if (SettingMetadata::model()->getSetting('disable_print_notes_copy') === 'off') {
                $this->render('print', array('copy' => 'notes'));
            }
            if (Yii::app()->params['disable_prescription_patient_copy'] === 'off') {
                $this->render('print', array('copy' => 'patient'));
            }
        }
    }

    public function actionPrintCopy($id)
    {
        $this->actionPrint($id);

        $eventid = 3686356;
        $api = Yii::app()->moduleAPI->get('OphCiExamination');
        $api->printEvent($eventid);
    }


    public function actionPDFPrint($id)
    {
        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($id))) {
            throw new Exception("Prescription not found for event id: $id");
        }

        $prescription->printed_by_user = Yii::app()->session['user'] ? Yii::app()->session['user']->id : null;
        $prescription->printed_date = date('Y-m-d H:i:s');

        if (!$prescription->save()) {
            throw new Exception('Unable to save prescription: ' . print_r($prescription->getErrors(), true));
        }

        Audit::add(
            'print-prescription',
            'print',
            Yii::app()->session['user_auth']->username . ' printed the prescription.'
        );

        $event = \Event::model()->findByPk($id);
        $this->pdf_print_suffix = $event->site_id ?? \Yii::app()->session['selected_site_id'];

        $document_count = 1;
        if (SettingMetadata::model()->getSetting('disable_print_notes_copy') === 'off') {
            $document_count++;
        }

        if (Yii::app()->params['disable_prescription_patient_copy'] === 'off') {
            $document_count++;
        }

        $this->pdf_print_documents = $document_count;

        $print_mode = Yii::app()->request->getParam('print_mode');

        if ($print_mode === 'WP10' || $print_mode === 'FP10') {
            $this->print_args = '?print_mode=' . $print_mode . '&print_footer=false';
            Yii::app()->puppeteer->leftMargin = '0mm';
            Yii::app()->puppeteer->rightMargin = '0mm';
            Yii::app()->puppeteer->topMargin = '6mm';
            Yii::app()->puppeteer->bottomMargin = '0mm';
            Yii::app()->puppeteer->scale = 0.998;
        } else {
            $this->print_args = null;
        }

        return parent::actionPDFPrint($id);
    }

    /**
     * Print action for a prescription event, called when a prescription has not ben printed.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionDoPrint($id)
    {
        $print_mode = Yii::app()->request->getParam('print_mode');
        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($id))) {
            throw new Exception("Prescription not found for event id: $id");
        }

        if ($print_mode === 'FP10') {
            $prescription->print = self::FP10_PRINT_MODE;
        } elseif ($print_mode === 'WP10') {
            $prescription->print = self::WP10_PRINT_MODE;
        } else {
            $prescription->print = self::NORMAL_PRINT_MODE;
        }

        $prescription->draft = 0;

        if (!$prescription->save()) {
            throw new Exception('Unable to save prescription: ' . print_r($prescription->getErrors(), true));
        }

        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }

        // FIXME: this should be using the info method
        $event->info = 'Printed';

        if (!$event->save()) {
            throw new Exception('Unable to save event: ' . print_r($event->getErrors(), true));
        }

        echo '1';
    }

    /**
     * Mark a prescription element as printed - called when printing a prescription that has already
     * been printed.
     *
     * @TODO: is this necessary if the print action is already marking the prescription printed?
     *
     * @throws Exception
     */
    public function actionMarkPrinted()
    {
        $event_id = Yii::app()->request->getParam('event_id');
        if (!$event_id) {
            throw new Exception('Prescription id not provided');
        }

        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($event_id))) {
            throw new Exception('Prescription not found for event id: ' . $event_id);
        }

        if ($prescription->print >= 1) {
            $prescription->print = 0;

            if (!$prescription->update(['print', 'printed'])) {
                throw new Exception('Unable to save prescription: ' . print_r($prescription->getErrors(), true));
            }
        }

        $this->printInit($event_id);
        $this->printLog($event_id, false);

        echo '1';
    }

    public function checkCreateAccess()
    {
        $api = Yii::app()->moduleAPI->get('OphDrPrescription');
        return $this->checkAccess($api->createOprn, $this->firm, $this->episode, $this->event_type);
    }

    public function checkPrintAccess()
    {
        return $this->checkAccess('OprnPrintPrescription');
    }

    public function checkEditAccess()
    {
        $editAccess = $this->checkAccess('OprnEditPrescription', $this->event);
        if ($editAccess) {
            return $editAccess;
        }
        $px_element = null;
        if ($this->event) {
            $px_element = Element_OphDrPrescription_Details::model()->find('event_id=?', array($this->event->id));
        }
        if ($px_element) {
            $non_pgd_entries = array_filter($px_element->getEntries(), function ($entry) {
                return !$entry->pgd;
            });
            if (empty($non_pgd_entries)) {
                $curr_user_id = \Yii::app()->user->id;
                $editAccess = true;
                foreach ($px_element->pgds as $pgd) {
                    if (!in_array($curr_user_id, $pgd->getAuthedUserIDs()) || !parent::checkEditAccess()) {
                        $editAccess = false;
                        break;
                    }
                }
            }
        }
        return $editAccess;
    }

    /**
     * @return MedicationSet|null
     */

    public function getCommonDrugsRefSet()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];
        $rule = MedicationSetRule::model()->findByAttributes(array(
            'subspecialty_id' => $subspecialty_id,
            'site_id' => $site_id,
            'usage_code_id' => \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])->queryScalar()
        ));
        if ($rule) {
            return $rule->medicationSet;
        } else {
            return null;
        }
    }

    /**
     * Render the form for a OphDrPrescription_Item, MedicationSetItem or Medication (by id).
     *
     * @param $key
     * @param OphDrPrescription_Item|MedicationSetItem|int $source
     *
     * @throws CException
     * @throws Exception
     */
    public function renderPrescriptionItem($key, $source, $label = null)
    {
        $item = new OphDrPrescription_Item();
        $item->bound_key = substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);
        if (is_a($source, 'OphDrPrescription_Item')) {
            // Source is a prescription item, so we should clone it
            foreach (
                array(
                         'medication_id',
                         'pgdpsd_id',
                         'duration_id',
                         'frequency_id',
                         'dose',
                         'dose_unit_term',
                         'laterality',
                         'route_id',
                         'dispense_condition_id',
                         'dispense_location_id'
                     ) as $field
            ) {
                $item->$field = $source->$field;
            }

            if ($source->tapers) {
                $tapers = array();
                foreach ($source->tapers as $taper) {
                    $taper_model = new OphDrPrescription_ItemTaper();
                    $taper_model->dose = $taper->dose;
                    $taper_model->frequency_id = $taper->frequency_id;
                    $taper_model->duration_id = $taper->duration_id;
                    $tapers[] = $taper_model;
                }
                $item->tapers = $tapers;
            }
        } else {
            if (is_a($source, MedicationSetItem::class)) {
                $item->medication_id = $source->medication_id;
                $item->frequency_id = $source->default_frequency_id;
                $item->form_id = $source->default_form_id ? $source->default_form_id : $source->medication->default_form_id;
                $item->dose = $source->default_dose ? $source->default_dose : $source->medication->default_dose;
                $item->dose_unit_term = $source->default_dose_unit_term ? $source->default_dose_unit_term : $source->medication->default_dose_unit_term;
                $item->route_id = $source->default_route_id ? $source->default_route_id : $source->medication->default_route_id;
                $item->dispense_condition_id = $source->default_dispense_condition_id;
                $item->dispense_location_id = $source->default_dispense_location_id;
                $item->duration_id = $source->default_duration_id;

                if ($source->tapers) {
                    $tapers = array();
                    foreach ($source->tapers as $taper) {
                        $taper_model = new OphDrPrescription_ItemTaper();
                        foreach (array('duration_id', 'frequency_id', 'dose') as $field) {
                            if ($taper->$field) {
                                $taper_model->$field = $taper->$field;
                            } else {
                                $taper_model->$field = $item->$field;
                            }
                        }
                        $tapers[] = $taper_model;
                    }
                    $item->tapers = $tapers;
                }
            } elseif (is_a($source, OphDrPGDPSD_PGDPSDMeds::class)) {
                $item->pgdpsd_id = $source->pgdpsd_id;
                $item->medication_id = $source->medication_id;
                $item->dose = $source->dose;
                $item->dose_unit_term = $source->dose_unit_term;
                $item->route_id = $source->route_id;
                $item->frequency_id = $source->frequency_id;
                $item->duration_id = $source->duration_id;
                $item->dispense_condition_id = $source->dispense_condition_id;
                $item->dispense_location_id = $source->dispense_location_id;
                $item->comments = $source->comments;
            } elseif (is_int($source) || (int) $source) {
                // as typed, save as a new local drug - with no defaults
                if ($source == EventMedicationUse::USER_MEDICATION_ID) {
                    $medication = new Medication();
                    $medication->preferred_term = $label;
                    $medication->short_term = $label;
                    $medication->source_type = EventMedicationUse::USER_MEDICATION_SOURCE_TYPE;
                    $medication->source_subtype = EventMedicationUse::USER_MEDICATION_SOURCE_SUBTYPE;
                    $medication->preferred_code = Medication::getNextUnmappedPreferredCode();

                    if ($medication->save()) {
                        $medication->addDefaultSearchIndex();
                    } else {
                        throw new Exception(print_r($medication->getErrors(), true));
                    }

                    $item->medication_id = $medication->id;
                } else {
                    // Source is an integer (!=-1), so we use it as a drug_id
                    $item->medication_id = $source;
                }

                $medSet = $this->getCommonDrugsRefSet();
                $item->loadDefaults($medSet);
            } else {
                throw new CException('Invalid prescription item source: ' . print_r($source));
            }
            // Populate route option from episode for Eye
            if ($episode = $this->episode) {
                if ($principal_eye = $episode->eye) {
                    $lat_id = MedicationLaterality::model()->find(
                        'name = :eye_name',
                        array(':eye_name' => $principal_eye->name)
                    );
                    $item->laterality = ($lat_id) ? $lat_id : null;
                }
                //check operation note eye and use instead of original diagnosis
                if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
                    if ($apieye = $api->getLastEye($this->patient)) {
                        $item->laterality = $apieye;
                    }
                }
            }
        }
        $unit_options = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions;
        if (isset($this->patient)) {
            $this->renderPartial(
                '/default/form_Element_OphDrPrescription_Details_Item',
                array('key' => $key, 'item' => $item, 'patient' => $this->patient, 'unit_options' => $unit_options)
            );
        } else {
            $output = $this->renderPartial(
                '/default/form_Element_OphDrPrescription_Details_Item',
                array('key' => $key, 'item' => $item, 'unit_options' => $unit_options),
                true
            );

            return $output;
        }
    }

    public function actionUpdate($id, $reason = null)
    {
        global $reason_id;
        global $reason_other_text;

        $model = Element_OphDrPrescription_Details::model()
            ->findBySql('SELECT * FROM et_ophdrprescription_details WHERE event_id = :id', [':id' => $id]);

        if (!$model->isEditableByMedication()) {
            throw new CHttpException(403, 'You are not authorised to update the Prescription from this page.');
        } elseif ($reason === null && !$model->draft) {
            $this->render('ask_reason', array(
                'id'        =>  $id,
                'draft'     => $model->draft,
                'printed'   => $model->printed
            ));
        } else {
            if (isset($_GET['do_not_save']) && $_GET['do_not_save'] === '1') {
                $reason_id = isset($_GET['reason']) ? $_GET['reason'] : 0;
                $reason_other_text = isset($_GET['reason_other']) ? $_GET['reason_other'] : '';
               // $_POST=null;
            } else {
                $reason_id = $model->edit_reason_id;
                $reason_other_text = $model->edit_reason_other;
            }
            $this->showReasonForEdit($reason_id, $reason_other_text);
            parent::actionUpdate($id);
        }
    }

    /*
     * Finalize as "Save as final" prescription event,
     * when a prescription event is created as the result of a medication management element from an examination event
     *
     * @param       integer     event_id
     * @param       integer     element_id
     * @return      json_array
     */
    public function actionFinalize()
    {
        if (Yii::app()->request->isPostRequest) {
            $eventID = Yii::app()->request->getPost('event');
            $elementID = Yii::app()->request->getPost('element');

            $model = Element_OphDrPrescription_Details::model()->findByAttributes([
                'event_id' => $eventID, 'id' => $elementID
            ]);

            $prescription_items = OphDrPrescription_Item::model()->findAll(
                "event_id=:event_id",
                [':event_id' => $eventID]
            );

            $prescription_items_by_id = [];
            $prescribed_medication_models = [];
            foreach ($prescription_items as $prescription_item) {
                $prescribed_medication_model = EventMedicationUse::model()->findByAttributes(
                    ['prescription_item_id' => $prescription_item->id]
                );

                if ($prescribed_medication_model) {
                    $prescription_items_by_id[$prescription_item->id] = $prescription_item;
                    $prescribed_medication_models[] = $prescribed_medication_model;
                }
            }

            foreach ($prescribed_medication_models as $prescribed_medication) {
                $stop_date_from_duration = $prescription_items_by_id[$prescribed_medication->prescription_item_id]->stopDateFromDuration();
                $prescribed_medication->end_date = !is_null($stop_date_from_duration) ? $stop_date_from_duration->format('Y-m-d') : null;
                $prescribed_medication->update();
            }

            if ($model) {
                $model->draft = 0;
                $model->authorised_by_user = Yii::app()->session['user'] ? Yii::app()->session['user']->id : null;
                $model->authorised_date = date('Y-m-d H:i:s');
                $model->update();
                Audit::add(
                    'authorise-prescription',
                    'authorise',
                    Yii::app()->session['user_auth']->username . ' authorises the prescription.'
                );
                $result = [
                    'success' => 1
                ];
            } else {
                $result = [
                    'success' => 0
                ];
            }

            echo json_encode($result);
        }
    }

    /*
     * Finalize as "Save as final" prescription event, with e-signing,
     * when a prescription event is created as the result of a medication management element from an examination event
     * or for a regular prescription where a signature has been supplied on the view screen
     *
     * @param       integer     event_id
     */
    public function actionFinalizeWithSignatures()
    {
        if (Yii::app()->request->isPostRequest) {
            $eventID = Yii::app()->request->getPost('event');

            $signatures = Yii::app()->request->getPost('OEModule_OphCiExamination_models_MedicationManagement');
            $signatures = $signatures ?? Yii::app()->request->getPost('Element_OphDrPrescription_Esign');

            $signatures = $signatures['signatures'];

            $model = Element_OphDrPrescription_Details::model()->findByAttributes([
                'event_id' => $eventID
            ]);

            $prescription_items = OphDrPrescription_Item::model()->findAll(
                "event_id=:event_id",
                [':event_id' => $eventID]
            );

            $prescription_items_by_id = [];
            $prescribed_medication_models = [];
            foreach ($prescription_items as $prescription_item) {
                $prescribed_medication_model = EventMedicationUse::model()->findByAttributes(
                    ['prescription_item_id' => $prescription_item->id]
                );

                if ($prescribed_medication_model) {
                    $prescription_items_by_id[$prescription_item->id] = $prescription_item;
                    $prescribed_medication_models[] = $prescribed_medication_model;
                }
            }

            foreach ($prescribed_medication_models as $prescribed_medication) {
                $stop_date_from_duration = $prescription_items_by_id[$prescribed_medication->prescription_item_id]->stopDateFromDuration();
                $prescribed_medication->end_date = !is_null($stop_date_from_duration) ? $stop_date_from_duration->format('Y-m-d') : null;
                $prescribed_medication->update();
            }

            if ($model) {
                $model->draft = 0;
                $model->authorised_by_user = Yii::app()->session['user'] ? Yii::app()->session['user']->id : null;
                $model->authorised_date = date('Y-m-d H:i:s');
                $model->update();

                if ($medication_management = $model->isSignedByMedication()) {
                    $mm_signatures = $medication_management->getSignatures();
                    $index = 0;

                    foreach ($mm_signatures as $signature) {
                        $signature->signatory_name = $signatures[$index]['signatory_name'];
                        $signature->proof = $signatures[$index]['proof'];
                        $signature->setDataFromProof();

                        $index++;
                    }

                    $medication_management->signatures = $mm_signatures;

                    if (!$medication_management->save()) {
                        throw new Exception("Failed to save Medication Management prescription Electronic Signatures");
                    }
                } else {
                    $prescription_esign = Element_OphDrPrescription_Esign::model()->findByAttributes([
                        'event_id' => $eventID
                    ]);

                    $es_signatures = $prescription_esign->getSignatures();
                    $index = 0;

                    $transaction = Yii::app()->db->beginTransaction();
                    $errors = array();

                    foreach ($es_signatures as $signature) {
                        $signature->signatory_name = $signatures[$index]['signatory_name'];
                        $signature->proof = $signatures[$index]['proof'];
                        $signature->setDataFromProof();

                        $signature->element_id = $prescription_esign->id;

                        if (!$signature->save()) {
                            $errors[] = $signature->getErrors();
                        }

                        $index++;
                    }

                    if (count($errors) > 0) {
                        $transaction->rollback();

                        throw new Exception("Failed to save Prescription Electronic Signatures");
                    } else {
                        $transaction->commit();
                    }
                }

                Audit::add(
                    'authorise-prescription',
                    'authorise',
                    Yii::app()->session['user_auth']->username . ' authorises the prescription.'
                );

                return $this->redirect(array('/OphDrPrescription/default/view/' . $eventID));
            }

            throw new Exception("Prescription Details model not found for event " . $eventID);
        }
    }

    /**
     * Group the different kind of drug items for the printout
     *
     * @param $items
     * @return OphDrPrescription_Item[]
     */
    public function groupItems($items)
    {
        $item_group = array();
        foreach ($items as $item) {
            $item_group[$item->dispense_condition_id][] = $item;
        }
        return $item_group;
    }

    /**
     * Function gets the last Element_OphTrOperationnote_SiteTheatre
     * and returns it if site and subspecialty are the same as parameters
     * @param $prescribed_date
     * @param $firm_id
     * @return Element_OphTrOperationnote_SiteTheatre | bool
     */
    public function getSiteAndTheatreForSameDayEvent($prescribed_date, $firm_id)
    {
        $api = Yii::app()->moduleAPI->get('OphTrOperationnote');
        if ($api) {
            $site_theatre = $api->getElementFromLatestEvent(
                'Element_OphTrOperationnote_SiteTheatre',
                $this->patient,
                true
            );
            if ($site_theatre && $site_theatre->event->NHSDate('event_date') === $prescribed_date && $site_theatre->event->episode->firm_id === $firm_id) {
                return $site_theatre;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function afterUpdateElements($event)
    {
        $this->sendEmail($event);
        parent::afterUpdateElements($event);
    }

    /**
     * @inheritDoc
     */
    protected function afterCreateElements($event)
    {
        $this->sendEmail($event);
        parent::afterCreateElements($event);
    }

    private function sendEmail(\Event $event): void
    {
        if (($api = \Yii::app()->moduleAPI->get("OphInCocoa")) && isset($_POST["saveandpost"])) {
            /** @var \OEModule\OphInCocoa\components\OphInCocoa_API $api */
            $api->sendPrescription($this->patient, $event, $this, \Yii::app()->session['selected_site_id']);
            // PDF printing messed up the HTTP header so reset the defaults
            header('Content-Type: text/html');
            header_remove('Content-Length');
        }
    }

    public function getSiteAndTheatreForLatestEvent()
    {
        if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
            if (
                $site_theatre = $api->getElementFromLatestEvent(
                    'Element_OphTrOperationnote_SiteTheatre',
                    $this->patient,
                    true
                )
            ) {
                return $site_theatre;
            }
        }
        return false;
    }
}
