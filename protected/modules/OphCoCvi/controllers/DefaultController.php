<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;

use \OEModule\OphCoCvi\models;
use \OEModule\OphCoCvi\components\OphCoCvi_Manager;
use \OEModule\OphCoCvi\components\LabelManager;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1;

/**
 * Class DefaultController
 *
 * @package OEModule\OphCoCvi\controllers
 */
class DefaultController extends \BaseEventTypeController
{
    public $event_prompt;
    public $cvi_limit = 1;
    protected $cvi_manager;

    public $demographicsData;

    const ACTION_TYPE_LIST = 'List';

    protected static $action_types = array(
        'clinicalInfoDisorderList' => self::ACTION_TYPE_FORM,
        'deleteDiagnosisNotCoveredElement' => self::ACTION_TYPE_FORM,
        'cilinicalDiagnosisAutocomplete' => self::ACTION_TYPE_FORM,
        'getVisualAcuityDatas' => self::ACTION_TYPE_FORM,
        'consentsignature' => self::ACTION_TYPE_EDIT,
        'retrieveconsentsignature' => self::ACTION_TYPE_EDIT,
        'displayconsentsignature' => self::ACTION_TYPE_VIEW,
        'removeconsentsignature' => self::ACTION_TYPE_EDIT,
        'addPatientSignature' => self::ACTION_TYPE_EDIT,
        'issue' => self::ACTION_TYPE_EDIT,
        'signCVI' => self::ACTION_TYPE_EDIT,
        'list' => self::ACTION_TYPE_LIST,
        'export' => self::ACTION_TYPE_LIST,
        'LabelPDFprint' => self::ACTION_TYPE_VIEW,
        'signConsultantSignatureElement' => self::ACTION_TYPE_VIEW,
        'printEmptyConsent' => self::ACTION_TYPE_VIEW,
        'printConsent' => self::ACTION_TYPE_VIEW,
        'printInfoSheet' => self::ACTION_TYPE_VIEW,
        'printVisualyImpaired' => self::ACTION_TYPE_VIEW,
        'printQRSignature' => self::ACTION_TYPE_FORM,
    );

    public function actionPrintQRSignature($event_id, $element_id, $element_type_id)
    {
        $this->initWithEventId($event_id);

        $this->layout = '//layouts/print';

        $auto_print = \Yii::app()->request->getParam('auto_print', true);
        $inject_autoprint_js = $auto_print == "0" ? false : $auto_print;

        /**
         * we could use \Yii::app()->moduleAPI->get('OphCoCvi')->getUniqueCodeForCviEvent($this->event);
         * but on API side we don't know the event type.
         * In API event and event_type is determinated from the uniqe code : $uniq_code->eventFromUniqueCode($code);
         * Solution would be to pass the event id or event_type as well.
         * But keep in mind, we need to keep the QR code data sort to make sure the JS QR reader can read out the data
         */
        $unique_code = \Patient::model()->getUniqueCodeForEvent($event_id);

        $qr_code_data =
            "@code:" . $unique_code .
            "@key:" . "1" .
            "@x_i:" . json_encode(['e_id' => $element_id, 'e_t_id' => $element_type_id]);

        //$html = $this->renderPartial("application.modules.OphTrConsent.views.default.print_PatientConsentShareInformation", [
        $html = $this->renderPartial("print_Element_OphCoCvi_ConsentSignature", [
            'qr_code_data' => $qr_code_data,
            'event' => $this->event,
            'patient' => $this->patient,
            'qr_code_data' => $qr_code_data
        ],
            true
        );

        $wk = new \WKHtmlToPDF();
        $wk->setCanvasImagePath($this->event->imageDirectory);
        $wk->setPatient($this->event->episode->patient);
        $wk->setBarcode('');
        $wk->setDocref('');

        $wk->setCssUrl($this->assetPath,'print.css');
        $wk->setMarginLeft('10mm');
        $wk->setMarginRight('10mm');
        $wk->setMarginBottom('35mm');

        $wk->generatePDF($this->event->imageDirectory, 'event', $this->pdf_print_suffix, $html, (boolean) @$_GET['html'], $inject_autoprint_js, null, null, false);
        $pdf = $this->event->getPDF($this->pdf_print_suffix);

        header('Content-Type: application/pdf');
        header('Content-Length: '.filesize($pdf));

        readfile($pdf);
        unlink($pdf);
    }

    /** @var string label used in session storage for the list filter values */
    protected static $FILTER_LIST_KEY = 'OphCoCvi_list_filter';

    /**
     * Create Form with check for the cvi existing events count
     *
     * @throws \Exception
     */
    public function actionCreate()
    {
//        echo '<pre>' . print_r($_POST, true) . '</pre>';
//        die(__FILE__ . " :: " . __LINE__);

        $create_new_cvi = $this->request->getParam('createnewcvi', null);
        if ($create_new_cvi !== null) {
            $cancel_url = $this->episode ? '/patient/episode/' . $this->episode->id
                : '/patient/episodes/' . $this->patient->id;
            if ($create_new_cvi == 1) {
                if (!$this->getManager()->canCreateEventForPatient($this->patient)) {
                    $this->getApp()->user->setFlash('warning.cvi_create', 'You cannot create another CVI whilst one exists that has not been issued.');
                    $this->redirect(array($cancel_url));
                } else {
                    parent::actionCreate();
                }
            } else {
                $this->redirect(array($cancel_url));
            }
        } else {
            $cvi_events = $this->getApp()->moduleAPI->get('OphCoCvi');
            $current_cvis = $cvi_events->getEvents($this->patient);
            if (count($current_cvis) >= $this->cvi_limit) {
                $this->render('select_event', array(
                    'current_cvis' => $current_cvis,
                    'can_create' => $this->getManager()->canCreateEventForPatient($this->patient),
                ), false);
            } else {
                parent::actionCreate();
            }
        }
    }

    /**
     * Currently uses the OprnEditCvi operation to check for access
     *
     * @return boolean
     */
    public function checkListAccess()
    {
        return $this->checkAccess('OprnEditCvi', $this->getApp()->user->id);
    }

    /**
     * This is a granular permission check, and should be used in conjunection with checkEditAcess
     *
     * @return boolean
     */
    public function checkClericalEditAccess()
    {
        if ($this->checkAdminAccess()) {
            //TODO: consider encapsulating this in biz rule for edit clerical
            return true;
        }

        return $this->checkAccess('OprnEditClericalCvi', $this->getApp()->user->id);
    }

    /**
     * This is a granular permission check, and should be used in conjunection with checkEditAcess
     *
     * @return boolean
     */
    public function checkClinicalEditAccess()
    {
        return $this->checkAccess('OprnEditClinicalCvi', $this->getApp()->user->id) && $this->checkEditAccess();
    }

