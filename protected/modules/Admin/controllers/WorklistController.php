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
class WorklistController extends BaseAdminController
{
    public $items_per_page = 30;
    public $group = 'Worklist';

    /**
     * @var WorklistManager
     */
    public $manager;

    /**
     * @param $action
     *
     * @return bool
     */
    protected function beforeAction($action)
    {
        $this->manager = new WorklistManager();

        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return array(
            'SetupPathwayStepPicker' => ['class' => 'application.behaviors.SetupPathwayStepPickerBehavior',],
        );
    }
    /**
     * @param string $type - the classification of the message
     * @param $message - the message to display
     * @param string $id - the flash element id suffix. defaults to message
     */
    protected function flashMessage($type = 'success', $message, $id = 'message')
    {
        Yii::app()->user->setFlash("{$type}.{$id}", $message);
    }

    /**
     * List the current definitions.
     */
    public function actionDefinitions()
    {
        $definitions = $this->manager->getWorklistDefinitions();

        $this->render('definitions', array(
            'definitions' => $definitions,
        ));
    }

    /**
     * View a definition.
     *
     * @param null $id
     *
     * @throws CHttpException
     */
    public function actionDefinition($id = null)
    {
        $definition = $this->getWorklistDefinition($id);

        $this->render('definition', array(
            'definition' => $definition,
        ));
    }

    /**
     * Create or Edit a WorklistDefinition.
     *
     * @param null $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionUpdate($id = null)
    {
        $definition = $this->manager->getWorklistDefinition($id);

        if (!$definition) {
            throw new CHttpException(404, 'Worklist definition could not be '.($id ? 'found' : 'created'));
        }

        if (!$this->manager->canUpdateWorklistDefinition($definition)) {
            throw new CHttpException(409, 'Cannot change mappings for un-editable Definition');
        }

        if (isset($_POST['WorklistDefinition'])) {
            $definition->attributes = $_POST['WorklistDefinition'];
            if (!$this->manager->saveWorklistDefinition($definition)) {
                $errors = $definition->getErrors();
            } else {
                $this->flashMessage('success', 'Worklist Definition saved');

                return $this->redirect(array('/Admin/worklist/definitions'));
            }
        }

        $this->render('definition_edit', array(
            'definition' => $definition,
            'errors' => @$errors,
        ));
    }

    /**
     * @param $id
     *
     * @throws CDbException
     * @throws CHttpException
     */
    public function actionDefinitionDelete($id)
    {
        $definition = $this->getWorklistDefinition($id);

        if (!$this->manager->canUpdateWorklistDefinition($definition)) {
            throw new CHttpException(409, 'Cannot delete a definition that is not valid for editing.');
        }

        if ($definition->delete()) {
            $this->flashMessage('success', 'Worklist Definition deleted.');
        } else {
            $this->flashMessage('error', 'Could not delete worklist definition');
        }

        return $this->redirect('/Admin/worklist/definitions');
    }

    /**
     * Convenience Wrapper.
     *
     * @param $id
     *
     * @return null|WorklistDefinition
     *
     * @throws CHttpException
     */
    protected function getWorklistDefinition($id)
    {
        $definition = $this->manager->getWorklistDefinition($id);

        if (!$definition) {
            throw new CHttpException(404, 'Worklist definition not found');
        }

        return $definition;
    }

    /**
     * List of worklists for a definition.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionWorklists($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $this->render('definition_worklists', array(
            'definition' => $definition,
        ));
    }

    /**
     * Update the Worklist Definition Mapping Attribute order.
     */
    public function actionDefinitionSort()
    {
        $definition_ids = @$_POST['item_ids'] ?: array();

        if (count($definition_ids)) {
            if (!$this->manager->setWorklistDefinitionDisplayOrder($definition_ids)) {
                OELog::log(print_r($this->manager->getErrors(), true));
                $this->flashMessage('error', 'Could not reorder definitions');
            } else {
                $this->flashMessage('success', 'Definitions re-ordered');
            }
        }

        $this->redirect('/Admin/worklist/definitions/');
    }

