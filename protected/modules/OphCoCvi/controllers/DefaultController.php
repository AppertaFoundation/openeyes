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

namespace OEModule\OphCoCvi\controllers;

use OEModule\OphCoCvi\components\LabelManager;
use OEModule\OphCoCvi\components\OphCoCvi_Manager;
use OEModule\OphCoCvi\models;

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
        'consentsignature' => self::ACTION_TYPE_EDIT,
        'retrieveconsentsignature' => self::ACTION_TYPE_EDIT,
        'displayconsentsignature' => self::ACTION_TYPE_VIEW,
        'removeconsentsignature' => self::ACTION_TYPE_EDIT,
        'issue' => self::ACTION_TYPE_EDIT,
        'signCVI' => self::ACTION_TYPE_EDIT,
        'list' => self::ACTION_TYPE_LIST,
        'LabelPDFprint' => self::ACTION_TYPE_VIEW,
        'getSignatureByPin' => self::ACTION_TYPE_FORM,
        'saveCapturedSignature' => self::ACTION_TYPE_FORM,
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
            ]
        ];
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
        $create_new_cvi = $this->request->getParam('createnewcvi', null);
        if ($create_new_cvi !== null) {
            $cancel_url = \Yii::app()->createURL("/patient/summary/", array("id" => $this->patient->id));
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
            foreach (array('left', 'right') as $side) {
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
                        $cvi_ass->main_cause = array_key_exists(
                            'main_cause',
                            $data_disorder
                        ) ? $data_disorder['main_cause'] : false;
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
        $comments_data = array_key_exists(
            'cvi_disorder_section',
            $data[$model_name]
        ) ? $data[$model_name]['cvi_disorder_section'] : array();
        $element->updateDisorderSectionComments($comments_data);
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
            foreach (array('date_from', 'date_to', 'subspecialty_id', 'site_id', 'consultant_ids', 'show_issued', 'issue_status', 'issue_complete', 'issue_incomplete', 'createdby_ids') as $key) {
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

        $this->render('list', array('dp' => $dp, 'list_filter' => $filter, 'institution_id'=> \Institution::model()->getCurrent()->id,));
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
            if ($cls === 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo'
                && $el->isNewRecord
                && !$this->checkClinicalEditAccess()
            ) {
                // implies no values have been recorded yet for this element
                continue;
            }
            if ($cls === 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo'
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
     * Override to prevent the event_info element appearing in the sidebar when editing
     *
     * @param array $remove_list
     * @return string
     */
    public function getElementTree($remove_list = array())
    {
        $remove_list = array_merge($remove_list, array('OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo'));
        return parent::getElementTree($remove_list);
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
    public function getDisorderSections()
    {
        return models\OphCoCvi_ClinicalInfo_Disorder_Section::model()->active()->findAll();
    }

    /**
     * Simple abstraction wrapper to get the patient factors that should be rendered in the event form.
     *
     * @return mixed
     */
    public function getPatientFactors()
    {
        return models\OphCoCvi_ClericalInfo_PatientFactor::model()->active()->findAll();
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
}
