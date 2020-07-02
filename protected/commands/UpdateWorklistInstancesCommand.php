<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class UpdateWorklistInstancesCommand extends CConsoleCommand
{
    private $transaction;
    private $all_worklist_attributes = [
        'name',
        'description',
        'mappings'
    ];

    private $special_attributes = [
        'mappings'
    ];

    public function getName()
    {
        return 'Update Worklist Instances Command.';
    }

    public function getHelp()
    {
        return "A script to update worklist instances to match the worklist definition.\n
      Optional parameters to specify 1) which worklists to update. 2) which parameters to update\n
      If no parameters are supplied then all worklists parameters for all worklists will be updated\n
      The first parameter is a comma separated string specifying which parameters to update,\n
      Options include:\n
        - all - all parameters will be updated\n
        - mappings - Mappings will be updated\n
        - name - Names will be updated\n
        - description - Descriptions will be updated\n
      The second parameter is a comma separated string specifying which worklists to update by ID,\n
      Options include:\n
        - all - All worklists will be updated\n
        - [ids] - The specified worklist definition IDs to be updated\n";
    }

    private function printErrors($message, $errors = "")
    {
        echo "$message\n";
        if (!empty($errors)) {
            echo "The following errors were found:\n";
            echo "$errors";
        }
        exit();
    }

    private function validateAttributes($raw_attribute_string)
    {
        $raw_attributes = explode(",", $raw_attribute_string);
        $errors = "";
        $attributes = [];

        if (in_array('all', $raw_attributes)) {
            $attributes = $this->all_worklist_attributes;
        } else {
            foreach ($raw_attributes as $raw_attribute) {
                if (!in_array($raw_attribute, $this->all_worklist_attributes)) {
                    $errors .= "'$raw_attribute' is not a recognised attribute, check the command help for accepted attributes.\n";
                    break;
                } else {
                    $attributes[] = $raw_attribute;
                }
            }
            if (!empty($errors)) {
                $this->printErrors("Attribute parsing failed", $errors);
            }
        }
        return $attributes;
    }

    private function validateWorklistDefinitions($raw_definitions_string)
    {
        $raw_definitions = explode(",", $raw_definitions_string);
        if (in_array('all', $raw_definitions)) {
            return [ 'all' ];
        } else {
            $errors = "";
            foreach ($raw_definitions as $id) {
                if (!is_numeric($id)) {
                    echo "ERROR\n";
                    $errors .= "'$id' is not a valid ID\n";
                }
            };
            if (!empty($errors)) {
                $this->printErrors("ID parsing failed", $errors);
            }
            return $raw_definitions;
        }
    }

    private function handleSpecialAttribute($worklist, $definition, $attribute)
    {
        switch ($attribute) {
            case "mappings":
                $definition_mappings = $definition->mappings;
                $worklist_mappings = $worklist->mapping_attributes;
                $missing_mappings = array_diff(
                    array_map(function ($mapping) {
                        return $mapping->key;
                    }, $definition_mappings),
                    array_map(function ($mapping) {
                        return $mapping->name;
                    }, $worklist_mappings)
                );
                foreach ($missing_mappings as $mapping_name) {
                    $new_mapping = new WorklistAttribute();
                    $new_mapping->name = $mapping_name;
                    $new_mapping->worklist_id = $worklist->id;
                    if (!$new_mapping->save()) {
                        $this->transaction->rollback();
                        $this->printErrors("There was an error saving the mappings");
                    }
                }
                break;
        }
    }

    public function run($args)
    {
        $worklist_attributes = [];
        $worklist_definition_ids = [];

        if (empty($args)) {
            $worklist_attributes = $this->all_worklist_attributes;
            $worklist_definition_ids = [ 'all' ];
        } else if (count($args) > 2) {
            echo "Update Worklist Instances does not accept more than 2 arguments\nrefer to the command help\n";
            return false;
        } else {
            $worklist_attributes = $this->validateAttributes($args[0]);
            $worklist_definition_ids = isset($args[1])
                ? $this->validateWorklistDefinitions($args[1])
                : [ 'all' ];
        }

        $worklist_definitions = in_array('all', $worklist_definition_ids)
            ? WorklistDefinition::model()->findAll()
            : WorklistDefinition::model()->findAllByPk($worklist_definition_ids);

        $this->transaction = Yii::app()->db->beginTransaction();

        foreach ($worklist_definitions as $worklist_definition) {
            $worklists = $worklist_definition->worklists;
            foreach ($worklists as $worklist) {
                foreach ($worklist_attributes as $attribute) {
                    if (in_array($attribute, $this->special_attributes)) {
                        $this->handleSpecialAttribute($worklist, $worklist_definition, $attribute);
                    } else {
                        $worklist->$attribute = $worklist_definition->$attribute;
                    }
                }
                if (!$worklist->save()) {
                    $this->transaction->rollback();
                    $this->printErrors("There was an error saving the worklists");
                }
            }
        }
        $this->transaction->commit();
        echo "[SUCCESS] " . count($worklist_definitions) . " Worklist(s) were updated.\n";
        return true;
    }
}
