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
    public $group = 'Worklists';

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
            if ($this->manager->updateWorklistDefinitionMapping($mapping,
                $_POST['WorklistDefinitionMapping']['key'],
                $_POST['WorklistDefinitionMapping']['valuelist'],
                $_POST['WorklistDefinitionMapping']['willdisplay'])) {
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
            if ($this->manager->updateWorklistDefinitionMapping($mapping,
                $_POST['WorklistDefinitionMapping']['key'],
                $_POST['WorklistDefinitionMapping']['valuelist'],
                $_POST['WorklistDefinitionMapping']['willdisplay'])) {
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
}
