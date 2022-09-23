<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;

use OEModule\OphCoCvi\models;
use OEModule\OphCoCvi\components\OphCoCvi_Manager;
use OEModule\OphCoCvi\components\LabelManager;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;

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
        'issue' => self::ACTION_TYPE_EDIT,
        'signCVI' => self::ACTION_TYPE_EDIT,
        'list' => self::ACTION_TYPE_LIST,
        'export' => self::ACTION_TYPE_LIST,
        'LabelPDFprint' => self::ACTION_TYPE_VIEW,
        'sign' => self::ACTION_TYPE_EDIT,
        'getSignatureByPin' => self::ACTION_TYPE_FORM,
        'saveCapturedSignature' => self::ACTION_TYPE_FORM,
        'esignDevicePopup' => self::ACTION_TYPE_FORM,
        'postSignRequest' => self::ACTION_TYPE_FORM,
        'printEmptyConsent' => self::ACTION_TYPE_PRINT,
        'printConsent' => self::ACTION_TYPE_FORM,
        'consentPage' => self::ACTION_TYPE_PRINT,
        'printVisualyImpaired' => self::ACTION_TYPE_PRINT,
        'printInfoSheet' => self::ACTION_TYPE_PRINT,
        'printQRSignature' => self::ACTION_TYPE_FORM,
        'renderQRSignature' => self::ACTION_TYPE_PRINT,
        'updateSignatureRole' => self::ACTION_TYPE_FORM,
        'deleteSignature' => self::ACTION_TYPE_FORM,
    );

    /**
     * @inheritDoc
     */
    public function actions()
    {
        return [
            'getSignatureByPin' => [
                'class' => \GetSignatureByPinAction::class
            ],
            'saveCapturedSignature' => [
                'class' => \SaveCapturedSignatureAction::class
            ],
            'esignDevicePopup' => [
                'class' => \EsignDevicePopupAction::class
            ],
            'postSignRequest' => [
                'class' => \PostSignRequestAction::class
            ]
        ];
    }

    /** @var string label used in session storage for the list filter values */
    protected static $FILTER_LIST_KEY = 'OphCoCvi_list_filter';


    public function actionRenderQRSignature($event_id)
    {
        $request = \Yii::app()->getRequest();
        $this->printInit($event_id);

        $this->layout = '//layouts/print';
        $this->pdf_print_suffix = 'qr_signature';

        $unique_code = \UniqueCodes::codeForEventId($event_id);

        $esign_element = $this->event->getElementByClass('OEModule\OphCoCvi\models\Element_OphCoCvi_Esign');
        $esign_element_id = $esign_element->id;
        $esign_element_type_id = $esign_element->getElementType()->id;

        $esign = \OphCoCvi_Signature::model()->find('element_id=:element_id', [':element_id' => $esign_element_id]);

        $qr_code_data =
            "@code:" . $unique_code .
            "@key:" . "1" .
            "@x_i:" . json_encode(['e_id' => $esign_element_id, 'e_t_id' => $esign_element_type_id]);

        $this->pdf_print_html =$this->render(
            "print_Element_OphCoCvi_QRSignature",
            [
                'qr_code_data' => $qr_code_data,
                'event' => $this->event,
                'patient' => $this->patient,
                'esign' => $esign,
                'role' => urldecode($request->getQuery('role')),
                'signatory' => urldecode($request->getQuery('signatory')),
            ],
            true
        );
        echo $this->pdf_print_html;
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function actionPrintQRSignature($event_id)
    {
        $this->printInit($event_id);
        $wk = \Yii::app()->puppeteer;
        $wk->setDocRef($this->event->docref);
        $wk->setPatient($this->event->episode->patient);
        $wk->setBarcode($this->event->barcodeSVG);
        $wk->savePageToPDF(
            $this->event->imageDirectory,
            $this->pdf_print_suffix,
            '',
            'http://localhost/OphCoCvi/default/renderQRSignature?event_id='.$event_id.
            '&role='.\Yii::app()->request->getParam('role')
            .'&signatory='.\Yii::app()->request->getParam('signatory')
        );

        $pdf = $this->event->imageDirectory."/$this->pdf_print_suffix.pdf";

        header('Content-Type: application/pdf');
        header('Content-Length: '.filesize($pdf));

        readfile($pdf);
        @unlink($pdf);
    }

    /**
     * Create Form with check for the cvi existing events count
     *
     * @throws \Exception
     */
    public function actionCreate()
    {
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
            return !$this->getManager()->isIssued($this->event) && $this->checkAccess(
                'OprnEditCvi',
                $this->getApp()->user->id,
                array(
                    'firm' => $this->firm,
                    'event' => $this->event,
                )
            );
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
        // we allow print action if the patient signs - probably we need to separate the e-sign and print
        // but that would be a later refactor when this works development works
        if (\Yii::app()->request->getParam('sign') || \Yii::app()->request->getParam('issue')) {
            return true;
        }

        if (is_a($this->event, 'Event') && !$this->getManager()->isIssued($this->event)) {
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
    ) {
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
    ) {
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
                        $cvi_ass->affected = array_key_exists(
                            'affected',
                            $data_disorder
                        ) ? $data_disorder['affected'] : false;
                        $cvi_ass->eye_id = $eye_id;
                        $cvi_ass->main_cause = array_key_exists(
                            'main_cause',
                            $data_disorder
                        ) ? $data_disorder['main_cause'] : false;
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
        $preferred_language = \Language::model()->findByAttributes(array('pas_term' => 'eng'));
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

    public function saveComplexAttributes_Element_OphCoCvi_ClericalInfo(
        models\Element_OphCoCvi_ClericalInfo $element,
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

        $f = fopen($this->getManager()->outDir . $filename, "w");

        $headers = array( array('Event Date', 'Subspeciality', 'Site', 'Name', 'Hospital No.', 'Created By', 'Consultant signed by', 'Consultant in charge', 'Status', 'Issue Date'));
        foreach ($headers as $header) {
            fputcsv($f, $header, ",");
        }

        $filters = $this->getListFilter();
        $dataProvider = $this->getManager()->getListDataProvider($filters, false)->getData();

        if ($dataProvider) {
            $rows = [];
            foreach ($dataProvider as $originalRow) {
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

                $patient_identifier = \PatientIdentifierHelper::getIdentifierForPatient(
                    'LOCAL',
                    $originalRow->event->episode->patient_id,
                    $originalRow->site->institution_id, 
                    $originalRow->site_id
                );

                $rows[] = [
                    \Helper::convertMySQL2NHS($originalRow->event->event_date),
                    $originalRow->event->episode->getSubspecialtyText(),
                    $originalRow->site ? $originalRow->site->name : "-",
                    $originalRow->event->episode->patient->getFullName() . " (" . $originalRow->event->episode->patient->getAge() . "y)",
                    $patient_identifier ? "\t" . \PatientIdentifierHelper::getIdentifierValue($patient_identifier) : "-",
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
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        readfile($this->getManager()->outDir . $filename);

        unlink($this->getManager()->outDir . $filename);
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

        \Yii::app()->session['cvi_issue_print'] = true;
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

            if (
                in_array($cls, array('OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo'))
                && $el->isNewRecord
                && !$this->checkClinicalEditAccess()
            ) {
                // implies no values have been recorded yet for this element
                continue;
            }
            if (
                in_array($cls, array('OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo'))
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
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo':
                    if ($this->checkClinicalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo':
                    if ($this->checkClericalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics':
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
        if ($this->action->id === "create") {
            $this->getManager()->sendNotification($this->event);
        }
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
    public function initActionPrint()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    /**
     * Print action.
     *
     * @param int $id event id
     */
    public function actionPrint($id)
    {
        $this->printInit($id);

        if (\Yii::app()->request->getParam('sign')) {
            $this->printESign($id, $this->open_elements);
        } elseif (\Yii::app()->request->getParam('issue')) {
            $this->printIssue($id, $this->open_elements);
        } else {
            $this->printHTML($id, $this->open_elements);
        }
    }

    protected function printIssue($id, $elements, $template = '_issue')
    {
        $this->printESign($id, $elements, $template, [
            'with_esign_element' => false
        ]);
    }

    /**
     * Print e-sign form
     *
     * @param $id
     * @param $elements
     * @param string $template
     * @param array $options
     * @throws \Exception
     */
    protected function printESign($id, $elements, $template = '_issue', $options = [])
    {
        $institution_id = \Institution::model()->getCurrent()->id;
        $site_id = \Yii::app()->session['selected_site_id'];
        $primary_identifier = \PatientIdentifierHelper::getIdentifierForPatient(\SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
        $secondary_identifier = \PatientIdentifierHelper::getIdentifierForPatient(\SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->patient->id, $institution_id, $site_id);

        $this->layout = '//layouts/print';
        $this->render($template, array_merge([
            'elements' => $elements,
            'eventId' => $id,

            'patient' => $this->patient,
            'primary_identifier' => $primary_identifier->value,
            'secondary_identifier' => $secondary_identifier->value ?? null,
        ], $options));
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
        $fullpath = $dir . "/pdf/$filename";
        header("Content-type:application/pdf");
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);
        \Yii::app()->end();
    }

    public function actionPrintEmptyConsent($event_id)
    {
        $this->printInit($event_id);
        $unique_code = \UniqueCodes::codeForEventId($event_id);

        $element = $this->event->getElementByClass('OEModule\OphCoCvi\models\Element_OphCoCvi_Esign');
        $element_id = $element->id;
        $element_type_id = $element->getElementType()->id;

        $qr_code_data =
            "@code:" . $unique_code .
            "@key:" . "1" .
            "@x_i:" . json_encode(['e_id' => $element_id, 'e_t_id' => $element_type_id]);

        $this->layout = '//layouts/print';
        $this->render('_consent', [
            'elements' => $this->open_elements,
            'eventId' => $this->event->id,
            'qr_code_data' => $qr_code_data,
            'print_empty' => true,
        ]);
    }

    public function actionPrintInfoSheet()
    {
        $this->outputStaticPdfFile("CVI_info_sheet.pdf");
    }

    public function actionConsentPage($event_id)
    {
        $this->printInit($event_id);
        $this->layout = '//layouts/print';
        $this->pdf_print_suffix = 'consent_page';
        echo $this->render(
            '_consent',
            [
                'elements' => $this->open_elements,
                'eventId' => $this->event->id,
                'print_empty' => false,
                'patient' => $this->patient,
            ],
            true
        );
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function actionPrintConsent($event_id)
    {
        $this->printInit($event_id);
        $wk = \Yii::app()->puppeteer;
        $wk->setDocRef($this->event->docref);
        $wk->setPatient($this->event->episode->patient);
        $wk->setBarcode($this->event->barcodeSVG);
        $wk->savePageToPDF(
            $this->event->imageDirectory,
            $this->pdf_print_suffix,
            '',
            'http://localhost/OphCoCvi/default/consentPage?event_id='.$event_id
        );

        $pdf = $this->event->imageDirectory."/$this->pdf_print_suffix.pdf";

        header('Content-Type: application/pdf');
        header('Content-Length: '.filesize($pdf));

        readfile($pdf);
        @unlink($pdf);
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
        if (
            (empty($this->demographicsData['address'])) &&
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
     * If element provided than we fetch sections
     * @param null $patient_type
     * @param null $element
     * @return array
     */
    public function getDisorderSections($patient_type = null): array
    {

        $patient_type = !is_null($patient_type) ? $patient_type :
            ($this->getGetPatientAge() < 18
                ? Element_OphCoCvi_ClinicalInfo::CVI_TYPE_CHILD
                : Element_OphCoCvi_ClinicalInfo::CVI_TYPE_ADULT);

        return OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAllByAttributes([
                'patient_type' => $patient_type,
                'deleted' => 0
            ]);
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
        $element = new Element_OphCoCvi_ClinicalInfo();

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

        $disorder_sections = OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAll(
            array(
                "condition" => '
                event_type_version = (SELECT MAX(event_type_version) AS maxVersion FROM ophcocvi_clinicinfo_disorder)
                AND patient_type = ' . (int)$patient_type,
                "order"     => "display_order"
            )
        );
        $this->renderPartial('ajax_load_diagnosis_list', ['disorder_sections' => $disorder_sections, 'element' => $element]);
    }

    public function actionPrintVisualyImpaired(int $event_id)
    {
        $this->initWithEventId($event_id);
        $this->print_args = "?issue=1&is_visual_impairment=1";
        parent::actionPDFPrint($event_id);
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
            )
        );
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

    public function initActionSign()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    public function actionSign($id)
    {
        if (!$element_type = \ElementType::model()->findByPk(\Yii::app()->request->getParam("element_type_id"))) {
            throw new \CHttpException(500, "Element type not found");
        }
        if (!$element = $this->event->getElementByClass($element_type->class_name)) {
            throw new \CHttpException(500, "Element not found");
        }
        $this->redirect("/OphCoCvi/default/print/$id?html=1&auto_print=0&sign=1" .
            "&element_type_id=" . \Yii::app()->request->getParam("element_type_id") .
            "&signature_type=" . \Yii::app()->request->getParam("signature_type") .
            "&signatory_role=" . \Yii::app()->request->getParam("signatory_role") .
            "&signature_name=" . \Yii::app()->request->getParam("signatory_name") .
            "&element_id=" . $element->id .
            "&deviceSign=" . \Yii::app()->request->getParam("deviceSign"));
    }

    /**
     * Incasesensitive diagnosis search for autocomplete
     * @param $term
     */
    public function actionCilinicalDiagnosisAutocomplete($term)
    {
        $search = "%" . strtolower($term) . "%";
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

        $this->renderJSON($diagnosis);
    }

    public function actionGetVisualAcuityDatas($unit_id)
    {
        if ($unit_id == Element_OphCoCvi_ClinicalInfo::VISUAL_ACUITY_TYPE_SNELLEN) {
            $datas = Element_OphCoCvi_ClinicalInfo::model()->getSnellenDatas();
        } elseif ($unit_id == Element_OphCoCvi_ClinicalInfo::VISUAL_ACUITY_TYPE_LOGMAR) {
            $datas = Element_OphCoCvi_ClinicalInfo::model()->getLogmarDatas();
        }

        $this->renderJSON($datas);
    }

    public function actionUpdateSignatureRole()
    {
        $signature_id = \Yii::app()->request->getPost('signature_id');
        $role_name = \Yii::app()->request->getPost('role_name');

        $signature_element = \OphCoCvi_Signature::model()->findByPk($signature_id);
        $signature_element->signatory_role = $role_name;
        if ($signature_element->saveAttributes(['signatory_role'])) {
            return true;
        }
    }

    public function actionDeleteSignature($event_id, $signature_id)
    {
        $this->layout = '//layouts/events_and_episodes';
        $event = \Event::model()->findByPk($event_id);
        $this->patient = $event->patient;
        $this->event = $event;

        if (!empty($_POST)) {
            if (\Yii::app()->request->getPost('delete_reason', '') === '') {
                \Yii::app()->user->setFlash(
                    'warning',
                    'Please enter a reason for deleting this signature.'
                );
            } else {
                $signature = \OphCoCvi_Signature::model()->findByPk($signature_id);
                $signature->status = \OphCoCvi_Signature::STATUS_DELETED;
                $signature->delete_reason = \Yii::app()->request->getPost('delete_reason');
                $signature->validate();

                if ($signature->save()) {
                    \Yii::app()->user->setFlash(
                        'success',
                        'An signature was deleted.'
                    );
                } else {
                    \Yii::app()->user->setFlash(
                        'warning',
                        'Something went wrong.'
                    );
                }

                $this->redirect('/OphCoCvi/default/view/' . $this->event->id);
            }
        }

        $this->render('_delete_signature', [
            "signature_id" => $signature_id
        ]);
    }
}
