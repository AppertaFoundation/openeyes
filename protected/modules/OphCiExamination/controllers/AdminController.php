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

use Audit;
use CDbCriteria;
use OEModule\OphCiExamination\components\ExaminationHelper;
use OEModule\OphCiExamination\controllers\traits\AdminForColourVision;
use OEModule\OphCiExamination\controllers\traits\AdminForContrastSensitivity;
use OEModule\OphCiExamination\controllers\traits\AdminForElementAttribute;
use OEModule\OphCiExamination\controllers\traits\AdminForCoverAndPrismCover;
use OEModule\OphCiExamination\controllers\traits\AdminForPrismReflex;
use OEModule\OphCiExamination\controllers\traits\AdminForNinePositions;
use OEModule\OphCiExamination\controllers\traits\AdminForRefraction;
use OEModule\OphCiExamination\controllers\traits\AdminForSensoryFunction;
use OEModule\OphCiExamination\controllers\traits\AdminForStereoAcuity;
use OEModule\OphCiExamination\controllers\traits\AdminForStrabismusManagement;
use OEModule\OphCiExamination\controllers\traits\AdminForVisualAcuity;
use OEModule\OphCiExamination\controllers\traits\AdminForSynoptophore;
use OEModule\OphCiExamination\models;
use Yii;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;
use OEModule\OphCiExamination\models\OphCiExaminationAllergy;

class AdminController extends \ModuleAdminController
{
    use AdminForSensoryFunction;
    use AdminForStereoAcuity;
    use AdminForColourVision;
    use AdminForVisualAcuity;
    use AdminForElementAttribute;
    use AdminForCoverAndPrismCover;
    use AdminForPrismReflex;
    use AdminForSynoptophore;
    use AdminForRefraction;
    use AdminForContrastSensitivity;
    use AdminForNinePositions;
    use AdminForStrabismusManagement;

    public $group = 'Examination';

    public $defaultAction = 'ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason';

