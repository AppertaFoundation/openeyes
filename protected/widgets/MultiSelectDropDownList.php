<?php
/**
 * (C) OpenEyes Foundation, 2018
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

class MultiSelectDropDownList extends BaseCWidget
{
    /**
     * @var string the template to be used to control the layout of various sections in the view.
     * These tokens are recognized: {DropDown}, {List}, {Label}.
     */
    public $template = "
    <td class='fade'>{Label}</td>
    <td class='js-multiselect-dropdown-wrapper'>
      {DropDown}
      <div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div>
    </td>";

    public $options = [];

    private $default_options = [
        'dropDown' => [
            'name' => 'multiSelectDropdownList',
            'data' => null,
            'htmlOptions' => ['class' => 'cols-11']
        ]
    ];

    /**
     * Renders the main content of the view.
     * The content is divided into sections, such as summary, items, pager.
     * Each section is rendered by a method named as "renderXyz", where "Xyz" is the section name.
     * The rendering results will replace the corresponding placeholders in {@link template}.
     */
    public function render($view, $data = null, $return = false)
    {
        ob_start();
        echo preg_replace_callback("/{(\w+)}/", array($this, 'renderSection'), $this->template);
        ob_end_flush();
    }

    public function renderLabel()
    {
        echo isset($this->options['label']) ? $this->options['label'] : null;
    }

    public function renderDropDown()
    {
        $name = isset($this->options['dropDown']['name']) ? $this->options['dropDown']['name'] : null;
        $data = isset($this->options['dropDown']['data']) ? $this->options['dropDown']['data'] : null;

        $remove = isset($this->options['dropDown']['selectedItems']) ? $this->options['dropDown']['selectedItems'] : [];
        $data = array_diff_key($data, array_flip($remove));

        $doprdown_html_options = isset($this->options['dropDown']['htmlOptions']) ?
            $this->options['dropDown']['htmlOptions'] : [];

        $html_options = array_merge($this->default_options['dropDown']['htmlOptions'], $doprdown_html_options);
        //selected values are displayed in an ul-li list so the dropdown doesn't need a selected value
        $select = null;
        echo \CHtml::dropDownList($name, $select, $data, $html_options);
    }

    public function renderList()
    {
        $items = isset($this->options['dropDown']['selectedItems']) ? $this->options['dropDown']['selectedItems'] : [];
        echo \CHtml::openTag('ul', [
            'class' => 'oe-multi-select inline',
            'style' => (!$items ? 'display:none' : ''),
            'data-inputname' => $this->options['dropDown']['selectedItemsInputName']]);

        foreach ($items as $value) {
            echo \CHtml::openTag('li');
            echo isset($this->options['dropDown']['data'][$value]) ? $this->options['dropDown']['data'][$value] : '';
            //acting strange: \CHtml::tag('i', ['encode' => false, 'class' =>'oe-i remove-circle small-icon pad-left']);
            echo '<i class="oe-i remove-circle small-icon pad-left"></i>';
            echo \CHtml::hiddenField($this->options['dropDown']['selectedItemsInputName'], $value);

            echo \CHtml::closeTag('li') . " ";
        }
        echo \CHtml::closeTag('ul');
    }

    /**
     * Renders a section.
     * This method is invoked by {@link render} for every placeholder found in {@link template}.
     * It should return the rendering result that would replace the placeholder.
     * @param array $matches the matches, where $matches[0] represents the whole placeholder,
     * while $matches[1] contains the name of the matched placeholder.
     * @return string the rendering result of the section
     */
    protected function renderSection($matches)
    {
        $method = 'render' . $matches[1];
        if (method_exists($this, $method)) {
            $this->$method();
            $html = ob_get_contents();
            ob_clean();

            return $html;
        } else {
            return $matches[0];
        }
    }
}