    /**
     * Modify acceptable worklist wait times.
     */
    public function actionWaitTimes()
    {
        $this->group = 'Worklist';
        $this->genericAdmin(
            'Wait Times',
            'WorklistWaitTime',
            [
                'label_field' => 'label',
                'extra_fields' => ['wait_minutes' => ['field' => 'wait_minutes', 'type' => 'text']],
                'div_wrapper_class' => 'cols-5',
                'return_url' => '/Admin/worklist/waitTimes'
            ],
            null,
            true
        );
    }

    public function actionVisualFieldTestTypes()
    {
        $this->group = 'Worklist';
        $this->genericAdmin(
            'Visual Field Test Types',
            'VisualFieldTestType',
            [
                'label_field' => 'long_name',
                'extra_fields' => [
                    'short_name' => [
                        'field' => 'short_name',
                        'type' => 'text'
                    ]
                ],
                'div_wrapper_class' => 'cols-5',
                'return_url' => '/Admin/worklist/visualFieldTestTypes'
            ]
        );
    }

    public function actionVisualFieldTestOptions()
    {
        $this->group = 'Worklist';
        $this->genericAdmin(
            'Visual Field Test Options',
            'VisualFieldTestOption',
            [
                'label_field' => 'long_name',
                'extra_fields' => [
                    'short_name' => [
                        'field' => 'short_name',
                        'type' => 'text'
                    ]
                ],
                'div_wrapper_class' => 'cols-5',
                'return_url' => '/Admin/worklist/visualFieldTestOptions'
            ]
        );
    }

    public function actionVisualFieldTestPresets()
    {
        $this->group = 'Worklist';
        $this->genericAdmin(
            'Visual Field Test Presets',
            'VisualFieldTestPreset',
            [
                'label_field' => 'name',
                'extra_fields' => [
                    'test_type_id' => [
                        'field' => 'test_type_id',
                        'type' => 'lookup',
                        'model' => 'VisualFieldTestType'
                    ],
                    'option_id' => [
                        'field' => 'option_id',
                        'type' => 'lookup',
                        'model' => 'VisualFieldTestOption'
                    ],
                ],
                'div_wrapper_class' => 'cols-5',
                'return_url' => '/Admin/worklist/visualFieldTestPresets'
            ],
            null,
            true
        );
    }

    /**
     * List custom path steps
     */
    public function actionCustomPathSteps()
    {
        $this->render('custom_pathsteps', [
            'custom_pathsteps' => PathwayStepType::getCustomTypes($this->checkAccess('admin')),
        ]);
    }

