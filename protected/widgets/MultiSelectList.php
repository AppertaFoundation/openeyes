<?php

/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class MultiSelectList extends BaseFieldWidget
{
    public $default_options = array();
    public $filtered_options = array();
    public $relation;
    public $selected_ids = array();
    public $relation_id_field;
    public $options;
    public $inline = false;
    public $showRemoveAllLink = false;
    public $sorted = false;
    public $noSelectionsMessage;
    public $sortable;
    public $auto_data_order = false;
    public $through = false;
    public $link = '';

    public function init()
    {
        // only use array 1 if we get a multidemension array (for example when passing in active, you still want the allocated entries to display but give the active ones the option to be selected)
        $lasttval = end($this->options);
        $firstval = reset($this->options);
        if (isset($firstval)&& is_array($firstval)) {
            $safe_options = $this->options;
            if (isset($safe_options[1])) {
                $this->filtered_options = $safe_options[1];
            }
            $this->options = array_merge($firstval, $lasttval);
        } else {
            $this->filtered_options = $this->options;
        }

        if (empty($_POST)) {
            if ($this->element && $this->element->{$this->relation}) {
                foreach ($this->element->{$this->relation} as $item) {
                    $this->selected_ids[] = $item->{$this->relation_id_field};
                    unset($this->filtered_options[$item->{$this->relation_id_field}]);
                }
            } elseif (!$this->element || !$this->element->id) {
                if (is_array($this->default_options)) {
                    $this->selected_ids = $this->default_options;
                    foreach ($this->default_options as $id) {
                        unset($this->filtered_options[$id]);
                    }
                }
            }
        } else {
            // the field name might be an URL encoded array (in the case of the institution levels)
            $fieldPath = strrpos($this->field, '[')
                ? explode('[', str_replace(']', '', $this->field))
                : $this->field;

            // if field name (fieldPath) is an array then we must navigate the
            // post object down the field path ensuring the data is set
            if (is_array($fieldPath) && count($fieldPath) > 0 ) {
                $data = $this->navigateFieldPathInPost($fieldPath);
                if ($data && is_array($data)) {
                    foreach ($data as $id) {
                        $this->selected_ids[] = $id;
                        unset($this->filtered_options[$id]);
                    }
                }
            }
            // if field name not an array then parse data from $_POST
            elseif (!is_array($fieldPath) && isset($_POST[$this->field]) && is_array($_POST[$this->field])) {
                foreach ($_POST[$this->field] as $id) {
                    $this->selected_ids[] = $id;
                    unset($this->filtered_options[$id]);
                }
            }
            // approach for retrieving the posted value does not work. The following (more standard) approach does
            elseif (isset($_POST[CHtml::modelName($this->element)][$this->relation]) && is_array($_POST[CHtml::modelName($this->element)][$this->relation])) {
                foreach ($_POST[CHtml::modelName($this->element)][$this->relation] as $id) {
                    $this->selected_ids[] = $id;
                    unset($this->filtered_options[$id]);
                }
            }
        }

        //NOTE: don't call parent init as the field behaviour doesn't work for the relations attribute with models
    }

    private function navigateFieldPathInPost($fieldPath)
    {
        $data = $_POST;
        $success = true;
        for ($i = 0; $i<count($fieldPath); $i++) {
            if (!array_key_exists($fieldPath[$i], $data)) {
                // field path doesn't resolve to good data in post object
                $success = false;
                return false;
            }

            // narrowing data down to required elements
            $data = $data[$fieldPath[$i]];
        }

        if ($success && is_array($data)) {
            return $data;
        }

        return false;
    }
}
