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

namespace OEModule\OphCiExamination\controllers;

use Eye;
use OELog;
use OEModule\OphCiExamination\components;
use OEModule\OphCiExamination\models;
use OEModule\OphCiExamination\models\AdviceLeafletEntry;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status;
use OEModule\OphCiExamination\models\MedicationManagement;
use OEModule\OphCiExamination\models\OphCiExamination_AE_RedFlags_Options_Assignment;
use OEModule\OphGeneric\models\Assessment;
use OEModule\OphGeneric\models\AssessmentEntry;
use OEModule\PASAPI\resources\HL7_A03;
use OEModule\PASAPI\resources\HL7_A08;
use OEModule\PatientTicketing\models\TicketQueueAssignment;
use services\DateTime;
use OEModule\PatientTicketing\models\QueueOutcome;
use Yii;

/*
 * This is the controller class for the OphCiExamination event. It provides the required methods for the ajax loading of elements, and rendering the required and optional elements (including the children relationship)
 */

class DefaultController extends \BaseEventTypeController
{
    use traits\DefaultForVisualAcuity;

    protected static $action_types = array(
        'step' => self::ACTION_TYPE_EDIT,
        'getDisorder' => self::ACTION_TYPE_FORM,
        'loadInjectionQuestions' => self::ACTION_TYPE_FORM,
        'getScaleForInstrument' => self::ACTION_TYPE_FORM,
        'getPreviousIOPAverage' => self::ACTION_TYPE_FORM,
        'getPostOpComplicationList' => self::ACTION_TYPE_FORM,
        'getPostOpComplicationAutocopleteList' => self::ACTION_TYPE_FORM,
        'dismissCVIalert' => self::ACTION_TYPE_FORM,
        'getDrFeatures' => self::ACTION_TYPE_FORM,
        'getOctAssessment' => self::ACTION_TYPE_FORM,
        'getAttachment' => self::ACTION_TYPE_FORM,
        'resolveSafeguardingElement' => self::ACTION_TYPE_SAFEGUARDING,
        'getSignatureByPin' => self::ACTION_TYPE_FORM,
        'getSignatureByUsernameAndPin' => self::ACTION_TYPE_FORM,
        'searchInstitutions' => self::ACTION_TYPE_FORM,
        'medicationManagementEditable' => self::ACTION_TYPE_FORM,
        'checkPrescriptionAutoSignEnabled' => self::ACTION_TYPE_FORM,
        'saveDraft' => self::ACTION_TYPE_FORM,
    );

    private const ACTION_TYPE_SAFEGUARDING = 'Safeguarding';

    /**
     * Set to true if the index search bar should appear in the header when creating/editing the event
     *
     * @var bool
     */
    protected $show_index_search = true;

    protected $show_element_sidebar = true;

    protected $show_manage_elements = true;

    // if set to true, we are advancing the current event step
    protected $set;
    protected $mandatoryElements;
    protected $allergies = array();
    protected $deletedAllergies = array();
    private $assigned_element_set = null;
    private $step = false;

    /**
     * @inheritDoc
     */
    public function actions()
    {
        return [
            'getSignatureByPin' => [
                'class' => \GetSignatureByPinAction::class
            ],
            'getSignatureByUsernameAndPin' => [
                'class' => \GetSignatureByUsernameAndPinAction::class
            ]
        ];
    }

    public function getTitle()
    {
        $title = parent::getTitle();
        $current = $this->step ?: $this->getCurrentStep();
        if (count($current->workflow->steps) > 1) {
            $title .= ' (' . $current->name . ')';
        }
        return $title;
    }

    /**
     * @param null $event
     * @return null|models\OphCiExamination_ElementSet
     */
    protected function getCurrentStep($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        if (
            isset(Yii::app()->session['active_step_state_data']['workflow_step_id'])
            && Yii::app()->session['active_step_state_data']['workflow_step_id']
        ) {
            return models\OphCiExamination_ElementSet::model()->findByPk(
                Yii::app()->session['active_step_state_data']['workflow_step_id']
            );
        }

        return $this->assigned_element_set ? $this->assigned_element_set->step : $this->getFirstStep();
    }

    /**
     * Returns Element Set Assignment
     * @param Event $event
     * @return mixed|null
     */
    public function getElementSetAssignment($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        if (
            $event && !$event->isNewRecord && $assignment = models\OphCiExamination_Event_ElementSet_Assignment::model(
            )->find('event_id = ?', array($event->id))
        ) {
            return $assignment;
        }

        return null;
    }

    /**
     * Get the first workflow step using rules.
     *
     * @return models\OphCiExamination_ElementSet
     */
    protected function getFirstStep()
    {
        $institution_id = $this->institution->id;
        $firm_id = $this->firm->id;
        $status_id = ($this->episode) ? $this->episode->episode_status_id : 1;

        $workflow = new models\OphCiExamination_Workflow_Rule();

        return $workflow->findWorkflowCascading($firm_id, $status_id)->getFirstStep();
    }

    /**
     * Get all the available element types for the event
     *
     * @return array
     */
    public function getAllElementTypes()
    {
        if ($this->action->id == 'update') {
            return parent::getAllElementTypes();
        }

        $remove = $this->getElementFilterList(false);
        return array_filter(
            parent::getAllElementTypes(),
            function ($et) use ($remove) {
                return !in_array($et->class_name, $remove);
            }
        );
    }

    public function getElementTree($remove_list = array())
    {
        return parent::getElementTree($this->getElementFilterList());
    }

    /**
     * Call editInit to set up jsVars.
     */
    public function initActionCreate()
    {
        parent::initActionCreate();
        $this->initEdit();
    }

    /**
     * Sets up jsvars for editing.
     */
    protected function initEdit()
    {
        $this->jsVars['Element_OphCiExamination_IntraocularPressure_link_instruments'] = models\Element_OphCiExamination_IntraocularPressure::model(
        )->getSetting('link_instruments') ? 'true' : 'false';

        if (Yii::app()->hasModule('OphCoTherapyapplication')) {
            $this->jsVars['OphCiExamination_loadQuestions_url'] = $this->createURL('loadInjectionQuestions');
        }

        Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/core.js", \CClientScript::POS_HEAD);

        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath, true);

