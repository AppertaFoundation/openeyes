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

use OEModule\OphCiExamination\components\ExaminationHelper;
use Yii;
use Audit;
use CDbCriteria;
use OEModule\OphCiExamination\models;

class AdminController extends \ModuleAdminController
{
    public $defaultAction = 'ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason';

    public function actionEditIOPInstruments()
    {
        $this->genericAdmin(
            'Edit Intraocular Pressure Instruments',
            'OEModule\OphCiExamination\models\OphCiExamination_Instrument',
            array(
                'extra_fields' => array(
                    array('field' => 'short_name', 'type' => 'text',
                        'model' => 'OEModule\OphCiExamination\models\OphCiExamination_Instrument', ),
                ),
            )
        );
    }

    // No Treatment Reason views

    /**
     * list the reasons that can be selected for not providing an injection treatment.
     */
    public function actionViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason()
    {
        $model_list = models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findAll(array('order' => 'display_order asc'));
        $this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortNoTreatmentReasons');
        $this->jsVars['OphCiExamination_model_status_url'] = $this->createUrl('setNoTreatmentReasonStatus');

        Audit::add('admin', 'list', null, null, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_InjectionManagementComplex_NoTreatmentReason'));

        $this->render('list', array(
                'model_list' => $model_list,
                'title' => 'No Treatment Reasons',
                'model_class' => 'OphCiExamination_InjectionManagementComplex_NoTreatmentReason',
        ));
    }

    /**
     * create a new no treatment reason for injection.
     */
    public function actionCreateOphCiExamination_InjectionManagementComplex_NoTreatmentReason()
    {
        $model = new models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason();

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($bottom_drug = models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->find(array('order' => 'display_order desc'))) {
                $display_order = $bottom_drug->display_order + 1;
            } else {
                $display_order = 1;
            }
            $model->display_order = $display_order;

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCiExamination', 'model' => 'InjectionManagementComplex_NoTreatmentReason'));
                Yii::app()->user->setFlash('success', 'Injection Management No Treatment reason added');

                $this->redirect(array('ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason'));
            }
        }

