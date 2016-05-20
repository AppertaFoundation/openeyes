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
class WorklistAdminController extends BaseAdminController
{
    public $layout = 'admin';
    public $items_per_page = 30;

    /**
     * @var WorklistManager
     */
    private $manager;

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
    protected function flashMessage($type = 'success', $message, $id = "message")
    {
        Yii::app()->user->setFlash("{$type}.{$id}", $message);
    }

    public function actionDefinitions()
    {
        $definitions = $this->manager->getWorklistDefinitions();

        $this->render('//admin/worklists/definitions', array(
            'definitions' => $definitions,
            'errors' => @$errors
        ));
    }


    public function actionDefinition($id = null)
    {
        $definition = $this->manager->getWorklistDefinition($id);

        if (!$definition)
            throw new CHttpException(404, "Worklist definition not found");

        if (isset($_POST['WorklistDefinition'])) {
            $definition->attributes = $_POST['WorklistDefinition'];
            if (!$this->manager->saveWorklistDefinition($definition)) {
                $errors = $definition->getErrors();
            }
            else {
                $this->flashMessage('success', 'Worklist Definition saved');
                return $this->redirect(array('/worklistAdmin/definitions'));
            }
        }

        $this->render('//admin/worklists/definition', array(
            'definition' => $definition,
            'errors' => @$errors
        ));
    }

    /**
     * @param $id
     * @return null|WorklistDefinition
     * @throws CHttpException
     */
    protected function getWorklistDefinition($id)
    {
        $definition = $this->manager->getWorklistDefinition($id);

        if (!$definition)
            throw new CHttpException(404, "Worklist definition not found");

        return $definition;
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionDefinitionGenerate($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $new_count = $this->manager->generateAutomaticWorklists($definition);

        if ($new_count === false) {
            OELog::log(print_r($this->manager->getErrors(), true));
            $this->flashMessage('error', "There was a problem generating worklists for {$definition->name}.");
        }
        else {
            $this->flashMessage("success", "Worklist Generation Completed for {$definition->name}. {$new_count} new instances created.");
        }

        $this->redirect(array('/worklistAdmin/definitions'));
    }

    /**
     * List the WorklistDefinitionMappings for the given id
     *
     * @param $id
     * @throws CHttpException
     */
    public function actionDefinitionMappings($id)
    {
        $definition = $this->getWorklistDefinition($id);

        $this->render('//admin/worklists/definition_mappings', array(
            'definition' => $definition,
        ));
    }

    /**
     * Create a new WorkflowDefinitionMapping for the given WorkflowDefinition
     *
     * @param $id
     * @throws CHttpException
     */
    public function actionAddDefinitionMapping($id)
    {
        $definition = $this->getWorklistDefinition($id);

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
                $this->redirect(array('/worklistAdmin/definitionMappings/' . $id));
            }
            else {
                $errors = $mapping->getErrors();
                $errors[] = $this->manager->getErrors();
            }
        }

        $this->render('//admin/worklists/definition_mapping', array(
            'mapping' => $mapping,
            'errors' => @$errors
        ));

    }

    /**
     * Update a WorkflowDefinitionMapping
     *
     * @param $id
     * @throws CHttpException
     */
    public function actionUpdateDefinitionMapping($id)
    {
        if (!$mapping = WorklistDefinitionMapping::model()->findByPk($id))
            throw new CHttpException(404, "Worklist Definition Mapping not found.");

        if (isset($_POST['WorklistDefinitionMapping'])) {
            $mapping->attributes = $_POST['WorklistDefinitionMapping'];
            if ($this->manager->updateWorklistDefinitionMapping($mapping,
                $_POST['WorklistDefinitionMapping']['key'],
                $_POST['WorklistDefinitionMapping']['valuelist'],
                $_POST['WorklistDefinitionMapping']['willdisplay'])) {

                $this->flashMessage('success', 'Worklist Definition Mapping saved.');
                $this->redirect(array('/worklistAdmin/definitionMappings/' . $mapping->worklist_definition_id));
            }
            else {
                $errors = $mapping->getErrors();
                $errors[] = $this->manager->getErrors();
            }
        }

        $this->render('//admin/worklists/definition_mapping', array(
            'mapping' => $mapping,
            'errors' => @$errors
        ));
    }

    public function actionDeleteDefinitionMapping($id)
    {
        if (!$mapping = WorklistDefinitionMapping::model()->findByPk($id))
            throw new CHttpException(404, "Worklist Definition Mapping not found.");

        if ($mapping->delete()) {
            $this->flashMessage('success', "Mapping removed.");
        }
        else {
            $this->flashMessage('error', "Cannot delete mapping.");
        }

        $this->redirect(array('/worklistAdmin/definitionMappings/' . $mapping->worklist_definition_id));
    }

    /**
     * Update the Worklist Definition Mapping Attribute order
     */
    public function actionDefinitionMappingSort($id)
    {
        $definition = $this->getWorklistDefinition($id);
        $mapping_ids = @$_POST['item_ids'] ? : array();

        if (count($mapping_ids)) {
            if (!$this->manager->setWorklistDefinitionMappingDisplayOrder($definition, $mapping_ids)) {
                OELog::log(print_r($this->manager->getErrors(), true));
                $this->flashMessage('error', "Could not reorder mappings");
            } else {
                $this->flashMessage('success', 'Mappings re-ordered');
            }
        }

        $this->redirect('/worklistAdmin/definitionMappings/' . $id);
    }


}