        Yii::app()->clientScript->registerScriptFile(
            $assetManager->getPublishedUrl($baseAssetsPath, true) . '/OpenEyes.UI.InputFieldValidation.js',
            \CClientScript::POS_END
        );
    }

    public function initActionStep()
    {
        $this->initActionUpdate();
    }

    /**
     * Call editInit to setup jsVars.
     */
    public function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit();
    }

    public function actionUpdate($id)
    {
        $this->setCurrentSet();

        parent::actionUpdate($id);
    }

    /**
     * @throws \CException
     */
    protected function setCurrentSet()
    {
        if (
            isset(Yii::app()->session['active_step_state_data']['workflow_step_id'])
            && Yii::app()->session['active_step_state_data']['workflow_step_id']
        ) {
            if (!$this->set) {
                // getCurrentStep, when the workflow step ID is specified as above,
                // will return the specified workflow element set. Otherwise it returns the current (or first) step.
                $this->set = $this->getCurrentStep();

                //if $this->set is null than no workflow rule to apply
                $this->mandatoryElements = isset($this->set) ? $this->set->MandatoryElementTypes : null;
            }

            if (!$this->assigned_element_set && $this->event) {
                \OELog::log("Assignment not found for event id: {$this->event->id}");
            }

            $this->step = $this->getCurrentStep();
        } else {
            if (!$this->set) {
                // Note: getCurrentStep() will return firstStep if there were no steps before
                $this->set = $this->assigned_element_set && $this->action->id !== 'update' ? $this->getNextStep(
                ) : $this->getCurrentStep();

                //if $this->set is null than no workflow rule to apply
                $this->mandatoryElements = isset($this->set) ? $this->set->MandatoryElementTypes : null;
            }

            if (!$this->assigned_element_set && $this->event) {
                \OELog::log("Assignment not found for event id: {$this->event->id}");
            }

            if ($this->action->id === 'update' && (!isset($this->assigned_element_set) || !$this->assigned_element_set->step_completed)) {
                $this->step = $this->getCurrentStep();
            }
        }
    }

    /**
     * Get the next workflow step.
     *
     * @param \Event $event
     *
     * @return models\OphCiExamination_ElementSet
     */
    protected function getNextStep($event = null)
    {
        $step = $this->getCurrentStep();

        return $step->getNextStep();
    }

    public function actionGetDrFeatures()
    {
        $feature_id_list = Yii::app()->request->getQuery('feature_list');

        $feature_list = models\OphCiExamination_DRGrading_Feature::model()->findAllByPk($feature_id_list);
        $features = array();
        foreach ($feature_list as $feature) {
            $features[] = array(
                'name' => $feature->name,
                'id' => $feature->id,
                'grade' => $feature->grade,
            );
            if ($feature->name === 'No DR' || $feature->name === 'Nil') {
                // Discard all other options
                break;
            }
        }

        $this->renderJSON($features);
        Yii::app()->end();
    }

    public function renderOpenElements($action, $form = null, $date = null)
    {
        if ($action === 'renderEventImage') {
            $action = 'view';
        }
        $step_id = \Yii::app()->request->getParam('step_id');

        $elements = $this->getElements($action);

        // add OpenEyes.UI.RestrictedData js
        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath, true);

        \Yii::app()->clientScript->registerScriptFile(
            $assetManager->getPublishedUrl($baseAssetsPath, true) . '/OpenEyes.UI.RestrictData.js',
            \CClientScript::POS_END
        );

        /* @var \OEModule\OphCoCvi\components\OphCoCvi_API $cvi_api */
        $cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');

        // Render the CVI alert above all the other elements
        if ($cvi_api) {
            $visual_acuities = array_filter($elements, function ($element) {
                return get_class($element) === models\Element_OphCiExamination_VisualAcuity::class;
            });
            echo $cvi_api->renderAlertForVA(
                $this->patient,
                $visual_acuities[0] ?? null,
                $action === 'view'
            );
        }

        if ($action !== 'view' && $action !== 'createImage') {
            parent::renderOpenElements($action, $form, $date);

            return;
        }

        $this->renderPartial(
            'view_summary',
            array('action' => $action, 'form' => $form, 'data' => $date, 'patient' => $this->patient)
        );

        $filteredElements = array_filter($elements, function ($element) {
            return !in_array(get_class($element), array(
                // Ignore elements that are displayed in the view summary
                models\Element_OphCiExamination_History::class,
                models\PastSurgery::class,
                models\SystemicSurgery::class,
                models\SystemicDiagnoses::class,
                models\Element_OphCiExamination_Diagnoses::class,
                models\HistoryMedications::class,
                models\FamilyHistory::class,
                models\SocialHistory::class,
                models\Element_OphCiExamination_Management::class,
                models\OCT::class,
            ), true);
        });

        $this->renderElements($filteredElements, $action, $form, $date);
    }

    public function getOptionalElements()
    {
        $elements = parent::getOptionalElements();

        return $this->filterElements($elements);
    }

    /**
     * Ajax action to load the questions for a side and disorder_id.
     */
    public function actionLoadInjectionQuestions()
    {
        // need a side specification for the form element names
        $side = @$_GET['side'];
        if (!in_array($side, array('left', 'right'))) {
            throw new \Exception('Invalid side argument');
        }

        // disorder id verification
        $questions = array();
        foreach (@$_GET['disorders'] as $did) {
            if ((int)$did) {
                foreach (
                    models\Element_OphCiExamination_InjectionManagementComplex::model(
                    )->getInjectionQuestionsForDisorderId($did) as $q
                ) {
                    $questions[] = $q;
                }
            }
        }

        // need a form object
        $form = Yii::app()->getWidgetFactory()->createWidget($this, 'BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('class' => 'sliding'),
        ));

        $element = new models\Element_OphCiExamination_InjectionManagementComplex();

        // and now render
        $this->renderPartial(
            'form_Element_OphCiExamination_InjectionManagementComplex_questions',
            array('element' => $element, 'form' => $form, 'side' => $side, 'questions' => $questions),
            false,
            false
        );
    }

    public function actionGetScaleForInstrument($name)
    {
        $instrument_id = @$_GET['instrument_id'];
        $side = @$_GET['side'];
        $index = @$_GET['index'];
        $instrument = models\OphCiExamination_Instrument::model()->findByPk($instrument_id);
        if ($instrument) {
            if ($scale = $instrument->scale) {
                $value = new models\OphCiExamination_IntraocularPressure_Value();
                $this->renderPartial(
                    '_qualitative_scale',
                    ['name' => $name, 'value' => $value, 'scale' => $scale, 'side' => $side, 'index' => $index]
                );
            }
        }
    }

    public function actionSearchInstitutions($term)
    {
        $institutions = \Institution::model()->findAll(
            'LOWER(name) LIKE CONCAT(LOWER(:term), \'%\')',
            [':term' => $term]
        );

        $output = '';

        foreach ($institutions as $institution) {
            $output .= "<li data-transfer-institution-id=\"$institution->id\" data-label=\"$institution->name\">
    <span class=\"restrict-width\">$institution->name</span>
</li>";
        }
        echo $output;
    }

    public function actionGetPreviousIOPAverage()
    {
        if (!$patient = \Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new \Exception('Patient not found: ' . @$_GET['patient_id']);
        }

        if (!in_array(@$_GET['side'], array('left', 'right'))) {
            throw new \Exception('Invalid side: ' . @$_GET['side']);
        }

        $side = ucfirst(@$_GET['side']);

        $api = $this->getApp()->moduleAPI->get('OphCiExamination');
        $result = $api->{"getLastIOPReading{$side}"}($patient);

        echo $result;
    }

    public function actionCheckPrescriptionAutoSignEnabled()
    {
        echo json_encode(['auto_sign_enabled' => \SettingMetadata::model()->checkSetting('require_pin_for_prescription', 'no')]);
    }

    public function actionCreate()
    {
        $this->setCurrentSet();
        $this->step = $this->getCurrentStep();

        if (Yii::app()->request->getPost('patientticketing__notes', null) != null) {
            $_POST['patientticketing__notes'] = htmlspecialchars(
                Yii::app()->request->getPost(
                    'patientticketing__notes',
                    null
                )
            );
        }

        parent::actionCreate();
    }

    public function getPupilliaryAbnormalitiesList($selected_id)
    {
        $criteria = new \CDbCriteria();

        $criteria->order = 'display_order asc';

        if ($selected_id) {
            $criteria->addCondition('active = 1 or id = :selected_id');
            $criteria->params[':selected_id'] = $selected_id;
        } else {
            $criteria->addCondition('active = 1');
        }

        return \CHtml::listData(
            models\OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findAll($criteria),
            'id',
            'name'
        );
    }

    /**
     * Is this element required in the UI? (Prevents the user from being able
     * to remove the element.).
     *
     * @param \BaseEventTypeElement $element
     *
     * @return bool
     */

    public function isRequiredInUI(\BaseEventTypeElement $element)
    {
        if (isset($this->mandatoryElements)) {
            foreach ($this->mandatoryElements as $mandatoryElement) {
                $class_name = get_class($element);
                if ($class_name === $mandatoryElement->class_name) {
                    return true;
                }
            }
        }

        return parent::isRequiredInUI($element);
    }

    public function actionGetPostOpComplicationList()
    {
        $element_id = \Yii::app()->request->getParam('element_id', null);
        $operation_note_id = \Yii::app()->request->getParam('operation_note_id', null);
        $eye_id = \Yii::app()->request->getParam('eye_id', null);

        if ($element_id) {
            $element = models\Element_OphCiExamination_PostOpComplications::model()->findByPk($element_id);
        } else {
            $element = new models\Element_OphCiExamination_PostOpComplications();
        }

        $right_complications = $element->getRecordedComplications(\Eye::RIGHT, $operation_note_id);
        $left_complications = $element->getRecordedComplications(\Eye::LEFT, $operation_note_id);

        $right_data = array();
        $left_data = array();
        foreach ($right_complications as $right_complication) {
            $right_data[] = array('id' => $right_complication['id'], 'name' => $right_complication['name']);
        }

        foreach ($left_complications as $left_complication) {
            $left_data[] = array('id' => $left_complication['id'], 'name' => $left_complication['name']);
        }

        $firm = \Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $right_select_values = models\OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList(
            $element_id,
            $operation_note_id,
            $subspecialty_id,
            \Eye::RIGHT
        );

        $right_select = array();
        foreach ($right_select_values as $right_select_value) {
            $right_select[] = array(
                'id' => $right_select_value->id,
                'name' => $right_select_value->name,
                'display_order' => $right_select_value->display_order
            );
        }

        $left_select_values = models\OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList(
            $element_id,
            $operation_note_id,
            $subspecialty_id,
            \Eye::LEFT
        );
        foreach ($left_select_values as $left_select_value) {
            $left_select[] = array(
                'id' => $left_select_value->id,
                'name' => $left_select_value->name,
                'display_order' => $left_select_value->display_order
            );
        }

        echo \CJSON::encode(array(
            'right_values' => $right_data,
            'left_values' => $left_data,
            'right_select' => $right_select,
            'left_select' => $left_select,
        ));
    }

    public function actionGetPostOpComplicationAutocopleteList()
    {
        $isAjax = \Yii::app()->request->getParam('ajax', false);

        if (\Yii::app()->request->isAjaxRequest || $isAjax) {
            $term = \Yii::app()->request->getParam('term', false);

            $element_id = \Yii::app()->request->getParam('element_id', null);
            $operation_note_id = \Yii::app()->request->getParam('operation_note_id', null);
            $eye_id = \Yii::app()->request->getParam('eye_id', null);

            $firm = \Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $select_values = models\OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList(
                    $element_id,
                    $operation_note_id,
                    $subspecialty_id,
                    $eye_id,
                    $term
                );

                $select = array();
                foreach ($select_values as $select_value) {
                    $select[] = array('value' => $select_value->id, 'label' => $select_value->name);
                }
            }

            echo \CJSON::encode($select);
        }
    }

    /**
     * Setting the CVI alert flag to dismiss
     *
     * @param int $element_id
     */
    public function actionDismissCVIalert($element_id)
    {
        $is_ajax = $this->getApp()->request->getParam('ajax', false);
        $cvi_api = $this->getApp()->moduleAPI->get('OphCoCvi');

        if ($cvi_api && ($this->getApp()->request->isAjaxRequest || $is_ajax)) {
            $element = models\Element_OphCiExamination_VisualAcuity::model()->findByPk($element_id);
            $element->cvi_alert_dismissed = 1;

            if ($element->save()) {
                echo \CJSON::encode(array('success' => 'true'));
            }
        }
    }

    /**
     * Need split event files.
     * @TODO: determine if this should be defined by controller property
     *
     * @param $action
     * @return bool
     * @throws \CHttpException
     */
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, \AssetManager::OUTPUT_SCREEN);
        $this->jsVars['OE_MODEL_PREFIX'] = 'OEModule_OphCiExamination_models_';
        $this->jsVars['default_iris_colour'] = \SettingMetadata::model()->getSetting(
            'OphCiExamination_default_iris_colour'
        );
        $parentBeforeAction = parent::beforeAction($action);

        $this->assigned_element_set = $this->getElementSetAssignment();

        return $parentBeforeAction;
    }

    /**
     * Applies workflow and filtering to the element retrieval.
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getEventElements()
    {
        if (!$this->event || $this->event->isNewRecord) {
            $elements = $this->getElementsByWorkflow($this->set, $this->episode);
        } else {
            $elements = $this->getSortedElements();
            if ($this->step) {
                $elements = $this->mergeNextStep($elements);
            }
        }

        return $this->filterElements($elements);
    }

    /**
     * Returns the current events elements ordered by workflow set
     * where applicable.
     * @return \BaseEventTypeElement[]
     */
    protected function getSortedElements()
    {
        $set = $this->set ?: $this->getSetFromEpisode($this->episode);
        $sortable_elements = [];

        foreach ($this->event->getElements() as $element) {
            $flow_order = $set->getSetElementOrder($element);
            if ($flow_order) {
                $sortable_elements[$flow_order] = $element;
            } else {
                $sortable_elements[$set->getWorkFlowMaximumDisplayOrder() + $element->display_order] = $element;
            }
        }

        ksort($sortable_elements);
        return $sortable_elements;
    }

    public function getElements($action = 'edit')
    {
        $set = $this->set ? $this->set : $this->getSetFromEpisode($this->episode);
        $elements = array();
        if (is_array($this->open_elements)) {
            foreach ($this->open_elements as $element) {
                $flow_order = $set->getSetElementOrder($element);
                if ($element->getElementType()) {
                    if ($flow_order) {
                        $elements[$flow_order] = $element;
                    } else {
                        $elements[$set->getWorkFlowMaximumDisplayOrder() + $element->display_order] = $element;
                    }
                }
            }
        }
        ksort($elements);
        return $elements;
    }

    /**
     * Filters elements based on coded dependencies.
     *
     * @param \BaseEventTypeElement[] $elements
     * @return \BaseEventTypeElement[]
     */
    protected function filterElements($elements)
    {
        $remove = $this->getElementFilterList();

        $final = array();
        foreach ($elements as $el) {
            if (in_array(get_class($el), $remove)) {
                if ($el->id > null || $this->checkElementsForData($this->getElements($el->getElementType()))) {
                    $final[] = $el;
                }
            } else {
                $final[] = $el;
            }
        }
        return $final;
    }

    /**
     * List of elements that should be filtered out from the event.
     *
     * @return array
     */
    protected function getElementFilterList($include_hidden = true)
    {
        $remove = components\ExaminationHelper::elementFilterList();

        if ($include_hidden && $this->set) {
            foreach ($this->set->HiddenElementTypes as $element) {
                $remove[] = $element->class_name;
            }
        }
        return $remove;
    }

    /**
     * Check data in child elements
     *
     * @param \BaseEventTypeElement[] $elements
     * @return boolean
     */
    protected function checkElementsForData($elements)
    {
        foreach ($elements as $element) {
            if ($element->id > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pulls in the diagnosis from the episode and ophthalmic diagnoses from the patient, and sets an appropriate list
     * of unique diagnoses.
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_Diagnoses($element, $action)
    {
        if ($element->isNewRecord) {
            // set the diagnoses to match the current patient diagnoses for the episode
            // and any other ophthalmic secondary diagnoses the patient has
            $diagnoses = array();
            $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');

            if ($this->episode && $this->episode->diagnosis) {
                $principal_diagnosis = $exam_api->getPrincipalOphtalmicDiagnosis(
                    $this->episode,
                    $this->episode->diagnosis->id
                );

                $d = new models\OphCiExamination_Diagnosis();
                $d->disorder_id = $this->episode->disorder_id;
                $d->principal = true;
                $d->date = $principal_diagnosis ? $principal_diagnosis->date : null;
                $d->eye_id = $this->episode->eye_id;

                $diagnoses[] = $d;
            }

            foreach ($this->patient->getOphthalmicDiagnoses() as $sd) {
                $d = new models\OphCiExamination_Diagnosis();
                $d->disorder_id = $sd->disorder_id;
                $d->eye_id = $sd->eye_id;
                $d->date = $sd->date;

                $diagnoses[] = $d;
            }

            // ensure unique
            $_diagnoses = array();
            foreach ($diagnoses as $d) {
                $already_in = false;
                foreach ($_diagnoses as $ad) {
                    if (($d->disorder_id === $ad->disorder_id) && ($d->date === $ad->date)) {
                        $already_in = true;
                        // set the eye correctly (The principal diagnosis for the episode is the first diagnosis, so
                        // no need to check that.
                        if ($d->eye_id != $ad->eye_id) {
                            $ad->eye_id = \Eye::BOTH;
                        }
                        break;
                    }
                }
                if (!$already_in) {
                    $_diagnoses[] = $d;
                }
            }
            $element->diagnoses = $_diagnoses;
        }
    }

    protected function setElementDefaultOptions_Element_OphCiExamination_InjectionManagementComplex($element, $action)
    {
        $previous_id = \Yii::app()->request->getParam('previous_id');
        $right_eye = Eye::getIdFromName('right');
        $left_eye = Eye::getIdFromName('left');

        // If the $previous_id is not empty, it means this function is triggered by Copy functionality
        if (!empty($previous_id)) {
            $answer_obj = models\OphCiExamination_InjectionManagementComplex_Answer::model()->findAll(
                'element_id = ' . $previous_id
            );
            $risk_obj = models\OphCiExamination_InjectionManagementComplex_RiskAssignment::model()->findAll(
                'element_id = ' . $previous_id
            );
            $left_answers = array();
            $right_answers = array();
            $risk_assignments = array();
            $left_risks = array();
            $right_risks = array();
            foreach ($answer_obj as $obj) {
                if (intval($obj->eye_id) === $left_eye) {
                    $left_answers[] = $obj;
                } elseif (intval($obj->eye_id) === $right_eye) {
                    $right_answers[] = $obj;
                } else {
                    // In case 'Both' will be in use
                    $left_answers[] = $obj;
                    $right_answers[] = $obj;
                }
            }
            foreach ($risk_obj as $obj) {
                $complication = models\OphCiExamination_InjectionManagementComplex_Risk::model()->find(
                    'id = ' . $obj->risk_id
                );
                if (intval($obj->eye_id) === $left_eye) {
                    $left_risks[] = $complication;
                } elseif (intval($obj->eye_id) === $right_eye) {
                    $right_risks[] = $complication;
                } else {
                    // In case 'Both' will be in use
                    $left_risks[] = $complication;
                    $right_answers[] = $obj;
                }
            }
            $element->left_risks = $left_risks;
            $element->right_risks = $right_risks;
            $element->answers = $answer_obj;
            $element->right_answers = $right_answers;
            $element->left_answers = $left_answers;
        }
    }

    /**
     * Set the allergies against the Element_OphCiExamination_Allergy element
     * It's a child element of History.
     *
     * @param models\Element_OphCiExamination_History $element
     * @param $data
     * @param $index
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_History($element, $action)
    {
        if ($action == 'create' || $action == 'update') {
            $this->allergies = $this->patient->allergyAssignments;
        }
    }

    /**
     * Set the allergies against the Element_OphCiExamination_Allergy element.
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_Allergy($element, $action)
    {
        if ($action == 'create' || $action == 'update') {
            $this->allergies = $this->patient->allergyAssignments;
        }
    }

    /**
     * Action to move the workflow forward a step on the given event.
     *
     * @param $id
     */
    public function actionStep($id)
    {
        $context = $this->event->firm ?? $this->event->episode->firm;

        $this->setContext($context);

        $step_id = \Yii::app()->request->getParam('step_id');
        if (!isset(Yii::app()->session['active_step_id'])) {
            Yii::app()->session['active_worklist_patient_id'] = \Yii::app()->request->getParam('worklist_patient_id');
            Yii::app()->session['active_step_id'] = \Yii::app()->request->getParam('worklist_step_id');
        }
        $target_step = models\OphCiExamination_ElementSet::model()->findByPk($step_id);
        if ($target_step) {
            $saved_step = $this->assigned_element_set->step ?? null;
            $this->set = $target_step;
            /**
             * If the saved step and the target step belong to the same workflow
             * and the saved step is at a later stage then the target step
             *  then use saved step as the current step
             * otherwise use target step as the current step
             */
            if ($saved_step && $saved_step->workflow_id === $target_step->workflow_id && $saved_step->position > $target_step->position) {
                $this->step = $saved_step;
            } else {
                $this->step = $target_step;
            }
        } else {
            $this->step = $this->getCurrentStep()->getNextStep();
            $this->set = $this->step;
        }

        // This is the same as update, but with a few extras, so we call the update code and then pick up on the action later
        parent::actionUpdate($id);
    }

    /**
     * Override action value when action is step to be update.
     *
     * @param \BaseEventTypeElement $element
     * @param string $action
     * @param \BaseEventTypeCActiveForm $form
     * @param array $data
     * @param array $template_data
     * @param array $view_data
     * @param bool $return
     * @param bool $processOutput
     * @throws \Exception
     */
    protected function renderElement(
        $element,
        $action,
        $form,
        ?array $data = null,
        ?array $template_data = [],
        ?array $view_data = [],
        bool $return = false,
        bool $processOutput = false
    ) {
        if ($action == 'step') {
            $action = 'update';
        }

        $class_array = '';
        if (!empty($element)) {
            $cls = get_class($element);
            if (!empty($cls)) {
                $class_array = explode('\\', (get_class($element)));
            }
        }

        $active_check = "";

        $view_data = array_merge(array(
            'active_check' => $active_check,
        ), $view_data);

        parent::renderElement($element, $action, $form, $data, $template_data, $view_data, $return, $processOutput);
    }

    /**
     * Advance the workflow step for the event if requested.
     *
     * @param \Event $event
     *
     * @throws \CException
     */
    protected function afterUpdateElements($event)
    {
        parent::afterUpdateElements($event);
        $this->persistPcrRisk();
        if ($this->step) {
            // Advance the workflow
            if (
                !$assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find(
                    'event_id = ?',
                    array($event->id)
                )
            ) {
                // Create initial workflow assignment if event hasn't already got one
                $assignment = new models\OphCiExamination_Event_ElementSet_Assignment();
                $assignment->event_id = $event->id;
            }

            $assignment->step_id = $this->step->id;
            $assignment->step_completed = 1;
            if (!$assignment->save()) {
                throw new \CException('Cannot save assignment');
            }
        }

        // save email address in the contact model
        $this->saveContactEmailAddressForCommunicationPreferences($_POST);
    }

    protected function afterCreateEvent($event)
    {
        parent::afterCreateEvent($event);
        // This condition is working under the assumption that the subspecialty ref_spec value for A&E is AE.
        // Change this if it is a different value.
        if ($event->episode->getSubspecialty()->getTreeName() === 'AE') {
            $clinical_outcome_entry = null;
            $discharge_status = \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()->find("name like 'Discharge%'");

            $clinical_outcome = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::model()->find(
                "event_id = ?",
                array($event->id)
            );
            if ($clinical_outcome) {
                $clinical_outcome_entry = \OEModule\OphCiExamination\models\ClinicOutcomeEntry::model()->find(
                    "element_id = ? and status_id = ? ",
                    array($clinical_outcome->id, $discharge_status->id)
                );
            }
            if ($clinical_outcome_entry) {
                $this->pasCallout($event, 'A08');
                $this->pasCallout($event, 'A03');
            } else {
                $this->pasCallout($event, 'A08');
            }
        }
    }

    protected function afterUpdateEvent($event)
    {
        parent::afterUpdateEvent($event);
        // This condition is working under the assumption that the subspecialty ref_spec value for A&E is AE.
        // Change this if it is a different value.
        if ($event->episode->getSubspecialty()->getTreeName() === 'AE') {
            $clinical_outcome_entry = null;
            $discharge_status = \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()->find("name like 'Discharge%'");

            $clinical_outcome = \OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::model()->find(
                "event_id = ?",
                array($event->id)
            );
            if ($clinical_outcome) {
                $clinical_outcome_entry = \OEModule\OphCiExamination\models\ClinicOutcomeEntry::model()->find(
                    "element_id = ? and status_id = ? ",
                    array($clinical_outcome->id, $discharge_status->id)
                );
            }
            if ($clinical_outcome_entry) {
                $this->pasCallout($event, 'A08');
                $this->pasCallout($event, 'A03');
            } else {
                $this->pasCallout($event, 'A08');
            }
        }
    }

    protected function afterDeleteEvent($event)
    {
        parent::afterDeleteEvent($event);
        // This condition is working under the assumption that the subspecialty ref_spec value for A&E is AE.
        // Change this if it is a different value.
        if ($event->episode->getSubspecialty()->getTreeName() === 'AE') {
            $this->pasCallout($event, 'A11');
        }
    }

    public function isEventApplicableToWorklistPathstepData($pathstep_data)
    {
        return
            parent::isEventApplicableToWorklistPathstepData($pathstep_data) &&
            (!isset($pathstep_data->workflow_step_id) ||
                (isset($this->step->id) && $pathstep_data->workflow_step_id == $this->step->id));
    }

    /***
     * Construct a PAS message for the specified trigger event here and send it to the PAS.
     * @param \Event $event
     * @param string $hl7_trigger_event "A03|A08|A11"
     */
    protected function pasCallout($event, $hl7_trigger_event)
    {
        switch ($hl7_trigger_event) {
            case 'A08':
                $hl7_a08 = new HL7_A08();
                $hl7_a08->setDataFromEvent($event->id);
                Yii::app()->event->dispatch(
                    'emergency_care_update',
                    $hl7_a08
                );
                break;
            case 'A03':
                $hl7_a03 = new HL7_A03();
                $hl7_a03->setDataFromEvent($event->id);
                Yii::app()->event->dispatch(
                    'emergency_care_update',
                    $hl7_a03
                );
                break;
        }
    }

    protected function afterCreateElements($event)
    {
        parent::afterCreateElements($event);
        $this->persistPcrRisk();
        if ($this->step) {
            // Advance the workflow
            if (
                !$assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find(
                    'event_id = ?',
                    array($event->id)
                )
            ) {
                // Create initial workflow assignment if event hasn't already got one
                $assignment = new models\OphCiExamination_Event_ElementSet_Assignment();
                $assignment->event_id = $event->id;
            }

            $assignment->step_id = $this->step->id;
            $assignment->step_completed = 1;
            if (!$assignment->save()) {
                throw new \CException('Cannot save assignment');
            }
        }

        // save email address in the contact model
        $this->saveContactEmailAddressForCommunicationPreferences($_POST);
    }

    /**
     * Merge workflow next step elements into existing elements.
     *
     * @param array $elements
     *
     * @return array
     * @throws \CException
     *
     */
    protected function mergeNextStep($elements)
    {
        if (!$event = $this->event) {
            throw new \CException('No event set for step merging');
        }

        //TODO: should we be passing episode here?
        $extra_elements = $this->getElementsByWorkflow($this->set, $this->episode);
        $extra_by_etid = array();

        foreach ($extra_elements as $extra) {
            $extra_by_etid[$extra->getElementType()->id] = $extra;
        }

        $merged_elements = array();
        foreach ($elements as $element) {
            $element_type = $element->getElementType();
            $merged_elements[] = $element;
            if (isset($extra_by_etid[$element_type->id])) {
                unset($extra_by_etid[$element_type->id]);
            }
        }

        foreach ($extra_by_etid as $extra_element) {
            $extra_element->setDefaultOptions($this->patient);

            // Precache Element Type to avoid bug in usort
            $extra_element->getElementType();

            $merged_elements[] = $extra_element;
        }
        $sortable_merged_elements = [];

        foreach ($merged_elements as $element) {
            $flow_order = $this->step->getSetElementOrder($element);
            if ($flow_order) {
                $sortable_merged_elements[$flow_order] = $element;
            } else {
                $sortable_merged_elements[$this->step->getWorkFlowMaximumDisplayOrder(
                ) + $element->display_order] = $element;
            }
        }

        ksort($sortable_merged_elements);

        return $sortable_merged_elements;
    }

    protected function getSetFromEpisode($episode)
    {
        $institution_id = $this->institution->id;
        $firm_id = $this->firm->id;
        $status_id = ($episode) ? $episode->episode_status_id : 1;
        $workflow = new models\OphCiExamination_Workflow_Rule();
        return $workflow->findWorkflowCascading($firm_id, $status_id)->getFirstStep();
    }

    /**
     * Get the array of elements for the current site, subspecialty, episode status and workflow position
     *
     * @param models\OphCiExamination_ElementSet $set
     * @param \Episode $episode
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getElementsByWorkflow($set = null, $episode = null)
    {
        $elements = array();
        if (!$set) {
            $set = $this->getSetFromEpisode($episode);
        }

        if ($set) {
            $element_types = $set->DefaultElementTypes;
            foreach ($element_types as $element_type) {
                $elements[$element_type->id] = $element_type->getInstance();
            }
            $this->mandatoryElements = $set->MandatoryElementTypes;
        }

        $this->set = $set;

        return $this->filterElements($elements);
    }

    /**
     * Ajax function for quick disorder lookup.
     *
     * Used when eyedraw elements have doodles that are associated with disorders
     *
     * @throws \Exception
     */
    public function actionGetDisorder()
    {
        if (!@$_GET['disorder_id']) {
            return;
        }
        if (!$disorder = \Disorder::model()->findByPk(@$_GET['disorder_id'])) {
            throw new \Exception('Unable to find disorder: ' . @$_GET['disorder_id']);
        }

        // For some reason JSON_HEX_QUOT | JSON_HEX_APOS doesn't escape ?
        $this->renderJSON(array('id' => $disorder->id, 'name' => $disorder->term));
        Yii::app()->end();
    }

    /**
     * Get all the attributes for an element.
     *
     * @param \BaseEventTypeElement $element
     * @param int $subspecialty_id
     *
     * @return models\OphCiExamination_Attribute[]
     */
    public function getAttributes($element, $subspecialty_id = null)
    {
        $attributes = models\OphCiExamination_Attribute::model()->findAllByElementAndSubspecialty(
            $element->ElementType->id,
            $subspecialty_id
        );

        return $attributes;
    }

    protected function saveComplexAttributes_AdviceGiven($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);

        $entries_by_leaflet_id = [];
        foreach ($element->leaflet_entries as $entry) {
            $entries_by_leaflet_id[$entry->id] = $entry->leaflet_id;
        }

        $posted_leaflet_ids = $data[$model_name]['leaflet_entries'] ?? [];

        foreach ($posted_leaflet_ids as $i => $leaflet_id) {
            // new entry, save it
            if (!in_array($leaflet_id, $entries_by_leaflet_id)) {
                $leaflet_entry = new AdviceLeafletEntry();
                $leaflet_entry->element_id = $element->id;
                $leaflet_entry->leaflet_id = $leaflet_id;
                $leaflet_entry->display_order = $i + 1;
                $leaflet_entry->save();
            }
        }

        // delete all entries not in the POST
        $leaflets_to_delete = array_diff($entries_by_leaflet_id, $posted_leaflet_ids);

        if ($leaflets_to_delete) {
            AdviceLeafletEntry::model()->deleteAllByAttributes([
                'leaflet_id' => $leaflets_to_delete
            ], 'element_id =:el_id', [':el_id' => $element->id]);
        } elseif (!$posted_leaflet_ids) {
            // no leaflet was posted, remove everything
            AdviceLeafletEntry::model()->deleteAllByAttributes([
                'element_id' => $element->id
            ]);
        }
    }

    /**
     * associate the answers and risks from the data with the Element_OphCiExamination_InjectionManagementComplex element for
     * validation.
     *
     * @param models\Element_OphCiExamination_InjectionManagementComplex $element
     * @param array $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_InjectionManagementComplex($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
            $answers = array();
            $risks = array();
            $checker = 'has' . ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side . '_Answer'])) {
                    foreach ($data[$model_name][$side . '_Answer'] as $id => $p_ans) {
                        $answer = new models\OphCiExamination_InjectionManagementComplex_Answer();
                        $answer->question_id = $id;
                        $answer->answer = $p_ans;
                        $answer->eye_id = $eye_id;
                        $answers[] = $answer;
                    }
                }
                if (isset($data[$model_name][$side . '_risks']) && is_array($data[$model_name][$side . '_risks'])) {
                    foreach ($data[$model_name][$side . '_risks'] as $risk_id) {
                        if (
                            $risk = models\OphCiExamination_InjectionManagementComplex_Risk::model()->findByPk(
                                $risk_id
                            )
                        ) {
                            $risks[] = $risk;
                        }
                    }
                }
            }
            $element->{$side . '_answers'} = $answers;
            $element->{$side . '_risks'} = $risks;
        }
    }

    /**
     * Wrapper to set validation rules on DR Grading element.
     */
    protected function setComplexAttributes_Element_OphCiExamination_DRGrading($element, $data, $index)
    {
        $this->set_DiabeticDiagnosis($element, $data);
    }

    protected function setComplexAttributes_Element_OphCiExamination_AE_RedFlags($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $_data = $data[$model_name] ?? [];
        $flag_assignments = $_data['flag_assignment'] ?? [];

        $flag_assignment_objects = [];
        foreach ($flag_assignments as $flag_assignment) {
            $new_assignment = new OphCiExamination_AE_RedFlags_Options_Assignment();
            $new_assignment->red_flag_id = $flag_assignment['red_flag_id'];
            $new_assignment->element_id = $element->id;

            $flag_assignment_objects[] = $new_assignment;
        }

        $element->flag_assignment = $flag_assignment_objects;
    }

    public function saveComplexAttributes_Element_OphCiExamination_AE_RedFlags($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $_data = $data[$model_name] ?? [];
        $posted_flag_assignment = $_data['flag_assignment'] ?? [];

        $element->refresh();
        $collection = new \ModelCollection($element->flag_assignment);
        $existing_flag_ids = $collection->pluck('red_flag_id');

        foreach ($posted_flag_assignment as $flag) {
            // is new ?
            $assignment = OphCiExamination_AE_RedFlags_Options_Assignment::model()->countByAttributes(
                ['element_id' => $element->id, 'red_flag_id' => $flag['red_flag_id']]
            );
            if (!$assignment) {
                $new_assignment = new OphCiExamination_AE_RedFlags_Options_Assignment();
                $new_assignment->red_flag_id = $flag['red_flag_id'];
                $new_assignment->element_id = $element->id;
                $new_assignment->save();
            }
        }

        // delete not posted flags
        $flag_ids_to_delete = array_diff(
            $existing_flag_ids,
            array_map(function ($f) {
                return $f['red_flag_id'];
            }, $posted_flag_assignment)
        );

        if ($flag_ids_to_delete) {
            OphCiExamination_AE_RedFlags_Options_Assignment::model()->deleteAllByAttributes([
                'red_flag_id' => $flag_ids_to_delete
            ], 'element_id =:el_id', [':el_id' => $element->id]);
        } elseif (!$posted_flag_assignment) {
            OphCiExamination_AE_RedFlags_Options_Assignment::model()->deleteAllByAttributes([
                'element_id' => $element->id
            ]);
        }
    }

    protected function setComplexAttributes_Element_OphCiExamination_ClinicProcedures($element, $data, $index)
    {
        $entries = $data['OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicProcedures']['entries'] ?: [];
        $element->refresh();
        $entry_list = [];

        foreach ($entries as $entry) {
            $procedure_entry = new models\OphCiExamination_ClinicProcedures_Entry();
            $procedure_entry->procedure_id = $entry['procedure_id'];
            $procedure_entry->outcome_time = $entry['outcome_time'];
            $date = new DateTime($entry['date']);
            $procedure_entry->date = $date->format('Y-m-d');
            $procedure_entry->comments = (array_key_exists(
                'comments',
                $entry
            ) && !empty($entry['comments'])) ? $entry['comments'] : null;
            $eye_id = 0;
            if (array_key_exists('left_eye', $entry)) {
                $eye_id += 1;
            }
            if (array_key_exists('right_eye', $entry)) {
                $eye_id += 2;
            }
            $procedure_entry->eye_id = $eye_id;
            $entry_list[] = $procedure_entry;
        }
        $element->entries = $entry_list;
    }

    protected function saveComplexAttributes_Element_OphCiExamination_ClinicProcedures($element, $data, $index)
    {
        models\OphCiExamination_ClinicProcedures_Entry::model()->deleteAll('element_id = ?', array($element->id));
        $entries = $data['OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicProcedures']['entries'] ?: [];

        foreach ($entries as $entry) {
            $procedure_entry = new models\OphCiExamination_ClinicProcedures_Entry();
            $procedure_entry->element_id = $element->id;
            $procedure_entry->procedure_id = $entry['procedure_id'];
            $procedure_entry->outcome_time = $entry['outcome_time'];
            $date = new DateTime($entry['date']);
            $procedure_entry->date = $date->format('Y-m-d');
            $procedure_entry->comments = (array_key_exists(
                'comments',
                $entry
            ) && !empty($entry['comments'])) ? $entry['comments'] : null;
            $procedure_entry->subspecialty_id = $element->event->firm->serviceSubspecialtyAssignment->subspecialty->id;
            $eye_id = 0;
            if (array_key_exists('left_eye', $entry)) {
                $eye_id += 1;
            }
            if (array_key_exists('right_eye', $entry)) {
                $eye_id += 2;
            }
            $procedure_entry->eye_id = $eye_id;
            $procedure_entry->save();
        }
    }

    /**
     * If the Patient does not currently have a diabetic diagnosis, specify that it's required
     * so the validation rules can check for it being set in the given element (currently only DR Grading).
     *
     * @param \BaseEventTypeElement $element
     * @param array $data
     */
    private function set_DiabeticDiagnosis($element, $data)
    {
        if (
            isset(Yii::app()->params['ophciexamination_drgrading_type_required'])
            && Yii::app()->params['ophciexamination_drgrading_type_required']
            && !$this->patient->getDiabetesType()
        ) {
            if (!$element->secondarydiagnosis_disorder_id) {
                $element->secondarydiagnosis_disorder_required = true;
            }
        }
    }

    /**
     * Set the diagnoses against the Element_OphCiExamination_Diagnoses element.
     *
     * @param models\Element_OphCiExamination_Diagnoses $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_Diagnoses($element, $data, $index)
    {
        $diagnoses = array();
        $model_name = \CHtml::modelName($element);
        $principal_diagnosis_row_key = \Yii::app()->request->getPost('principal_diagnosis_row_key', null);

        if (isset($data[$model_name])) {
            $diagnoses_data = $data[$model_name];


            if (
                array_key_exists(
                    'no_ophthalmic_diagnoses',
                    $diagnoses_data
                ) && $diagnoses_data['no_ophthalmic_diagnoses'] === '1'
            ) {
                if (!$element->no_ophthalmic_diagnoses_date) {
                    $element->no_ophthalmic_diagnoses_date = date('Y-m-d H:i:s');
                }
            } else {
                $element->no_ophthalmic_diagnoses_date = null;
            }

            if (isset($diagnoses_data['entries'])) {
                foreach ($diagnoses_data['entries'] as $i => $disorder) {
                    $diagnosis = null;
                    if (isset($disorder['id']) && $disorder['id']) {
                        $diagnosis = models\OphCiExamination_Diagnosis::model()->findByPk($disorder['id']);
                    }
                    if (!$diagnosis) {
                        $diagnosis = new models\OphCiExamination_Diagnosis();
                    }

                    $diagnosis->eye_id = \Helper::getEyeIdFromArray($disorder);
                    $diagnosis->disorder_id = $disorder['disorder_id'];
                    $diagnosis->principal = ($principal_diagnosis_row_key == $disorder['row_key']);
                    $diagnosis->date = isset($disorder['date']) ? $disorder['date'] : null;
                    $diagnoses[] = $diagnosis;
                }
            }
        }
        $element->diagnoses = $diagnoses;
    }

    /**
     * set the dilation treatments against the element from the provided data.
     *
     * @param models\Element_OphCiExamination_Dilation $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_Dilation(
        models\Element_OphCiExamination_Dilation $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
            $dilations = array();
            $checker = 'has' . ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side . '_treatments'])) {
                    foreach ($data[$model_name][$side . '_treatments'] as $idx => $p_treat) {
                        $dilation = null;
                        if (@$p_treat['id']) {
                            $dilation = models\OphCiExamination_Dilation_Treatment::model()->findByPk($p_treat['id']);
                        }
                        if ($dilation == null) {
                            $dilation = new models\OphCiExamination_Dilation_Treatment();
                        }
                        $dilation->attributes = $p_treat;
                        $dilations[] = $dilation;
                    }
                }
            }
            $element->{$side . '_treatments'} = $dilations;
        }
    }

    protected function setComplexAttributes_Element_OphCiExamination_DR_Retinopathy($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        foreach (
            array(
                'left' => \Eye::LEFT,
                'right' => \Eye::RIGHT,
            ) as $side => $eye_id
        ) {
            $features = array();
            $checker = 'has' . ucfirst($side);
            if ($element->$checker() && isset($data[$model_name][$side . '_retinopathy_features'])) {
                foreach ($data[$model_name][$side . '_retinopathy_features'] as $model) {
                    if (@$model['id']) {
                        if (!$feature = models\RetinopathyFeature::model()->findByPk($model['id'])) {
                            $feature = new models\RetinopathyFeature();
                        }
                    } else {
                        $feature = new models\RetinopathyFeature();
                    }
                    $feature->attributes = $model;
                    $feature->eye_id = $eye_id;
                    $features[] = $feature;
                }
            }
            $element->{$side . '_retinopathy_features'} = $features;
        }
    }

    protected function setComplexAttributes_Element_OphCiExamination_DR_Maculopathy($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        foreach (
            array(
                'left' => \Eye::LEFT,
                'right' => \Eye::RIGHT,
            ) as $side => $eye_id
        ) {
            $features = array();
            $checker = 'has' . ucfirst($side);
            if ($element->$checker() && isset($data[$model_name][$side . '_maculopathy_features'])) {
                foreach ($data[$model_name][$side . '_maculopathy_features'] as $model) {
                    if (@$model['id']) {
                        if (!$feature = models\MaculopathyFeature::model()->findByPk($model['id'])) {
                            $feature = new models\MaculopathyFeature();
                        }
                    } else {
                        $feature = new models\MaculopathyFeature();
                    }
                    $feature->attributes = $model;
                    $feature->eye_id = $eye_id;
                    $features[] = $feature;
                }
            }
            $element->{$side . '_maculopathy_features'} = $features;
        }
    }

    protected function saveComplexAttributes_Element_OphCiExamination_DR_Retinopathy($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element->updateFeatures(
            \Eye::LEFT,
            $element->hasLeft() ?
                @$data[$model_name]['left_retinopathy_features'] :
                array()
        );
        $element->updateFeatures(
            \Eye::RIGHT,
            $element->hasRight() ?
                @$data[$model_name]['right_retinopathy_features'] :
                array()
        );
    }

    protected function saveComplexAttributes_Element_OphCiExamination_DR_Maculopathy($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element->updateFeatures(
            \Eye::LEFT,
            $element->hasLeft() ?
                @$data[$model_name]['left_maculopathy_features'] :
                array()
        );
        $element->updateFeatures(
            \Eye::RIGHT,
            $element->hasRight() ?
                @$data[$model_name]['right_maculopathy_features'] :
                array()
        );
    }

    /**
     * Save question answers and risks.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_InjectionManagementComplex(
        $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        $element->updateQuestionAnswers(
            \Eye::LEFT,
            $element->hasLeft() && isset($data[$model_name]['left_Answer']) ?
                $data[$model_name]['left_Answer'] :
                array()
        );
        $element->updateQuestionAnswers(
            \Eye::RIGHT,
            $element->hasRight() && isset($data[$model_name]['right_Answer']) ?
                $data[$model_name]['right_Answer'] :
                array()
        );
        $element->updateRisks(
            \Eye::LEFT,
            $element->hasLeft() && isset($data[$model_name]['left_risks']) ?
                $data[$model_name]['left_risks'] :
                array()
        );
        $element->updateRisks(
            \Eye::RIGHT,
            $element->hasRight() && isset($data[$model_name]['right_risks']) ?
                $data[$model_name]['right_risks'] :
                array()
        );
    }

    /**
     * Save pain score.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_Pain($element, $data, $index)
    {
        $entries = $data['OEModule_OphCiExamination_models_Element_OphCiExamination_Pain']['entries'];

        foreach ($entries as $entry) {
            if (isset($entry['id'])) {
                $entry_object = \OEModule\OphCiExamination\models\OphCiExamination_Pain_Entry::model()->findByPk(
                    $entry['id']
                );
            } else {
                $entry_object = new \OEModule\OphCiExamination\models\OphCiExamination_Pain_Entry();
            }

            $entry_object->element_id = $element->id;
            $entry_object->pain_score = $entry['pain_score'];
            $entry_object->comment = $entry['comment'];
            $entry_object->datetime = $entry['datetime'];

            $entry_object->save();
        }

        $ids_to_delete = json_decode($data['pain_ids_to_delete']);
        foreach ($ids_to_delete as $id) {
            $object_to_delete = \OEModule\OphCiExamination\models\OphCiExamination_Pain_Entry::model()->findByPk($id);
            if ($object_to_delete->element_id === $element->id) {
                $object_to_delete->delete();
            } else {
                throw new \Exception("Tried to delete pain entry from another element!");
            }
        }
    }

    /**
     * Save diagnoses.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_Diagnoses($element, $data, $index)
    {
        // FIXME: the form elements for this are a bit weird, and not consistent in terms of using a standard template
        $model_name = \CHtml::modelName($element);
        $diagnoses = array();
        $principal_diagnosis_row_key = \Yii::app()->request->getPost('principal_diagnosis_row_key', null);

        // This is to accommodate a hack introduced in OE-4409
        if (isset($data[$model_name]) && isset($data[$model_name]['force_validation'])) {
            unset($data[$model_name]['force_validation']);
        }

        if (isset($data[$model_name])) {
            $diagnoses_data = $data[$model_name];
            if (isset($diagnoses_data['entries'])) {
                foreach ($diagnoses_data['entries'] as $i => $disorder) {
                    $diagnoses[] = [
                        'id' => $disorder['id'] ?? null,
                        'eye_id' => \Helper::getEyeIdFromArray($disorder),
                        'disorder_id' => $disorder['disorder_id'],
                        'principal' => ($principal_diagnosis_row_key == $disorder['row_key']),
                        'date' => isset($disorder['date']) ? $disorder['date'] : null
                    ];
                }
            }
        }

        if (!empty($data[$model_name]['disorder_id'])) {
            foreach ($data[$model_name]['disorder_id'] as $i => $disorder_id) {
                $diagnoses[] = array(
                    'eye_id' => $diagnosis_eyes[$i],
                    'disorder_id' => $disorder_id,
                    'principal' => (@$data['principal_diagnosis'] == $disorder_id),
                    'date' => isset($data[$model_name]['date'][$i]) ? $data[$model_name]['date'][$i] : null
                );
            }
        }

        $element->updateDiagnoses($diagnoses);
    }

    /**
     * Save allergies - because it's part of the History element it need to be saved from that element.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_History($element, $data, $index)
    {
        $patient = \Patient::model()->findByPk($this->patient->id);

        // we remove all current allergy data
        if (!empty($data['deleted_allergies'])) {
            foreach ($data['deleted_allergies'] as $i => $assignment_id) {
                if ($assignment_id > 0) {
                    $allergyToDel = \PatientAllergyAssignment::model()->findByPk($assignment_id);
                    if ($allergyToDel) {
                        $allergyToDel->delete();
                    }
                }
            }
        }

        if (isset($data['no_allergies']) && $data['no_allergies']) {
            $patient->setNoAllergies();
        } else {
            if (!empty($data['selected_allergies'])) {
                foreach ($data['selected_allergies'] as $i => $allergy_id) {
                    $allergyObject = \Allergy::model()->findByPk($allergy_id);
                    if ($data['other_names'][$i] == 'undefined') {
                        $data['other_names'][$i] = '';
                    }
                    $patient->addAllergy(
                        $allergyObject,
                        $data['other_names'][$i],
                        $data['allergy_comments'][$i],
                        false,
                        $this->event->id
                    );
                }
            }
        }
    }

    /**
     * Save the dilation treatments.
     *
     * @param models\Element_OphCiExamination_Dilation $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_Dilation(
        models\Element_OphCiExamination_Dilation $element,
        $data,
        $index
    ) {
        $model_name = \CHtml::modelName($element);
        $element->updateTreatments(
            \Eye::LEFT,
            $element->hasLeft() ?
                @$data[$model_name]['left_treatments'] :
                array()
        );
        $element->updateTreatments(
            \Eye::RIGHT,
            $element->hasRight() ?
                @$data[$model_name]['right_treatments'] :
                array()
        );
    }

    protected function setComplexAttributes_Element_OphCiExamination_IntraocularPressure(
        models\Element_OphCiExamination_IntraocularPressure $element,
        $data
    ) {
        $model_name = \CHtml::modelName(models\OphCiExamination_IntraocularPressure_Value::model());

        foreach (array('left', 'right') as $side) {
            $values = array();
            if (isset($data[$model_name]["{$side}_values"])) {
                foreach ($data[$model_name]["{$side}_values"] as $attrs) {
                    $value = new models\OphCiExamination_IntraocularPressure_Value();
                    $value->attributes = $attrs;

                    if ($value->instrument->scale) {
                        $value->reading_id = null;
                    } else {
                        $value->qualitative_reading_id = null;
                    }
                    $values[] = $value;
                }
            }
            $element->{"{$side}_values"} = $values;
        }
    }

    protected function saveComplexAttributes_Element_OphCiExamination_IntraocularPressure(
        models\Element_OphCiExamination_IntraocularPressure $element,
        $data
    ) {
        models\OphCiExamination_IntraocularPressure_Value::model()->deleteAll('element_id = ?', array($element->id));

        foreach (array('left', 'right') as $side) {
            foreach ($element->{"{$side}_values"} as $value) {
                $value->element_id = $element->id;
                $value->save();
            }
        }
    }

    protected function setElementDefaultOptions_Element_OphCiExamination_OverallManagementPlan(
        models\Element_OphCiExamination_OverallManagementPlan $element,
        $action
    ) {
        if (
            $previous_om = models\Element_OphCiExamination_OverallManagementPlan::model()->with(array(
                'event' => array(
                    'condition' => 'event.deleted = 0',
                    'with' => array(
                        'episode' => array(
                            'condition' => 'episode.deleted = 0 and episode.id = ' . $this->episode->id,
                        ),
                    ),
                    'order' => 'event.event_date desc, event.created_date desc',
                ),
            ))->find()
        ) {
            foreach ($previous_om->attributes as $key => $value) {
                if (
                    !in_array(
                        $key,
                        array('id', 'created_date', 'created_user_id', 'last_modified_date', 'last_modified_user_id')
                    )
                ) {
                    $element->$key = $value;
                }
            }
        }
    }

    /**
     * Actually handles the processing of patient ticketing if the module is present and a referral has been selected.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_ClinicOutcome($element, $data, $index)
    {
        $api = Yii::app()->moduleAPI->get('PatientTicketing');
        $entries = isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome']['entries']) ? $data['OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome']['entries'] : [];
        $patient_ticket_ids = models\OphCiExamination_ClinicOutcome_Status::model()->getPatientTicketIds();

        foreach ($entries as $entry) {
            if (array_search($entry['status_id'], $patient_ticket_ids) !== false) {
                $queue = $api->getQueueForUserAndFirm(Yii::app()->user, $this->firm, $data['patientticket_queue']);
                $queue_data = array_merge($data, $api->extractQueueData($queue, $data));
                if (!$api->getTicketForEvent($this->event)) {
                    $api->createTicketForEvent($this->event, $queue, Yii::app()->user->id, $this->firm, $queue_data);
                } else {
                    $api->updateTicketForEvent($this->event);
                }
            }
        }
    }

    protected function saveComplexAttributes_HistoryIOP($element, $data, $index)
    {
        $iop_element_this_event = models\Element_OphCiExamination_IntraocularPressure::model()->findByAttributes(
            ['event_id' => $this->event->id]
        );
        $data = $data['OEModule_OphCiExamination_models_HistoryIOP'];
        $examination_ids = [];
        $iop_elements = [];

        foreach (['left', 'right'] as $side) {
            if (array_key_exists("{$side}_values", $data) && $data["{$side}_values"]) {
                foreach ($data["{$side}_values"] as $index => $values) {
                    if ($values['examination_date'] !== date('d-m-Y')) {
                        if (isset($examination_ids[$values['examination_date']])) {
                            continue;
                        } else {
                            // create a new event and set the event_date as selected iop date
                            $examination_event = new \Event();
                            $examination_event->episode_id = $element->event->episode_id;
                            $examination_event->created_user_id = $examination_event->last_modified_user_id = \Yii::app(
                            )->user->id;
                            $examination_event->event_date = \DateTime::createFromFormat(
                                'd-m-Y',
                                $values['examination_date']
                            )->format('Y-m-d');
                            $examination_event->event_type_id = $element->event->event_type_id;

                            if (!$examination_event->save()) {
                                throw new \Exception(
                                    'Unable to save a new examination for the IOP readings: ' . print_r(
                                        $examination_event->errors,
                                        true
                                    )
                                );
                            }

                            $examination_ids[$values['examination_date']] = $examination_event->id;
                        }
                    }
                }
            }
        }

        foreach (['left', 'right'] as $side) {
            if (array_key_exists("{$side}_values", $data) && $data["{$side}_values"]) {
                foreach ($data["{$side}_values"] as $index => $values) {
                    if (!isset($iop_elements[$values['examination_date']])) {
                        // the same date as today: use the newly created event
                        if ($values['examination_date'] === date('d-m-Y')) {
                            // if an IOP element already exists for this event, use it
                            if ($iop_element_this_event) {
                                $iop_element = $iop_element_this_event;
                            } else {
                                // otherwise create a new iop element
                                $iop_element = new models\Element_OphCiExamination_IntraocularPressure();
                            }
                            // set event_id as current event's id
                            $iop_element->event_id = $this->event->id;
                        } else {
                            // create a new iop element and set the event_id computed before from $examination_ids
                            $iop_element = new models\Element_OphCiExamination_IntraocularPressure();
                            $iop_element->event_id = $examination_ids[$values['examination_date']];
                        }

                        // set both sides comments as not recorded
                        $iop_element["left_comments"] = "IOP values not recorded for this eye.";
                        $iop_element["right_comments"] = "IOP values not recorded for this eye.";

                        if (!$iop_element->save(false)) {
                            throw new \Exception(
                                'Unable to save a new IOP element: ' . print_r($iop_element->errors, true)
                            );
                        }

                        $iop_elements[$values['examination_date']] = $iop_element;
                    }

                    $iop_element = $iop_elements[$values['examination_date']];
                    if (isset($values["{$side}_comments"])) {
                        // override current sides comments if exists
                        $iop_element["{$side}_comments"] = $values["{$side}_comments"];

                        if (!$iop_element->save(false)) {
                            throw new \Exception(
                                'Unable to save a new IOP element: ' . print_r($iop_element->errors, true)
                            );
                        }
                    }

                    // create a reading record from the values the user has given
                    $reading = new models\OphCiExamination_IntraocularPressure_Value();
                    // examination_date and comments are not actual fields in IOP so delete them to prevent warnings
                    unset($values['examination_date']);
                    unset($values["{$side}_comments"]);
                    $reading->attributes = $values;
                    $reading->element_id = $iop_element->id;

                    if (!$reading->save()) {
                        throw new \Exception(
                            'Unable to save reading for the IOP element: ' . print_r($reading->errors, true)
                        );
                    }
                }
            }
        }
    }

    protected function saveComplexAttributes_MedicationManagement($element, $data, $index)
    {
        $data = $data['OEModule_OphCiExamination_models_MedicationManagement'];

        if (!is_null($element->prescription_id) && isset($data['prescription_reason'])) {
            $reason_other = \OphDrPrescriptionEditReasons::model()->find(
                'caption=:caption',
                [':caption' => 'Other, please specify:']
            );
            if ($reason_other) {
                $reason_other_id = $reason_other->id ?: null;
            }

            $prescription = $element->prescription;
            $edit_reason = \OphDrPrescriptionEditReasons::model()->findByPk($data['prescription_reason']);
            $audit_prescription_edit_reason = $edit_reason ? $edit_reason->caption : '';
            $prescription->edit_reason_id = $edit_reason ? $edit_reason->id : '';
            if ($data['prescription_reason'] === $reason_other_id) {
                $audit_prescription_edit_reason .= ' ' . $data['reason_other'];
                $prescription->edit_reason_other .= ' - ' . $data['reason_other'];
            }

            if (!$prescription->save()) {
                throw new \Exception("Error while saving prescription: " . print_r($prescription->getErrors(), true));
            }

            foreach ($element->entries as $entry) {
                if ($entry->hasLinkedPrescribedEntry()) {
                    $prescribed_entry = $entry->prescriptionItem();

                    $prescribed_entry->laterality = $entry->laterality;
                    $prescribed_entry->dose = $entry->dose;
                    $prescribed_entry->dose_unit_term = $entry->dose_unit_term;
                    $prescribed_entry->route_id = $entry->route_id;
                    $prescribed_entry->frequency_id = $entry->frequency_id;
                    $prescribed_entry->duration_id = $entry->duration_id;
                    $prescribed_entry->dispense_location_id = $entry->dispense_location_id;
                    $prescribed_entry->dispense_condition_id = $entry->dispense_condition_id;
                    $prescribed_entry->start_date = $entry->start_date;
                    $prescribed_entry->end_date = $entry->end_date;
                    $prescribed_entry->stop_reason_id = $entry->stop_reason_id;
                    $prescribed_entry->comments = $entry->comments;

                    $prescribed_entry->save();
                }
            }

            $prescription->event->audit(
                'event',
                'update',
                serialize(
                    array_merge(
                        $prescription->attributes,
                        ['prescription_edit_reason' => $audit_prescription_edit_reason]
                    )
                ),
                null,
                array('module' => 'Prescription', 'model' => 'Element_OphDrPrescription_Details')
            );
        }
    }

    protected function saveComplexAttributes_Element_OphCiExamination_Safeguarding($element, $data, $index)
    {
        $element_model_name = \CHtml::modelName(
            \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::model()
        );
        $element_data = $data[$element_model_name];

        if ($data['clear_safeguarding_paediatric_fields']) {
            $element->has_social_worker = 0;
            $element->under_protection_plan = 0;
            $element->accompanying_person_name = null;
            $element->responsible_parent_name = null;
        } else {
            if (array_key_exists('accompanying_person_name', $element_data)) {
                $element->accompanying_person_name = $element_data['accompanying_person_name'];
            }
            if (array_key_exists('responsible_parent_name', $element_data)) {
                $element->responsible_parent_name = $element_data['responsible_parent_name'];
            }
        }

        $element->save();

        $existing_entries = \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry::model(
        )->findAllByAttributes(array('element_id' => $element->id));

        if (isset($data[$element_model_name]['entries'])) {
            $entries = $data[$element_model_name]['entries'];

            foreach ($entries as $entry) {
                $entry_object = null;

                foreach ($existing_entries as $key => $existing_entry) {
                    if ($entry['concern_id'] == $existing_entry->concern_id) {
                        $entry_object = $existing_entry;
                        unset($existing_entries[$key]);
                        break;
                    }
                }

                if (!isset($entry_object)) {
                    $entry_object = new \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry();
                }

                $entry_object->element_id = $element->id;
                $entry_object->concern_id = $entry['concern_id'];
                if (isset($entry['comment']) && !empty($entry['comment'])) {
                    $entry_object->comment = $entry['comment'];
                }

                $entry_object->save();
            }
        }

        //The only entries remaining in the existing_entries list are those that were not found in POST data, indicating that they were not in the front end list
        foreach ($existing_entries as $object_to_delete) {
            if ($object_to_delete->element_id === $element->id) {
                $object_to_delete->delete();
            } else {
                throw new \Exception("Tried to delete safeguarding entry from another element!");
            }
        }
    }

    /**
     * @return bool
     */
    public function checkSafeguardingAccess()
    {
        return $this->checkAccess('Safeguarding', Yii::app()->session['user']);
    }

    public function actionResolveSafeguardingElement()
    {
        $element = \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::model()->findByPk(
            $_POST['element_id']
        );

        $element->outcome_id = $_POST['outcome_id'];
        $element->outcome_comments = $_POST['outcome_comments'];

        // Using == instead of === as the POST request values are stringified
        if ($element->outcome_id == \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::CONFIRM_SAFEGUARDING_CONCERNS) {
            $risk_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();

            $risk_entry->risk_id = \OEModule\OphCiExamination\models\OphCiExaminationRisk::model()->findByAttributes(
                array('name' => 'Safeguarding')
            )->id;
            $risk_entry->comments = $element->outcome_comments;
            $risk_entry->has_risk = 1;

            $risks_element =
                $element->event->getElementByClass(\OEModule\OphCiExamination\models\HistoryRisks::class) ?:
                    new \OEModule\OphCiExamination\models\HistoryRisks();

            $existing_entries = $risks_element->entries;

            $risks_element->event_id = $element->event_id;

            $existing_entries[] = $risk_entry;

            $risks_element->entries = $existing_entries;

            if (!$risks_element->save()) {
                echo \CJSON::encode(array("success" => false, "errors" => $risks_element->getErrors()));
                return;
            }
        }

        if (!$element->save()) {
            echo \CJSON::encode(array("success" => false, "errors" => $element->getErrors()));
            return;
        }

        echo \CJSON::encode(array("success" => true));
    }

    /**
     * Unpacks any data that has been sent in JSON form.
     *
     * @param array $data
     * @return array $data
     */
    protected function unpackJSONAttributes($data)
    {
        foreach ($data as $elementName => $elementData) {
            if (is_array($elementData) && array_key_exists('JSON_string', $elementData)) {
                $data[$elementName] = json_decode(
                    str_replace("'", '"', $data[$elementName]['JSON_string']),
                    true
                );
            }
        }
        return $data;
    }

    /**
     * custom validation for virtual clinic referral.
     *
     * this should hand off validation to a faked PatientTicket request via the API.
     *
     * @param array $data
     * @return array|mixed
     * @throws \Exception
     */
    protected function setAndValidateElementsFromData($data)
    {
        $data = $this->unpackJSONAttributes($data);
        $errors = parent::setAndValidateElementsFromData($data);

        if (isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_CommunicationPreferences'])) {
            $errors = $this->setAndValidateCommunicationPreferencesFromData($data, $errors);
        }

        if (isset($data['OEModule_OphGeneric_models_Assessment'])) {
            $errors = $this->setAndValidateOctFromData($data, $errors);
        }

        if (isset($data['OEModule_OphCiExamination_models_HistoryIOP'])) {
            $errors = $this->setAndValidateHistoryIopFromData($data, $errors);
        }

        $history_meds = $this->getOpenElementByClassName('OEModule_OphCiExamination_models_HistoryMedications');
        if ($history_meds) {
            $errors = $this->setAndValidateHistoryMedicationsFromData($errors, $history_meds);
        }

        $posted_risk = [];
        if (isset($data['OEModule_OphCiExamination_models_HistoryRisks']['entries'])) {
            $posted_risk = array_map(function ($r) {
                return $r['risk_id'];
            }, $data['OEModule_OphCiExamination_models_HistoryRisks']['entries']);
        }

        // Element was open, we check the required risks
        if (isset($data['OEModule_OphCiExamination_models_HistoryRisks'])) {
            $errors = $this->setAndValidateHistoryRisksFromData($errors, $posted_risk);
        }

        $api = Yii::app()->moduleAPI->get('PatientTicketing');
        if (isset($data['patientticket_queue']) && $api) {
            $errors = $this->setAndValidatePatientTicketingFromData($data, $errors, $api);
        }

        if (isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses'])) {
            $errors = $this->setAndValidateOphthalmicDiagnosesFromData($data, $errors);
        }

        if (isset($data['OEModule_OphCiExamination_models_PupillaryAbnormalities'])) {
            $errors = $this->setAndValidatePupillaryAbnormalitiesFromData($data, $errors);
        }

        if (isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_Observations'])) {
            $errors = $this->setAndValidateObservationsFromData($data, $errors);
        }

        return $errors;
    }

    protected function setAndValidateObservationsFromData($data, $errors)
    {
        $et_name = 'OEModule_OphCiExamination_models_Element_OphCiExamination_Observations';
        $observation = $this->getOpenElementByClassName($et_name);
        $posted_entries = $data[$et_name]['entries'];

        foreach ($posted_entries as $i => $posted_entry) {
            if (isset($entry['id'])) {
                $entry = models\ObservationEntry::model()->findByPk($entry['id']);
            } else {
                $entry = new models\ObservationEntry();
            }

            $entry->attributes = $posted_entry;

            if (!$entry->validate()) {
                $entry_errors = $entry->getErrors();

                foreach ($entry_errors as $entry_error_attribute_name => $entry_error_messages) {
                    foreach ($entry_error_messages as $entry_error_message) {
                        $observation->addError(
                            "entries" . $i . '_' . $entry_error_attribute_name,
                            $entry_error_message
                        );
                        $errors['Observations'][] = $i + 1 . " {$entry->getAttributeLabel($entry_error_attribute_name)} {$entry_error_message}";
                        $observation->setFrontEndError($et_name . '_entries_' . $i . '_' . $entry_error_attribute_name);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Custom validation on HistoryIOP element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateHistoryIopFromData($data, $errors)
    {
        $et_name = models\HistoryIOP::model()->getElementTypeName();
        $historyIOP = $this->getOpenElementByClassName('OEModule_OphCiExamination_models_HistoryIOP');
        $entries = $data['OEModule_OphCiExamination_models_HistoryIOP'];
        foreach (['left', 'right'] as $side) {
            if (isset($entries["{$side}_values"])) {
                // set the examination dates in HistoryIOP model for custom validation
                $historyIOP->examination_dates["{$side}_values"] = array_column(
                    $entries["{$side}_values"],
                    'examination_date'
                );

                foreach ($entries["{$side}_values"] as $index => $value) {
                    $reading = new models\OphCiExamination_IntraocularPressure_Value();
                    // examination_date and comments are not actual fields in IOP so delete them to prevent warnings
                    unset($value['examination_date']);
                    unset($value["{$side}_comments"]);
                    $reading->attributes = $value;
                    if (!$reading->validate()) {
                        $readingErrors = $reading->getErrors();
                        foreach ($readingErrors as $readingErrorAttributeName => $readingErrorMessages) {
                            foreach ($readingErrorMessages as $readingErrorMessage) {
                                $historyIOP->addError(
                                    "{$side}_values" . '_' . $index . '_' . $readingErrorAttributeName,
                                    $readingErrorMessage
                                );
                                $errors[$et_name][] = $readingErrorMessage;
                            }
                        }
                    }
                }
            }
        }

        // validate historyIOP (examination dates especially)
        if (!$historyIOP->validate()) {
            foreach ($historyIOP->getErrors() as $index => $HistoryIOP_errors) {
                foreach ($HistoryIOP_errors as $error) {
                    $errors[$et_name][] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * Custom validation on Pupillary Abnormalities element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidatePupillaryAbnormalitiesFromData($data, $errors)
    {
        $et_name = models\PupillaryAbnormalities::model()->getElementTypeName();
        $data = $data['OEModule_OphCiExamination_models_PupillaryAbnormalities'];
        $pupillary_abnormalities = $this->getOpenElementByClassName(
            'OEModule_OphCiExamination_models_PupillaryAbnormalities'
        );

        $pupillary_abnormalities->eye_id = $data['eye_id'];

        foreach (['left', 'right'] as $side) {
            if ($pupillary_abnormalities->hasEye($side)) {
                if (isset($data['entries_' . $side])) {
                    $entries = [];

                    foreach ($data['entries_' . $side] as $index => $value) {
                        $entry = new models\PupillaryAbnormalityEntry();
                        $entry->attributes = $value;
                        $entries[] = $entry;
                        if (!$entry->validate()) {
                            $entryErrors = $entry->getErrors();
                            foreach ($entryErrors as $entryErrorAttributeName => $entryErrorMessages) {
                                foreach ($entryErrorMessages as $entryErrorMessage) {
                                    $pupillary_abnormalities->addError(
                                        "entries_{$side}_" . $index . '_' . $entryErrorAttributeName,
                                        $entryErrorMessage
                                    );
                                    $errors[$et_name][] = ucfirst($side) . ' ' . $entry->getDisplayAbnormality(
                                    ) . " - " . $entryErrorMessage;
                                }
                            }
                        }
                    }
                    $pupillary_abnormalities->{'entries_' . $side} = $entries;
                }
            }
        }

        return $errors;
    }

    /**
     * @param $errors
     * @param $history_meds
     * @return mixed
     */
    protected function setAndValidateHistoryMedicationsFromData($errors, $history_meds)
    {
        if ($history_meds->hasRisks()) {
            if (!$this->getOpenElementByClassName('OEModule_OphCiExamination_models_HistoryRisks')) {
                if (!array_key_exists($this->event_type->name, $errors)) {
                    $errors[$this->event_type->name] = array();
                }
                $errors[$this->event_type->name][] = 'History Risks element is required when History Medications has entries with associated Risks';
            }
        }
        return $errors;
    }

    /**
     * @param $errors
     * @param $posted_risk
     * @return mixed
     */
    protected function setAndValidateHistoryRisksFromData($errors, $posted_risk)
    {
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');

        $missing_risks = [];
        foreach ($exam_api->getRequiredRisks($this->patient) as $required_risk) {
            if (!in_array($required_risk->id, $posted_risk)) {
                $missing_risks[] = $required_risk;
            }
        }

        $et_name = models\HistoryRisks::model()->getElementTypeName();
        foreach ($missing_risks as $missing_risk) {
            $errors[$et_name][$missing_risk->name] = 'Missing required risks: ' . $missing_risk->name;
        }

        return $errors;
    }

    /**
     * Custom validation for patient ticketing
     *
     * @param $data
     * @param $errors
     * @param $api
     * @return mixed
     */
    protected function setAndValidatePatientTicketingFromData($data, $errors, $api)
    {
        $err = [];
        if (isset($data['patientticket_queue'])) {
            if (empty($data['patientticket_queue'])) {
                $err['patientticket_queue'] = 'You must select a valid Virtual Clinic for referral';
            } else {
                $queue = $api->getQueueForUserAndFirm(Yii::app()->user, $this->firm, $data['patientticket_queue']);
                if (!$queue) {
                    $err['patientticket_queue'] = 'Virtual Clinic not found';
                } else {
                    if (
                        QueueOutcome::model()->exists('queue_id=:queue_id', [':queue_id' => $queue->id]) && $api->canAddPatientToQueue($this->patient, $queue)
                    ) {
                        list($ignore, $fld_errs) = $api->extractQueueData($queue, $data, true);
                        $err = array_merge($err, $fld_errs);
                    }
                }
            }
        }

        if (count($err)) {
            $et_name = models\Element_OphCiExamination_ClinicOutcome::model()->getElementTypeName();
            if (isset($errors[$et_name]) && $errors[$et_name]) {
                $errors[$et_name] = array_merge($errors[$et_name], $err);
            } else {
                $errors[$et_name] = $err;
            }
        }

        return $errors;
    }

    protected function setAndValidateOphthalmicDiagnosesFromData($data, $errors)
    {
        $et_name = models\Element_OphCiExamination_Diagnoses::model()->getElementTypeName();
        $diagnoses = $this->getOpenElementByClassName(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses'
        );

        $entries = array_key_exists(
            'entries',
            $data['OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses']
        ) ?
            $data['OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses']['entries'] :
            [];
        $duplicate_exists = false;

        $concat_occurrences = [];
        foreach ($entries as $entry) {
            if (isset($entry['right_eye']) && isset($entry['left_eye'])) {
                $eye_id = \Eye::BOTH;
            } elseif (isset($entry['right_eye'])) {
                $eye_id = \Eye::RIGHT;
            } elseif (isset($entry['left_eye'])) {
                $eye_id = \Eye::LEFT;
            } else {
                continue;
            }

            // create a string with concatenation of  the columns that must be unique
            $concat_data = $eye_id . $entry['disorder_id'] . $entry['date'];

            // do not take into consideration rows that have an id associated as they are already in the database
            if (isset($entry['id']) && $entry['id']) {
                $concat_occurrences[] = $concat_data;
                continue;
            }

            // search if the input is already present in the database
            $not_already_exists = true;
            if (isset($diagnoses->id)) {
                $criteria = new \CDbCriteria();
                $criteria->addCondition('element_diagnoses_id=:element_diagnoses_id');
                $criteria->addCondition('eye_id=:eye_id');
                $criteria->addCondition('disorder_id=:disorder_id');
                $criteria->addCondition('date=:date');

                $criteria->params[":element_diagnoses_id"] = $diagnoses->id;
                $criteria->params[":eye_id"] = $eye_id;
                $criteria->params[":disorder_id"] = $entry['disorder_id'];
                $criteria->params[":date"] = $entry['date'];

                $count_saved_diagnosis = models\OphCiExamination_Diagnosis::model()->count($criteria);

                // allow no more than one appearance in the database
                $not_already_exists = $count_saved_diagnosis <= 1;
            }

            // if the concatenated info is not already present (neither on the screen nor in the database)
            if ($not_already_exists && !in_array($concat_data, $concat_occurrences)) {
                // add it to the known array
                $concat_occurrences[] = $concat_data;
            } else {
                $duplicate_exists = true;
            }
        }

        // if there is any duplicate, add error message
        if ($duplicate_exists) {
            $errors[$et_name][] = "You have 1 or more duplicate diagnoses. Each combination of diagnosis, eye side and date must be unique.";
        }

        return $errors;
    }

    protected function setComplexAttributes_Element_OphCiExamination_FurtherFindings($element, $data, $index)
    {
        $assignments = array();

        if (!empty($data['OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']['further_findings_assignment'])) {
            foreach ($data['OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']['further_findings_assignment'] as $i => $item) {
                if (!$finding = \Finding::model()->findByPk($item['id'])) {
                    throw new \Exception("Finding not found: {$item['id']}");
                }
                $assignment = new models\OphCiExamination_FurtherFindings_Assignment();
                $assignment->finding_id = $finding->id;
                $assignment->description = @$item['description'];

                $assignments[] = $assignment;
            }
        }

        $element->further_findings_assignment = $assignments;
    }

    protected function saveComplexAttributes_Element_OphCiExamination_FurtherFindings($element, $data, $index)
    {
        $ids = array();

        if (!empty($element->further_findings_assignment)) {
            foreach ($element->further_findings_assignment as $assignment) {
                $assignment->element_id = $element->id;

                if (!$assignment->save()) {
                    throw new \Exception(
                        'Unable to save further finding assignment: ' . print_r($assignment->errors, true)
                    );
                }

                $ids[] = $assignment->id;
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_id = :eid');
        $criteria->params[':eid'] = $element->id;

        if (!empty($ids)) {
            $criteria->addNotInCondition('id', $ids);
        }

        models\OphCiExamination_FurtherFindings_Assignment::model()->deleteAll($criteria);
    }

    protected function saveComplexAttributes_Element_OphCiExamination_Contacts($element, $data, $index)
    {
        $patient = \Patient::model()->findByPk($this->patient->id);
        if (
            isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts']) &&
            isset($data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['contact_id'])
        ) {
            $contact_ids = $data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['contact_id'];
            $comments = $data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['comments'];
        } else {
            $contact_ids = [];
        }
        $patientContactAssignments = \PatientContactAssignment::model()->findAll(
            "patient_id = ?",
            [$patient->id]
        );


        foreach ($contact_ids as $key => $contact_id) {
            $foundExistingAssignment = false;
            foreach ($patientContactAssignments as $patientContactAssignment) {
                if ($patientContactAssignment->contact_id == $contact_id) {
                    $patientContactAssignment->comment = $comments[$key];
                    $patientContactAssignment->save();
                    $foundExistingAssignment = true;
                    break;
                }
            }
            if (!$foundExistingAssignment) {
                $patientContactAssignment = new \PatientContactAssignment();
                $patientContactAssignment->patient_id = $patient->id;
                $patientContactAssignment->contact_id = $contact_id;
                $patientContactAssignment->comment = isset($comments[$key]) ? $comments[$key] : null;
                $patientContactAssignment->save();
            }
        }

        $patientContactAssignments = array_filter(
            $patientContactAssignments,
            function ($assignment) use ($contact_ids) {
                return !in_array($assignment->contact_id, $contact_ids);
            }
        );

        foreach ($patientContactAssignments as $patientContactAssignment) {
            $patientContactAssignment->delete();
        }
    }

    private function getOtherSide($side1, $side2, $selectedSide)
    {
        return $selectedSide === $side1 ? $side2 : $side1;
    }

    public function actionGetOctAssessment($assessment_ids)
    {
        $assessment_ids = json_decode($assessment_ids);
        $event_type = \EventType::model()->find('name = "Examination"');
        $assessments = [];
        $api = Yii::app()->moduleAPI->get('OphGeneric');


        foreach ($assessment_ids as $assessment_id) {
            $assessment = Assessment::model()->findByPk($assessment_id);
            $eye = $api->getLaterality($assessment->event->id);
            $datetime = new DateTime($assessment->event->event_date);

            if (intval($eye->id) === Eye::BOTH) {
                $html_left = "";
                $html_right = "";

                foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) {
                    $html = '<div class="js-element-eye ' . $eye_side . '-eye ' . $page_side . '" data-side="' . $eye_side . '">'
                                . '<div class="assessment cols-full" data-assessment-id="' . $assessment->id . '"'
                                . ' data-assessment-side="' . $eye->name . '"'
                                . ' data-assessment-date="' . $datetime->format('Ymd') . '"'
                                . ' data-assessment-time="' . $datetime->format('His') . '">'
                                . $this->widget('application.modules.OphGeneric.widgets.Assessment', [
                                    // TODO during the development cycle this will be overridden in the widget init
                                    'assessment' => $assessment,
                                    'entry' => $assessment->{$eye_side . "_assessment"},
                                    'patient' => $this->patient,
                                    'event_type' => $event_type,
                                    'side' => $eye_side,
                                    'key' => $assessment->id
                                ], true)
                                . '</div>'
                                . ($page_side === "right" ? '<div class="flex-layout flex-right"><i class="oe-i trash js-delete-assessment"></i></div>' : '')
                                . '</div>';
                    if ($eye_side === "left") {
                        $html_left = $html;
                    } else {
                        $html_right = $html;
                    }
                }
                $assessments[] = [
                    "html_left" => $html_left,
                    "html_right" => $html_right,
                    "side" => $eye->name,
                    "date" => $datetime->format('Ymd'),
                    "time" => $datetime->format('His')
                ];
            } else {
                $html = '';
                if (intval($eye->id) === Eye::LEFT) {
                    $page_side = 'right';
                } else {
                    $page_side = 'left';
                }
                $eye_side = strtolower($eye->getAdjective());
                $html .= '<div class="js-element-eye ' . $eye_side . '-eye ' . $page_side . '" data-side="' . $eye_side . '">';

                $assessments[] = [
                    "html" => $html
                        . '<div class="assessment cols-full" data-assessment-id="' . $assessment->id . '"'
                        . ' data-assessment-side="' . $eye->name . '"'
                        . ' data-assessment-date="' . $datetime->format('Ymd') . '"'
                        . ' data-assessment-time="' . $datetime->format('His') . '">'
                        . $this->widget('application.modules.OphGeneric.widgets.Assessment', [
                            // TODO TODO TODO during the development cycle this will be overridden in the widget init
                            'assessment' => $assessment,
                            'entry' => $assessment->readings[0],
                            'patient' => $this->patient,
                            'event_type' => $event_type,
                            'key' => $assessment->id,
                            'side' => $eye_side
                        ], true)
                        . '</div><div class="flex-layout flex-right"><i class="oe-i trash js-delete-assessment"></i></div></div>',
                    "side" => $eye->name,
                    "date" => $datetime->format('Ymd'),
                    "time" => $datetime->format('His')
                ];
            }
        }

        $this->renderJSON($assessments);
    }

    public function actionGetAttachment($assessment_ids)
    {
        $assessment_ids = json_decode($assessment_ids);
        $event_ids = [];

        foreach ($assessment_ids as $assessment_id) {
            $assessment = Assessment::model()->findByPk($assessment_id);
            $event_ids[] = $assessment->event_id;
        }

        $this->widget(
            'application.modules.OphGeneric.widgets.Attachment',
            [
                'event_ids' => $event_ids,
                'allow_attach' => false,
                'element' => null,
                'show_titles' => true,
                'is_examination' => true,
            ]
        );
    }

    protected function saveComplexAttributes_OCT($element, $data, $index)
    {
        if (isset($data['OEModule_OphGeneric_models_Assessment'])) {
            $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

            foreach ($entries as $assessment_id => $assessment_entry_sides) {
                foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                    $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                    $assessment_entry->attributes = $assessment_entry_data;
                    $assessment_entry->save(false);
                }
            }
        }
    }

    /**
     * Custom validation for OCT element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateOctFromData($data, $errors)
    {
        $et_name = Assessment::model()->getElementTypeName();
        $OCT = $this->getOpenElementByClassName('OEModule_OphCiExamination_models_OCT');
        $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

        //TODO: make validation error link to Assessment work:
        // currently, "addError()" is not called so function "scrolToElement()" is not called

        foreach ($entries as $assessment_id => $assessment_entry_sides) {
            foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                $assessment_entry->attributes = $assessment_entry_data;

                if (!$assessment_entry->validate()) {
                    $assessmentErrors = $assessment_entry->getErrors();
                    foreach ($assessmentErrors as $assessmentErrorsAttributeName => $assessmentErrorsMessages) {
                        foreach ($assessmentErrorsMessages as $assessmentErrorMessage) {
                            $OCT->setFrontEndError(
                                'OEModule_OphGeneric_models_Assessment_entries_' . $assessment_id . '_' . $eye_side . '_' . $assessmentErrorsAttributeName
                            );
                            $errors[$et_name][] = $assessmentErrorMessage;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Custom validation for Communication Preferences element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateCommunicationPreferencesFromData($data, $errors)
    {
        $et_name = models\Element_OphCiExamination_CommunicationPreferences::model()->getElementTypeName();
        $agrees_to_insecure_email_correspondence = $data['OEModule_OphCiExamination_models_Element_OphCiExamination_CommunicationPreferences']['agrees_to_insecure_email_correspondence'];
        if ($agrees_to_insecure_email_correspondence === '1') {
            // check if the email is empty
            $contactEmail = Yii::app()->request->getPost('Contact', null);
            if ($contactEmail['email'] === '') {
                $errors[$et_name][] = 'Please enter an email address.';
            }
        }
        return $errors;
    }

    /**
     * Updates the email address in the contact data model for the Communication Preferences element.
     *
     * @param $data
     * @throws \CException
     */
    protected function saveContactEmailAddressForCommunicationPreferences($data)
    {
        if (
            isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_CommunicationPreferences']) &&
            $data['OEModule_OphCiExamination_models_Element_OphCiExamination_CommunicationPreferences']['agrees_to_insecure_email_correspondence'] === '1'
        ) {
            $contactEmail = Yii::app()->request->getPost('Contact');
            $Contact = \Contact::model()->findByPk($contactEmail['id']);
            $Contact->email = $contactEmail['email'];
            if (!$Contact->update(["email"])) {
                throw new \CException('Cannot save contact');
            }
        }
    }

    protected function getPastClinicProcedures()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        $procedure_elements = $exam_api->getElements(
            'models\Element_OphCiExamination_ClinicProcedures',
            $this->patient,
            false,
            null,
            null
        );
        return $procedure_elements;
    }

    protected function getTriageTreatAsField($element)
    {
        $model_name = \CHtml::modelName($element);
        $age = $this->patient->getAge();
        if ($age < 13) {
            return '<label class="highlight inline"><input value="0" name="' . $model_name . '[triage][treat_as_adult]" type="hidden">Paediatric</label>';
        } elseif ($age < 16) {
            return '<label class="highlight inline"><input value="0" name="' . $model_name . '[triage][treat_as_adult]" type="radio" checked>Paediatric</label>
                    <label class="highlight inline"><input value="1" name="' . $model_name . '[triage][treat_as_adult]" type="radio">Adult</label>';
        } elseif ($age < 18) {
            return '<label class="highlight inline"><input value="0" name="' . $model_name . '[triage][treat_as_adult]" type="radio">Paediatric</label>
                    <label class="highlight inline"><input value="1" name="' . $model_name . '[triage][treat_as_adult]" type="radio" checked>Adult</label>';
        } else {
            return '<label class="highlight inline"><input value="1" name="' . $model_name . '[triage][treat_as_adult]" type="hidden">Adult</label>';
        }
    }

    public function actionMedicationManagementEditable($patient_id, $event_date)
    {
        $this->renderJSON(self::getMedicationManagementEditable($patient_id, $event_date));
    }

    public static function getMedicationManagementEditable($patient_id, $event_date)
    {
        $patient = \Patient::model()->findByPk($patient_id);
        $api = \Yii::app()->moduleAPI->get('OphCiExamination');
        $latest_mm_datetime = null;
        $latest_mh_datetime = null;

        $latest_med_management_element = $api->getLatestElement('models\MedicationManagement', $patient);
        $latest_med_history_element = $api->getLatestElement('models\HistoryMedications', $patient);
        if ($latest_med_management_element) {
            $latest_mm_datetime = $latest_med_management_element->event ? ($latest_med_management_element->event->event_date ?? null) : null;
        }
        if ($latest_med_history_element) {
            $latest_mh_datetime = $latest_med_history_element->event ? ($latest_med_history_element->event->event_date ?? null) : null;
        }

        $latest_med_element_datetime = max($latest_mm_datetime, $latest_mh_datetime);

        $current_datetime = new DateTime();

        $event_datetime = DateTime::createFromFormat('d M Y', $event_date);

        $error_messages = array();

        if ($latest_med_element_datetime > $event_datetime) {
            $error_messages[] = 'Patient has a more recent event with a medication element';
        }

        if ($current_datetime->diff($event_datetime)->days > 2) {
            $error_messages[] = 'The editable period for this element has passed';
        }

        return array('errorMessages' => $error_messages);
    }

    /** Return contexts based on Subspecialty Id
     *
     * @param $contexts
     * @param $subspecialty_id
     * @return array
     */
    public function getContextFromSubspecialty($contexts, $subspecialty_id)
    {
        foreach ($contexts as $context) {
            if ($context['id'] == $subspecialty_id) {
                return $context['contexts'];
            }
        }

        return [];
    }
}
