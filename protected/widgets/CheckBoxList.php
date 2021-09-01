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
class CheckBoxList extends SelectionWidget
{
    public $selected_items;
    public $maxwidth;
    public $no_element = false;
    public $label_above = false;
    public $field_value = false;

    public function init()
    {
        parent::init();

        if ( \Yii::app()->request->isPostRequest ) {
            $this->selected_items = \Yii::app()->request->getPost($this->name, $this->selected_items);
        }

        if ($this->selected_items && !is_array($this->selected_items) && isset($this->element->{$this->selected_items}) ) {
            $selected_items = $this->element->{$this->selected_items};

            $this->selected_items = array();

            foreach ($selected_items as $selected_item) {
                $this->selected_items[] = $selected_item->id;
            }
        } elseif ( !is_array($this->selected_items)  ) {
            // End of the day $this->selected_items must be an array
            // here $this->selected_items is neither an array nor a relation of the element, -> means we have no selected data

            $this->selected_items = array();
        }
    }
}
