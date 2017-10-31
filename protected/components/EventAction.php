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
            $action->htmlOptions['id'] = 'et_'.strtolower($name);
        }

        return $action;
    }

    public static function printButton($label = 'Print', $name = 'print', $options = array(), $htmlOptions = array())
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
        $this->htmlOptions['class'] .= ' '.$this->options['level'];
        $this->htmlOptions['class'] .= ' event-action';
        $label = CHtml::encode($this->label);

        if ($this->options['level'] === 'save') {
            $this->htmlOptions['class'] .= ' button secondary small';
        }
        if ($this->options['level'] === 'delete') {
            $content = '<span class="icon-button-small-trash-can"></span>';
            $content .= '<span class="hide-offscreen">'.$label.'</span>';
            $label = $content;
            $this->htmlOptions['class'] .= ' button button-icon small';
        }
        if ($this->options['level'] === 'cancel') {
            $this->htmlOptions['class'] .= ' button small';
        }
        if ($this->options['level'] === 'print') {
            $label = '<span class="oe-btn-icon print">' . $label . '</span>';
            $this->htmlOptions['class'] .= ' button oe-oescape-switch';
        }

        if ($this->options['disabled']) {
            $this->htmlOptions['class'] .= ' disabled';
            $this->htmlOptions['disabled'] = 'disabled';
        }

        if ($this->type == 'button') {
            return CHtml::htmlButton($label, $this->htmlOptions);
        } elseif ($this->type == 'link') {
            return CHtml::link($label, $this->href, $this->htmlOptions);
        }
    }
}