    public function actionEditCustomPathStep($id = null)
    {
        $pathwayStepType = Yii::app()->request->getPost('PathwayStepType');
        $pathwayStepTypePreset = Yii::app()->request->getPost('PathwayStepTypePresetAssignment');
        $errors = [];

        if ($id === null) {
            $model = new PathwayStepType();
            $preset_model = new PathwayStepTypePresetAssignment();
        } else {
            $model = PathwayStepType::model()->findByPk($id);
            $preset_model = PathwayStepTypePresetAssignment::model()->find('custom_pathway_step_type_id = ?', [$id]) ?? new PathwayStepTypePresetAssignment();
        }

        if (!empty($pathwayStepType) || !empty($pathwayStepTypePreset)) {
            $transaction = Yii::app()->db->beginTransaction();

            $model->attributes = $pathwayStepType;
            // Custom steps have type process only
            $model->type = 'process';
            // An extra save call for new path step
            if ($id === null) {
                if (!$model->save()) {
                    $errors[] = $model->getErrors();
                } else {
                    $id = Yii::app()->db->getLastInsertID();
                    $model->createMapping(ReferenceData::LEVEL_INSTITUTION, Institution::model()->getCurrent()->id);
                }
            }

            $preset_model->custom_pathway_step_type_id = $id;
            $preset_model->standard_pathway_step_type_id = $pathwayStepTypePreset['standard_pathway_step_type_id'];
            $preset_model->preset_short_name = $preset_model->standard_pathway_step_type->short_name;
            if ($preset_model->preset_short_name === 'Book Apt.') {
                /**
                 * The preset ID is saved as a three digit value, where
                 * first digit is the duration period, between days, weeks, months and years
                 * and the other two are for duration value, between 1 and 18
                 */
                $preset_model->preset_id = '';
                if (array_key_exists('duration_period', $pathwayStepTypePreset) && $pathwayStepTypePreset['duration_period'] !== '') {
                    $preset_model->preset_id = $pathwayStepTypePreset['duration_period'] * 100;
                }
                if (array_key_exists('duration_value', $pathwayStepTypePreset) && $pathwayStepTypePreset['duration_value'] !== '') {
                    $preset_model->preset_id += $pathwayStepTypePreset['duration_value'];
                }
            } elseif (array_key_exists('preset_id', $pathwayStepTypePreset)) {
                $preset_model->preset_id = $pathwayStepTypePreset['preset_id'];
            }
            $model->widget_view = $preset_model->standard_pathway_step_type->widget_view;
            if (array_key_exists('site_id', $pathwayStepTypePreset)) {
                $preset_model->site_id = $pathwayStepTypePreset['site_id'];
            }
            if (array_key_exists('subspecialty_id', $pathwayStepTypePreset)) {
                $preset_model->subspecialty_id = $pathwayStepTypePreset['subspecialty_id'];
            }
            if (array_key_exists('firm_id', $pathwayStepTypePreset)) {
                $preset_model->firm_id = $pathwayStepTypePreset['firm_id'];
            }
            if (!$preset_model->save()) {
                $errors[] = $preset_model->getErrors();
            }

            $model->state_data_template = $preset_model->getStateDataTemplate();

            if (empty($errors) && $model->save()) {
                $transaction->commit();
                $this->redirect('/Admin/worklist/customPathSteps');
            } else {
                $transaction->rollback();
            }
        }

        $examination_workflows = CHtml::listData(
            \OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findAll(
                [
                    'condition' => 'institution_id = :institution_id',
                    'order' => 'name asc',
                    'params' => [':institution_id' => Yii::app()->session['selected_institution_id']]
                ]), 'name', 'id'
        );
        $examination_workflow_steps = OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule::model()->findWorkflowSteps(
            Yii::app()->session['selected_institution_id'],
            null
        );
        $letter_macros = CHtml::listData(
            LetterMacro::model()->findAll([
                'with' => 'institutions',
                'condition' => 'institutions_institutions.institution_id = :institution_id',
                'order' => 't.name asc',
                'params' => [':institution_id' => Yii::app()->session['selected_institution_id']]
            ]), 'name', 'id'
        );
        $pgd_sets = CHtml::listData(
            OphDrPGDPSD_PGDPSD::model()->findAll([
                'condition' => 'institution_id = :institution_id AND LOWER(type) = "pgd" AND active = 1',
                'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
                'order' => 'name asc',
            ]), 'name', 'id'
        );

        $this->render('update_custom_pathstep', [
            'model' => $model,
            'preset_model' => $preset_model,
            'examination_workflow_steps' => CJSON::encode($examination_workflow_steps),
            'letter_macros' => json_encode($letter_macros, JSON_THROW_ON_ERROR),
            'pgd_sets' => json_encode($pgd_sets, JSON_THROW_ON_ERROR),
            'errors' => @$errors
        ]);
    }

