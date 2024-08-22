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

/**
 * Base class for all elements.
 */
class BaseElement extends BaseActiveRecordVersioned
{
    /**
     * Fields which are copied by the loadFromExisting() method
     * By default these are taken from the "safe" scenario of the model rules, but
     * should be overridden for more complex requirements.
     *
     * @return array:
     */
    protected function copiedFields()
    {
        $rules = $this->rules();
        $fields = null;
        foreach ($rules as $rule) {
            if ($rule[1] == 'safe') {
                $fields = $rule[0];
                break;
            }
        }
        $fields = explode(',', $fields);
        $no_copy = array('event_id', 'id');
        foreach ($fields as $index => $field) {
            if (in_array($field, $no_copy)) {
                unset($fields[$index]);
            } else {
                $fields[$index] = trim($field);
            }
        }

        return $fields;
    }

    /**
     * Load an existing element's data into this one
     * The base implementation simply uses copiedFields(), but it may be
     * overridden to allow for more complex relationships.
     *
     * @param static $element
     */
    public function loadFromExisting($element)
    {
        foreach ($this->copiedFields() as $attribute) {
            if (isset($element->$attribute)) {
                $this->$attribute = $element->$attribute;
            }
        }
    }

    public function getFromExisting($element): array
    {
        $fields = array();
        foreach ($this->copiedFields() as $attribute) {
            if (isset($element->$attribute)) {
                $fields[$attribute] = $element->$attribute;
            }
        }
        return $fields;
    }

    /**
     * Apply data (in array format) to the element.
     *
     * @param array $data
     */
    public function applyData(array $data, $index = null): void
    {
        $model_name = CHtml::modelName($this);
        $el_data = $index !== null
                 ? ($data[$model_name][$index] ?? [])
                 : $data[$model_name];

        if ($widget_cls = $this->getWidgetClass()) {
            $widget = $this->createWidget($widget_cls, array(
                'patient' => $this->event->episode->patient,
                'element' => $this,
                'data' => $el_data,
                'mode' => \BaseEventElementWidget::$EVENT_EDIT_MODE,
            ));
            $this->widget = $widget;
        } else {
            if ((!Yii::app()->controller->hasConflict() || $this->isNewRecord)) {
                $this->attributes = Helper::convertNHS2MySQL($el_data);
                $this->applyComplexData($data, $index);
                $this->event = Yii::app()->controller->event;
                $this->event_id = Yii::app()->controller->event->id;
            }
        }

        if (Yii::app()->controller->hasConflict() && !$this->isNewRecord) {
            $this->refresh();
            return;
        }
    }

    public function getDefaults(array $context): array
    {
        $fields = array();
        if ($context['action'] == 'create') {
            $fields = $this->getDefaultFormOptions($context);
        } elseif ($context['action'] == 'update') {
            $fields = $this->getUpdateOptions($context);
        }

        $el_method = 'getElementDefaultOptions_' . Helper::getNSShortname($this);
        if (method_exists($context['controller'], $el_method)) {
            $fields = array_merge($fields, $context['controller']->$el_method($this, $context['action']));
        }

        return $fields;
    }

    public function getDefaultFormOptions(array $context): array
    {
        return array();
    }

    public function getUpdateOptions(array $context): array
    {
        return array();
    }

    /**
     *  When isDirtyWhenNewRecord returns true, an element which is a new record
     *  will have its element_dirty field set to true, which is useful e.g, for avoiding
     *  the discard empty popups dialogue box.
     *
     * @return bool
     */
    public function isDirtyWhenNewRecord(): bool
    {
        return false;
    }

    /**
     * Apply data (in array format) to the element. Override this in each element to apply complex data.
     *
     * @param array $data
     * @param index An optional index, used only for elements that permit multiple instances
     */
    protected function applyComplexData(array $data, $index): void
    {
    }

    public static function canHaveMultipleOf()
    {
        return false;
    }

    public function getTemplateIndex()
    {
        return null;
    }
}