    public function actions()
    {
        return [
            'sortWorkflowElementSetItem' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => models\OphCiExamination_ElementSetItem::model(),
                'modelName' => 'OphCiExamination_ElementSetItem'
            ],
        ];
    }

    public function actionViewIOPInstruments()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Instrument'));

        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js', true);
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        if (\Yii::app()->request->isPostRequest) {
            $instruments = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Instrument', []);
            foreach ($instruments as $instrument) {
                $model = models\OphCiExamination_Instrument::model()->findByPk($instrument['id']);
                $model->display_order = $instrument['display_order'];
                $model->save();
            }
        }

        $model_list = models\OphCiExamination_Instrument::model()->findAll([
            'order' => 'display_order asc',
            'with' => 'institutions',
            'condition' => !Yii::app()->user->checkAccess('admin') ? 'institutions_institutions.institution_id = :institution_id' : '',
            'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
        ]);

        $this->render('list_OphCiExamination_IOPInstruments', array(
            'model_class' => 'OphCiExamination_Instrument',
            'model_list' => $model_list,
            'title' => 'IOP Instruments',
        ));
    }

    public function actionEditIOPInstrument($id)
    {
        $model = models\OphCiExamination_Instrument::model()->findByPk((int) $id);

        if (isset($_POST[\CHtml::modelName($model)])) {
            $post_attributes = $_POST[\CHtml::modelName($model)];
            try {
                $model = models\OphCiExamination_Instrument::model()->findByPk($id);
                $model->name = $post_attributes['name'];
                $model->short_name = $post_attributes['short_name'];
                $model->active = $post_attributes['active'];
                $model->visible = $post_attributes['visible'];
                $model->save();

                if (Yii::app()->user->checkAccess('admin')) {
                    models\OphCiExamination_Instrument_Institution::model()->deleteAll('instrument_id = :instrument_id', [':instrument_id' => $id]);
                } elseif ($model->hasMapping(\ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id'])) {
                    $model->deleteMapping(\ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);
                }

                if (array_key_exists('institutions', $post_attributes) && !empty($post_attributes['institutions'])) {
                    $model->createMappings(\ReferenceData::LEVEL_INSTITUTION, $post_attributes['institutions']);
                }

                Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Instrument'));
                Yii::app()->user->setFlash('success', 'IOP Instrument updated');
            } catch (Exception $e) {
                throw new CHttpException(500, $e->getMessage(), true);
            }

            $this->redirect(array('ViewIOPInstruments'));
        }

        $multiselect_list = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/MultiSelectList.js', true);
        Yii::app()->getClientScript()->registerScriptFile($multiselect_list);

        $this->render('update', array(
            'model' => $model,
            'title' => 'Edit IOP Instrument',
            'cancel_uri' => '/OphCiExamination/admin/viewIOPInstruments'
        ));
    }

    public function actionAddIOPInstrument()
    {
        $model = new models\OphCiExamination_Instrument();

        if (isset($_POST[\CHtml::modelName($model)])) {
            $post_attributes = $_POST[\CHtml::modelName($model)];
            if (Yii::app()->user->checkAccess('admin')) {
                // Only admins can create instances at installation level
                if (isset($post_attributes['institutions'])) {
                    $institutions = $post_attributes['institutions'];
                } else {
                    $institutions = [];
                }
            } else {
                // Save instance only at instition level
                $institutions[] = Yii::app()->session['selected_institution_id'];
            }
            try {
                $model->name = $post_attributes['name'];
                $model->short_name = $post_attributes['short_name'];
                $model->active = $post_attributes['active'];
                $model->visible = $post_attributes['visible'];

                $criteria=new CDbCriteria;
                $criteria->select = 'max(display_order) AS display_order';
                $order = $model->model()->find($criteria);
                $model->display_order = (int)$order['display_order'] + 1;

                if ($model->save(false)) {
                    if (!empty($institutions)) {
                        $model->createMappings(\ReferenceData::LEVEL_INSTITUTION, $institutions);
                    }
                    Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Instrument'));
                    Yii::app()->user->setFlash('success', 'IOP Instrument created');
                }
            } catch (Exception $e) {
                throw new CHttpException(500, $e->getMessage(), true);
            }

            $this->redirect(array('ViewIOPInstruments'));
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add IOP Instrument',
            'cancel_uri' => '/OphCiExamination/admin/viewIOPInstruments'
        ));
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

                    $this->redirect('ViewOphCiExamination_InjectionManagementComplex_Question?disorder_id='.$model->disorder_id);
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
            'cancel_uri' => \Yii::app()->request->urlReferrer ?: '/OphCiExamination/admin/viewOphCiExamination_InjectionManagementComplex_Question',
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
                'model_list' => models\OphCiExamination_Workflow::model()->findAll(['condition' => 'institution_id = :institution_id', 'order' => 'name asc', 'params' => [':institution_id' => Yii::app()->session['selected_institution_id']]]),
                'title' => 'Workflows',
        ));
    }

    public function actionAddWorkflow()
    {
        $model = new models\OphCiExamination_Workflow();
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true, -1);

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Workflow'));
                Yii::app()->user->setFlash('success', 'Workflow added');

                $this->redirect(array('editWorkflow', 'id' => $model->getPrimaryKey()));
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
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true, -1);

        $model = models\OphCiExamination_Workflow::model()->findByPk((int) $id);

        if (isset($_POST[\CHtml::modelName($model)])) {
            $updated_attributes = $_POST[\CHtml::modelName($model)];
            $model->name = $updated_attributes['name'];
            $model->institution_id = $updated_attributes['institution_id'];

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
        $criteria->addNotInCondition('t.class_name', ExaminationHelper::elementFilterList());
        $criteria->order = 't.display_order asc';

        $element_types = \ElementType::model()->findAll($criteria);

        $this->renderPartial('_update_Workflow_ElementSetItem', array(
            'step' => $step,
            'element_types' => $element_types,
        ));
    }

    public function actionSetWorkflowToDefault()
    {
        $element_set_id = Yii::app()->request->getParam('element_set_id');
        if (!$element_set_id) {
            echo 0;
        }

        $transaction = Yii::app()->db->beginTransaction();

        $default_types = \ElementType::model()->findAll();
        foreach ($default_types as $type) {
            $element_set = models\OphCiExamination_ElementSet::model()->findByPk($element_set_id);
            $items_to_edit = $element_set ? $element_set->items : [];
            $items_to_edit = array_filter($items_to_edit, function ($item) use ($type) {
                return $item->element_type_id == $type->id;
            });

            if (count($items_to_edit) == 1) {
                $item = array_pop($items_to_edit);
                $item->display_order = $type->display_order;
                if (!$item->save()) {
                    $transaction->rollback();
                    echo 0;
                    return;
                }
            }
        }
        $transaction->commit();
        echo 1;
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

        $post = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ElementSetItem', []);
        $item->attributes = array_shift($post);

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

        $this->renderJSON(array(
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
        $workflow_id = Yii::app()->request->getParam('workflow_id');
        $element_set_id = Yii::app()->request->getParam('element_set_id');
        $step = models\OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?', array($workflow_id, $element_set_id));
        if (!$step) {
            throw new \Exception('Unknown element set '.$element_set_id.' for workflow '.$workflow_id);
        }

        $step->name = Yii::app()->request->getParam('step_name');

        if (!$step->save()) {
            throw new \Exception('Unable to save element set: '.print_r($step->getErrors(), true));
        }

        echo '1';
    }

    public function actionSaveWorkflowDisplayOrderEditStatus()
    {
        $workflow_id = Yii::app()->request->getParam('workflow_id');
        $element_set_id = Yii::app()->request->getParam('element_set_id');
        $step = models\OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?', array($workflow_id, $element_set_id));
        if (!$step) {
            throw new \Exception('Unknown element set '.$element_set_id.' for workflow '.$workflow_id);
        }

        $step->display_order_edited = Yii::app()->request->getParam('display_order_edited');

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
                'model_list' => models\OphCiExamination_Workflow_Rule::model()->findAll(
                    array(
                        'condition' => 'institution_id IS NULL OR institution_id = :institution_id',
                        'order' => 't.id asc',
                        'params' => [':institution_id' => Yii::app()->session['selected_institution_id']]
                    )
                ),
                'title' => 'Workflow rules',
        ));
    }

    public function actionGetInstitutionFirms($id)
    {
        $firms = Yii::app()->db->createCommand()
            ->select('id, name')
            ->from('firm')
            ->where('institution_id = :id', [':id' => $id])
            ->queryAll();

        $this->renderJSON($firms);
    }

    public function actionGetInstitutionWorkflows($id)
    {
        $workflows = Yii::app()->db->createCommand()
            ->select('id, name')
            ->from('ophciexamination_workflow')
            ->where('institution_id = :id', [':id' => $id])
            ->queryAll();

        $this->renderJSON($workflows);
    }

    public function actionEditWorkflowRule($id)
    {
        if (!$model = models\OphCiExamination_Workflow_Rule::model()->findByPk($id)) {
            throw new \Exception("Workflow rule not found: $id");
        }

        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true, -1);

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

        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), true, -1);

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

    public function actionManageOverallPeriods()
    {
        $this->genericAdmin(
            'Edit Overall Periods',
            'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod',
            ['div_wrapper_class' => 'cols-4']
        );
    }

    public function actionManageVisitIntervals()
    {
        $this->genericAdmin(
            'Edit Visit Intervals',
            'OEModule\OphCiExamination\models\OphCiExamination_VisitInterval',
            ['extra_fields' => [
                    [
                        'field' => 'institution_id',
                        'type' => 'institution',
                        'model' => models\OphCiExamination_VisitInterval::model(),
                        'current_institution_only' => true,
                        'htmlOptions' => ['class' => 'cols-full'],
                    ],
                ],
                'filter_fields' => [
                    ['field' => 'institution_id',
                        'value' => \Institution::model()->getCurrent()->name,
                        'choices' => \Institution::model()->getList(true)],
                ],
                'filters_ready' => isset($_GET['institution_id']) && $_GET['institution_id'] === Yii::app()->session['selected_institution_id'],
            ]);
    }

    public function actionManageGlaucomaStatuses()
    {
        $this->genericAdmin(
            'Edit Glaucoma Statuses',
            'OEModule\OphCiExamination\models\OphCiExamination_GlaucomaStatus',
            ['div_wrapper_class' => 'cols-4']
        );
    }

    public function actionManageDropRelProbs()
    {
        $this->genericAdmin(
            'Edit Drop Related Problems',
            'OEModule\OphCiExamination\models\OphCiExamination_DropRelProb',
            ['div_wrapper_class' => 'cols-5']
        );
    }

    public function actionManageDrops()
    {
        $this->genericAdmin(
            'Edit Drops Options',
            'OEModule\OphCiExamination\models\OphCiExamination_Drops',
            ['div_wrapper_class' => 'cols-5']
        );
    }

    public function actionManageManagementSurgery()
    {
        $this->genericAdmin(
            'Edit Surgery Management Options',
            'OEModule\OphCiExamination\models\OphCiExamination_ManagementSurgery',
            ['div_wrapper_class' => 'cols-5' ,'input_class' => 'cols-full']
        );
    }

    public function actionManageTargetIOPs()
    {
        $this->genericAdmin(
            'Edit Target Iop Values',
            'OEModule\OphCiExamination\models\OphCiExamination_TargetIop',
            ['div_wrapper_class' => 'cols-4']
        );
    }

    /**
     * Admin for primary reason for surgery table.
     */
    public function actionPrimaryReasonForSurgery()
    {
        $this->genericAdmin(
            'Edit Reasons for Surgery',
            'OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery',
            ['div_wrapper_class' => 'cols-5', 'input_class' => 'cols-full']
        );
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
                                        'empty' => 'Select',
                                        'nowrapper' => true,
                                ),
                                'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
                            ),
                        ),
                'div_wrapper_class' => 'cols-6',
                )
        );
    }

    public function actionManageClinicOutcomesStatus()
    {
        $extra_fields = [
            [
                'field' => 'episode_status_id',
                'type' => 'lookup',
                'model' => 'EpisodeStatus',
            ],
            [
                'field' => 'institution_id',
                'type' => 'institution',
                'model' => models\OphCiExamination_ClinicOutcome_Status::model(),
                'current_institution_only' => true,
            ],
            [
                'field' => 'subspecialties',
                'type' => 'multilookup',
                'noSelectionsMessage' => 'All Subspecialties',
                'htmlOptions' => [
                    'empty' => 'Select',
                    'nowrapper' => true,
                ],
                'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
            ],
            [
                'field' => 'followup',
                'type' => 'boolean',
            ],
        ];

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
                'div_wrapper_class' => 'cols-8',
                'filter_fields' => [
                    ['field' => 'institution_id',
                    'value' => \Institution::model()->getCurrent()->name,
                    'choices' => \Institution::model()->getList(true)],
                ],
                'filters_ready' => isset($_GET['institution_id']) && $_GET['institution_id'] === Yii::app()->session['selected_institution_id'],
            )
        );
    }

    public function actionPostOpComplications($institution_id = null, $subspecialty_id = null)
    {
        if ($institution_id === null) {
            $institution_id = Yii::app()->session['selected_institution_id'];
        }
        $this->render('list_OphCiExamination_PostOpComplications', array(
                'institution_id' => $institution_id,
                'subspecialty_id' => $subspecialty_id,
                'enabled_items' => models\OphCiExamination_PostOpComplications::model()->enabled($institution_id, $subspecialty_id)->findAll(),
                'available_items' => models\OphCiExamination_PostOpComplications::model()->available($subspecialty_id)->findAll(),
        ));
    }

    public function actionUpdatePostOpComplications()
    {
        $item_ids = Yii::app()->request->getParam('item_ids', array());
        $institution_id = Yii::app()->request->getParam('institution_id', array());
        $subspecialty_id = Yii::app()->request->getParam('subspecialty_id', null);

        $tx = Yii::app()->db->beginTransaction();
        models\OphCiExamination_PostOpComplications::model()->assign($item_ids, $institution_id, $subspecialty_id);
        $tx->commit();

        $this->redirect(array('/OphCiExamination/admin/postOpComplications?institution_id='. $institution_id .'&subspecialty_id='. $subspecialty_id));
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
    public function actionEditInvoiceStatus($id)
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


    /**
     * Delete invoice
     * @throws \Exception
     */
    public function actionDeleteInvoiceStatus()
    {
        if (is_array(@$_POST['select'])) {
            foreach ($_POST['select'] as $rule_id) {
                if ($rule = models\InvoiceStatus::model()->findByPk($rule_id)) {
                    if (!$rule->delete()) {
                        echo 'Unable to delete Invoice Status';
                        throw new \Exception('Unable to delete Invoice Status: '.print_r($rule->getErrors(), true));
                    }
                }
            }
        }

        echo 1;
    }

    public function actionDrGradingFeatures()
    {
        $this->pageTitle = 'OpenEyes - DR Grading Feature Admin';
        $extra_fields = array(
            array(
                'field' => 'grade',
                'type' => 'dropdown',
                'htmlOptions' => array(
                    'empty' => 'Select',
                    'nowrapper' => true,
                ),
                'options' => array(
                    'R0' => 'R0',
                    'R1' => 'R1',
                    'R2' => 'R2',
                    'R3a' => 'R3a',
                    'R3s' => 'R3s',
                    'M0' => 'M0',
                    'M1' => 'M1',
                )
            )
        );
        $this->genericAdmin(
            'Edit DR Grading features',
            models\OphCiExamination_DRGrading_Feature::class,
            array(
                'div_wrapper_class' => 'cols-5',
                'extra_fields' => $extra_fields,
            )
        );
    }

    /**
     * Lists and allows editing of Allergy records.
     *
     * @throws Exception
     */
    public function actionAllergies()
    {
        $this->genericAdmin('Edit Allergies', OphCiExaminationAllergy::class, ['div_wrapper_class' => 'cols-5']);
    }

    public function actionRisks()
    {
        $extra_fields = array(
            array(
                'field' => 'tags',
                'type' => 'multilookup',
                'noSelectionsMessage' => 'No Tags',
                'htmlOptions' => array(
                    'empty' => 'Select',
                    'nowrapper' => true,
                ),
                'options' => \CHtml::listData(\Tag::model()->findAll(), 'id', 'name')
            ),
            array(
                'field' => 'display_on_whiteboard',
                'type' => 'boolean',
            ),
        );

        $this->genericAdmin(
            'Edit Risks',
            OphCiExaminationRisk::class,
            array(
                'extra_fields' => $extra_fields,
                'div_wrapper_class' => 'cols-6',
            )
        );
    }

    public function actionSocialHistory()
    {
        $this->render('socialhistory');
    }

    public function actionSocialHistoryOccupation()
    {
        $this->genericAdmin(
            models\SocialHistory::model()->getAttributeLabel('occupation_id'),
            'OEModule\OphCiExamination\models\SocialHistoryOccupation'
        );
    }

    public function actionSocialHistoryDrivingStatus()
    {
        $this->genericAdmin(
            models\SocialHistory::model()->getAttributeLabel('driving_statuses'),
            'OEModule\OphCiExamination\models\SocialHistoryDrivingStatus'
        );
    }

    public function actionSocialHistorySmokingStatus()
    {
        $this->genericAdmin(
            models\SocialHistory::model()->getAttributeLabel('smoking_status_id'),
            'OEModule\OphCiExamination\models\SocialHistorySmokingStatus'
        );
    }

    public function actionSocialHistoryAccommodation()
    {
        $this->genericAdmin(
            models\SocialHistory::model()->getAttributeLabel('accommodation_id'),
            'OEModule\OphCiExamination\models\SocialHistoryAccommodation'
        );
    }

    public function actionFamilyHistory()
    {
        $this->render('familyhistory');
    }

    public function actionFamilyHistoryRelative()
    {
        $this->genericAdmin(
            models\FamilyHistory_Entry::model()->getAttributeLabel('relative_id'),
            'OEModule\OphCiExamination\models\FamilyHistoryRelative',
            ['div_wrapper_class' => 'cols-6']
        );
    }

    public function actionFamilyHistoryCondition()
    {
        $this->genericAdmin(
            models\FamilyHistory_Entry::model()->getAttributeLabel('condition_id'),
            'OEModule\OphCiExamination\models\FamilyHistoryCondition'
        );
    }

    public function actionMedicationManagementSets()
    {
        $this->genericAdmin(
            'Medication Management drug sets',
            models\MedicationManagementRefSet::class,
            array(
                'description' => 'Medications in these sets will be automatically be pulled into the medication management element.',
                'label_field' => 'ref_set_id',
                'extra_fields' => array(
                    array('field' => 'ref_set_id', 'type' => 'lookup',
                        'model' => \MedicationSet::class, ),
                ),
            )
        );
    }

    public function actionChangeWorkflowStepActiveStatus()
    {
        $step = models\OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?', array($_POST['workflow_id'], $_POST['element_set_id']));
        if (!$step) {
            throw new \Exception('Unknown element set '.$_POST['element_set_id'].' for workflow '.$_POST['workflow_id']);
        }

        $step->is_active = ($step->is_active === '1' ? 0 : 1);
        if (!$step->save()) {
            throw new \Exception('Unable to change element set is_active status: '.print_r($step->getErrors(), true));
        }

        echo '1';
    }

    public function actionCorrectionTypes()
    {
        $this->genericAdmin('Correction Types',
            models\CorrectionType::class,
            [
                'description' => 'Correction Types are used in multiple examination elements',
            ]
        );
    }
}
