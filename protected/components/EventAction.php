<?php

/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class EventAction
{
    public $name;
    public $type;
    public $label;
    public $href;
    public $htmlOptions;
    public $options = array(
        'level' => 'primary',
        'disabled' => false,
    );

    public static function button($label, $name, $options = null, $htmlOptions = null)
    {
        $action = new self($label, 'button', $options, $htmlOptions);
        $action->htmlOptions['name'] = $name;
        if (!isset($action->htmlOptions['type'])) {
            $action->htmlOptions['type'] = 'submit';
        }
        if (!isset($action->htmlOptions['id'])) {
            $action->htmlOptions['id'] = 'et_' . strtolower($name);
        }

        return $action;
    }

    public static function dropdownToButton( $label, $name, $selectOptions = [], $options = null, $htmlOptions = null )
    {
        $action = new self($label, 'dropdown', $options, $htmlOptions);

        $action->selectOptions = $selectOptions;
        $action->htmlOptions['name'] = $name;
        if (!isset($action->htmlOptions['class'])) {
            $action->htmlOptions['class'] = 'primary';
        }
        if (!isset($action->htmlOptions['id'])) {
            $action->htmlOptions['id'] = 'et_'.strtolower($name);
        }
        if (!isset($action->htmlOptions['empty'])) {
            $action->htmlOptions['empty'] = 'Please select';
        }

        return $action;

    }

    public static function printButton($label = 'Print this event', $name = 'print', $options = array(), $htmlOptions = array())
    {
        $options = array_merge(array('level' => 'print'), $options);
        $htmlOptions = array_merge(array('class' => 'button small'), $htmlOptions);

        return static::button($label, $name, $options, $htmlOptions);
    }

    public static function link($label, $href = '#', $options = null, $htmlOptions = null)
    {
        $action = new self($label, 'link', $options, $htmlOptions);
        $action->href = $href;

        return $action;
    }

    public function __construct($label, $type, $options = null, $htmlOptions = null)
    {
        $this->label = $label;
        $this->type = $type;
        $this->htmlOptions = $htmlOptions;
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = '';
        }
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $this->options[$key] = $value;
            }
        }
    }

    public function toHtml()
    {
        $this->htmlOptions['class'] .= ' button header-tab';
        $label = CHtml::encode($this->label);

        if ($this->options['level'] === 'save') {
            $this->htmlOptions['class'] .= ' green';
        }
        if ($this->options['level'] === 'delete') {
            $label = '';
            $this->htmlOptions['class'] = 'button trash header-icon-btn icon';
            $this->htmlOptions['id'] = 'js-delete-event-btn';
        }
        if ($this->options['level'] === 'cancel') {
            $this->htmlOptions['class'] .= ' red';
            $this->htmlOptions['id'] = 'et_cancel';
        }
        if ($this->options['level'] === 'print') {
            $label = '<i class="oe-i print"></i>';
            $this->htmlOptions['class'] .= ' icon';
        }
        if ($this->options['level'] === 'read') {
            $label .= '<i class="oe-i save small pad-left selected"></i>';
            $this->htmlOptions['class'] .= ' icon';
        }

        if ($this->options['disabled']) {
            $this->htmlOptions['class'] .= ' disabled';
            $this->htmlOptions['disabled'] = 'disabled';
        }

        if ($this->type === 'button') {
            return CHtml::htmlButton($label, $this->htmlOptions);
        } elseif ($this->type === 'link') {
            return CHtml::link($label, $this->href, $this->htmlOptions);
        }
    }

    public static function printDropDownButtonAsHtml($print_actions)
    {
        $print_button_html = '';
        foreach ($print_actions as $action) {
           $action->htmlOptions['class'] .= ' header-tab';
           $action->label = CHtml::encode($action->label);
           if ($action->options['disabled']) {
                $action->htmlOptions['class'] .= ' disabled';
                $action->htmlOptions['disabled'] = 'disabled';
           }

            $print_button_html .= '<li>'. CHtml::htmlButton($action->label, $action->htmlOptions) .'</li>';
        }

        return '<div class="header-dropdown" id="js-header-print-dropdown">
                    <div class="header-icon-btn print dropdown" id="js-header-print-dropdown-btn"></div>
                    <div class="dropdown-btns" id="js-header-print-subnav" style="display: none">
                        <ul>'.$print_button_html.'</ul>
                    </div>
                </div>';
    }
}