    /**
     * List of patients on a worklist (only supporting worklists generated by a definition.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionWorklistPatients($id)
    {
        $worklist = $this->manager->getWorklist($id);
        if (!$worklist) {
            throw new CHttpException(404, 'Worklist not found');
        }

        if (!$worklist->worklist_definition) {
            throw new CHttpException(400, 'Worklist does not have a definition so not viewable in this admin.');
        }

        $this->render('worklist_patients', array(
            'worklist' => $worklist,
        ));
    }

    /**
     * Delete the generated worklists for a worklist definition.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionWorklistsDelete($id)
    {
        $definition = $this->getWorklistDefinition($id);

        if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == $id) {
            if ($this->manager->deleteWorklistDefinitionInstances($definition)) {
                $this->flashMessage('success', "Instances removed for Worklist Definition {$definition->name}");
            } else {
                $this->flashMessage('error', "Unable to delete instances for Worklist Definition {$definition->name}");
            }
            $this->redirect('/Admin/worklist/definitions');
        }
        $this->render('definition_worklists_delete', array(
            'definition' => $definition,
        ));
    }
    /**
     * Generate instances for the given WorklistDefinition.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionGenerate($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $new_count = $this->manager->generateAutomaticWorklists($definition);

        if ($new_count === false) {
            OELog::log(print_r($this->manager->getErrors(), true));
            $this->flashMessage('error', "There was a problem generating worklists for {$definition->name}.");
        } else {
            $this->flashMessage('success', "Worklist Generation Completed for {$definition->name}. {$new_count} new instances created.");
        }

        $this->redirect(array('/Admin/worklist/definitions'));
    }

    /**
     * List the WorklistDefinitionMappings for the given id.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionMappings($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $this->render('definition_mappings', array(
            'definition' => $definition,
        ));
    }

    /**
     * Create a new WorkflowDefinitionMapping for the given WorkflowDefinition.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionAddDefinitionMapping($id)
    {
        $definition = $this->getWorklistDefinition($id);

        if (!$this->manager->canUpdateWorklistDefinition($definition)) {
            throw new CHttpException(409, 'Cannot add mapping to un-editable Definition');
        }

        $mapping = new WorklistDefinitionMapping();
        $mapping->worklist_definition_id = $definition->id;
        $mapping->worklist_definition = $definition;

        if (isset($_POST['WorklistDefinitionMapping'])) {
            $mapping->attributes = $_POST['WorklistDefinitionMapping'];
            if ($this->manager->updateWorklistDefinitionMapping(
                $mapping,
                $_POST['WorklistDefinitionMapping']['key'],
                $_POST['WorklistDefinitionMapping']['valuelist'],
                $_POST['WorklistDefinitionMapping']['willdisplay']
            )) {
                $this->flashMessage('success', 'Worklist Definition Mapping saved.');
                $this->redirect(array('/Admin/worklist/definitionMappings/'.$id));
            } else {
                $errors = $mapping->getErrors();
                $errors[] = $this->manager->getErrors();
            }
        }

        $this->render('definition_mapping', array(
            'mapping' => $mapping,
            'errors' => @$errors,
        ));
    }

    /**
     * Update a WorkflowDefinitionMapping.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionMappingUpdate($id)
    {
        if (!$mapping = WorklistDefinitionMapping::model()->findByPk($id)) {
            throw new CHttpException(404, 'Worklist Definition Mapping not found.');
        }

        if (!$this->manager->canUpdateWorklistDefinition($mapping->worklist_definition)) {
            throw new CHttpException(409, 'Cannot change mappings for un-editable Definition');
        }

        if (isset($_POST['WorklistDefinitionMapping'])) {
            $mapping->attributes = $_POST['WorklistDefinitionMapping'];
            if ($this->manager->updateWorklistDefinitionMapping(
                $mapping,
                $_POST['WorklistDefinitionMapping']['key'],
                $_POST['WorklistDefinitionMapping']['valuelist'],
                $_POST['WorklistDefinitionMapping']['willdisplay']
            )) {
                $this->flashMessage('success', 'Worklist Definition Mapping saved.');
                $this->redirect(array('/Admin/worklist/definitionMappings/'.$mapping->worklist_definition_id));
            } else {
                $errors = $mapping->getErrors();
                $errors[] = $this->manager->getErrors();
            }
        }

        $this->render('definition_mapping', array(
            'mapping' => $mapping,
            'errors' => @$errors,
        ));
    }

    /**
     * Delete a definition mapping.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionMappingDelete($id)
    {
        if (!$mapping = WorklistDefinitionMapping::model()->findByPk($id)) {
            throw new CHttpException(404, 'Worklist Definition Mapping not found.');
        }

        if (!$this->manager->canUpdateWorklistDefinition($mapping->worklist_definition)) {
            throw new CHttpException(409, 'Cannot delete mapping for un-editable Definition');
        }

        if ($mapping->delete()) {
            $this->flashMessage('success', 'Mapping removed.');
        } else {
            $this->flashMessage('error', 'Cannot delete mapping.');
        }

        $this->redirect(array('/Admin/worklist/definitionMappings/'.$mapping->worklist_definition_id));
    }

    /**
     * Update the Worklist Definition Mapping Attribute order.
     *
     * @param $id
     */
    public function actionDefinitionMappingSort($id)
    {
        $definition = $this->getWorklistDefinition($id);
        $mapping_ids = @$_POST['item_ids'] ?: array();

        if (count($mapping_ids)) {
            if (!$this->manager->setWorklistDefinitionMappingDisplayOrder($definition, $mapping_ids)) {
                OELog::log(print_r($this->manager->getErrors(), true));
                $this->flashMessage('error', 'Could not reorder mappings');
            } else {
                $this->flashMessage('success', 'Mappings re-ordered');
            }
        }

        $this->redirect('/Admin/worklist/definitionMappings/'.$id);
    }

