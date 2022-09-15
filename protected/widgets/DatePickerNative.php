<?php
/**
 * (C) Apperta Foundation, 2022
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
 *
 * */

/**
 * DatePickerNative
 *
 * This Widget replaces archived DatePicker, replicating the same behaviour using native browser date input behaviour and
 * removing the need for external JS libraries to provide calendar / date picker UI. Details on this decision are provided
 * in ADR 0004
 *
 * @link /docs/adr/0004-standardise-on-native-datepicker-functionality-with-a-consistent-widget.md
 */
class DatePickerNative extends BaseFieldWidget
{
    public $name;
    public $options = array();

    protected $date_input_format = 'Y-m-d';

    /**
     * Run the widget
     */
    public function run()
    {
        if ($this->isPostRequest()) {
            if ($this->fieldHasBeenPosted()) {
                $this->value = $this->getValueFromPostRequest();
            } else {
                $this->setValueFromDefault();
            }
        } else {
            if ($this->elementHasField()) {
                $this->value = $this->getValueFromElement();
            } else {
                $this->setValueFromDefault();
            }
        }

        parent::run();
    }

    public function getRequest()
    {
        return $this->getController()->getApp()->request;
    }

    public function getInputName()
    {
        return $this->name;
    }

    public function getInputId()
    {
        return $this->getHtmlOption('id') ?? CHtml::modelName($this->element) . '_' . $this->field . '_0';
    }

    public function getMaxDate()
    {
        return $this->formatDateValue($this->options['maxDate'] ?? null);
    }

    public function getMinDate()
    {
        return $this->formatDateValue($this->options['minDate'] ?? null);
    }

    public function getValue()
    {
        return $this->formatDateValue($this->value ?? null);
    }

    public function getHtmlOption(string $key)
    {
        return array_key_exists($key, $this->htmlOptions) && $this->htmlOptions[$key] ? $this->htmlOptions[$key] : null;
    }

    public function getLabel()
    {
        return $this->element->getAttributeLabel($this->field);
    }

    public function getLayoutColumns(?string $key)
    {
        if (!$key) {
            return $this->layoutColumns;
        }

        return $this->layoutColumns[$key] ?? "";
    }

    protected function isPostRequest()
    {
        return $this->getRequest()->isPostRequest;
    }

    protected function fieldHasBeenPosted()
    {
        if ($this->name) {
            // true if non null value, or null explicitly returned from underlying $_POST in the Request
            return $this->getRequest()->getPost($this->name) !== null;
        }

        return isset($this->getElementFieldsFromPost()[$this->field]);
    }

    protected function getValueFromPostRequest()
    {
        if ($this->name && $this->getRequest()->getPost($this->name)) {
            return $this->getRequest()->getPost($this->name);
        } elseif (isset($this->getElementFieldsFromPost()[$this->field])) {
            return $this->getElementFieldsFromPost()[$this->field];
        }

        return null;
    }

    protected function elementHasField()
    {
        return $this->getValueFromElement() ?? false;
    }

    protected function getValueFromElement()
    {
        return $this->formatDateValue($this->element->{$this->field});
    }

    protected function formatDateValue(?string $value)
    {
        if (!$value) {
            return null;
        }

        if ($value === 'today') {
            return date($this->date_input_format);
        }

        return date($this->date_input_format, strtotime($value));
    }

    protected function setValueFromDefault()
    {
        if ($this->htmlOptions['null'] ?? false) {
            $this->value = null;
        } else {
            $this->value = "today";
        }
    }

    protected function getElementFieldsFromPost(): array
    {
        return $this->getRequest()->getPost(get_class($this->element)) ?? [];
    }
}
