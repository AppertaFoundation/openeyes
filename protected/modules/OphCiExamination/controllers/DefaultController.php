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

use Yii;
use OEModule\OphCiExamination\models;
use OEModule\OphCiExamination\components;

/*
 * This is the controller class for the OphCiExamination event. It provides the required methods for the ajax loading of elements, and rendering the required and optional elements (including the children relationship)
 */

class DefaultController extends \BaseEventTypeController
{
    protected static $action_types = array(
        'step' => self::ACTION_TYPE_EDIT,
        'getDisorder' => self::ACTION_TYPE_FORM,
        'loadInjectionQuestions' => self::ACTION_TYPE_FORM,
        'getScaleForInstrument' => self::ACTION_TYPE_FORM,
        'getPreviousIOPAverage' => self::ACTION_TYPE_FORM,
        'getPostOpComplicationList' => self::ACTION_TYPE_FORM,
        'getPostOpComplicationAutocopleteList' => self::ACTION_TYPE_FORM,
        'dismissCVIalert' => self::ACTION_TYPE_FORM
    );

    /**
     * Set to true if the index search bar should appear in the header when creating/editing the event
     *
     * @var bool
     */
    protected $show_index_search = true;

    // if set to true, we are advancing the current event step
    private $step = false;

    protected $set;

    protected $mandatoryElements;

    protected $allergies = array();

    protected $deletedAllergies = array();

    public function getTitle()
    {
        $title = parent::getTitle();
        $current = $this->step ? : $this->getCurrentStep();
        if (count($current->workflow->steps) > 1) {
            $title .= ' (' . $current->name . ')';
        }
        return $title;
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
        $this->jsVars['default_iris_colour'] = \SettingMetadata::model()->getSetting('OphCiExamination_default_iris_colour');
        return parent::beforeAction($action);
    }