    public function actionDefinitionDisplayContexts($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $this->render('definition_display_contexts', array(
            'definition' => $definition,
        ));
    }

    /**
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionDisplayContextAdd($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $display_context = new WorklistDefinitionDisplayContext();
        $display_context->worklist_definition_id = $definition->id;
        $display_context->worklist_definition = $definition;

        if (isset($_POST['WorklistDefinitionDisplayContext'])) {
            $display_context->attributes = $_POST['WorklistDefinitionDisplayContext'];
            if ($display_context->save()) {
                $this->flashMessage('success', 'Worklist Definition Display Context saved.');
                $this->redirect(array('/Admin/worklist/definitionDisplayContexts/'.$id));
            } else {
                $errors = $display_context->getErrors();
            }
        }

        $this->render('definition_display_context_edit', array(
            'display_context' => $display_context,
            'errors' => @$errors,
        ));
    }

    /**
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionDefinitionDisplayContextDelete($id)
    {
        if (!$display_context = WorklistDefinitionDisplayContext::model()->findByPk($id)) {
            throw new CHttpException(404, 'Worklist Definition Display Context not found.');
        }

        if ($display_context->delete()) {
            $this->flashMessage('success', 'Display Context removed.');
        } else {
            $this->flashMessage('error', 'Cannot delete Display Context.');
        }

        $this->redirect(array('/Admin/worklist/definitionDisplayContexts/'.$display_context->worklist_definition_id));
    }

    public function actionPresetPathways()
    {
        $pathway_types = PathwayType::model()->findAll('is_preset = 1');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
        $worklist_js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist_admin.js', true);
        Yii::app()->clientScript->registerScriptFile($worklist_js, ClientScript::POS_END);

        $picker_setup = $this->setupPicker();
        $path_step_type_ids = json_encode($this->getPathwayStepTypesRequirePicker());
        $this->render('preset_pathways', array(
            'pathway_types' => $pathway_types,
            'picker_setup' => $picker_setup,
            'path_step_type_ids' => $path_step_type_ids,
            'path_steps' => PathwayStepType::getPathTypes(),
            'standard_steps' => PathwayStepType::getStandardTypes(),
            'custom_steps' => PathwayStepType::getCustomTypes($this->checkAccess('admin')),
        ));
    }

    public function actionAddPathwayPreset()
    {
        $pathway_type = new PathwayType();
        if (isset($_POST['PathwayType'])) {
            $pathway_type->attributes = $_POST['PathwayType'];
            if ($pathway_type->save()) {
                $this->redirect('/Admin/worklist/presetPathways');
            }
            $errors = $pathway_type->getErrors();
        }
        $this->render('edit_pathway_type', array(
            'pathway_type' => $pathway_type,
            'errors' => @$errors,
        ));
    }

    /**
     * @throws CHttpException
     */
    public function actionEditPathwayPreset($id)
    {
        $pathway_type = PathwayType::model()->findByPk($id);
        if ($pathway_type) {
            if (isset($_POST['PathwayType'])) {
                $pathway_type->attributes = $_POST['PathwayType'];
                if ($pathway_type->save()) {
                    $this->redirect('/Admin/worklist/presetPathways');
                }
                $errors = $pathway_type->getErrors();
            }
            $this->render('edit_pathway_type', array(
                'pathway_type' => $pathway_type,
                'errors' => @$errors,
            ));
        } else {
            throw new CHttpException(404, 'Unable to retrieve pathway preset for editing.');
        }
    }

