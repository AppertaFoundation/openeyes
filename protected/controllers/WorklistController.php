<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class WorklistController extends BaseController
{
    public $layout = 'worklist';
    /**
     * @var WorklistManager
     */
    protected $manager;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('OprnWorklist')));
    }

    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
        if ($action->getId() === "print") {
            $newblue_path = 'application.assets.newblue';
            Yii::app()->assetManager->registerCssFile('css/style_oe3_print.min.css', $newblue_path, null);
        }

        $this->manager = new WorklistManager();

        return parent::beforeAction($action);
    }
    protected function prescriberDomData()
    {
        $ret = array(
            'preset_orders' => array(),
            'is_prescriber' => false,
            'popup' => null,
            'assign_preset_btn' => null,
        );
        if ($is_prescriber = $this->checkAccess('Prescribe')) {
            $preset_criteria = new CDbCriteria();
            $preset_criteria->compare('LOWER(type)', 'psd');
            $preset_criteria->compare('active', true);
            $preset_orders = OphDrPGDPSD_PGDPSD::model()->findAll($preset_criteria) ? : array();
            $popup = $this->renderPartial(
                'worklist_psd_assignment_popup',
                array(
                    'preset_orders' => $preset_orders,
                ),
                true,
            );
            $ret['preset_orders'] = $preset_orders;
            $ret['is_prescriber'] = $is_prescriber;
            $ret['popup'] = $popup;
            $ret['assign_preset_btn'] = "<div class='button-stack'><button disabled class='green hint' id='js-worklist-psd-add'>Assign Preset Order to selected patients</button></div>";
        }
        return $ret;
    }
    public function actionView()
    {
        $this->layout = 'main';
        $date_from = Yii::app()->request->getQuery('date_from');
        $date_to = Yii::app()->request->getQuery('date_to');
        $redirect = false;

        if (!isset(Yii::app()->session['worklist'])) {
            Yii::app()->session['worklist'] = [];
        }

        if ($date_from || $date_to) {
            foreach (['date_from', 'date_to'] as $date) {
                ${$date} = is_numeric(str_replace([" ", "/"], "", ${$date})) ? str_replace([" ", "/"], "-", ${$date}) : str_replace(['/'], " ", ${$date});
            }
            Yii::app()->session['worklist'] = ['date_from' => $date_from, 'date_to' => $date_to];
        }

        if (count(Yii::app()->session['worklist']) > 0) {
            foreach (['date_from', 'date_to'] as $date) {
                if (Yii::app()->session['worklist'][$date] && !${$date}) {
                    ${$date} = str_replace(" ", "+", Yii::app()->session['worklist'][$date]);
                    $redirect = true;
                }
            }
        }

        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.InputFieldValidation.js'), ClientScript::POS_END);
        $worklist_js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', true);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($worklist_js, ClientScript::POS_END);
        if ($redirect) {
            return $this->redirect(array('/worklist/view?date_from='.$date_from.'&date_to='.$date_to));
        }

        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null);
        $sync_interval_setting_key = 'worklist_auto_sync_interval';
        $sync_interval_settings = \SettingMetadata::model()->find("`key` = 'worklist_auto_sync_interval'");
        $sync_interval_options = unserialize($sync_interval_settings->data);
        $sync_interval_value = $sync_interval_settings->getSetting();
        $prescriber_dom_data = $this->prescriberDomData();
        $this->render(
            'index',
            array(
                'worklists' => $worklists,
                'sync_interval_options' => $sync_interval_options,
                'sync_interval_value' => $sync_interval_value,
                'sync_interval_setting_key' => $sync_interval_setting_key,
                'is_prescriber' => $prescriber_dom_data['is_prescriber'],
                'preset_popup' => $prescriber_dom_data['popup'],
                'assign_preset_btn' => $prescriber_dom_data['assign_preset_btn'],
            )
        );
    }

    /**
     * Redirect to a suitable worklist default action.
     */
    public function actionIndex()
    {
        return $this->redirect(array('/worklist/manual'));
    }

    /**
     * Manage User's manual worklists.
     */
    public function actionManual()
    {
        $current_worklists = $this->manager->getCurrentManualWorklistsForUser(Yii::app()->user);
        $available_worklists = $this->manager->getAvailableManualWorklistsForUser(Yii::app()->user);

        $this->render('//worklist/manual/index', array(
            'current_worklists' => $current_worklists,
            'available_worklists' => $available_worklists,
        ));
    }

    public function actionManualAdd()
    {
        $worklist = new Worklist();

        if (!empty($_POST)) {
            $worklist->attributes = $_POST['Worklist'];
            if ($this->manager->createWorklistForUser($worklist)) {
                Audit::add('Manual-Worklist', 'add', $worklist->id);
                $this->redirect('/worklist/manual');
            } else {
                $errors = $worklist->getErrors();
            }
        }

        $this->render('//worklist/manual/add', array(
            'worklist' => $worklist,
            'errors' => @$errors,
        ));
    }

    /**
     * Update the worklist display order for the current user based on the submitted ids.
     */
    public function actionManualUpdateDisplayOrder()
    {
        $worklist_ids = @$_POST['item_ids'] ? explode(',', $_POST['item_ids']) : array();

        if (!$this->manager->setWorklistDisplayOrderForUser(Yii::app()->user, $worklist_ids)) {
            OELog::log(print_r($this->manager->getErrors(), true));
            throw new Exception('Unable to save new display order for worklists');
        }

        $this->redirect('/worklist/manual');
    }

    public function actionPrint($date_from = null, $date_to = null, $list_id = null)
    {
        $this->layout = '//layouts/print';
        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null);
        if ($list_id) {
            $worklists = array_filter($worklists, function ($e) use ($list_id) {
                return (int)$e->id === (int)$list_id;
            });
        }


        $this->render('//worklist/print', array('worklists' => $worklists));
    }

    public function actionClearDates()
    {
        Yii::app()->session->remove('worklist');
        return $this->redirect(array('/worklist/view'));
    }

    public function actionRenderPopup()
    {
        if (isset($_POST['patientId'])) {
            $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
            $patientId = $_POST['patientId'];
            $patient = Patient::model()->findByPk($patientId);

            $deceased = $patient->isDeceased();
            $institution = Institution::model()->getCurrent();
            $selected_site_id = Yii::app()->session['selected_site_id'];
            $display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];
            $display_secondary_number_usage_code = Yii::app()->params['display_secondary_number_usage_code'];
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
            $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
            $patientIdentifiers = null;
            foreach ($patient->identifiers as $patientIdentifier) {
                $patientIdentifiers[] = [
                    'longTitle' => $patientIdentifier->patientIdentifierType->long_title,
                    'shortTitle' => $patientIdentifier->patientIdentifierType->short_title,
                    'value' => $patientIdentifier->value,
                    'valueDisplayPrefix' => $patientIdentifier->patientIdentifierType->value_display_prefix,
                    'valueDisplaySuffix' => $patientIdentifier->patientIdentifierType->value_display_suffix,
                    'patientIdentifierStatus' => $patientIdentifier->patientIdentifierStatus,
                    'description' => $patientIdentifier->patientIdentifierStatus ? $patientIdentifier->patientIdentifierStatus->description : null,
                    'iconBannerClassName' => $patientIdentifier->patientIdentifierStatus ? $patientIdentifier->patientIdentifierStatus->icon->banner_class_name : null,
                ];
            }

            $patientDeletedIdentifiers = null;
            foreach (PatientIdentifier::model()->resetScope(true)->findAll('deleted = 1 AND patient_id = ?', [$patient->id]) as $patientDeletedIdentifier) {
                $patientDeletedIdentifiers[] = [
                    'identifier' => $patientDeletedIdentifier,
                    'longTitle' => $patientDeletedIdentifier->patientIdentifierType->long_title,
                    'shortTitle' => $patientDeletedIdentifier->patientIdentifierType->short_title,
                    'value' => $patientDeletedIdentifier->value,
                    'valueDisplayPrefix' => $patientDeletedIdentifier->patientIdentifierType->value_display_prefix,
                    'valueDisplaySuffix' => $patientDeletedIdentifier->patientIdentifierType->value_display_suffix,
                ];
            }

            $patientLocalIdentifiers = null;
            foreach ($patient->localIdentifiers as $patientLocalIdentifier) {
                $patientLocalIdentifiers[] = [
                    'hasValue' => $patientLocalIdentifier->hasValue(),
                    'shortTitle' => $patientLocalIdentifier->patientIdentifierType->short_title,
                    'displayValue' => $patientLocalIdentifier->getDisplayValue(),
                ];
            }

            $patientData['href'] = (new CoreAPI())->generatePatientLandingPageLink($patient);
            $patientData['lastname'] = $patient->getLast_name();
            $patientData['firstname'] = $patient->getFirst_name();
            $patientData['title'] = $patient->getTitle();
            $patientData['hospitalNumberPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($primary_identifier);
            $patientData['hospitalNumberValue'] = PatientIdentifierHelper::getIdentifierValue($primary_identifier);
            $patientData['displayPrimaryNumberUsageCode'] = $display_primary_number_usage_code;
            $patientData['patientPrimaryIdentifierStatus'] = $display_primary_number_usage_code === 'GLOBAL' && $primary_identifier && $primary_identifier->patientIdentifierStatus;
            $patientData['patientPrimaryIdentifierStatusClassName'] = $primary_identifier->patientIdentifierStatus->icon->class_name ?? 'exclamation';
            $patientData['patientIdentifiers'] = $patientIdentifiers;
            $patientData['patientDeletedIdentifiers'] = $patientDeletedIdentifiers;
            $patientData['patientLocalIdentifiers'] = $patientLocalIdentifiers;

            $patientData['patientGlobalIdentifier'] = $patient->globalIdentifier;
            $patientData['patientGlobalIdentifierPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($patient->globalIdentifier);
            $patientData['patientGlobalIdentifierLabel'] = PatientIdentifierHelper::getIdentifierValue($patient->globalIdentifier);

            $patientData['displaySecondaryNumberUsageCode'] = $display_secondary_number_usage_code;
            $patientData['patientSecondaryIdentifierStatus'] = $display_secondary_number_usage_code === 'GLOBAL' && $secondary_identifier && $secondary_identifier->patientIdentifierStatus;
            $patientData['patientSecondaryIdentifierStatusClassName'] = $secondary_identifier->patientIdentifierStatus->icon->class_name ?? 'exclamation';
            $patientData['nhsNumberPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier);
            $patientData['nhsNumberValue'] = PatientIdentifierHelper::getIdentifierValue($secondary_identifier);
            $patientData['gender'] = $patient->getGenderString();
            $patientData['deceased'] = boolval($deceased);
            $patientData['dateOfDeath'] = Helper::convertDate2NHS($patient->date_of_death);

            $patientData['patientAge'] = $patient->getAge();

            // Get Allergies data.
            $allergiesWidget = $this->widget(\OEModule\OphCiExamination\widgets\Allergies::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
            ), false);

            $patientData['patientAllergies']['hasAllergyStatus'] = boolval(!$patient->hasAllergyStatus());
            $patientData['patientAllergies']['noAllergiesDate'] = !boolval(!$patient->hasAllergyStatus()) && $allergiesWidget->element->no_allergies_date;
            $patientData['patientAllergies']['data'] = !boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date;
            if (!boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date) {
                $patientData['patientAllergies']['entries'] = null;
                foreach ($allergiesWidget->element->entries as $i => $entry) {
                    if ($entry->getDisplayHasAllergy() === 'Present') {
                        $patientData['patientAllergies']['entries'][] = [
                            'displayAllergy' => $entry->getDisplayAllergy(),
                            'reactionString' => ' ' . $entry->getReactionString(),
                            'comments' => $entry->comments,
                            'lastModifiedUser' => User::model()->findByPk($entry->last_modified_user_id)->getFullName(),
                        ];
                    }
                }
            }

            // Get risks data
            $historyRisksWidget = $this->widget(\OEModule\OphCiExamination\widgets\HistoryRisks::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
            ), false);

            $riskAlertInfo = false;
            $noRisksDate = false;
            if (boolval(!$patient->hasRiskStatus()) && boolval(!$patient->getDiabetes())) {
                $riskAlertInfo = true;
            } elseif ($historyRisksWidget->element->no_risks_date) {
                $noRisksDate = true;
            } else {
                $patientData['patientRisks']['entries'] = null;
                foreach ($historyRisksWidget->element->entries as $i => $entry) {
                    if ($entry->getDisplayHasRisk() === 'Present') {
                        $patientData['patientRisks']['entries'][] = [
                            'displayRisk' => $entry->getDisplayRisk(),
                            'comments' => $entry->comments,
                        ];
                    }
                }
                foreach ($patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET) as $disorder) {
                    $patientData['patientRisks']['disorders'][] = [
                        'disorderTerm' => $disorder->term,
                    ];
                }
            }

            $patientData['patientRisks']['riskAlertInfo'] = $riskAlertInfo;
            $patientData['patientRisks']['noRisksDate'] = $noRisksDate;

            //Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans
            $vaData = $exam_api->getMostRecentVADataStandardised($patient);
            if ($vaData) {
                $patientData['vaData'] = [
                    'has_beo' => $vaData['has_beo'],
                    'beo_result' => $vaData['has_beo'] ? $vaData['beo_result'] : null,
                    'beo_method_abbr' => $vaData['has_beo'] ? $vaData['beo_method_abbr'] : null,
                    'has_right' => $vaData['has_right'],
                    'right_result' => $vaData['has_right'] ? $vaData['right_result'] : null,
                    'right_method_abbr' => $vaData['has_right'] ? $vaData['right_method_abbr'] : null,
                    'has_left' => $vaData['has_left'],
                    'left_result' => $vaData['has_left'] ? $vaData['left_result'] : null,
                    'left_method_abbr' => $vaData['has_left'] ? $vaData['left_method_abbr'] : null,
                    'event_date' => Helper::convertDate2NHS($vaData['event_date'])
                ];
            }
            $refractionData = $exam_api->getLatestRefractionReadingFromAnyElementType($patient);
            if ($refractionData) {
                $patientData['refractionData'] = [
                    'has_left' => (bool)$refractionData['left'],
                    'left' => $refractionData['left'],
                    'has_right' => (bool)$refractionData['right'],
                    'right' => $refractionData['right'],
                    'event_date' => Helper::convertDate2NHS($refractionData['event_date'])
                ];
            }
            $leftCCT = $exam_api->getCCTLeft($patient);
            $rightCCT = $exam_api->getCCTRight($patient);
            if ($leftCCT !== null || $rightCCT !== null) {
                $patientData['cct'] = [
                    'has_left' => (bool)$leftCCT ,
                    'left' => $leftCCT,
                    'has_right' => (bool)$rightCCT,
                    'right' => $rightCCT,
                    'event_date' => Helper::convertDate2NHS($exam_api->getCCTDate($patient))
                ];
            }
            $cviStatus = $patient->getCviSummary();
            if ($cviStatus[0] !== 'Unknown') {
                $patientData['cvi'] = [
                    'data' => $cviStatus[0],
                    'date' => ($cviStatus[1] && $cviStatus[1] !== '0000-00-00') ? Helper::convertDate2HTML($cviStatus[1]) : 'N/A',
                ];
            }
            foreach ($patient->getOphthalmicDiagnosesSummary() as $ophthalmic_diagnosis) {
                list($side, $name, $date) = explode('~', $ophthalmic_diagnosis);
                $temp = [];
                $temp['name'] = $name;
                $temp['date'] = $date;

                $laterality = $this->getLaterality(null, $side);
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];

                $patientData['ophthalmicDiagnosis'][] = $temp;
            }
            foreach ($patient->systemicDiagnoses as $diagnosis) {
                $temp = [];
                $temp['term'] = $diagnosis->disorder->term;
                $temp['date'] = $diagnosis->getFormatedDate();

                $eye = $diagnosis->eye;
                $laterality = $this->getLaterality($eye, NULL);
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];

                $patientData['systemicDiagnoses'][] = $temp;
            }
            // Get Past Surgery data.
            $pastSurgeryWidget = $this->createWidget(\OEModule\OphCiExamination\widgets\PastSurgery::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                'popupListSeparator' => '<br/>',
            ));
            $pastSurgeryData = $pastSurgeryWidget->getViewData();
            $operations = is_array($pastSurgeryData) ? $pastSurgeryData['operations'] : false;
            $patientData['pastSurgery']['nilRecord'] = (!$operations || sizeof($operations)==0) && !$pastSurgeryWidget->element->no_pastsurgery_date;
            $patientData['pastSurgery']['noPreviousData'] = !((!$operations || sizeof($operations)==0) && !$pastSurgeryWidget->element->no_pastsurgery_date) && $pastSurgeryWidget->element->no_pastsurgery_date;
            $pastSurgeryDataExists = !((!$operations || sizeof($operations)==0) && !$pastSurgeryWidget->element->no_pastsurgery_date) && !($pastSurgeryWidget->element->no_pastsurgery_date);
            if ($pastSurgeryDataExists) {
                foreach ($operations as $operation) {
                    $temp = [];
                    $temp['operation'] = isset($operation['object']) ? $operation['object']->operation : $operation['operation'];
                    $temp['date'] = isset($operation['object']) ? $operation['object']->getDisplayDate() : Helper::formatFuzzyDate($operation['date']);
                    $temp['has_link'] = isset($operation['link']);
                    $temp['link'] = $operation['link'] ?? false;
                    $side = $operation['side'] ?? (isset($operation['object']) ? $operation['object']->side : '');
                    $laterality = $this->getLaterality(null, $side);
                    $temp['left'] = $laterality['left'];
                    $temp['right'] = $laterality['right'];

                    $patientData['pastSurgery']['operation'][] = $temp;
                }
            }

            $historyMedicationsWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\HistoryMedications::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $historyMedicationsData = $historyMedicationsWidget->getViewData();
            $current_systemic_meds = null;
            $stopped_systemic_meds = null;
            $current_eye_meds = null;
            $stopped_eye_meds = null;
            $element = null;
            if (is_array($historyMedicationsData)) {
                $current_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !$e->isStopped();
                };
                $stopped_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !$e->isChangedMedication();
                };
                $systemic_filter = function ($med) {
                    return $med->laterality === null;
                };
                $eye_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !is_null($e->route_id) && $e->route->has_laterality;
                };

                $element = $historyMedicationsData['element'];
                $current = $historyMedicationsData['current'];
                $stopped = $historyMedicationsData['stopped'];
                $current = $element->mergeMedicationEntries($current);
                $current = array_filter($current, $current_filter);
                $current = $historyMedicationsWidget->sortEntriesByDate($current);
                $stopped = array_filter($stopped, $stopped_filter);
                $stopped = $historyMedicationsWidget->sortEntriesByDate($stopped, false);
                $current_systemic_meds = array_filter($current, $systemic_filter);
                $stopped_systemic_meds = array_filter($stopped, $systemic_filter);
                $current_eye_meds = array_filter($current, $eye_filter);
                $stopped_eye_meds = array_filter($stopped, $eye_filter);
            }
            $nilRecord = false;
            $noPreviousData = false;
            if (empty($current_systemic_meds) && empty($stopped_systemic_meds) && is_null($element->no_systemic_medications_date)) {
                $nilRecord = true;
            } elseif (empty($current_systemic_meds) && empty($stopped_systemic_meds) && !is_null($element->no_systemic_medications_date)) {
                $noPreviousData = true;
            } else {
                if ($current_systemic_meds) {
                    $patientData['systemicMedications']['currentSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_systemic_meds, true, false, true, false);
                }
                if ($stopped_systemic_meds) {
                    $patientData['systemicMedications']['stoppedSystemicMedsSize'] = sizeof($stopped_systemic_meds);
                    $patientData['systemicMedications']['stoppedSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_systemic_meds, false, false, false, false);
                }
                $patientData['historyMedications']['id'] = CHtml::modelName($element);
            }

            $patientData['systemicMedications']['nilRecord'] = $nilRecord;
            $patientData['systemicMedications']['noPreviousData'] = $noPreviousData;

            $nilRecord = false;
            $noPreviousData = false;
            if (empty($current_eye_meds) && empty($stopped_eye_meds) && is_null($element->no_ophthalmic_medications_date)) {
                $nilRecord = true;
            } elseif (empty($current_eye_meds) && empty($stopped_eye_meds) && !is_null($element->no_ophthalmic_medications_date)) {
                $noPreviousData = true;
            } else {
                if ($current_eye_meds) {
                    $patientData['eyeMedications']['currentEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_eye_meds, true, true, true, true);
                }
                if ($stopped_eye_meds) {
                    $patientData['eyeMedications']['stoppedEyeMedsSize'] = sizeof($stopped_eye_meds);
                    $patientData['eyeMedications']['stoppedEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_eye_meds, false, true, false, true);
                }
            }
            $patientData['eyeMedications']['nilRecord'] = $nilRecord;
            $patientData['eyeMedications']['noPreviousData'] = $noPreviousData;

            $familyHistoryWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\FamilyHistory::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $familyHistoryData = $familyHistoryWidget->getViewData();
            $element = $familyHistoryData['element'];
            if (empty($element->entries) && empty($element->no_family_history_date)) {
                $patientData['familyHistory']['nilRecord'] = true;
            } else {
                $patientData['familyHistory']['noFamilyHistory'] = empty($element->no_family_history_date) && !empty($element->entries);
                if (!empty($element->entries)) {
                    $patientData['familyHistory']['modelName'] = CHtml::modelName($element);
                    foreach ($element->entries as $i => $entry) {
                        $temp = [];
                        $temp['relativeDisplay'] = $entry->displayrelative;
                        $temp['sideDisplay'] = $entry->side->name;
                        $temp['conditionDisplay'] = $entry->displaycondition;
                        $temp['comments'] = $entry->comments;
                        $patientData['familyHistory']['entries'][] = $temp;
                    }
                }
            }

            $socialHistoryWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\SocialHistory::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $socialHistoryData = $socialHistoryWidget->getViewData();
            $element = $socialHistoryData['element'];
            if (!$element || !$element->id) {
                $patientData['socialHistory']['nilRecord'] = true;
            } else {
                if ($element->occupation) {
                    $patientData['socialHistory']['occupation']['label'] = CHtml::encode($element->getAttributeLabel('occupation_id'));
                    $patientData['socialHistory']['occupation']['value'] = \CHtml::encode($element->getDisplayOccupation());
                }
                if ($element->driving_statuses) {
                    $patientData['socialHistory']['drivingStatuses']['label'] = CHtml::encode($element->getAttributeLabel('driving_statuses'));
                    $temp = '';
                    foreach ($element->driving_statuses as $item) {
                        $temp .= $item->name . '<br/>';
                    }
                    $patientData['socialHistory']['drivingStatuses']['value'] = $temp;
                }
                if ($element->smoking_status) {
                    $patientData['socialHistory']['smokingStatus']['label'] = CHtml::encode($element->getAttributeLabel('smoking_status_id'));
                    $patientData['socialHistory']['smokingStatus']['value'] =\CHtml::encode($element->smoking_status->name);
                }
                if ($element->accommodation) {
                    $patientData['socialHistory']['accommodation']['label'] = CHtml::encode($element->getAttributeLabel('accommodation_id'));
                    $patientData['socialHistory']['accommodation']['value'] =\CHtml::encode($element->accommodation->name);
                }
                if ($element->comments) {
                    $patientData['socialHistory']['comments']['label'] = CHtml::encode($element->getAttributeLabel('comments'));
                    $patientData['socialHistory']['comments']['value'] = \CHtml::encode($element->comments);
                }
                if (isset($element->carer)) {
                    $patientData['socialHistory']['carer']['label'] = CHtml::encode($element->getAttributeLabel('carer_id'));
                    $patientData['socialHistory']['carer']['value'] =\CHtml::encode($element->carer);
                }
                if (isset($element->alcohol_intake)) {
                    $patientData['socialHistory']['alcoholIntake']['label'] = CHtml::encode($element->getAttributeLabel('alcohol_intake'));
                    $patientData['socialHistory']['alcoholIntake']['value'] =\CHtml::encode($element->alcohol_intake) . ' units/week';
                }
                if (isset($element->substance_misuse)) {
                    $patientData['socialHistory']['substanceMisuse']['label'] = CHtml::encode($element->getAttributeLabel('substance_misuse'));
                    $patientData['socialHistory']['substanceMisuse']['value'] =\CHtml::encode($element->substance_misuse->name);
                }
            }

            $summaries = $exam_api->getManagementSummaries($patient);
            foreach ($summaries as $summary) {
                $temp = [];
                $temp['service'] = $summary->service;
                $temp['comments'] = $summary->comments;
                $temp['day'] = $summary->date[0];
                $temp['month'] = $summary->date[1];
                $temp['year'] = $summary->date[2];
                $temp['user'] = $summary->user;
                $patientData['managementSummaries'][] = $temp;
            }

            $appointment = $this->createWidget('Appointment', ['patient' => $patient, 'pro_theme' => 'pro-theme', 'is_popup' => true]);

            foreach ($appointment->worklist_patients as $worklistPatient) {
                $temp = [];
                $temp['time'] = date('H:i', strtotime($worklistPatient->when));
                $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
                $temp['name'] = $worklistPatient->worklist->name;
                $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
                $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklistPatient->id]);

                if (isset($worklistStatus)) {
                    $temp['status'] = $worklistStatus->attribute_value;
                } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                    $temp['status'] = 'Did not attend.';
                }
                $patientData['worklistPatients'][] = $temp;
            }
            if ($appointment->past_worklist_patients_count != 0) {
                $patientData['pastWorklistPatientsCount'] = $appointment->past_worklist_patients_count;
                $criteria = new \CDbCriteria();
                $criteria->join = " JOIN worklist w ON w.id = t.worklist_id";
                $start_of_today = date("Y-m-d");
                $criteria->addCondition('t.when < "' . $start_of_today . '"');
                $criteria->order = 't.when desc';

                $past_worklist_patients = WorklistPatient::model()->findAllByAttributes(
                    ['patient_id' => $patientId],
                    $criteria
                );
                foreach ($past_worklist_patients as $worklistPatient) {
                    $temp = [];
                    $temp['time'] = date('H:i', strtotime($worklistPatient->when));
                    $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
                    $temp['name'] = $worklistPatient->worklist->name;
                    $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
                    $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklistPatient->id]);

                    if (isset($worklistStatus)) {
                        $temp['status'] = $worklistStatus->attribute_value;
                    } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                        $temp['status'] = 'Did not attend.';
                    }
                    $patientData['pastWorklistPatients'][] = $temp;
                }
            }

            $plansProblemsWidget = $this->createWidget('application.widgets.PlansProblemsWidget', [
                'patient_id' => $patient->id
            ]);
            foreach ($plansProblemsWidget->current_plans_problems as $planProblem) {
                $temp = [];
                $temp['name'] = $planProblem->name;
                $temp['tooltipContent'] = 'Created: ' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '');
                $temp['id'] = $planProblem->id;
                $temp['currentPlanProblems'][] = $temp;
            }
            if ($plansProblemsWidget->past_plans_problems != 0) {
                foreach ($plansProblemsWidget->past_plans_problems as $planProblem) {
                    $temp = [];
                    $temp['name'] = $planProblem->name;
                    $temp['tooltipContent'] = 'Created:' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '') .
                    '<br /> Closed:' . Helper::convertDate2NHS($planProblem->last_modified_date) . ($planProblem->lastModifiedUser ? ' by ' . $planProblem->lastModifiedUser->getFullNameAndTitle() : '');
                    $temp['id'] = $planProblem->id;
                    $temp['lastModifiedDate'] = \Helper::convertDate2NHS($planProblem->last_modified_date);
                    $temp['pastPlanProblems'][] = $temp;
                }
            }

            if (Yii::app()->getModule('OETrial')) {
                foreach ($patient->trials as $trialPatient) {
                    $temp = [];
                    if (Yii::app()->user->checkAccess('TaskViewTrial')) {
                        $temp['trial'] = CHtml::link(CHtml::encode($trialPatient->trial->name),
                            Yii::app()->controller->createUrl('/OETrial/trial/permissions',
                                array('id' => $trialPatient->trial_id)));
                    } else {
                        $temp['trial'] = CHtml::encode($trialPatient->trial->name);
                    }
                    $temp['date'] = $trialPatient->trial->getStartedDateForDisplay().' - '.$trialPatient->trial->getClosedDateForDisplay();
                    $coordinators = $trialPatient->trial->getTrialStudyCoordinators();
                    if (sizeof($coordinators)) {
                        $studyCoordinators = '';
                        foreach ($coordinators as $item) {
                            $studyCoordinators .= $item->user->getFullName() . "<br />";
                        }
                        $temp['studyCoordinator'] = $studyCoordinators;
                    } else {
                        $temp['studyCoordinator'] = 'N/A';
                    }

                    $temp['treatment'] = $trialPatient->treatmentType->name;
                    $temp['type'] = $trialPatient->trial->trialType->name;
                    $temp['status'] = $trialPatient->status->name;

                    $patientData['currentTrails'][] = $temp;
                }
            }

            $this->renderJSON($patientData);
        }
    }

    protected function patientHistoryMedicationsData($historyMedicationsWidget, $history_meds, $current, $getComments = false, $showLink = false, $getLaterality = false) : array
    {
        $result = [];

        $index = 0;
        foreach ($history_meds as $history_med) {
            $temp = [];
            $temp['index'] = $index;
            $temp['display'] = $history_med->getMedicationDisplay(true);
            if ($getComments) {
                $comments = $history_med->getComments();
                if (!empty($comments)) {
                    $temp['comments'] = $comments;
                }
            }
            if (!empty($history_med->getChangeHistory())) {
                $temp['historyTooltipContent'] = $history_med->getChangeHistoryTooltipContent($history_med->getChangeHistory());
            }
            $info_box = new MedicationInfoBox();
            $info_box->medication_id = $history_med->medication->id;
            $info_box->init();
            $tooltip_content = $history_med->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
            if (!empty($tooltip_content)) {
                $temp['icon'] = $info_box->getIcon();
                $temp['tooltipContent'] = $tooltip_content;
            }
            if ($getLaterality) {
                $laterality = $this->getLaterality(null, $history_med->getLateralityDisplay(), '');
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];
            }
            $temp['date'] = $current ? $history_med->getStartDateDisplay() : $history_med->getEndDateDisplay();

            if ($showLink) {
                if (($history_med->prescription_item_id && isset($history_med->prescription_item->prescription->event))) {
                    $link = $historyMedicationsWidget->getPrescriptionLink($history_med->prescription_item);
                } else {
                    $link = $history_med->isPrescription() ? $historyMedicationsWidget->getPrescriptionLink($history_med) : $historyMedicationsWidget->getExaminationLink($history_med);
                }
                $tooltip_content = 'View' . (strpos(strtolower($link), 'prescription') ? ' prescription' : ' examination');
                $temp['link'] = $link;
                $temp['linkTooltipContent'] = $tooltip_content;
            }

            $result[] = $temp;
        }

        return $result;
    }

    private function getLaterality($eye, $laterality, $pad = 'pad', $size = 'small')
    {
        $show_if_both_eyes_are_null = true;
        $left = false;
        $right = false;
        $return = [];

        if ($eye !== null) {
            $left = $eye->id & Eye::LEFT;
            $right = $eye->id & Eye::RIGHT;
        } else {
            switch (strtolower($laterality)) {
                case 'left':
                case 'l':
                    $left = true;
                    $right = false;
                    break;
                case 'right':
                case 'r':
                    $left = false;
                    $right = true;
                    break;
                case 'b':
                case 'bilateral':
                case 'both':
                    $left = true;
                    $right = true;
                    break;
            }
        }

        if ($show_if_both_eyes_are_null || $right || $left) {
            $return['left'] = $size . ' ' . $pad . ' ' . ($left ? 'L' : 'NA');
            $return['right'] = $size . ' ' . $pad . ' ' . ($right ? 'R' : 'NA');
        }
        return $return;
    }

    public function actionAutoRefresh()
    {
        $date_from = Yii::app()->request->getParam('date_from');
        $date_to = Yii::app()->request->getParam('date_to');
        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null);

        $prescriber_dom_data = $this->prescriberDomData();
        $dom = array();
        $dom['main'] = null;
        $dom['filter'] = "<li><a class='js-worklist-filter' href='#' data-worklist='all'>All</a></li>";
        $dom['popup'] = $prescriber_dom_data['popup'];
        foreach ($worklists as $worklist) {
            $dom['main'] .= $this->renderPartial('_worklist', array('worklist' => $worklist, 'is_prescriber' => $prescriber_dom_data['is_prescriber']), true);
            $dom['filter'] .= "<li><a href='#' class='js-worklist-filter' data-worklist='js-worklist-{$worklist->id}'>{$worklist->name} : {$worklist->getDisplayShortDate()}</a></li>";
        }
        $dom['refresh_time'] = date('H:i');
        $this->renderJSON($dom);
    }
}