    /**
     * Applies workflow and filtering to the element retrieval.
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getEventElements()
    {
        if (!$this->event || $this->event->isNewRecord) {
            $elements = $this->getElementsByWorkflow(null, $this->episode);
        } else {
            $elements = $this->event->getElements();
            if ($this->step) {
                $elements = $this->mergeNextStep($elements);
            }
        }

        return $this->filterElements($elements);
    }

    /**
     * Check data in child elements
     *
     * @param \BaseEventTypeElement[] $elements
     * @return boolean
     */
    protected function checkElementsForData($elements)
    {
        foreach($elements as $element)
        {
            if($element->id > 0)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * List of elements that should be filtered out from the event.
     *
     * @return array
     */
    protected function getElementFilterList($include_hidden=true)
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
                if($el->id > null || $this->checkElementsForData($this->getElements($el->getElementType()))) {
                    $final[] = $el;
                }
            }else{
                $final[] = $el;
            }
        }
        return $final;
    }

    /**
     * Get all the available element types for the event
     *
     * @return array
     */
    public function getAllElementTypes()
    {
        $remove = $this->getElementFilterList(false);
        return array_filter(
            parent::getAllElementTypes(),
            function($et) use ($remove) {
                return !in_array($et->class_name, $remove);
            });
    }

    public function getElementTree($remove_list = array())
    {
        return parent::getElementTree($this->getElementFilterList());
    }

    /**
     * Sets up jsvars for editing.
     */
    protected function initEdit()
    {
        $this->jsVars['Element_OphCiExamination_IntraocularPressure_link_instruments'] = models\Element_OphCiExamination_IntraocularPressure::model()->getSetting('link_instruments') ? 'true' : 'false';

        if (Yii::app()->hasModule('OphCoTherapyapplication')) {
            $this->jsVars['OphCiExamination_loadQuestions_url'] = $this->createURL('loadInjectionQuestions');
        }

        $this->jsVars['Element_OphCiExamination_Refraction_sphere'] = array();

        foreach (models\OphCiExamination_Refraction_Sphere_Integer::model()->findAll(array('order' => 'display_order asc')) as $si) {
            $this->jsVars['Element_OphCiExamination_Refraction_sphere'][$si->sign_id][] = $si->value;
        }

        $this->jsVars['Element_OphCiExamination_Refraction_cylinder'] = array();

        foreach (models\OphCiExamination_Refraction_Cylinder_Integer::model()->findAll(array('order' => 'display_order asc')) as $si) {
            $this->jsVars['Element_OphCiExamination_Refraction_cylinder'][$si->sign_id][] = $si->value;
        }

        Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/core.js", \CClientScript::POS_HEAD);

        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath);

        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.UI.InputFieldValidation.js', \CClientScript::POS_END);
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
     * Call editInit to setup jsVars.
     */
    public function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit();
    }

    public function initActionStep()
    {
        $this->initActionUpdate();
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

            if ($this->episode->diagnosis) {
                $principal_diagnosis = $exam_api->getPrincipalOphtalmicDiagnosis($this->episode, $this->episode->diagnosis->id);

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
                    if ($d->disorder_id == $ad->disorder_id) {
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

    /**
     * Set the allergies against the Element_OphCiExamination_Allergy element
     * It's a child element of History.
     *
     * @param Element_OphCiExamination_History $element
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
        $step_id = \Yii::app()->request->getParam('step_id');

        if ($step_id) {
            $this->step = models\OphCiExamination_ElementSet::model()->findByPk($step_id);
        } else {
            $this->step = $this->getCurrentStep()->getNextStep();
        }

        // This is the same as update, but with a few extras, so we call the update code and then pick up on the action later
        $this->actionUpdate($id);
    }

    public function renderOpenElements($action, $form = null, $date = null)
    {
        $elements = $this->getElements($action);

        /* @var \OEModule\OphCoCvi\components\OphCoCvi_API $cvi_api */
        $cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
        /* @var models\Element_OphCiExamination_VisualAcuity $element */

        $visual_acuities = array_filter($elements, function ($element) {
            return get_class($element) === models\Element_OphCiExamination_VisualAcuity::class;
        });
        $visualAcuity = array_shift($visual_acuities);

        // Render the CVI alert above all th other elements
        if ($cvi_api) {
            echo $cvi_api->renderAlertForVA($this->patient, $visualAcuity, $action === 'view');
        }

        if ($action !== 'view' && $action !== 'createImage') {
            parent::renderOpenElements($action, $form, $date);

            return;
        }

        $this->renderPartial('view_summary', array('action' => $action, 'form' => $form, 'data' => $date));

        $filteredElements = array_filter($elements, function ($element) {
            return !in_array(get_class($element), array(
                // Ignore elements that are displayed in the view summary
                models\Element_OphCiExamination_History::class,
                models\PastSurgery::class,
                models\SystemicDiagnoses::class,
                models\Element_OphCiExamination_Diagnoses::class,
                models\HistoryMedications::class,
                models\FamilyHistory::class,
                models\SocialHistory::class,
                models\HistoryIOP::class,
            ), true);
        });

        $this->renderElements($filteredElements, $action, $form, $date);
    }

    /**
     * Override action value when action is step to be update.
     *
     * @param \BaseEventTypeElement $element
     * @param string $action
     * @param \BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     * @param array $view_data
     * @param bool $return
     * @param bool $processOutput
     * @throws \Exception
     */
    protected function renderElement($element, $action, $form, $data, $view_data = array(), $return = false, $processOutput = false)
    {
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

        parent::renderElement($element, $action, $form, $data, $view_data, $return, $processOutput);
    }

    /**
     * Advance the workflow step for the event if requested.
     *
     * @param Event $event
     *
     * @throws \CException
     */
    protected function afterUpdateElements($event)
    {
        parent::afterUpdateElements($event);
        $this->persistPcrRisk();
        if ($this->step) {
            // Advance the workflow
            if (!$assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id))) {
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
    }

    protected function afterCreateElements($event)
    {
        parent::afterCreateElements($event);
        $this->persistPcrRisk();
        if ($this->step) {
            // Advance the workflow
            if (!$assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id))) {
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
    }

    public function getOptionalElements()
    {
        $elements = parent::getOptionalElements();

        return $this->filterElements($elements);
    }

    /**
     * Get the first workflow step using rules.
     *
     * @return OphCiExamination_ElementSet
     */
    protected function getFirstStep()
    {
        $firm_id = $this->firm->id;
        $status_id = ($this->episode) ? $this->episode->episode_status_id : 1;
        $workflow = new models\OphCiExamination_Workflow_Rule();

        return $workflow->findWorkflowCascading($firm_id, $status_id)->getFirstStep();
    }

    /**
     * Returns Element Set Assignment
     * @param null $event
     * @return mixed|null
     */
    public function getElementSetAssignment($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        if ($event && !$event->isNewRecord && $assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id))) {
            return $assignment;
        }

        return null;

    }

    /**
     * @param null $event
     * @return null|OphCiExamination_ElementSet
     */
    protected function getCurrentStep($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        $assignment = $this->getElementSetAssignment($event);

        return $assignment ? $assignment->step : $this->getFirstStep();
    }

    /**
     * Get the next workflow step.
     *
     * @param Event $event
     *
     * @return OphCiExamination_ElementSet
     */
    protected function getNextStep($event = null)
    {
        $step = $this->getCurrentStep();

        return $step->getNextStep();
    }

    /**
     * Merge workflow next step elements into existing elements.
     *
     * @param array       $elements
     *
     * @throws \CException
     *
     * @return array
     */
    protected function mergeNextStep($elements)
    {
        if (!$event = $this->event) {
            throw new \CException('No event set for step merging');
        }

        //TODO: should we be passing episode here?
        $extra_elements = $this->getElementsByWorkflow($this->step, $this->episode);
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
        usort($merged_elements, function ($a, $b) {
            if ($a->getElementType()->display_order == $b->getElementType()->display_order) {
                return 0;
            }

            return ($a->getElementType()->display_order > $b->getElementType()->display_order) ? 1 : -1;
        });

        return $merged_elements;
    }

    /**
     * Get the array of elements for the current site, subspecialty, episode status and workflow position
     *
     * @param OphCiExamination_ElementSet $set
     * @param Episode $episode
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getElementsByWorkflow($set = null, $episode = null)
    {
        $elements = array();
        if (!$set) {
            $firm_id = $this->firm->id;
            $status_id = ($episode) ? $episode->episode_status_id : 1;
            $workflow = new models\OphCiExamination_Workflow_Rule();
            $set = $workflow->findWorkflowCascading($firm_id, $status_id)->getFirstStep();
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
     * @throws Exception
     */
    public function actionGetDisorder()
    {
        if (!@$_GET['disorder_id']) {
            return;
        }
        if (!$disorder = \Disorder::model()->findByPk(@$_GET['disorder_id'])) {
            throw new \Exception('Unable to find disorder: '.@$_GET['disorder_id']);
        }

        header('Content-type: application/json');
        // For some reason JSON_HEX_QUOT | JSON_HEX_APOS doesn't escape ?
        echo json_encode(array('id' => $disorder->id, 'name' => addslashes($disorder->term)));
        Yii::app()->end();
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
            if ((int) $did) {
                foreach (models\Element_OphCiExamination_InjectionManagementComplex::model()->getInjectionQuestionsForDisorderId($did) as $q) {
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
                false, false
        );
    }

    /**
     * Get all the attributes for an element.
     *
     * @param BaseEventTypeElement $element
     * @param int                  $subspecialty_id
     *
     * @return OphCiExamination_Attribute[]
     */
    public function getAttributes($element, $subspecialty_id = null)
    {
        $attributes = models\OphCiExamination_Attribute::model()->findAllByElementAndSubspecialty($element->ElementType->id, $subspecialty_id);

        return $attributes;
    }

    /**
     * associate the answers and risks from the data with the Element_OphCiExamination_InjectionManagementComplex element for
     * validation.
     *
     * @param Element_OphCiExamination_InjectionManagementComplex $element
     * @param array                                               $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_InjectionManagementComplex($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
            $answers = array();
            $risks = array();
            $checker = 'has'.ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side.'_Answer'])) {
                    foreach ($data[$model_name][$side.'_Answer'] as $id => $p_ans) {
                        $answer = new models\OphCiExamination_InjectionManagementComplex_Answer();
                        $answer->question_id = $id;
                        $answer->answer = $p_ans;
                        $answer->eye_id = $eye_id;
                        $answers[] = $answer;
                    }
                }
                if (isset($data[$model_name][$side.'_risks']) && is_array($data[$model_name][$side.'_risks'])) {
                    foreach ($data[$model_name][$side.'_risks'] as $risk_id) {
                        if ($risk = models\OphCiExamination_InjectionManagementComplex_Risk::model()->findByPk($risk_id)) {
                            $risks[] = $risk;
                        }
                    }
                }
            }
            $element->{$side.'_answers'} = $answers;
            $element->{$side.'_risks'} = $risks;
        }
    }

    /**
     * If the Patient does not currently have a diabetic diagnosis, specify that it's required
     * so the validation rules can check for it being set in the given element (currently only DR Grading).
     *
     * @param BaseEventTypeElement $element
     * @param array                $data
     */
    private function _set_DiabeticDiagnosis($element, $data)
    {
        if (isset(Yii::app()->params['ophciexamination_drgrading_type_required'])
            && Yii::app()->params['ophciexamination_drgrading_type_required']
            && !$this->patient->getDiabetesType()) {
            if (!$element->secondarydiagnosis_disorder_id) {
                $element->secondarydiagnosis_disorder_required = true;
            }
        }
    }

    /**
     * Wrapper to set validation rules on DR Grading element.
     */
    protected function setComplexAttributes_Element_OphCiExamination_DRGrading($element, $data, $index)
    {
        $this->_set_DiabeticDiagnosis($element, $data);
    }

    /**
     * Set the diagnoses against the Element_OphCiExamination_Diagnoses element.
     *
     * @param Element_OphCiExamination_Diagnoses $element
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
    protected function setComplexAttributes_Element_OphCiExamination_Dilation(models\Element_OphCiExamination_Dilation $element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT) as $side => $eye_id) {
            $dilations = array();
            $checker = 'has'.ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side.'_treatments'])) {
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
            $element->{$side.'_treatments'} = $dilations;
        }
    }

    /**
     * Set the colour vision readings against the Element_OphCiExamination_ColourVision element.
     *
     * @param Element_OphCiExamination_ColourVision $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_ColourVision($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);

        foreach (array('left' => \Eye::LEFT,
                                 'right' => \Eye::RIGHT, ) as $side => $eye_id) {
            $readings = array();
            $checker = 'has'.ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side.'_readings'])) {
                    foreach ($data[$model_name][$side.'_readings'] as $p_read) {
                        if (@$p_read['id']) {
                            if (!$reading = models\OphCiExamination_ColourVision_Reading::model()->findByPk($p_read['id'])) {
                                $reading = new models\OphCiExamination_ColourVision_Reading();
                            }
                        } else {
                            $reading = new models\OphCiExamination_ColourVision_Reading();
                        }
                        $reading->attributes = $p_read;
                        $reading->eye_id = $eye_id;
                        $readings[] = $reading;
                    }
                }
            }
            $element->{$side.'_readings'} = $readings;
        }
    }

    /**
     * Save Colour Vision readings.
     *
     * @param Element_OphCiExamination_ColourVision $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_ColourVision($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element->updateReadings(\Eye::LEFT, $element->hasLeft() ?
                        @$data[$model_name]['left_readings'] :
                        array());
        $element->updateReadings(\Eye::RIGHT, $element->hasRight() ?
                        @$data[$model_name]['right_readings'] :
                        array());
    }

    /**
     * Save question answers and risks.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphCiExamination_InjectionManagementComplex($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element->updateQuestionAnswers(\Eye::LEFT,
            $element->hasLeft() && isset($data[$model_name]['left_Answer']) ?
            $data[$model_name]['left_Answer'] :
            array());
        $element->updateQuestionAnswers(\Eye::RIGHT,
            $element->hasRight() && isset($data[$model_name]['right_Answer']) ?
            $data[$model_name]['right_Answer'] :
            array());
        $element->updateRisks(\Eye::LEFT,
            $element->hasLeft() && isset($data[$model_name]['left_risks']) ?
            $data[$model_name]['left_risks'] :
            array());
        $element->updateRisks(\Eye::RIGHT,
            $element->hasRight() && isset($data[$model_name]['right_risks']) ?
            $data[$model_name]['right_risks'] :
            array());
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
                        'id' => $disorder['id'],
                        'eye_id' => \Helper::getEyeIdFromArray($disorder),
                        'disorder_id' => $disorder['disorder_id'],
                        'principal' => ($principal_diagnosis_row_key == $disorder['row_key']),
                        'date' => isset($disorder['date']) ? $disorder['date'] : null
                    ];
                }
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
                    $patient->addAllergy($allergyObject, $data['other_names'][$i], $data['allergy_comments'][$i], false, $this->event->id);
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
    protected function saveComplexAttributes_Element_OphCiExamination_Dilation(models\Element_OphCiExamination_Dilation $element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element->updateTreatments(\Eye::LEFT, $element->hasLeft() ?
                @$data[$model_name]['left_treatments'] :
                array());
        $element->updateTreatments(\Eye::RIGHT, $element->hasRight() ?
                @$data[$model_name]['right_treatments'] :
                array());
    }

    protected function setComplexAttributes_Element_OphCiExamination_IntraocularPressure(models\Element_OphCiExamination_IntraocularPressure $element, $data)
    {
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

    protected function saveComplexAttributes_Element_OphCiExamination_IntraocularPressure(models\Element_OphCiExamination_IntraocularPressure $element, $data)
    {
        models\OphCiExamination_IntraocularPressure_Value::model()->deleteAll('element_id = ?', array($element->id));

        foreach (array('left', 'right') as $side) {
            foreach ($element->{"{$side}_values"} as $value) {
                $value->element_id = $element->id;
                $value->save();
            }
        }
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
                $this->renderPartial('_qualitative_scale', ['name' => $name, 'value' => $value, 'scale' => $scale, 'side' => $side, 'index' => $index]);
            }
        }
    }

    protected function setElementDefaultOptions_Element_OphCiExamination_OverallManagementPlan(models\Element_OphCiExamination_OverallManagementPlan $element, $action)
    {
        if ($action == 'create') {
            if ($previous_om = models\Element_OphCiExamination_OverallManagementPlan::model()->with(array(
                    'event' => array(
                        'condition' => 'event.deleted = 0',
                        'with' => array(
                            'episode' => array(
                                'condition' => 'episode.deleted = 0 and episode.id = '.$this->episode->id,
                            ),
                        ),
                      'order' => 'event.event_date desc, event.created_date desc',
                    ),
                ))->find()) {
                foreach ($previous_om->attributes as $key => $value) {
                    if (!in_array($key, array('id', 'created_date', 'created_user_id', 'last_modified_date', 'last_modified_user_id'))) {
                        $element->$key = $value;
                    }
                }
            }
        }
    }

    protected function setElementDefaultOptions_Element_OphCiExamination_Refraction(models\Element_OphCiExamination_Refraction $element, $action)
    {
        if ($action == 'create') {
            $element->right_type_id = 1;
            $element->left_type_id = 1;
        }
    }

    public function actionGetPreviousIOPAverage()
    {
        if (!$patient = \Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new \Exception('Patient not found: '.@$_GET['patient_id']);
        }

        if (!in_array(@$_GET['side'], array('left', 'right'))) {
            throw new \Exception('Invalid side: '.@$_GET['side']);
        }

        $side = ucfirst(@$_GET['side']);

        $api = $this->getApp()->moduleAPI->get('OphCiExamination');
        $result = $api->{"getLastIOPReading{$side}"}($patient);

        echo $result;
    }

    public function actionCreate()
    {
        $this->setCurrentSet();
        $this->step = $this->getCurrentStep();

        if (Yii::app()->request->getPost('patientticketing__notes', null) != null) {
            $_POST['patientticketing__notes'] = htmlspecialchars(Yii::app()->request->getPost('patientticketing__notes',
                null));
        }

        parent::actionCreate();
    }

    public function actionUpdate($id)
    {
        $this->setCurrentSet();

        parent::actionUpdate($id);
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

        return \CHtml::listData(models\OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findAll($criteria), 'id', 'name');
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
        if ($element->status && $element->status->patientticket && $api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            if (isset($data['patientticket_queue'])) {
                $queue = $api->getQueueForUserAndFirm(Yii::app()->user, $this->firm, $data['patientticket_queue']);
                $queue_data = $api->extractQueueData($queue, $data);
                $api->createTicketForEvent($this->event, $queue, Yii::app()->user, $this->firm, $queue_data);
            } else {
                $api->updateTicketForEvent($this->event);
            }
        }
    }

    private function getOtherSide($side1, $side2, $selectedSide) {
        return $selectedSide === $side1 ? $side2 : $side1;
    }

    protected function saveComplexAttributes_HistoryIOP($element, $data, $index)
    {
        $data = $data['OEModule_OphCiExamination_models_HistoryIOP'];

        foreach (['left', 'right'] as $side) {
            if (array_key_exists("{$side}_values", $data) && $data["{$side}_values"]) {
                foreach ($data["{$side}_values"] as $index => $values) {
                    // create a new event and set the event_date as selected iop date
                    $examination_date = explode("-", $values['examination_date']);
                    $year_format = (strlen($examination_date[2]) === 2 ? 'y' : 'Y');
                    $examinationEvent = new \Event();
                    $examinationEvent->episode_id = $element->event->episode_id;
                    $examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = \Yii::app()->user->id;
                    $examinationEvent->event_date = \DateTime::createFromFormat('d-m-'.$year_format, $values['examination_date'])->format('Y-m-d');
                    $examinationEvent->event_type_id = $element->event->event_type_id;

                    if (!$examinationEvent->save()) {
                        throw new \Exception('Unable to save a new examination for the IOP readings: ' . print_r($examinationEvent->errors, true));
                    }

                    // create a new iop element
                    $iop_element = new models\Element_OphCiExamination_IntraocularPressure();
                    $iop_element->event_id = $examinationEvent->id;
                    if (isset($values["{$side}_comments"]) && $values["{$side}_comments"]) {
                        $iop_element["{$side}_comments"] = $values["{$side}_comments"];
                    }
                    $iop_element[$this->getOtherSide('left', 'right', $side) . "_comments"] = "IOP values not recorded for this eye.";

                    if (!$iop_element->save(false)) {
                        throw new \Exception('Unable to save a new IOP element: ' . print_r($iop_element->errors, true));
                    }

                    // create a reading record from the values the user has given
                    $reading = new models\OphCiExamination_IntraocularPressure_Value();
                    // examination_date and comments are not actual fields in IOP so delete them to prevent warnings
                    unset($values['examination_date']);
                    unset($values["{$side}_comments"]);
                    $reading->attributes = $values;
                    $reading->element_id = $iop_element->id;

                    if (!$reading->save()) {
                        throw new \Exception('Unable to save reading for the IOP element: ' . print_r($reading->errors, true));
                    }
                }
            }
        }
    }

    /**
     * Custom validation on HistoryIOP element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateHistoryIopFromData($data, $errors) {
        $et_name = models\HistoryIOP::model()->getElementTypeName();
        $historyIOP = $this->getOpenElementByClassName('OEModule_OphCiExamination_models_HistoryIOP');
        $entries = $data['OEModule_OphCiExamination_models_HistoryIOP'];
        foreach (['left', 'right'] as $side) {
            if (isset($entries["{$side}_values"])) {
                // set the examination dates in HistoryIOP model for custom validation
                $historyIOP->examination_dates["{$side}_values"] = array_column($entries["{$side}_values"], 'examination_date');

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
                                $historyIOP->addError("{$side}_values" . '_' . $index . '_' . $readingErrorAttributeName, $readingErrorMessage);
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
        $errors = parent::setAndValidateElementsFromData($data);
        if(isset($data['OEModule_OphCiExamination_models_HistoryIOP'])) {
            $errors = $this->setAndValidateHistoryIopFromData($data, $errors);
        }

        $history_meds = $this->getOpenElementByClassName('OEModule_OphCiExamination_models_HistoryMedications');
        if ($history_meds) {
            $errors = $this->setAndValidateHistoryMedicationsFromData($errors, $history_meds);
        }

        $posted_risk = [];
        if(isset($data['OEModule_OphCiExamination_models_HistoryRisks']['entries'])){
            $posted_risk = array_map(function($r){ return $r['risk_id'];}, $data['OEModule_OphCiExamination_models_HistoryRisks']['entries']);
        }

        // Element was open, we check the required risks
        if(isset($data['OEModule_OphCiExamination_models_HistoryRisks'])){
            $errors = $this->setAndValidateHistoryRisksFromData($errors, $posted_risk);
        }

        $api = Yii::app()->moduleAPI->get('PatientTicketing');
        if (isset($data['patientticket_queue']) && $api) {
            $errors = $this->setAndValidatePatientTicketingFromData($data, $errors, $api);
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
            if( !in_array($required_risk->id, $posted_risk) ){
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
        $co_sid = $data[\CHtml::modelName(models\Element_OphCiExamination_ClinicOutcome::model())]['status_id'];
        $status = models\OphCiExamination_ClinicOutcome_Status::model()->findByPk($co_sid);
        if ($status && $status->patientticket) {
            $err = array();
            $queue = null;
            if (!$data['patientticket_queue']) {
                $err['patientticket_queue'] = 'You must select a valid Virtual Clinic for referral';
            } elseif (!$queue = $api->getQueueForUserAndFirm(Yii::app()->user, $this->firm, $data['patientticket_queue'])) {
                $err['patientticket_queue'] = 'Virtual Clinic not found';
            }
            if ($queue) {
                if (!$api->canAddPatientToQueue($this->patient, $queue)) {
                    $err['patientticket_queue'] = 'Cannot add Patient to Queue';
                } else {
                    list($ignore, $fld_errs) = $api->extractQueueData($queue, $data, true);
                    $err = array_merge($err, $fld_errs);
                }
            }

            if (count($err)) {
                $et_name = models\Element_OphCiExamination_ClinicOutcome::model()->getElementTypeName();
                if(isset($errors[$et_name])) {
                    if ($errors[$et_name]) {
                        $errors[$et_name] = array_merge($errors[$et_name], $err);
                    } else {
                        $errors[$et_name] = $err;
                    }
                }
            }
        }

        return $errors;
    }

    protected function setComplexAttributes_Element_OphCiExamination_FurtherFindings($element, $data, $index)
    {
        $assignments = array();

        if (!empty($data['OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']['further_findings_assignment'])) {
            foreach ($data['OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']['further_findings_assignment'] as $i => $item) {
                if (!$finding = \Finding::model()->findByPk($item['id'])) {
                    throw new Exception("Finding not found: {$item['id']}");
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
                    throw new \Exception('Unable to save further finding assignment: '.print_r($assignment->errors, true));
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
        if (isset($data['OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts']) &&
            isset($data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['contact_id'])) {
            $contact_ids = $data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['contact_id'];
            $comments = $data["OEModule_OphCiExamination_models_Element_OphCiExamination_Contacts"]['comments'];
        } else {
            $contact_ids = [];
        }
        $patientContactAssignments = \PatientContactAssignment::model()->findAll(
            "patient_id = ?", [$patient->id]);


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
                $patientContactAssignment = new \PatientContactAssignment;
                $patientContactAssignment->patient_id = $patient->id;
                $patientContactAssignment->contact_id = $contact_id;
                $patientContactAssignment->comment = isset($comments[$key]) ? $comments[$key] : null;
                $patientContactAssignment->save();
            }
        }

        $patientContactAssignments = array_filter($patientContactAssignments, function ($assignment) use ($contact_ids) {
            return !in_array($assignment->contact_id, $contact_ids);
        });

        foreach ($patientContactAssignments as $patientContactAssignment) {
            $patientContactAssignment->delete();
        }
    }

    /**
     * Is this element required in the UI? (Prevents the user from being able
     * to remove the element.).
     *
     * @param BaseEventTypeElement $element
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

    /**
     * @throws \CException
     */
    protected function setCurrentSet()
    {
        $element_assignment = $this->getElementSetAssignment();
        if (!$this->set) {
            /*@TODO: probably the getNextStep() should be able to recognize if there were no steps completed before and return the first step
              Note: getCurrentStep() will return firstStep if there were no steps before */
            $this->set = $element_assignment && $this->action->id != 'update' ? $this->getNextStep() : $this->getCurrentStep();

            //if $this->set is null than no workflow rule to apply
            $this->mandatoryElements = isset($this->set) ? $this->set->MandatoryElementTypes : null;
        }

        if ($this->action->id == 'update' && !$element_assignment->step_completed) {
            $this->step = $this->getCurrentStep();
        }
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

        $right_select_values = models\OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList($element_id, $operation_note_id, $subspecialty_id, \Eye::RIGHT);

        $right_select = array();
        foreach ($right_select_values as $right_select_value) {
            $right_select[] = array('id' => $right_select_value->id, 'name' => $right_select_value->name, 'display_order' => $right_select_value->display_order);
        }

        $left_select_values = models\OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList($element_id, $operation_note_id, $subspecialty_id, \Eye::LEFT);
        foreach ($left_select_values as $left_select_value) {
            $left_select[] = array('id' => $left_select_value->id, 'name' => $left_select_value->name, 'display_order' => $left_select_value->display_order);
        }

        echo \CJSON::encode(array(
            'right_values' => $right_data,
            'left_values' => $left_data,
            'right_select' => $right_select,
            'left_select' => $left_select,
            )
        );
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
                            $element_id, $operation_note_id, $subspecialty_id, $eye_id, $term);

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

            if($element->save()) {
                echo \CJSON::encode(array('success' => 'true'));
            }
        }
    }
}