    /**
     * @throws Exception
     */
    public function actionDeactivatePathwayPresets()
    {
        $ids = Yii::app()->request->getPost('pathway');

        $pathway_types = PathwayType::model()->findAllByPk($ids);

        $transaction = Yii::app()->db->beginTransaction();

        try {
            foreach ($pathway_types as $pathway_type) {
                $pathway_type->active = false;
                $pathway_type->save();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $this->redirect('/Admin/worklist/presetPathways');
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionDuplicatePathwayPreset($id)
    {
        $source_pathway_type = PathwayType::model()->findByPk($id);

        if ($source_pathway_type) {
            $pathway_type = new PathwayType();
            $pathway_type->attributes = $source_pathway_type->attributes;
            if (isset($_POST['PathwayType'])) {
                $pathway_type->attributes = $_POST['PathwayType'];
                if ($pathway_type->save()) {
                    $pathway_type->refresh();
                    if ($source_pathway_type->duplicatePathwayTypeSteps($pathway_type->id)) {
                        $this->redirect('/Admin/worklist/presetPathways');
                    }
                }
                $errors = $pathway_type->getErrors();
            }
            $this->render('edit_pathway_type', array(
                'source_pathway_type' => $source_pathway_type,
                'pathway_type' => $pathway_type,
                'errors' => @$errors,
            ));
        } else {
            throw new CHttpException(404, 'Unable to retrieve pathway preset for duplication.');
        }
    }

    /**
     * @throws Exception
     */
    public function actionAddStepToPathway()
    {
        $id = Yii::app()->request->getPost('id');
        $pathway_id = Yii::app()->request->getPost('visit_id');
        $position = Yii::app()->request->getPost('position');
        $step_data = Yii::app()->request->getPost('step_data') ?: array();

        $step = PathwayStepType::model()->findByPk($id);
        // priority for firm_id: user input > template > current firm id
        $step_data['firm_id'] = $step_data['firm_id'] ?? $step->getState('firm_id') ?? Yii::app()->session['selected_firm_id'];
        // if the template has subspecialty_id, then setup for the step
        if($step->getState('subspecialty_id')){
            $step_data['subspecialty_id'] = $step->getState('subspecialty_id');
        }
        $new_step = null;
        if ($step) {
            $new_step = $step->createNewStepForPathwayType($pathway_id, $step_data, (int)$position);
        }

        if ($new_step) {
            $pathway = PathwayType::model()->findByPk($pathway_id);
            $this->renderJSON(
                [
                    'step_html' => $this->renderPartial(
                        '_clinical_pathway_admin',
                        ['pathway_type' => $pathway],
                        true
                    )
                ]
            );
        }
        throw new CHttpException(500, 'Unable to add step to pathway.');
    }

    /**
     * @param $term
     */
    public function actionGetAssignees($term)
    {
        $users = User::model()->with('contact')->findAll(
            'contact.first_name LIKE CONCAT(\'%\', :term, \'%\')',
            array(':term' => $term)
        );
        $this->renderJSON(
            array_map(
                static function ($item) {
                    return array(
                        'id' => $item->id,
                        'label' => $item->getFullName(),
                    );
                },
                $users
            )
        );
    }

    public function actionAddStepInstitutionMapping()
    {
        $ids = Yii::app()->request->getPost('select');
        $instances = PathwayStepType::model()->findAllByPk($ids);
        $institution_id = Institution::model()->getCurrent()->id;
        $errors = array();
        $status = 1;

        /**
         * @var $instances MappedReferenceData[]|PathwayStepType[]
         */
        foreach ($instances as $instance) {
            if (!$instance->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                $errors[] = $instance->getErrors();
            }
        }

        if (!empty($errors)) {
            $status = 0;
        }
        $this->redirect('/Admin/worklist/customPathSteps');
    }

    public function actionDeleteStepInstitutionMapping()
    {
        $ids = $_POST['select'];

        $instances = PathwayStepType::model()->findAllByPk($ids);
        $institution_id = Institution::model()->getCurrent()->id;
        $errors = array();
        $status = 1;

        /**
         * @var $instances MappedReferenceData[]|PathwayStepType[]
         */
        foreach ($instances as $instance) {
            if (!$instance->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                $errors[] = $instance->getErrors();
            }
        }

        if (!empty($errors)) {
            $status = 0;
        }

        $this->redirect('/Admin/worklist/customPathSteps');
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionAssignUserToPathway()
    {
        $id = Yii::app()->request->getPost('user_id');
        $pathway_id = Yii::app()->request->getPost('target_pathway_id');
        $pathway_type = PathwayType::model()->findByPk($pathway_id);

        if ($pathway_type) {
            $pathway_type->owner_id = $id;
            $pathway_type->save();
            $pathway_type->refresh();
            $this->renderJSON(array('id' => $id, 'initials' => $pathway_type->owner->getInitials()));
        }
        throw new CHttpException(404, 'Unable to retrieve pathway');
    }

    public function actionGetPresetDrugs($id)
    {
        $preset = OphDrPGDPSD_PGDPSD::model()->findByPk($id);
        $laterality = Yii::app()->request->getQuery('laterality');

        if ($preset) {
            $json = array_map(
                static function ($medication) use ($laterality) {
                    return array(
                        'id' => $medication->id,
                        'drug_name' => $medication->medication->preferred_term,
                        'dose' => $medication->dose . ' ' . $medication->dose_unit_term,
                        'route' => $medication->route->has_laterality ? false : $medication->route->term,
                        'laterality' => (bool)$medication->route->has_laterality,
                        'right_eye' => $laterality && ($laterality & MedicationLaterality::RIGHT),
                        'left_eye' => $laterality && ($laterality & MedicationLaterality::LEFT),
                    );
                },
                $preset->assigned_meds
            );
            $this->renderJSON($json);
        }
    }

    /**
     * @param $partial
     * @param $pathstep_id
     * @param $patient_id
     * @throws CException
     * @throws CHttpException
     */
    public function actionGetPathStep($partial, $pathstep_type_id)
    {
        $step = PathwayTypeStep::model()->findByPk($pathstep_type_id);

        if ($step) {
            $view_file = $step->step_type->widget_view ?? 'generic_step';
            $dom = $this->renderPartial(
                '//worklist/steps/' . $view_file,
                array(
                    'step' => $step,
                    'partial' => $partial
                ),
                true
            );
            $this->renderJSON($dom);
        }
    }

    /**
     * @throws CDbException
     */
    public function actionDeleteStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $step = PathwayTypeStep::model()->findByPk($step_id);
        if ($step) {
            $step->delete();
            echo '1';
        }
    }

    /**
     * @throws CHttpException
     */
    public function actionReorderStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $direction = Yii::app()->request->getPost('direction');
        $step = PathwayTypeStep::model()->findByPk($step_id);
        $altered_steps = array();

        if ($step) {
            $old_order = $step->queue_order;
            $new_order = $direction === 'left' ? $old_order - 1 : $old_order + 1;

            // As we're only moving one step, we should only have to reorder at most a single step.
            $step_to_reorder = PathwayTypeStep::model()->find(
                "pathway_type_id = :pathway_id AND id != :id AND queue_order = :order",
                [
                    'pathway_id' => $step->pathway_type_id,
                    ':id' => $step->id,
                    ':order' => $new_order
                ]
            );

            if ($step_to_reorder) {
                $step_to_reorder->queue_order = $old_order;
                $step_to_reorder->save();
                $step_to_reorder->refresh();
                $altered_steps[$step_to_reorder->id] = $step_to_reorder;
            }
            $step->queue_order = $new_order;
            if (!$step->save()) {
                throw new CHttpException('Unable to reorder step.');
            }
            $step->refresh();
            $altered_steps[$step->id] = $step;
        }

        $this->renderJSON($altered_steps);
    }
}
