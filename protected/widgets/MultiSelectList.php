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
            $this->options = array_merge ( $firstval, $lasttval);
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
            if (isset($_POST[$this->field]) && is_array($_POST[$this->field])) {
                foreach ($_POST[$this->field] as $id) {
                    $this->selected_ids[] = $id;
                    unset($this->filtered_options[$id]);
                }
            }
            // when the field being used contains the appropriate square brackets for defining the associative array, the original (above)
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
}