        $this->render('create', array(
                'model' => $model,
        ));
    }

    /**
     * update the no treatment reason with id $id.
     *
     * @param int $id
     */
    public function actionUpdateOphCiExamination_InjectionManagementComplex_NoTreatmentReason($id)
    {
        $model = models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk((int) $id);

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'update', $model->id, null, array('module' => 'OphCiExamination', 'model' => 'InjectionManagementComplex_NoTreatmentReason'));
                Yii::app()->user->setFlash('success', 'Injection Management No Treatment reason updated');

                $this->redirect(array('ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason'));
            }
        }

        $this->render('create', array(
                'model' => $model,
        ));
    }

    /*
     * sorts the no treatment reasons into the provided order (NOTE does not support a paginated list of reasons)
    */
    public function actionSortNoTreatmentReasons()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($drug = models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk($id)) {
                    $drug->display_order = $i + 1;
                    if (!$drug->save()) {
                        throw new \Exception('Unable to save drug: '.print_r($drug->getErrors(), true));
                    }
                }
            }
        }
    }

    /**
     * Update the enabled status of the given reason.
     */
    public function actionSetNoTreatmentReasonStatus()
    {
        if ($model = models\OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk((int) @$_POST['id'])) {
            if (!array_key_exists('enabled', $_POST)) {
                throw new \Exception('cannot determine status for reason');
            }

            if ($_POST['enabled']) {
                $model->active = true;
            } else {
                $model->active = false;
            }
            if (!$model->save()) {
                throw new \Exception('Unable to set reason status: '.print_r($model->getErrors(), true));
            }

            Audit::add('admin', 'set-reason-status', @$_POST['id'], null, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_InjectionManagementComplex_NoTreatmentReason'));
        } else {
            throw new \Exception('Cannot find reason with id'.@$_POST['id']);
        }
    }

    // Disorder Questions

    /**
     * list the questions set for the given disorder id.
     */
    public function actionViewOphCiExamination_InjectionManagementComplex_Question()
    {
        $this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortQuestions');
        $this->jsVars['OphCiExamination_model_status_url'] = $this->createUrl('setQuestionStatus');

        $model_list = array();
        $disorder_id = null;
        if (isset($_GET['disorder_id'])) {
            $disorder_id = (int) $_GET['disorder_id'];
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order asc';
            $criteria->condition = 'disorder_id = :disorder_id';
            $criteria->params = array(':disorder_id' => (int) $_GET['disorder_id']);

            $model_list = models\OphCiExamination_InjectionManagementComplex_Question::model()->findAll($criteria);

            $this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortQuestions');

            Audit::add('admin', 'list-for-disorder', $_GET['disorder_id'], null, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_InjectionManagementComplex_Question'));
        }

        $this->render('list_diagnosis_questions', array(
                'disorder_id' => $disorder_id,
                'model_list' => $model_list,
                'title' => 'Disorder Questions',
                'model_class' => 'OphCiExamination_InjectionManagementComplex_Question',
        ));
    }

    /**
     * create a question for the given disorder id.
     */
    public function actionCreateOphCiExamination_InjectionManagementComplex_Question()
    {
        $model = new models\OphCiExamination_InjectionManagementComplex_Question();

        if (isset($_POST[\CHtml::modelName($model)])) {
            // process submission
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->disorder_id) {
                // not a valid question otherwise
                $criteria = new CDbCriteria();
                $criteria->order = 'display_order desc';
                $criteria->condition = 'disorder_id = :disorder_id';
                $criteria->limit = 1;
                $criteria->params = array(':disorder_id' => $model->disorder_id);

                if ($bottom = models\OphCiExamination_InjectionManagementComplex_Question::model()->find($criteria)) {
                    $display_order = $bottom->display_order + 1;
                } else {
                    $display_order = 1;
                }
                $model->display_order = $display_order;

                if ($model->save()) {
                    Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCiExamination', 'model' => 'InjectionManagementComplex_Question'));
                    Yii::app()->user->setFlash('success', 'Injection Management Disorder Question added');

                    $this->redirect(array('ViewOphCiExamination_InjectionManagementComplex_Question', 'disorder_id' => $model->disorder_id));
                }
            }
        } elseif (isset($_GET['disorder_id'])) {
            // allow the ability to pre-select which disorder is being set for a question
            $model->disorder_id = $_GET['disorder_id'];
        }

        $this->render('create', array(
                'model' => $model,
        ));
    }

    /**
     * update the question for the specified id.
     *
     * @param int $id
     */
    public function actionUpdateOphCiExamination_InjectionManagementComplex_Question($id)
    {
        $model = models\OphCiExamination_InjectionManagementComplex_Question::model()->findByPk((int) $id);
        if (isset($_POST[\CHtml::modelName($model)])) {
            // process submission
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'update', $model->id, null, array('module' => 'OphCiExamination', 'model' => 'InjectionManagementComplex_Question'));
                Yii::app()->user->setFlash('success', 'Injection Management Disorder Question updated');

                $this->redirect(array('ViewOphCiExamination_InjectionManagementComplex_Question', 'disorder_id' => $model->disorder_id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * sorts questions into the given order.
     */
    public function actionSortQuestions()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($question = models\OphCiExamination_InjectionManagementComplex_Question::model()->findByPk($id)) {
                    $question->display_order = $i + 1;
                    if (!$question->save()) {
                        throw new \Exception('Unable to save question: '.print_r($question->getErrors(), true));
                    }
                }
            }
        }
    }

    /**
     * Update the enabled status of the given question.
     */
    public function actionSetQuestionStatus()
    {
        if ($model = models\OphCiExamination_InjectionManagementComplex_Question::model()->findByPk((int) @$_POST['id'])) {
            if (!array_key_exists('enabled', $_POST)) {
                throw new \Exception('cannot determine status for question');
            }

            if ($_POST['enabled']) {
                $model->active = true;
            } else {
                $model->active = false;
            }
            if (!$model->save()) {
                throw new \Exception('Unable to set question status: '.print_r($model->getErrors(), true));
            }

            Audit::add('admin', 'set-question-status', $_POST['id'], null, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_InjectionManagementComplex_Question'));
        } else {
            throw new \Exception('Cannot find question with id'.@$_POST['id']);
        }
    }

    public function actionViewWorkflows()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow'));

        $this->render('list_OphCiExamination_Workflow', array(
                'model_class' => 'OphCiExamination_Workflow',
                'model_list' => models\OphCiExamination_Workflow::model()->findAll(array('order' => 'name asc')),
                'title' => 'Workflows',
        ));
    }

    public function actionAddWorkflow()
    {
        $model = new models\OphCiExamination_Workflow();
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow'));
                Yii::app()->user->setFlash('success', 'Workflow added');

                $this->redirect(array('viewWorkflowRules'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add workflow',
            'cancel_uri' => '/OphCiExamination/admin/viewWorkflows',
        ));
    }

    public function actionEditWorkflow($id)
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

        $model = models\OphCiExamination_Workflow::model()->findByPk((int) $id);

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_ElementSet'));
                Yii::app()->user->setFlash('success', 'Workflow updated');

                $this->redirect(array('viewWorkflows'));
            }
        }

        $this->render('update', array(
                'model' => $model,
                'title' => 'Edit workflow',
                'cancel_uri' => '/OphCiExamination/admin/viewWorkflows',
                'related_view' => 'update_Workflow_ElementSets',
        ));
    }

    public function actionEditWorkflowStep()
    {
        if (!$step = models\OphCiExamination_ElementSet::model()->findByPk(@$_GET['step_id'])) {
            throw new \Exception('ElementSetItem not found: '.@$_GET['step_id']);
        }

        $element_type_ids = array();

        foreach ($step->items as $item) {
            $element_type_ids[] = $item->element_type_id;
        }

        $et_exam = \EventType::model()->find('class_name=?', array('OphCiExamination'));

        $criteria = new CDbCriteria();
        $criteria->addCondition('t.event_type_id = :event_type_id');
        $criteria->addNotInCondition('t.id', $element_type_ids);
        $criteria->params[':event_type_id'] = $et_exam->id;
        // deprecated or invalid element types for this installation
        $criteria->addNotInCondition('t.class_name', ExaminationHelper::elementFilterList()) ;
        $criteria->order = 'parent_element_type.name asc, t.name asc';

        $element_types = \ElementType::model()->with('parent_element_type')->findAll($criteria);
        uasort($element_types, function($a, $b) {
            return $a->nameWithParent > $b->nameWithParent;
        });

        $this->renderPartial('_update_Workflow_ElementSetItem', array(
            'step' => $step,
            'element_types' => $element_types,
        ));
    }

    public function actionReorderWorkflowSteps()
    {
        foreach ($_POST as $id => $position) {
            if ($id != 'YII_CSRF_TOKEN') {
                if (!$step = models\OphCiExamination_ElementSet::model()->findByPk($id)) {
                    throw new \Exception("Unable to find workflow step: $id");
                }
                $step->position = $position;

                if (!$step->save()) {
                    throw new \Exception('Unable to save workflow step: '.print_r($step->getErrors(), true));
                }
            }
        }

        echo '1';
    }

    public function actionAddElementTypeToWorkflowStep()
    {
        $et_exam = \EventType::model()->find('class_name=?', array('OphCiExamination'));

        if (!$element_type = \ElementType::model()->find('event_type_id = ? and id = ?', array($et_exam->id, @$_POST['element_type_id']))) {
            throw new \Exception('Unknown examination element type: '.@$_POST['element_type_id']);
        }

        if (!$step = models\OphCiExamination_ElementSet::model()->findByPk(@$_POST['step_id'])) {
            throw new \Exception('Unknown element set: '.@$_POST['step_id']);
        }

        if (!models\OphCiExamination_ElementSetItem::model()->find('set_id=? and element_type_id=?', array($step->id, $element_type->id))) {
            $item = new models\OphCiExamination_ElementSetItem();
            $item->set_id = $step->id;
            $item->element_type_id = $element_type->id;

            if (!$item->save()) {
                throw new \Exception('Unable to save element set item: '.print_r($item->getErrors(), true));
            }
        }

        echo '1';
    }

    public function actionRemoveElementTypeFromWorkflowStep()
    {
        if (!$item = models\OphCiExamination_ElementSetItem::model()->find('set_id=? and id=?', array(@$_POST['step_id'], @$_POST['element_type_item_id']))) {
            throw new \Exception('Element set item not found: '.@$_POST['element_type_item_id'].' in set '.@$_POST['step_id']);
        }

        if (!$item->delete()) {
            throw new \Exception('Unable to delete element set item: '.print_r($item->getErrors(), true));
        }

        echo '1';
    }

    public function actionUpdateElementAttribute($id)
    {
        $item = models\OphCiExamination_ElementSetItem::model()->findByPk($id);
        if (!$item) {
            throw new \CHttpException('404', 'Could not find item set');
        }

        $item->attributes = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ElementSetItem');

        if (!$item->save()) {
            throw new \Exception('Unable to update element set item: '.print_r($item->getErrors(), true));
        }

        echo '1';
    }

    public function actionAddworkflowStep()
    {
        if (!$workflow = models\OphCiExamination_Workflow::model()->findByPk(@$_POST['workflow_id'])) {
            throw new \Exception('Workflow not found: '.@$_POST['workflow_id']);
        }

        if ($current_last = models\OphCiExamination_ElementSet::model()->find(array(
            'condition' => 'workflow_id = :workflow_id',
            'params' => array(
                ':workflow_id' => $workflow->id,
            ),
            'order' => 'position desc',
        ))) {
            $current_last_position = $current_last->position;
        } else {
            $current_last_position = 0;
        }

        $set = new models\OphCiExamination_ElementSet();
        $set->workflow_id = $workflow->id;
        $set->position = $current_last_position + 1;
        $set->name = 'Step '.$set->position;

        if (!$set->save()) {
            throw new \Exception('Unable to save element set: '.print_r($set->getErrors(), true));
        }

        echo json_encode(array(
            'id' => $set->id,
            'position' => $set->position,
            'name' => $set->name,
        ));
    }

    public function actionRemoveWorkflowStep()
    {
        if (!$step = models\OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?', array(@$_POST['workflow_id'], @$_POST['element_set_id']))) {
            throw new \Exception('Unknown element set '.@$_POST['element_set_id'].' for workflow '.@$_POST['workflow_id']);
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('set_id = :set_id');
        $criteria->params[':set_id'] = $step->id;

        models\OphCiExamination_ElementSetItem::model()->deleteAll($criteria);

        if (!$step->delete()) {
            throw new \Exception('Unable to remove element set: '.print_r($step->getErrors(), true));
        }

        echo '1';
    }

    public function actionDeleteWorkflows()
    {
        if (!empty($_POST['workflows'])) {
            $workflow_criteria = new CDbCriteria();
            $workflow_criteria->addInCondition('workflow_id', $_POST['workflows']);
            $step_ids = array();
            foreach (models\OphCiExamination_ElementSet::model()->findAll($workflow_criteria) as $step) {
                $step_ids[] = $step->id;
            }
            if (!empty($step_ids)) {
                $setitem_criteria = new CDbCriteria();
                $setitem_criteria->addInCondition('set_id', $step_ids);

                models\OphCiExamination_ElementSetItem::model()->deleteAll($setitem_criteria);
                $event_stepitem_criteria = new CDbCriteria();
                $event_stepitem_criteria->addInCondition('step_id', $step_ids);
                models\OphCiExamination_Event_ElementSet_Assignment::model()->deleteAll($event_stepitem_criteria);
            }
            models\OphCiExamination_ElementSet::model()->deleteAll($workflow_criteria);
            models\OphCiExamination_Workflow_Rule::model()->deleteAll($workflow_criteria);
            $workflow = new CDbCriteria();
            $workflow->addInCondition('id', $_POST['workflows']);
            if (!models\OphCiExamination_Workflow::model()->deleteAll($workflow)) {
                throw new \Exception('Unable to remove Workflow : '.print_r(models\OphCiExamination_Workflow::model()->getErrors(), true));
            }
            echo 1;
        }
    }

    public function actionSaveWorkflowStepName()
    {
        if (!$step = models\OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?', array(@$_POST['workflow_id'], @$_POST['element_set_id']))) {
            throw new \Exception('Unknown element set '.@$_POST['element_set_id'].' for workflow '.@$_POST['workflow_id']);
        }

        $step->name = @$_POST['step_name'];

        if (!$step->save()) {
            throw new \Exception('Unable to save element set: '.print_r($step->getErrors(), true));
        }

        echo '1';
    }

    public function actionViewWorkflowRules()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow_Rule'));

        $this->render('list_OphCiExamination_Workflow_Rules', array(
                'model_class' => 'OphCiExamination_Workflow_Rule',
                'model_list' => models\OphCiExamination_Workflow_Rule::model()->findAll(array('order' => 'id asc')),
                'title' => 'Workflow rules',
        ));
    }

    public function actionEditWorkflowRule($id)
    {
        if (!$model = models\OphCiExamination_Workflow_Rule::model()->findByPk($id)) {
            throw new \Exception("Workflow rule not found: $id");
        }

        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow_Rule'));
                Yii::app()->user->setFlash('success', 'Workflow rule updated');

                $this->redirect(array('viewWorkflowRules'));
            }
        }

        $this->render('update', array(
                'model' => $model,
                'title' => 'Edit workflow rule',
                'cancel_uri' => '/OphCiExamination/admin/viewWorkflowRules',
        ));
    }

    public function actionAddWorkflowRule()
    {
        $model = new models\OphCiExamination_Workflow_Rule();

        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow_Rule'));
                Yii::app()->user->setFlash('success', 'Workflow rule updated');

                $this->redirect(array('viewWorkflowRules'));
            }
        }

        $this->render('update', array(
                'model' => $model,
                'title' => 'Add workflow rule',
                'cancel_uri' => '/OphCiExamination/admin/viewWorkflowRules',
        ));
    }

    public function actionDeleteWorkflowRules()
    {
        if (is_array(@$_POST['workflowrules'])) {
            foreach ($_POST['workflowrules'] as $rule_id) {
                if ($rule = models\OphCiExamination_Workflow_Rule::model()->findByPk($rule_id)) {
                    if (!$rule->delete()) {
                        throw new \Exception('Unable to delete workflow rule: '.print_r($rule->getErrors(), true));
                    }
                }
            }
        }

        echo '1';
    }

    public function actionManageElementAttributes()
    {
        $this->genericAdmin(
            'Manage Element Attributes',
            'OEModule\OphCiExamination\models\OphCiExamination_AttributeOption',
            array(
                'filter_fields' => array(
                    array('field' => 'attribute_element_id', 'model' => 'OEModule\OphCiExamination\models\OphCiExamination_AttributeElement'),
                ),
                'extra_fields' => array(
                    array('field' => 'subspecialty_id', 'type' => 'lookup', 'model' => 'Subspecialty'),
                ),
            )
        );
    }

    public function actionManageOverallPeriods()
    {
        $this->genericAdmin('Edit Overall Periods', 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod');
    }

    public function actionManageVisitIntervals()
    {
        $this->genericAdmin('Edit Visit Intervals', 'OEModule\OphCiExamination\models\OphCiExamination_VisitInterval');
    }

    public function actionManageGlaucomaStatuses()
    {
        $this->genericAdmin('Edit Glaucoma Statuses', 'OEModule\OphCiExamination\models\OphCiExamination_GlaucomaStatus');
    }

    public function actionManageDropRelProbs()
    {
        $this->genericAdmin('Edit Drop Related Problems', 'OEModule\OphCiExamination\models\OphCiExamination_DropRelProb');
    }

    public function actionManageDrops()
    {
        $this->genericAdmin('Edit Drops Options', 'OEModule\OphCiExamination\models\OphCiExamination_Drops');
    }

    public function actionManageManagementSurgery()
    {
        $this->genericAdmin('Edit Surgery Management Options', 'OEModule\OphCiExamination\models\OphCiExamination_ManagementSurgery');
    }

    public function actionManageTargetIOPs()
    {
        $this->genericAdmin('Edit Target Iop Values', 'OEModule\OphCiExamination\models\OphCiExamination_TargetIop');
    }

    /**
     * Admin for primary reason for surgery table.
     */
    public function actionPrimaryReasonForSurgery()
    {
        $this->genericAdmin('Edit Reasons for Surgery', 'OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery');
    }

    public function actionManageComorbidities()
    {
        $this->genericAdmin(
            'Edit Comorbities',
            'OEModule\OphCiExamination\models\OphCiExamination_Comorbidities_Item',
            array(
                'extra_fields' => array(
                            array(
                                'field' => 'subspecialties',
                                'type' => 'multilookup',
                                'noSelectionsMessage' => 'All Subspecialties',
                                'htmlOptions' => array(
                                        'empty' => '- Please Select -',
                                        'nowrapper' => true,
                                ),
                                'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
                            ),
                        ),
                )
        );
    }

    public function actionManageClinicOutcomesStatus()
    {
        $extra_fields = array(
            array(
                'field' => 'episode_status_id',
                'type' => 'lookup',
                'model' => 'EpisodeStatus',
            ),
            array(
                'field' => 'subspecialties',
                'type' => 'multilookup',
                'noSelectionsMessage' => 'All Subspecialties',
                'htmlOptions' => array(
                    'empty' => '- Please Select -',
                    'nowrapper' => true,
                ),
                'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
            ),
            array(
                'field' => 'followup',
                'type' => 'boolean',
            ),
        );

        if (Yii::app()->moduleAPI->get('PatientTicketing')) {
            $extra_fields[] = array(
                'field' => 'patientticket',
                'type' => 'boolean',
            );
        }

        $this->genericAdmin(
            'Edit Clinical Outcome Statuses',
            'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status',
            array(
                'extra_fields' => $extra_fields,
            )
        );
    }

    public function actionPostOpComplications($subspecialty_id = null)
    {
        $this->render('list_OphCiExamination_PostOpComplications', array(
                'subspecialty_id' => $subspecialty_id,
                'enabled_items' => models\OphCiExamination_PostOpComplications::model()->enabled($subspecialty_id)->findAll(),
                'available_items' => models\OphCiExamination_PostOpComplications::model()->available($subspecialty_id)->findAll(),
        ));
    }

    public function actionUpdatePostOpComplications()
    {
        $item_ids = Yii::app()->request->getParam('item_ids', array());
        $subspecialty_id = Yii::app()->request->getParam('subspecialty_id', null);

        $tx = Yii::app()->db->beginTransaction();
        models\OphCiExamination_PostOpComplications::model()->assign($item_ids, $subspecialty_id);
        $tx->commit();

        $this->redirect(array('/OphCiExamination/admin/postOpComplications', 'subspecialty_id' => $subspecialty_id));
    }

    /*
     * Invoice status admin list
     */
    public function actionInvoiceStatusList()
    {

        $model = new models\InvoiceStatus();

        $this->render('list_OphCiExamination_Invoice_status', array(
            'model_class' => $model,
            'model_list' => $model::model()->findAll(array('order' => 'id asc')),
            'title' => 'Invoice Statuses',
        ));

    }

    /*
     * Add new invoice status in admin screen
     */
    public function actionAddInvoiceStatus()
    {
        $model = new models\InvoiceStatus();

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                // Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow'));
                Yii::app()->user->setFlash('success', 'Invoice status added');

                $this->redirect(array('InvoiceStatusList'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add invoice status',
            'cancel_uri' => '/OphCiExamination/admin/InvoiceStatusList'
        ));
    }

    /*
     * Edit exist invoice
     */
    public function actionEditInvoiceStatus( $id )
    {
        $model = models\InvoiceStatus::model()->findByPk((int) $id);

        if (isset($_POST[\CHtml::modelName($model)])) {

            $model->attributes = $_POST[\CHtml::modelName($model)];
            if ($model->save()) {
               // Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_ElementSet'));
                Yii::app()->user->setFlash('success', 'Invoice status updated');

                $this->redirect(array('InvoiceStatusList'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Edit invoice status',
            'cancel_uri' => '/OphCiExamination/admin/InvoiceStatusList'
        ));
    }


    /*
     * Delete invoice
     */
    public function deleteInvoiceStatus( $id )
    {

    }

    /**
     * Lists and allows editing of Allergy records.
     *
     * @throws Exception
     */
    public function actionAllergies()
    {
        $this->genericAdmin('Edit Allergies', 'OEModule\OphCiExamination\models\OphCiExaminationAllergy');
    }

    public function actionRisks()
    {
        $extra_fields = array(
            array(
                'field' => 'tags',
                'type' => 'multilookup',
                'noSelectionsMessage' => 'No Tags',
                'htmlOptions' => array(
                    'empty' => '- Please Select -',
                    'nowrapper' => true,
                ),
                'options' => \CHtml::listData(\Tag::model()->findAll(), 'id', 'name')
            )
        );

        $this->genericAdmin(
            'Edit Risks',
            'OEModule\OphCiExamination\models\OphCiExaminationRisk',
            array(
                'extra_fields' => $extra_fields
            ));
    }

    public function actionSocialHistory()
    {
        $this->render('socialhistory');
    }

    public function actionSocialHistoryOccupation()
    {
        $this->genericAdmin(models\SocialHistory::model()->getAttributeLabel('occupation_id'),
            'OEModule\OphCiExamination\models\SocialHistoryOccupation');
    }

    public function actionSocialHistoryDrivingStatus()
    {
        $this->genericAdmin(models\SocialHistory::model()->getAttributeLabel('driving_statuses'),
            'OEModule\OphCiExamination\models\SocialHistoryDrivingStatus');
    }

    public function actionSocialHistorySmokingStatus()
    {
        $this->genericAdmin(models\SocialHistory::model()->getAttributeLabel('smoking_status_id'),
            'OEModule\OphCiExamination\models\SocialHistorySmokingStatus');
    }

    public function actionSocialHistoryAccommodation()
    {
        $this->genericAdmin(models\SocialHistory::model()->getAttributeLabel('accommodation_id'),
            'OEModule\OphCiExamination\models\SocialHistoryAccommodation');
    }

    public function actionFamilyHistory()
    {
        $this->render('familyhistory');
    }

    public function actionFamilyHistoryRelative()
    {
        $this->genericAdmin(models\FamilyHistory_Entry::model()->getAttributeLabel('relative_id'),
            'OEModule\OphCiExamination\models\FamilyHistoryRelative');
    }

    public function actionFamilyHistoryCondition()
    {
        $this->genericAdmin(models\FamilyHistory_Entry::model()->getAttributeLabel('condition_id'),
            'OEModule\OphCiExamination\models\FamilyHistoryCondition');
    }

    public function actionHistoryMedicationsStopReason()
    {
        $this->genericAdmin('Medication Stop Reason',
            'OEModule\OphCiExamination\models\HistoryMedicationsStopReason');
    }

}