    /**
     * @return bool
     */
    public function checkEditAccess()
    {
        if ($this->event->isNewRecord) {
            // because we are using this check for clinical edit access checks, we need to handle new events as well
            return $this->checkCreateAccess();
        } else {
            return !$this->getManager()->isIssued($this->event) && $this->checkAccess('OprnEditCvi',
                    $this->getApp()->user->id, array(
                        'firm' => $this->firm,
                        'event' => $this->event,
                    ));
                    /* Only events of the current version are editable */
                    //&& $this->event->version == $this->event->eventType->version;
        }
    }

    /**
     * @return bool
     */
    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnCreateCvi', $this->getApp()->user->id, array(
            'firm' => $this->firm,
            'episode' => $this->episode,
        ));
    }

    /**
     * @return bool
     */
    public function checkPrintAccess()
    {
        if (!$this->getManager()->isIssued($this->event)) {
            return false;
        }

        if ($this->checkAdminAccess()) {
            return true;
        }

        // check that the user has the general edit cvi permission, but not the specific edit permission on
        // the current event.
        return $this->checkAccess('OprnEditCvi', $this->getApp()->user->id);
    }

    /**
     * Ensure we invoke the CVI RBAC rules around requesting deletion.
     *
     * @return bool
     */
    public function checkRequestDeleteAccess()
    {
        return $this->checkEditAccess() && parent::checkRequestDeleteAccess();
    }

    /**
     * Override as the optional elements should not be rendered until completed through the
     * appropriate access levels.
     *
     * @return null
     */
    public function getOptionalElements()
    {
        return [];
    }

    /**
     * Override because we don't want elements removed from the UI if we have rendered them
     * Optionality is in place to support granular permission structure.
     *
     * @param \BaseEventTypeElement $element
     * @return bool
     */
    public function isRequiredInUI(\BaseEventTypeElement $element)
    {
        return true;
    }

    /**
     * Determine if the current event can be issued
     *
     * @return bool
     */
    public function canIssue()
    {
        if ($this->checkEditAccess()) {
            return $this->getManager()->canIssueCvi($this->event);
        } else {
            return false;
        }
    }

    protected function setElementDefaultOptions_Element_OphCoCvi_EventInfo(
        models\Element_OphCoCvi_EventInfo $element,
        $action
    )
    {
        if ($element->isNewRecord) {
            $element->site_id = $this->getApp()->session['selected_site_id'];
        }
    }
    
    protected function setElementDefaultOptions_Element_OphCoCvi_EventInfo_V1(
        models\Element_OphCoCvi_EventInfo_V1 $element,
        $action
    )
    {
        if ($element->isNewRecord) {
            $element->site_id = $this->getApp()->session['selected_site_id'];
        }
    }

    /**
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param                                      $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClinicalInfo(
        models\Element_OphCoCvi_ClinicalInfo $element,
        $action
    )
    {
        // only populate values into the new element if a clinical user
        if ($element->isNewRecord && $this->checkClinicalEditAccess()) {
            if ($exam_api = $this->getApp()->moduleAPI->get('OphCiExamination')) {
                if ($latest_exam = $exam_api->getMostRecentVAElementForPatient($this->patient)) {
                    // $element->examination_date = $latest_exam['event_date'];
                    // $element->best_corrected_right_va = $exam_api->getMostRecentVAForPatient($this->patient, 'right',
                    //    'aided', $latest_exam['element']);
                    // $element->best_corrected_left_va = $exam_api->getMostRecentVAForPatient($this->patient, 'left',
                    //    'aided', $latest_exam['element']);
                    // $element->unaided_right_va = $exam_api->getMostRecentVAForPatient($this->patient, 'right',
                    //    'unaided',
                    //    $latest_exam['element']);
                    // $element->unaided_left_va = $exam_api->getMostRecentVAForPatient($this->patient, 'left', 'unaided',
                    //    $latest_exam['element']);
                }
            }
            $cvi_disorders = models\OphCoCvi_ClinicalInfo_Disorder::model()->active()->findAll();
            $cvi_ids_by_disorder_id = array();
            foreach ($cvi_disorders as $cvid) {
                if ($disorder_id = $cvid->disorder_id) {
                    $cvi_ids_by_disorder_id[$disorder_id] = $cvid->id;
                }
            }

            if (count($cvi_ids_by_disorder_id)) {
                foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
                    $assignments = array();
                    foreach ($this->patient->getAllDisorders($eye_id) as $patient_disorder) {
                        if (array_key_exists($patient_disorder->id, $cvi_ids_by_disorder_id)) {
                            $cvi_ass = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
                            $cvi_ass->ophcocvi_clinicinfo_disorder_id = $cvi_ids_by_disorder_id[$patient_disorder->id];
                            $cvi_ass->affected = true;
                            $assignments[] = $cvi_ass;
                        }
                    }
                    $element->{$side . '_cvi_disorder_assignments'} = $assignments;
                }
            }
        }
    }

    /**
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param                                      $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClinicalInfo_V1(
        models\Element_OphCoCvi_ClinicalInfo_V1 $element,
        $action
    )
    {
        // only populate values into the new element if a clinical user
        if ($element->isNewRecord && $this->checkClinicalEditAccess()) {
            $cvi_disorders = models\OphCoCvi_ClinicalInfo_Disorder::model()->active()->findAll();
            $cvi_ids_by_disorder_id = array();
            foreach ($cvi_disorders as $cvid) {
                if ($disorder_id = $cvid->disorder_id) {
                    $cvi_ids_by_disorder_id[$disorder_id] = $cvid->id;
                }
            }

            if (count($cvi_ids_by_disorder_id)) {
                foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
                    $assignments = array();
                    foreach ($this->patient->getAllDisorders($eye_id) as $patient_disorder) {
                        if (array_key_exists($patient_disorder->id, $cvi_ids_by_disorder_id)) {
                            $cvi_ass = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
                            $cvi_ass->ophcocvi_clinicinfo_disorder_id = $cvi_ids_by_disorder_id[$patient_disorder->id];
                            $cvi_ass->affected = true;
                            $assignments[] = $cvi_ass;
                        }
                    }
                    $element->{$side . '_cvi_disorder_assignments'} = $assignments;
                }
            }
            $element->patient_type = ($this->getGetPatientAge() < 18) ? 1 : 0;
        }
    }

    protected function setElementDefaultOptions_Element_OphCoCvi_PatientSignature(models\Element_OphCoCvi_PatientSignature $element, $action)
    {
        $element->setDefaultOptions();
    }

    /**
     * <<<  * This just sets assignments for validation and the re-use in forms if a form submit does not validate
     *
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param                                      $data
     * @param                                      $index
     */
    protected function setComplexAttributes_Element_OphCoCvi_ClinicalInfo($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        if (array_key_exists($model_name, $data)) {
            foreach (array('left', 'right') as $side) {
                $cvi_assignments = array();
                $key = $side . '_disorders';
                if (array_key_exists($key, $data[$model_name])) {
                    foreach ($data[$model_name][$key] as $idx => $data_disorder) {
                        $cvi_ass = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
                        $cvi_ass->ophcocvi_clinicinfo_disorder_id = $idx;
                        $cvi_ass->affected = array_key_exists('affected',
                            $data_disorder) ? $data_disorder['affected'] : false;
                        $cvi_ass->main_cause = array_key_exists('main_cause',
                            $data_disorder) ? $data_disorder['main_cause'] : false;
                        $cvi_assignments[] = $cvi_ass;
                    }
                }
                $element->{$side . '_cvi_disorder_assignments'} = $cvi_assignments;
            }
            $comments = array();
            if (array_key_exists('cvi_disorder_section', $data[$model_name])) {
                foreach ($data[$model_name]['cvi_disorder_section'] as $id => $data_comments) {
                    $section_comment = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments();
                    $section_comment->ophcocvi_clinicinfo_disorder_section_id = $id;
                    $section_comment->comments = $data_comments['comments'];
                    $comments[] = $section_comment;
                }
            }
            $element->cvi_disorder_section_comments = $comments;
        }

    }
    /**
     * <<<  * This just sets assignments for validation and the re-use in forms if a form submit does not validate
     *
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param                                      $data
     * @param                                      $index
     */
    protected function setComplexAttributes_Element_OphCoCvi_ClinicalInfo_V1($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);

        if (array_key_exists($model_name, $data)) {

                foreach ($data[$model_name]['disorders'] ?? [] as $key => $disorder) {
                    $is_right = isset($disorder['right_eye']) && $disorder['right_eye'] == 1;
                    $is_left = isset($disorder['left_eye']) && $disorder['left_eye'] == 1;
                    $is_both = ($is_right && $is_left);

                    if ($is_both) {
                        $affected = \Eye::BOTH;
                    } else {
                        $affected = $is_right ? \Eye::RIGHT : ($is_left ? \Eye::LEFT : null);
                    }

                    switch ($affected) {
                        case 1:
                            $disorders['left_disorders'][$key]['affected'] = 1;
                            if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                                $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                            }
                            break;
                        case 2:
                            $disorders['right_disorders'][$key]['affected'] = 1;
                            if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                                $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                            }
                            break;
                        case 3:
                            $disorders['both_disorders'][$key]['affected'] = 1;
                            if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                                $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                            }
                            break;
                    }

                unset($data[$model_name]['disorders']);
                $data[$model_name] = array_merge($data[$model_name], $disorders);
            }

            if (!empty($data[$model_name]['diagnosis_not_covered'])) {
                $diagnosis_list = array();
                foreach ($data[$model_name]['diagnosis_not_covered'] as $key => $disorder) {
                    $diagnosis = new OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered();
                    $diagnosis->disorder_id = isset($disorder['disorder_id']) ? $disorder['disorder_id'] : false;
                    $diagnosis->eye_id = isset($disorder['eyes']) ? $disorder['eyes'] : false;
                    $diagnosis->main_cause = isset($disorder['main_cause']) ? $disorder['main_cause'] : false;
                    $diagnosis->disorder_type = isset($disorder['disorder_type']) ? $disorder['disorder_type'] : false;
                    if ($diagnosis->disorder_type == OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_DISORDER) {
                        $diagnosis->code = isset($disorder['code']) ? $disorder['code'] : false;
                    }
                    $diagnosis_list[] = $diagnosis;
                }
                $element->diagnosis_not_covered = $diagnosis_list;
            }

            $all_cvi_assignments = array();
            $main_cause_assignment = array();
            foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT, 'both' => \Eye::BOTH) as $side => $eye_id) {
                $cvi_assignments = array();
                $key = $side . '_disorders';
                if (array_key_exists($key, $data[$model_name])) {
                    foreach ($data[$model_name][$key] as $idx => $data_disorder) {
                        $cvi_ass = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
                        $cvi_ass->ophcocvi_clinicinfo_disorder_id = $idx;
                        $cvi_ass->affected = array_key_exists('affected',
                            $data_disorder) ? $data_disorder['affected'] : false;
                        $cvi_ass->eye_id = $eye_id;
                        $cvi_ass->main_cause = array_key_exists('main_cause',
                            $data_disorder) ? $data_disorder['main_cause'] : false;
                        if ($cvi_ass->main_cause == 1) {
                            $main_cause_assignment[] = $cvi_ass;
                        }
                        $cvi_assignments[] = $cvi_ass;
                        $all_cvi_assignments[] = $cvi_ass;
                    }
                }
                if (!empty($main_cause_assignment)) {
                    $element->main_cause_cvi_disorder_assignment = $main_cause_assignment;
                }
                $element->{$side . '_cvi_disorder_assignments'} = $cvi_assignments;
                $element->cvi_disorder_assignments = $all_cvi_assignments;
            }

            if (!isset($data[$model_name]['best_recorded_left_va'])) {
                $element->best_recorded_left_va = 0;
            }
            if (!isset($data[$model_name]['best_recorded_right_va'])) {
                $element->best_recorded_right_va = 0;
            }
            if (!isset($data[$model_name]['best_recorded_binocular_va'])) {
                $element->best_recorded_binocular_va = 0;
            }

        }
    }

    /**
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param                                      $data
     * @param                                      $index
     * @throws \Exception
     */
    protected function saveComplexAttributes_Element_OphCoCvi_ClinicalInfo(
        models\Element_OphCoCvi_ClinicalInfo $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        foreach (array('left', 'right') as $side) {
            $key = $side . '_disorders';
            $side_data = array_key_exists($key, $data[$model_name]) ? $data[$model_name][$key] : array();
            $element->updateDisorders($side, $side_data);
        }
        $comments_data = array_key_exists('cvi_disorder_section',
            $data[$model_name]) ? $data[$model_name]['cvi_disorder_section'] : array();
        $element->updateDisorderSectionComments($comments_data);
    }

    protected function saveComplexAttributes_Element_OphCoCvi_ClinicalInfo_V1(
        models\Element_OphCoCvi_ClinicalInfo_V1 $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);

        if (!$element->isNewRecord) {
            OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::model()->deleteAllByAttributes(['element_id' => $element->id]);
        }
        if (!empty($data[$model_name]['diagnosis_not_covered'])) {
            $element->updateDisordersNotCovered($data[$model_name]['diagnosis_not_covered']);
        }

        $disorders = array();
        if (!empty($data[$model_name]['disorders'])) {
            foreach ($data[$model_name]['disorders'] as $key => $disorder) {
                $is_right = isset($disorder['right_eye']) && $disorder['right_eye'] == 1;
                $is_left = isset($disorder['left_eye']) && $disorder['left_eye'] == 1;
                $is_both = ($is_right && $is_left);

                if ($is_both) {
                    $affected = \Eye::BOTH;
                } else {
                    $affected = $is_right ? \Eye::RIGHT : ($is_left ? \Eye::LEFT : null);
                }

                switch ($affected) {
                    case 1:
                        $disorders['left_disorders'][$key]['affected'] = 1;
                        if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                            $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                        }
                        break;
                    case 2:
                        $disorders['right_disorders'][$key]['affected'] = 1;
                        if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                            $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                        }
                        break;
                    case 3:
                        $disorders['both_disorders'][$key]['affected'] = 1;
                        if (isset($data[$model_name]['right_disorders'][$key]['main_cause'])) {
                            $disorders['right_disorders'][$key]['main_cause'] = $data[$model_name]['right_disorders'][$key]['main_cause'];
                        }
                        break;
                }
            }
            unset($data[$model_name]['disorders']);
            $data[$model_name] = array_merge($data[$model_name], $disorders);

            foreach (array('left', 'right', 'both') as $side) {
                $key = $side . '_disorders';
                $side_data = array_key_exists($key, $data[$model_name]) ? $data[$model_name][$key] : array();
                $element->updateDisorders($side, $side_data);
            }
        } else {
            if (!$element->isNewRecord) {
                models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->deleteAllByAttributes(['element_id' => $element->id]);
            }
        }

    }

    /**
     * @param models\Element_OphCoCvi_ClericalInfo $element
     * @param                                      $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClericalInfo(
        models\Element_OphCoCvi_ClericalInfo $element,
        $action
    ) {
        if ($element->isNewRecord && $this->checkClinicalEditAccess()) {
            if ($this->patient->isChild()) {
                $element->employment_status_id = models\OphCoCvi_ClericalInfo_EmploymentStatus::defaultChildStatusId();
            } elseif ($this->patient->socialhistory) {
                $element->employment_status_id = models\OphCoCvi_ClericalInfo_EmploymentStatus::defaultForSocialHistoryId($this->patient->socialhistory);
            }
        }
    }
    
    /**
     * @param models\Element_OphCoCvi_ClericalInfo $element
     * @param                                      $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClericalInfo_V1(
        models\Element_OphCoCvi_ClericalInfo_V1 $element,
        $action
    ) {
        if ($element->isNewRecord && $this->checkClinicalEditAccess()) {
            if ($this->patient->isChild()) {
                $element->employment_status_id = models\OphCoCvi_ClericalInfo_EmploymentStatus::defaultChildStatusId();
            } elseif ($this->patient->socialhistory) {
                $element->employment_status_id = models\OphCoCvi_ClericalInfo_EmploymentStatus::defaultForSocialHistoryId($this->patient->socialhistory);
            }
        }
        $preferred_language = \Language::model()->findByAttributes(array('pas_term'=>'eng'));
        $element->preferred_language_id = $element->preferred_language_id ? $element->preferred_language_id : $preferred_language->id;
    }

    /**
     * @param models\Element_OphCoCvi_ClericalInfo $element
     * @param                                      $data
     * @param                                      $index
     */
    protected function setComplexAttributes_Element_OphCoCvi_ClericalInfo(
        models\Element_OphCoCvi_ClericalInfo $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);

        if (array_key_exists($model_name, $data)) {
            $answers = array();
            if (array_key_exists('patient_factors', $data[$model_name])) {
                foreach ($data[$model_name]['patient_factors'] as $id => $data_answer) {
                    $a = new models\OphCoCvi_ClericalInfo_PatientFactor_Answer();
                    $a->patient_factor_id = $id;
                    $a->is_factor = isset($data_answer['is_factor']) ? $data_answer['is_factor'] : null;
                    $a->comments = isset($data_answer['comments']) ? $data_answer['comments'] : null;
                    $answers[] = $a;
                }
            }
            $element->patient_factor_answers = $answers;
        }
    }
    
    protected function setComplexAttributes_Element_OphCoCvi_ClericalInfo_V1(
        models\Element_OphCoCvi_ClericalInfo_V1 $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);

        if (array_key_exists($model_name, $data)) {
            $answers = array();
            if (array_key_exists('patient_factors', $data[$model_name])) {
                foreach ($data[$model_name]['patient_factors'] as $id => $data_answer) {
                    $a = new models\OphCoCvi_ClericalInfo_PatientFactor_Answer();
                    $a->patient_factor_id = $id;
                    $a->is_factor = isset($data_answer['is_factor']) ? $data_answer['is_factor'] : null;
                    $a->comments = isset($data_answer['comments']) ? $data_answer['comments'] : null;
                    $answers[] = $a;
                }
            }
            $element->patient_factor_answers = $answers;

            if (array_key_exists('preferred_format_ids', $data[$model_name])) {
                if (!empty($data[$model_name]['preferred_format_ids'])) {
                    foreach ($data[$model_name]['preferred_format_ids'] as $id => $data_preferred_format_id) {
                        $assignment = new models\OphCoCvi_Clericalinfo_Preferredformat_Assignment();
                        $assignment->element_id = $this->id;
                        $assignment->preferred_format_id = $data_preferred_format_id;
                        $preferred_format_ids[] = $assignment;
                    }
                    $element->preferred_format_assignments = $preferred_format_ids;
                }
            }
        }
    }

    /**
     * @param models\Element_OphCoCvi_ClericalInfo $element
     * @param                                      $data
     * @param                                      $index
     * @throws \Exception
     */
    public function saveComplexAttributes_Element_OphCoCvi_ClericalInfo(
        models\Element_OphCoCvi_ClericalInfo $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        if (array_key_exists($model_name, $data)) {
            $answer_data = array_key_exists('patient_factors', $data[$model_name]) ? $data[$model_name]['patient_factors'] : array();
            $element->updatePatientFactorAnswers($answer_data);
        }

    }
    
    public function saveComplexAttributes_Element_OphCoCvi_ClericalInfo_V1(
        models\Element_OphCoCvi_ClericalInfo_V1 $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        if (array_key_exists($model_name, $data)) {
            $answer_data = array_key_exists('patient_factors', $data[$model_name]) ? $data[$model_name]['patient_factors'] : array();
            $element->updatePatientFactorAnswers($answer_data);
            $preferred_format_datas = array_key_exists('preferred_format_ids', $data[$model_name]) ? $data[$model_name]['preferred_format_ids'] : array();
            $element->updatePreferredFormats($preferred_format_datas);
        }

    }

    /**
     * Sets the default values for the element from the patient
     *
     * @param models\Element_OphCoCvi_Demographics $element
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_Demographics(
        models\Element_OphCoCvi_Demographics $element
    ) {
        if ($element->isNewRecord) {
            $element->initFromPatient($this->patient);
        }
    }
    
    /**
     * Sets the default values for the element from the patient
     *
     * @param models\Element_OphCoCvi_Demographics $element
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_Demographics_V1(
        models\Element_OphCoCvi_Demographics_V1 $element
    ) {
        if ($element->isNewRecord) {
            $element->initFromPatient($this->patient);
        }
    }

    /**
     * @return OphCoCvi_Manager
     */
    public function getManager()
    {
        if (is_null($this->cvi_manager)) {
            $this->cvi_manager = new OphCoCvi_Manager($this->getApp());
        }

        return $this->cvi_manager;
    }

    /**
     * @var bool internal flag to indicate a filter has been applied on the list view
     */
    private $is_list_filtered = false;

    /**
     * @return bool
     */
    public function isListFiltered()
    {
        return $this->is_list_filtered;
    }

    /**
     *
     * @return array
     */
    protected function getListFilter()
    {
        $filter = array();

        // if POST, then a new filter is to be applied, otherwise retrieve from the session
        if ($this->request->isPostRequest) {
            foreach (array('date_from', 'date_to', 'subspecialty_id', 'site_id', 'consultant_ids', 'firm_ids', 'show_issued', 'issue_status', 'issue_complete', 'issue_incomplete', 'createdby_ids', 'missing_consultant_signature','missing_clerical_part') as $key) {
                $val = $this->request->getPost($key, null);
                $filter[$key] = $val;
            }
        } else {
            if ($session_filter = $this->getApp()->session[static::$FILTER_LIST_KEY]) {
                $filter = $session_filter;
            }
        }
        
        // set the is filtered flag for the controller
        foreach ($filter as $val) {
            if ($val) {
                $this->is_list_filtered = true;
                break;
            }
        }

        // store filter for later use
        $this->getApp()->session[static::$FILTER_LIST_KEY] = $filter;

        return $filter;
    }

    /**
     * Generate a list of all the CVI events for clerical use.
     */
    public function actionList()
    {
        $this->layout = '//layouts/main';
        $this->renderPatientPanel = false;

        $filter = $this->getListFilter();

        $dp = $this->getManager()->getListDataProvider($filter);
       
        $this->render('list', array('dp' => $dp, 'list_filter' => $filter));
    }
    
    /**
     * Export and download CVI list result to csv file
     */
    public function actionExport()
    {
        $filename = 'export.csv';

        $f = fopen( $this->getManager()->outDir.$filename , "w");
     
        $headers = array( array('Event Date', 'Subspeciality', 'Site', 'Name', 'Hospital No.', 'Created By', 'Consultant signed by', 'Consultant in charge', 'Status', 'Issue Date'));
        foreach ($headers as $header) {
            fputcsv($f, $header, ",");
        }

        $filters = $this->getListFilter();
        $dataProvider = $this->getManager()->getListDataProvider($filters , false)->getData();
       
        if($dataProvider){
           
            $rows = [];
            foreach($dataProvider as $originalRow){
                
                if ($consultant = $this->getManager()->getClinicalConsultant($originalRow)) {
                    $consultant = $consultant->getFullNameAndTitle();      
                } else {
                    $consultant = "-";
                }
                
                $inCharge = "-";
                if (!is_null($originalRow->consultant_in_charge_of_this_cvi_id)) {
                    $inCharge = $originalRow->consultantInChargeOfThisCvi->getNameAndSubspecialty();
                }
                
                $date = $originalRow->getIssueDateForDisplay();
                $issueDate = "-";
                if ($date) {
                    $issueDate = \Helper::convertMySQL2NHS($date);
                }
               
                $rows[] = [
                    \Helper::convertMySQL2NHS($originalRow->event->event_date),
                    $originalRow->event->episode->getSubspecialtyText(),
                    $originalRow->site ? $originalRow->site->name : "-",
                    $originalRow->event->episode->patient->getFullName() . " (" . $originalRow->event->episode->patient->getAge() . "y)",
                    $originalRow->event->episode->patient->hos_num,
                    $originalRow->user->getFullNameAndTitle(),
                    $consultant,
                    $inCharge,
                    $originalRow->event->info,
                    $issueDate
                ];
            }

            foreach ($rows as $row) {
                fputcsv($f, $row, ",");
            }
        }

        fclose($f);
        
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        readfile( $this->getManager()->outDir.$filename );
        
        unlink( $this->getManager()->outDir.$filename );
    }

    /**
     * @throws \CHttpException
     */
    public function initActionIssue()
    {
        $this->initWithEventId($this->request->getParam('id'));
        if (!$this->canIssue()) {
            throw new \CHttpException(403, 'Event cannot be issued.');
        }
    }

    /**
     * @param $id
     */
    public function actionIssue($id)
    {
        if ($this->getManager()->issueCvi($this->event, $this->getApp()->user->id)) {
            $this->getApp()->user->setFlash('success.cvi_issue', 'The CVI has been successfully generated.');
            //$this->redirect(array('/' . $this->event->eventType->class_name . '/default/pdfPrint/' . $id));
            $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id . '?print=1'));
        } else {
            $this->getApp()->user->setFlash('error.cvi_issue', 'The CVI could not be generated.');
            $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id));
        }


    }

    /**
     * Sets the title for the event display.
     */
    public function initActionView()
    {
        parent::initActionView();
        $this->setTitle($this->getManager()->getTitle($this->event));
        $this->jsVars['cvi_print_url'] = $this->getApp()->createUrl($this->getModule()->name . '/default/PDFprint/' . $this->event->id);
        $this->jsVars['label_print_url'] = $this->getApp()->createUrl($this->getModule()->name . '/default/LabelPDFprint/' . $this->event->id);
        if ($this->getApp()->request->getParam('print', null) == 1) {
            $this->jsVars['cvi_do_print'] = 1;
        }
    }

    /**
     * Sister method to the getElementsForEventType method, this loads up event elements for rendering (whether viewing or editing).
     * Because of the permissioning behaviours, need to be able to filter out clinical/clerical elements as appropriate.
     *
     * @return array
     */
    protected function getEventElements()
    {
        if ($this->event && !$this->event->isNewRecord) {
            $for_edit = in_array(strtolower($this->action->id), array('create', 'update'));
            $elements = $this->getManager()->getEventElements($this->event, $for_edit);
        } else {
            $elements = $this->event_type->getDefaultElements();
        }
 
        $final_elements = array();
        foreach ($elements as $el) {
            $cls = get_class($el);
            
            if (in_array($cls, array('OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo', 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1'))
                && $el->isNewRecord
                && !$this->checkClinicalEditAccess()
            ) {
                // implies no values have been recorded yet for this element
                continue;
            }
            if (in_array($cls, array('OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo', 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1'))
                && $el->isNewRecord
                && !$this->checkClericalEditAccess()
            ) {
                continue;
            }

            $final_elements[] = $el;
        }

        return $final_elements;
    }

    /**
     * @return models\Element_OphCoCvi_EventInfo[]
     */
    private function getElementsForEventInfo()
    {
        if ($this->event->isNewRecord) {
            return array(new models\Element_OphCoCvi_EventInfo());
        } else {
            return array($this->getManager()->getEventInfoElementForEvent($this->event));
        }
    }

    /**
     * Because form elements won't be submitted when editing without this access, we need to return the current
     * event element if it exists
     *
     * @return models\Element_OphCoCvi_ClinicalInfo[]|bool|null
     */
    private function getElementsForClinical()
    {
        if (!$this->checkClinicalEditAccess()) {
            $el = $this->event->isNewRecord ? null : $this->getManager()->getClinicalElementForEvent($this->event);

            return (!is_null($el)) ? array($el) : null;
        }

        return false;
    }

    /**
     * Because form elements won't be submitted when editing without this access, we need to return the current
     * event element if it exists
     *
     * @return models\Element_OphCoCvi_ClericalInfo|bool|null
     */
    private function getElementsForClerical()
    {
        if (!$this->checkClericalEditAccess()) {
            $el = $this->event->isNewRecord ? null : $this->getManager()->getClericalElementForEvent($this->event);

            return (!is_null($el)) ? array($el) : null;
        }

        return false;
    }

    /**
     * Override to support the fact that users might not have permission to edit specific event elements.
     *
     * @param \ElementType $element_type
     * @param              $data
     * @return array
     * @throws \Exception
     */
    protected function getElementsForElementType(\ElementType $element_type, $data)
    {
        $cls = $element_type->class_name;

        $map = array(
            'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo' => 'Clinical',
            'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo' => 'Clerical',
            'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1' => 'Clinical',
            'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1' => 'Clerical',
        );

        if (array_key_exists($cls, $map)) {
            $id = $map[$cls];
            $override = $this->{"getElementsFor{$id}"}();
            if ($override !== false) {
                return $override;
            }
        }

        return parent::getElementsForElementType($element_type, $data);
    }

    /**
     * We set the validation scenario for the models based on whether the user is saving as draft or performing a full save
     *
     * @TODO extend this behaviour so that user can specify they are only interested in validating a specific section.
     * @param $element
     */
    protected function setValidationScenarioForElement($element)
    {
        if ($this->request->getPost('save', null)) {
            // form has been submitted using the save button, so full validation rules should be applied to the elements
            // TODO: validation for signature(s)
            switch (get_class($element)) {
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1':
                    if ($this->checkClinicalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1':
                    if ($this->checkClericalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics_V1':
                    $element->setScenario('finalise');
                    break;
            }
        }
    }

    /**
     * Use the manager status for the event info text.
     *
     * @throws \Exception
     */
    protected function updateEventInfo()
    {
        $this->getManager()->updateEventInfo($this->event);
        if($this->action->id === "create") {
            $this->getManager()->sendNotification($this->event);
        }
    }
    
    /**
     * @throws \CHttpException
     */
    public function initActionAddPatientSignature()
    {
        $element_id = \Yii::app()->request->getPost("element_id");

        if($element = models\Element_OphCoCvi_PatientSignature::model()->findByPk($element_id)) {
           $this->initWithEventId($element->event_id);
        }
    }
    /*
     * Save patient signature in view mode
     *
     * @throws \Exception
     */

    public function actionAddPatientSignature()
    {
        $element_id = \Yii::app()->request->getPost("element_id");
        $protected_file_id = \Yii::app()->request->getPost("protected_file_id");

        if(!$protected_file = \ProtectedFile::model()->findByPk($protected_file_id)) {
            throw new \CHttpException(404);
        }

        if($element =  models\Element_OphCoCvi_PatientSignature::model()->findByPk($element_id)) {
            $element->protected_file_id = $protected_file_id;
            $element->signature_date = $protected_file->created_date;
            if(!$element->save(false)) {
                throw new \Exception("Could not add signature: ".print_r($element->errors, true));
            }
            
            $this->updateEventInfo();
            echo $protected_file_id;
        }
        
        echo "0";
    }

    public function actionSignConsultantSignatureElement($element_id, $element_type_id, $user_id)
    {
        $signature = parent::signConsultantSignature($element_id, $element_type_id, $user_id);

        if((int)$signature > 0){
            $element_id = \Yii::app()->request->getParam("element_id");
            if($element = models\Element_OphCoCvi_ConsultantSignature::model()->findByPk($element_id)) {
                $this->initWithEventId($element->event_id);   
                $this->updateEventInfo();
            }
        }
        echo $signature;
    }
   
    /**
     * @throws \CHttpException
     */
    public function initActionConsentSignature()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * Generate a version of the certificate for signing by the patient/representative for consent.
     *
     * @param $id
     */
    public function actionConsentSignature($id)
    {
        $pdf = $this->getManager()->generateConsentForm($this->event);
        $pdf->getPDF();
    }

    /**
     * @throws \CHttpException
     */
    public function initActionRemoveConsentSignature()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @param $id
     * @throws \CHttpException
     */
    public function actionRemoveConsentSignature($id)
    {
        $signature_file_id = (int)$this->request->getParam('signature_file_id');
        if ($signature_file_id) {
            $user = \User::model()->findByPk($this->getApp()->user->id);
            if ($this->getManager()->removeConsentSignature($this->event, $user, $signature_file_id)) {
                $this->getApp()->user->setFlash('success.cvi_consent_signature', 'Consent Signature removed.');
            } else {
                $this->getApp()->user->setFlash('error.cvi_consent_signature', 'Could not remove the consent signature.');
            }
        } else {
            throw new \CHttpException(403, 'Invalid Request');
        }


        $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id));
    }

    /**
     * @throws \CHttpException
     */
    public function initActionRetrieveConsentSignature()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @TODO: refactor
     * @param $id
     *
     * @throws \Exception
     */
    public function actionRetrieveConsentSignature($id)
    {
        $signature_element = $this->getManager()->getConsentSignatureElementForEvent($this->event);
        if ($signature_element->saveSignatureImageFromPortal()) {
            $this->event->audit('event', 'cvi-consent-added', null, 'CVI Consent Signature Added', array('user_id' => $this->getApp()->user->id));
            $this->getApp()->user->setFlash('success.cvi_consent_signature', 'Signature successfully loaded.');
            $this->updateEventInfo();
        } else {
            $this->getApp()->user->setFlash('error.cvi_consent_signature', 'Signature could not be found');
        }

        $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id));
    }

    /**
     * Ensure the event is setup on the controller
     *
     * @throws \CHttpException
     */
    public function initActionPDFPrint()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @param $id
     */
    public function actionPDFPrint($id)
    {
        $this->redirect(
             '/file/view/' . $this->getManager()->getEventInfoElementForEvent($this->event)->generated_document_id . '/'
            . $this->getManager()->getEventInfoElementForEvent($this->event)->generated_document->name
        );       
    }

    private function outputStaticPdfFile($filename)
    {
        $dir = \Yii::getPathOfAlias("application.modules.OphCoCvi.assets");
        $fullpath = $dir."/pdf/$filename";
        header("Content-type:application/pdf");
        header('Content-Length: '.filesize($fullpath));
        readfile($fullpath);
        \Yii::app()->end();
    }

    public function actionPrintEmptyConsent()
    {
        $this->outputStaticPdfFile("CVI_consent_page.pdf");
    }

    public function actionPrintInfoSheet()
    {
        $this->outputStaticPdfFile("CVI_info_sheet.pdf");
    }

    public function actionPrintConsent($event_id)
    {
        $this->initWithEventId($event_id);
        $mgr = new OphCoCvi_Manager();
        $pdf = $mgr->createConsentPdf($this->event);
        $mgr->clearImages();
        
        header("Content-type:application/pdf");
        header('Content-Length: '.filesize($pdf));
        readfile($pdf);
        unlink($pdf);
        \Yii::app()->end();
    }
    
    public function actionPrintVisualyImpaired( $event_id )
    {
        $this->initWithEventId($event_id);
       
        \Yii::app()->getAssetManager()->registerCssFile('print_visual_impaired.css', 'application.modules.OphCoCvi.assets.css');
        
        if($this->cvi_manager == null){
            $this->getManager();
        }
        
        if(method_exists($this,"getSession")) {
            $pdf_print_suffix = Yii::app()->user->id . '_' . rand();
        }else{
            $pdf_print_suffix = getmypid().rand();
        }
       
        $wk = new \WKHtmlToPDF();
        $wk->setMarginTop('20mm');
        $wk->setHeaderCss( \Yii::app()->getAssetManager()->publish( \Yii::getPathOfAlias('application.modules.OphCoCvi.assets.css').'/print_visual_impaired.css') );
        $wk->setCanvasImagePath($this->event->imageDirectory);
        $wk->setPatient($this->event->episode->patient);
        $wk->setBarcode($this->event->barcodeHTML);
        $wk->setDocref($this->event->docref);
        
        $cviElements = $this->cvi_manager->generateCviElementsForPDF( $this->event );
        
        $consultantGDImage = $this->cvi_manager->generateGDFromSignature( $cviElements['consultantSignature'] );
        $consultantSignature = $this->cvi_manager->changeConsultantImageFromGDObject('consultant_signature_'.mt_rand() , $consultantGDImage);
        
        $patientGDImage = $this->cvi_manager->generateGDFromSignature( $cviElements['PatientSignature'] );
        $patientSignature = $this->cvi_manager->changePatientImageFromGDObject('patient_signature_'.mt_rand() , $patientGDImage);
        
        $cviElements['consultantSignatureImgSrc'] = $this->cvi_manager->consultantSignatureImage;
        $cviElements['patientSignatureImgSrc'] = $this->cvi_manager->patientSignatureImage;
        
        $this->layout = '//layouts/print';
        $view = $this->render('/layouts/print_visual_impaired', [
            'elements' => $cviElements,
            'imageFolder' => realpath(__DIR__ . '/..') . '/assets/img/',
        ], true);

        $header = realpath(__DIR__ . '/..') . '/views/layouts/print_visual_impaired_header.php';
        
        $wk->generatePDF($this->cvi_manager->outDir, 'visualy_impaired', $pdf_print_suffix, $view, (boolean) @$_GET['html'], false, null, $header );
       
        $pdf = $this->cvi_manager->outDir.'visualy_impaired_'.$pdf_print_suffix.'.pdf';

        header('Content-Type: application/pdf');
        header('Content-Length: '.filesize($pdf));

        readfile($pdf);
        
        $this->cvi_manager->clearImages();
        unlink($pdf);
    }

    /**
     * init action
     */
    public function initActionLabelPDFprint()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @return bool
     */
    public function checkLabelPrintAccess()
    {
        $this->demographicsData = $this->getManager()->getDemographicsElementForEvent($this->event);
        if ((empty($this->demographicsData['address'])) &&
            (empty($this->demographicsData['gp_address'])) &&
            (empty($this->demographicsData['la_address']))
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $id
     * @throws \CHttpException
     */
    public function actionLabelPDFprint($id)
    {

        $firstLabel = (int)$this->request->getParam('firstLabel');

        $labelClass = new LabelManager(
            'labels.odt',
            realpath(__DIR__ . '/..') . '/views/odtTemplate',
            \Yii::app()->basePath . '/runtime/cache/cvi',
            'labels_' . mt_rand() . '.odt'
        );

        if (!$this->checkLabelPrintAccess()) {
            throw new \CHttpException(404);
        }

        $labelAddress = array(
            $this->demographicsData['title_surname'] . "," . $this->demographicsData['address'],
            $this->demographicsData['gp_name'] . "," . $this->demographicsData['gp_address'],
            $this->demographicsData['la_name'] . "," . $this->demographicsData['la_address'],
        );

        $labelClass->fillLabelsInTable('LabelsTable', $labelAddress, $firstLabel);

        $labelClass->saveContentXML();
        $labelClass->generatePDF();
        $labelClass->getPDF();
    }

    /**
     * @throws \CHttpException
     */
    public function initActionDisplayConsentSignature()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @param $id
     * @throws \CHttpException
     */
    public function actionDisplayConsentSignature($id)
    {
        $signature_element = $this->getManager()->getConsentSignatureElementForEvent($this->event);
        if (!$signature_element->checkSignature()) {
            throw new \CHttpException(404);
        }
        header('Content-Type: image/png');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        imagepng(imagecreatefromstring($signature_element->getDecryptedSignature()));

    }

    /**
     * @throws \CHttpException
     */
    public function initActionSignCVI()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * @param $id
     * @throws \CHttpException
     */
    public function actionSignCVI($id)
    {
        $pin = $this->getApp()->getRequest()->getParam('signature_pin', null);
        if ($pin !== null) {
            $user = \User::model()->findByPk($this->getApp()->user->id);
            if ($this->getManager()->signCvi($this->event, $user, $pin)) {
                $this->getApp()->user->setFlash('success.cvi_consultant_signature', 'CVI signed.');
                $this->updateEventInfo();
            } else {
                $this->getApp()->user->setFlash('error.cvi_consultant_signature', 'Unable to sign the CVI');
            }
        } else {
            throw new \CHttpException(403, "Invalid Request");
        }

        $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id));
    }
    
   
    /**
     * Simple wrapper to get the disorder sections that should be rendered in the event form.
     *
     * @return mixed
     */
    public function getDisorderSections_V1($patient_type = null)
    {
        if ($patient_type != '') {
            $listData = OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAllByAttributes(
                [
                    'patient_type' => $patient_type,
                   // 'event_type_version' => $this->event->eventType->version
                ]);
        } else {
            $listData = OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAllByAttributes(
                ['patient_type' =>
                    ($this->getGetPatientAge() < 18)
                        ? Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_CHILD
                        : Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_ADULT,
                    //'event_type_version' => $this->event->eventType->version
                ]);

            if(empty($listData)){
                $listData = OphCoCvi_ClinicalInfo_Disorder_Section::model()->findAll(
                    array(
                        "condition" => 'event_type_version = (SELECT MAX(version) AS maxVersion FROM ophcocvi_clinicinfo_disorder_section) 
                    AND patient_type = 
                    '.($this->getGetPatientAge() < 18)
                            ? Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_CHILD
                            : Element_OphCoCvi_ClinicalInfo_V1::CVI_TYPE_ADULT,
                        "order"     => "display_order"
                    ));
            }
        }

        return $listData;
    }

    public function getDisorderSections()
    {
        return models\OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAll();
    }

    public function getGetPatientAge()
    {
        $date1 = new \DateTime($this->patient->dob);
        $date2 = new \DateTime();
        $diff = $date1->diff($date2);

        return $diff->format('%y');
    }

    /*
     * month difference of patient from adult age
     */
    public function getGetPatientMonthDiff()
    {
        $patient_dob = $this->patient->dob;
        $dob = new \DateTime($patient_dob);
        $dob->add(new \DateInterval("P18Y"));
        $nowDate = new \DateTime();
        $difference = $nowDate->diff($dob);
        $month_diff = $difference->m;

        return $month_diff;
    }

    public function actionClinicalInfoDisorderList()
    {
        $element = new Element_OphCoCvi_ClinicalInfo_V1();

        $patient_type = \Yii::app()->request->getPost("patient_type");
        $patient_type = \Yii::app()->request->getPost("patient_type");
        $diagnosis_not_covered_list = \Yii::app()->request->getPost("diagnosis_not_covered_list");

        if (!is_null(\Yii::app()->request->getPost("transfer_data"))) {
            if (!empty($diagnosis_not_covered_list)) {
                $all_cvi_assignments = array();
                foreach ($diagnosis_not_covered_list as $key => $value) {
                    if (!empty($value)) {
                        $cvi_ass = new models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment();
                        $cvi_ass->ophcocvi_clinicinfo_disorder_id = $key;
                        $cvi_ass->affected = 1;
                        $cvi_ass->eye_id = $value['eyes'];
                        $cvi_ass->main_cause = $value['main_cause'];

                        if ($cvi_ass->eye_id == \Eye::LEFT) {
                            $all_cvi_assignments['left'][] = $cvi_ass;
                        }
                        if ($cvi_ass->eye_id == \Eye::RIGHT) {
                            $all_cvi_assignments['right'][] = $cvi_ass;
                        }
                        if ($cvi_ass->eye_id == \Eye::BOTH) {
                            $all_cvi_assignments['both'][] = $cvi_ass;
                        }

                        if ($cvi_ass->main_cause == 1) {
                            $all_cvi_assignments['right'][] = $cvi_ass;
                        }

                    } else {
                        unset($diagnosis_not_covered_list[$key]);
                    }
                }

                if (!empty($all_cvi_assignments)) {
                    if (!empty($all_cvi_assignments['left'])) {
                        $element->left_cvi_disorder_assignments = $all_cvi_assignments['left'];
                    }
                    if (!empty($all_cvi_assignments['right'])) {
                        $element->right_cvi_disorder_assignments = $all_cvi_assignments['right'];
                    }
                    if (!empty($all_cvi_assignments['both'])) {
                        $element->both_cvi_disorder_assignments = $all_cvi_assignments['both'];
                    }
                }
            }
        }

        $listData = OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAll(
            array(
                "condition" => '
                event_type_version = (SELECT MAX(event_type_version) AS maxVersion FROM ophcocvi_clinicinfo_disorder) 
                AND patient_type = '.$patient_type,
                "order"     => "display_order"
            ));
        $this->renderPartial('ajax_load_diagnosis_list', ['disorder_sections' => $listData, 'element' => $element]);
    }

    public function actionDeleteDiagnosisNotCoveredElement($data_id)
    {
        if (isset($data_id)) {
            OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::model()->deleteByPk($data_id);
            return true;
        }
    }



    /**
     * Simple abstraction wrapper to get the patient factors that should be rendered in the event form.
     *
     * @return mixed
     */
    public function getPatientFactors()
    {
        return models\OphCoCvi_ClericalInfo_PatientFactor::model()->active()->findAll(
            array(
                "order"     => "display_order"
        ));
    }

    /**
     * @return bool
     */
    protected function checkUserSigned()
    {
        if ($clinicalElement = $this->getManager()->getClinicalElementForEvent($this->event)) {
            return $clinicalElement->isSigned();
        } else {
            return false;
        }
    }

    public function actionCilinicalDiagnosisAutocomplete($term)
    {
        $search = "%".strtolower($term)."%";
        $where = '(LOWER(term) like :search or id like :search)';
        $where .= ' and active = 1';
        $diagnosis = \Yii::app()->db->createCommand()
            ->select('id, term AS value')
            ->from('disorder')
            ->where($where, array(
                ':search' => $search,
            ))
            ->order('term')
            ->queryAll();

        echo json_encode($diagnosis);
    }

    public function actionGetVisualAcuityDatas($unit_id)
    {
        if ($unit_id == Element_OphCoCvi_ClinicalInfo_V1::VISUAL_ACUITY_TYPE_SNELLEN) {
            $datas = Element_OphCoCvi_ClinicalInfo_V1::model()->getSnellenDatas();
        } else if ($unit_id == Element_OphCoCvi_ClinicalInfo_V1::VISUAL_ACUITY_TYPE_LOGMAR) {
            $datas = Element_OphCoCvi_ClinicalInfo_V1::model()->getLogmarDatas();
        }
        echo json_encode($datas);
    }

    public function actionSign($id, $element_type_id)
    {
        $_GET['auto_print'] = "0";
        $this->initWithEventId($id);
        $this->layout = "//layouts/sign";
        // Currently signing is restricted to the Consent page only
        $this->render("print_Element_OphCoCvi_ConsentSignature", array(
            "event" => $this->event,
            "element_type_id" => $element_type_id,
            "patient" => $this->event->episode->patient,
        ));
    }


    public function checkPatientSignature()
    {
        $patient_signature =  $this->event->getElementByClass(\OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature::class);
        return !is_null($patient_signature) && $patient_signature->isSigned();
    }

}
