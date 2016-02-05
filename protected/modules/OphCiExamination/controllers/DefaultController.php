<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers;

use Yii;
use \OEModule\OphCiExamination\models;
use \OEModule\OphCiExamination\components;

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
    );

    // if set to true, we are advancing the current event step
    private $step = false;

    protected $set;

    protected $mandatoryElements;

    protected $allergies = array();

    protected $deletedAllergies = array();

    /**
     * Need split event files
     * @TODO: determine if this should be defined by controller property
     *
     * @param CAction $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, \AssetManager::OUTPUT_SCREEN);
        $this->jsVars['OE_MODEL_PREFIX'] = 'OEModule_OphCiExamination_models_';
        return parent::beforeAction($action);
    }

    /**
     * Applies workflow and filtering to the element retrieval
     *
     * @return BaseEventTypeElement[]
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
     * Filters elements based on coded dependencies
     *
     * @TODO: need to ensure that we don't filter out elements that do exist when configuration changes
     * @param BaseEventTypeElement[] $elements
     * @return BaseEventTypeElement[]
     */
    protected function filterElements($elements)
    {
        if (Yii::app()->hasModule('OphCoTherapyapplication')) {
            $remove = array('OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagement');
        } else {
            $remove = array('OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagementComplex');
        }

        if ($this->set) {
            foreach ($this->set->HiddenElementTypes as $element) {
                $remove[] = $element->class_name;
            }
        }

        $final = array();
        foreach ($elements as $el) {
            if (!in_array(get_class($el), $remove)) {
                $final[] = $el;
            }
        }

        return $final;
    }

    /**
     * Sets up jsvars for editing
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
    }

    /**
     * Call editInit to set up jsVars
     */
    public function initActionCreate()
    {
        parent::initActionCreate();
        $this->initEdit();
    }

    /**
     * Call editInit to setup jsVars
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
     * of unique diagnoses
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphCiExamination_Diagnoses($element, $action)
    {
        if ($action == 'create') {
            // set the diagnoses to match the current patient diagnoses for the episode
            // and any other ophthalmic secondary diagnoses the patient has
            $diagnoses = array();
            if ($principal = $this->episode->diagnosis) {
                $d = new models\OphCiExamination_Diagnosis();
                $d->disorder_id = $principal->id;
                $d->principal = true;
                $d->eye_id = $this->episode->eye_id;
                $diagnoses[] = $d;
            }
            foreach ($this->patient->getOphthalmicDiagnoses() as $sd) {
                $d = new models\OphCiExamination_Diagnosis();
                $d->disorder_id = $sd->disorder_id;
                $d->eye_id = $sd->eye_id;
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
     * It's a child element of History
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
     * Set the allergies against the Element_OphCiExamination_Allergy element
     *
     */

    protected function setElementDefaultOptions_Element_OphCiExamination_Allergy($element, $action)
    {
        if ($action == 'create' || $action == 'update') {
            $this->allergies = $this->patient->allergyAssignments;
        }
    }

    /**
     * Action to move the workflow forward a step on the given event
     *
     * @param $id
     */
    public function actionStep($id)
    {
        $this->step = true;
        // This is the same as update, but with a few extras, so we call the update code and then pick up on the action later
        $this->actionUpdate($id);
    }

    /**
     * Override action value when action is step to be update.
     *
     * @param BaseEventTypeElement $element
     * @param string $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     * @param array $view_data
     * @param bool $return
     * @param bool $processOutput
     */
    protected function renderElement($element, $action, $form, $data, $view_data=array(), $return=false, $processOutput=false)
    {
        if ($action == 'step') {
            $action = 'update';
        }
        parent::renderElement($element, $action, $form, $data, $view_data, $return, $processOutput);
    }
    /**
     * Advance the workflow step for the event if requested
     *
     * @param Event $event
     * @throws CException
     */
    protected function afterUpdateElements($event)
    {
        if ($this->step) {
            // Advance the workflow
            if (!$assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id))) {
                // Create initial workflow assignment if event hasn't already got one
                $assignment = new models\OphCiExamination_Event_ElementSet_Assignment();
                $assignment->event_id = $event->id;
            }
            if (!$next_step = $this->getNextStep($event)) {
                throw new \CException('No next step available');
            }
            $assignment->step_id = $next_step->id;
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
     * extends standard method to filter elements
     *
     * (non-PHPdoc)
     * @see NestedElementsEventTypeController::getChildOptionalElements()
     */
    public function getChildOptionalElements($parent_type)
    {
        return $this->filterElements(parent::getChildOptionalElements($parent_type));
    }

    /**
     * Get the first workflow step using rules
     *
     * @TODO: examine what this is being used for as opposed to getting elements by workflow ...
     * @return OphCiExamination_ElementSet
     */
    protected function getFirstStep()
    {
        $firm_id = $this->firm->id;
        $status_id = $this->episode->episode_status_id;

        return models\OphCiExamination_Workflow_Rule::findWorkflow($firm_id, $status_id)->getFirstStep();
    }

    /**
     * Get the next workflow step
     * @param Event $event
     * @return OphCiExamination_ElementSet
     */
    protected function getNextStep($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }
        if ($assignment = models\OphCiExamination_Event_ElementSet_Assignment::model()->find('event_id = ?', array($event->id))) {
            $step = $assignment->step;
        } else {
            $step = $this->getFirstStep();
        }
        return $step->getNextStep();
    }

    /**
     * Merge workflow next step elements into existing elements
     *
     * @param array $elements
     * @param ElementType $parent
     * @throws \CException
     * @return array
     */
    protected function mergeNextStep($elements, $parent = null)
    {
        if (!$event = $this->event) {
            throw new \CException('No event set for step merging');
        }
        if (!$next_step = $this->getNextStep($event)) {
            throw new \CException('No next step available');
        }

        $parent_id = ($parent) ? $parent->id : null;
        //TODO: should we be passing episode here?
        $extra_elements = $this->getElementsByWorkflow($next_step, $this->episode, $parent_id);
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
            $extra_element->setDefaultOptions();

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
     * If $parent_id is provided, restrict to children of that element_type id
     *
     * @param OphCiExamination_ElementSet $set
     * @param Episode $episode
     * @param integer $parent_id
     * @return BaseEventTypeElement[]
     */
    protected function getElementsByWorkflow($set = null, $episode = null, $parent_id = null)
    {
        $elements = array();
        if (!$set) {
            $site_id = Yii::app()->session['selected_site_id'];
            $firm_id = $this->firm->id;
            $subspecialty_id = $this->firm->getSubspecialtyID();
            $status_id = ($episode) ? $episode->episode_status_id : 1;
            $set = models\OphCiExamination_Workflow_Rule::findWorkflow($firm_id, $status_id)->getFirstStep();
        }

        if ($set) {
            $element_types = $set->DefaultElementTypes;
            foreach ($element_types as $element_type) {
                if (!$parent_id || ($parent_id && $element_type->parent_element_type_id == $parent_id)) {
                    $elements[$element_type->id] = $element_type->getInstance();
                }
            }
            $this->mandatoryElements = $set->MandatoryElementTypes;
        }

        $this->set = $set;

        return $this->filterElements($elements);
    }

    /**
     * Ajax function for quick disorder lookup
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
        echo json_encode(array('id' => $disorder->id, 'name' => $disorder->term));
        Yii::app()->end();
    }

    /**
     * Ajax action to load the questions for a side and disorder_id
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
     * Get all the attributes for an element
     *
     * @param BaseEventTypeElement $element
     * @param integer $subspecialty_id
     * @return OphCiExamination_Attribute[]
     */
    public function getAttributes($element, $subspecialty_id = null)
    {
        $attributes = models\OphCiExamination_Attribute::model()->findAllByElementAndSubspecialty($element->ElementType->id, $subspecialty_id);
        return $attributes;
    }

    /**
     * associate the answers and risks from the data with the Element_OphCiExamination_InjectionManagementComplex element for
     * validation
     *
     * @param Element_OphCiExamination_InjectionManagementComplex $element
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
                if (isset($data[$model_name][$side . '_risks'])) {
                    foreach ($data[$model_name][$side . '_risks'] as $risk_id) {
                        if ($risk = models\OphCiExamination_InjectionManagementComplex_Risk::model()->findByPk($risk_id)) {
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
     * If the Patient does not currently have a diabetic diagnosis, specify that it's required
     * so the validation rules can check for it being set in the given element (currently only DR Grading)
     *
     * @param BaseEventTypeElement $element
     * @param array $data
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
     * Wrapper to set validation rules on DR Grading element
     *
     */
    protected function setComplexAttributes_Element_OphCiExamination_DRGrading($element, $data, $index)
    {
        $this->_set_DiabeticDiagnosis($element, $data);
    }

    /**
     * Set the diagnoses against the Element_OphCiExamination_Diagnoses element
     *
     * @param Element_OphCiExamination_Diagnoses $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_Diagnoses($element, $data, $index)
    {
        $diagnoses = array();
        $diagnosis_eyes = array();
        $model_name = \CHtml::modelName($element);

        if (isset($data[$model_name])) {
            foreach ($data[$model_name] as $key => $value) {
                if (preg_match('/^eye_id_[0-9]+$/', $key)) {
                    $diagnosis_eyes[] = $value;
                }
            }
        }

        if (is_array(@$data['selected_diagnoses'])) {
            foreach ($data['selected_diagnoses'] as $i => $disorder_id) {
                $diagnosis = new models\OphCiExamination_Diagnosis();
                $diagnosis->eye_id = $diagnosis_eyes[$i];
                $diagnosis->disorder_id = $disorder_id;
                $diagnosis->principal = (@$data['principal_diagnosis'] == $disorder_id);
                $diagnoses[] = $diagnosis;
            }
        }
        $element->diagnoses = $diagnoses;
    }

    /**
     * Set the allergies against the Element_OphCiExamination_Allergy element
     * It's a child element of History
     *
     * @param Element_OphCiExamination_History $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_History($element, $data, $index)
    {
        $allergies = array();
        // we add the original rows
        foreach ($this->patient->allergyAssignments as $paa) {
            $allergies[] = $paa;
        }

        // we remove the deleted ones
        if (!empty($data['deleted_allergies'])) {
            $this->deletedAllergies = $data['deleted_allergies'];
            foreach ($this->deletedAllergies as $deletedAssignmentId) {
                foreach ($allergies as $key=>$allergyRow) {
                    if ($allergyRow->id == $deletedAssignmentId) {
                        unset($allergies[$key]);
                    }
                }
            }
        }

        // and finally we just add the new ones
        if (!empty($data['selected_allergies'])) {
            foreach ($data['selected_allergies'] as $i => $allergy_id) {
                if ($data['other_names'][$i] == 'undefined') {
                    $data['other_names'][$i] = "";
                }
                $newAllergy = new \PatientAllergyAssignment;
                $newAllergy->allergy_id = $allergy_id;
                $newAllergy->other = $data['other_names'][$i];
                $newAllergy->comments = $data['allergy_comments'][$i];
                $allergies[] = $newAllergy;
            }
        }

        $this->allergies = $allergies;
    }


    /**
     * set the dilation treatments against the element from the provided data
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
            $checker = 'has' . ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side . '_treatments'])) {
                    foreach ($data[$model_name][$side . '_treatments'] as $idx => $p_treat) {
                        if (@$p_treat['id']) {
                            $dilation = models\OphCiExamination_Dilation_Treatment::model()->findByPk($p_treat['id']);
                        } else {
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

    /**
     * Set the colour vision readings against the Element_OphCiExamination_ColourVision element
     *
     * @param Element_OphCiExamination_ColourVision $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphCiExamination_ColourVision($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);

        foreach (array('left' => \Eye::LEFT,
                                 'right' => \Eye::RIGHT) as $side => $eye_id) {
            $readings = array();
            $checker = 'has' . ucfirst($side);
            if ($element->$checker()) {
                if (isset($data[$model_name][$side . '_readings'])) {
                    foreach ($data[$model_name][$side . '_readings'] as $p_read) {
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
            $element->{$side . '_readings'} = $readings;
        }
    }

    /**
     * Save Colour Vision readings
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
     * Save question answers and risks
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
     * Save diagnoses
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

        // This is to accommodate a hack introduced in OE-4409
        if (isset($data[$model_name]) && isset($data[$model_name]['force_validation'])) {
            unset($data[$model_name]['force_validation']);
        }

        $eyes = isset($data[$model_name]) ? array_values($data[$model_name]) : array();

        if (!empty($data['selected_diagnoses'])) {
            foreach ($data['selected_diagnoses'] as $i => $disorder_id) {
                $diagnoses[] = array(
                    'eye_id' => $eyes[$i],
                    'disorder_id' => $disorder_id,
                    'principal' => (@$data['principal_diagnosis'] == $disorder_id)
                );
            }
        }

        $element->updateDiagnoses($diagnoses);
    }

    /**
     * Save allergies - because it's part of the History element it need to be saved from that element
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
                if ($assignment_id >0) {
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
                        $data['other_names'][$i] = "";
                    }
                    $patient->addAllergy($allergyObject, $data['other_names'][$i], $data['allergy_comments'][$i], false, $this->event->id);
                }
            }
        }
    }

    /**
     * Save the dilation treatments
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
                    $value = new models\OphCiExamination_IntraocularPressure_Value;
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
        models\OphCiExamination_IntraocularPressure_Value::model()->deleteAll("element_id = ?", array($element->id));

        foreach (array('left', 'right') as $side) {
            foreach ($element->{"{$side}_values"} as $value) {
                $value->element_id = $element->id;
                $value->save();
            }
        }
    }

    public function actionGetScaleForInstrument()
    {
        if ($instrument = models\OphCiExamination_Instrument::model()->findByPk(@$_GET['instrument_id'])) {
            if ($scale = $instrument->scale) {
                $value = new models\OphCiExamination_IntraocularPressure_Value;
                $this->renderPartial('_qualitative_scale', array('value' => $value, 'scale' => $scale, 'side' => @$_GET['side'], 'index' => @$_GET['index']));
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
            throw new \Exception("Patient not found: ".@$_GET['patient_id']);
        }

        if (!in_array(@$_GET['side'], array('left', 'right'))) {
            throw new \Exception("Invalid side: ".@$_GET['side']);
        }

        $side = ucfirst(@$_GET['side']);

        $api = new components\OphCiExamination_API();
        $result = $api->{"getLastIOPReading{$side}"}($patient);

        echo $result;
    }

    public function actionCreate()
    {
        $this->setCurrentSet();

        if(Yii::app()->request->getPost('patientticketing__notes',null) != null) {
            $_POST['patientticketing__notes'] = htmlspecialchars(Yii::app()->request->getPost('patientticketing__notes',
                null));
        }

        parent::actionCreate();
    }

    public function getPupilliaryAbnormalitiesList($selected_id)
    {
        $criteria = new \CDbCriteria;

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

    /**
     * custom validation for virtual clinic referral
     *
     * @TODO: this should hand off validation to a faked PatientTicket request via the API.
     * @param array $data
     * @return array
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);
        if (isset($data['patientticket_queue']) && $api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            $co_sid = @$data[\CHtml::modelName(models\Element_OphCiExamination_ClinicOutcome::model())]['status_id'];
            $status = models\OphCiExamination_ClinicOutcome_Status::model()->findByPk($co_sid);
            if ($status && $status->patientticket) {
                $err = array();
                $queue = null;
                if (!$data['patientticket_queue']) {
                    $err['patientticket_queue'] = 'You must select a valid Virtual Clinic for referral';
                } elseif (!$queue = $api->getQueueForUserAndFirm(Yii::app()->user, $this->firm, $data['patientticket_queue'])) {
                    $err['patientticket_queue'] = "Virtual Clinic not found";
                }
                if ($queue) {
                    if (!$api->canAddPatientToQueue($this->patient, $queue)) {
                        $err['patientticket_queue'] = "Cannot add Patient to Queue";
                    } else {
                        list($ignore, $fld_errs) = $api->extractQueueData($queue, $data, true);
                        $err = array_merge($err, $fld_errs);
                    }
                }

                if (count($err)) {
                    $et_name = models\Element_OphCiExamination_ClinicOutcome::model()->getElementTypeName();
                    if (@$errors[$et_name]) {
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
                $assignment = new models\OphCiExamination_FurtherFindings_Assignment;
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
                    throw new \Exception("Unable to save further finding assignment: ".print_r($assignment->errors, true));
                }

                $ids[] = $assignment->id;
            }
        }

        $criteria = new \CDbCriteria;
        $criteria->addCondition('element_id = :eid');
        $criteria->params[':eid'] = $element->id;

        if (!empty($ids)) {
            $criteria->addNotInCondition('id', $ids);
        }

        models\OphCiExamination_FurtherFindings_Assignment::model()->deleteAll($criteria);
    }

    /**
     * Render the optional child elements for the given parent element type
     *
     * @param BaseEventTypeElement $parent_element
     * @param string $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     * @throws Exception
     */
    public function renderChildOptionalElements($parent_element, $action, $form=null, $data=null)
    {
        $this->setCurrentSet();
        $elements = $this->getChildOptionalElements($parent_element->getElementType());
        $this->filterElements($elements);
        foreach ($elements as $element) {
            $this->renderOptionalElement($element, $action, $form, $data);
        }
    }

    /**
     * Is this element required in the UI? (Prevents the user from being able
     * to remove the element.)
     * @param  BaseEventTypeElement  $element
     * @return boolean
     */
    public function isRequiredInUI(\BaseEventTypeElement $element)
    {
        if (isset($this->mandatoryElements)) {
            foreach ($this->mandatoryElements as $mandatoryElement) {
                if (get_class($element) === $mandatoryElement->class_name) {
                    return true;
                }
            }
        }
        return parent::isRequiredInUI($element);
    }

    /**
     * @throws models\CException
     */
    protected function setCurrentSet()
    {
        if (!$this->set) {
            $firm_id = $this->firm->id;
            $status_id = ($this->episode) ? $this->episode->episode_status_id : 1;
            $set = models\OphCiExamination_Workflow_Rule::findWorkflow($firm_id, $status_id)->getFirstStep();
            $this->set = $set;
            $this->mandatoryElements = $set->MandatoryElementTypes;
        }
    }
}
