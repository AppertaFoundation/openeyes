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
class BaseEventTypeCActiveForm extends FormLayout
{
    public function activeWidget($className, $element, $field, $properties = array(), $captureOutput = false)
    {
        $properties['element'] = $element;
        $properties['field'] = $field;

        return parent::widget($className, $properties, $captureOutput);
    }

    public function comment($element_name, $value, $htmlOptions = array(), $hidden = false, $layoutColumns = array())
    {
        $this->widget('application.widgets.Comment', array(
            'element_name' => $element_name,
            'value' => $value,
            'htmlOptions' => $htmlOptions,
            'hidden' => $hidden,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $model
     * @param string $field
     * @param array  $data
     * @param array  $htmlOptions
     * @param bool   $hidden
     * @param array  $layoutColumns
     */
    public function dropDownList($model, $field, $data, $htmlOptions = array(), $hidden = false, $layoutColumns = array())
    {
        $this->widget('application.widgets.DropDownList', array(
            'element' => $model,
            'field' => $field,
            'data' => $data,
            'htmlOptions' => $htmlOptions,
            'hidden' => $hidden,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $model
     * @param       $fields
     * @param       $datas
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function dropDownListRow($model, $fields, $datas, $htmlOptions = array(), $layoutColumns = array())
    {
        if (!isset($layoutColumns['field'])) {
            $layoutColumns['field'] = 12;
        }
        $this->widget('application.widgets.DropDownListRow', array(
            'element' => $model,
            'fields' => $fields,
            'datas' => $datas,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $id
     * @param       $data
     * @param       $selected_value
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function dropDownListNoPost($id, $data, $selected_value, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.DropDownListNoPost', array(
            'id' => $id,
            'data' => $data,
            'selected_value' => $selected_value,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param       $data
     * @param null  $selected_item
     * @param bool  $maxwidth
     * @param bool  $hidden
     * @param bool  $no_element
     * @param bool  $label_above
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function radioButtons(
        $element,
        $field,
        $data,
        $selected_item = null,
        $maxwidth = false,
        $hidden = false,
        $no_element = false,
        $label_above = false,
        $htmlOptions = array(),
        $layoutColumns = array()
    ) {
        $this->widget('application.widgets.RadioButtonList', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'data' => $data,
            'selected_item' => $selected_item,
            'maxwidth' => $maxwidth,
            'hidden' => $hidden,
            'no_element' => $no_element,
            'label_above' => $label_above,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $data
     * @param null  $selected_items
     * @param bool  $maxwidth
     * @param bool  $hidden
     * @param bool  $no_element
     * @param bool  $label_above
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function checkBoxes(
        $element,
        $data,
        $selected_items = null,
        $label = null,
        $maxwidth = false,
        $hidden = false,
        $no_element = false,
        $label_above = false,
        $htmlOptions = array(),
        $layoutColumns = array()
    ) {
        $this->widget('application.widgets.CheckBoxList', array(
            'element' => $element,
            'name' => "{$data}",
            'data' => $data,
            'selected_items' => $selected_items,
            'label' => $label,
            'maxwidth' => $maxwidth,
            'hidden' => $hidden,
            'no_element' => $no_element,
            'label_above' => $label_above,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }



    /**
     * @param       $element
     * @param       $field
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function radioBoolean($element, $field, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.RadioButtonList', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'data' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'selected_item' => $element->$field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function radioMultiOption($element, $field, $field_value, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.RadioButtonList', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'field_value' => $field_value,
            'data' => array(
                1 => 'Yes',
                0 => 'No',
                2 => 'Unknown',

            ),
            'selected_item' => $element->$field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param array $options
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function datePicker($element, $field, $options = array(), $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.DatePicker', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'options' => $options,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $options
     * @param bool   $hidden
     * @param array  $htmlOptions
     * @param array  $layoutColumns
     */
    public function textArea($element, $field, $options = array(), $hidden = false, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.TextArea', array_merge(array(
            'element' => $element,
            'field' => $field,
            'hidden' => $hidden,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ), $options));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $htmlOptions
     * @param array  $links
     * @param array  $layoutColumns
     */
    public function textField($element, $field, $htmlOptions = array(), $links = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.TextField', array(
            'element' => $element,
            'name' => @$htmlOptions['name'] ?: CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'htmlOptions' => $htmlOptions,
            'links' => $links,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $htmlOptions
     * @param array  $layoutColumns
     */
    public function fileField($element, $field, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.FileField', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $htmlOptions
     * @param array  $layoutColumns
     */
    public function passwordField($element, $field, $htmlOptions = array(), $layoutColumns = array())
    {
        $htmlOptions['password'] = 1;

        $this->widget('application.widgets.TextField', array(
            'element' => $element,
            'name' => CHtml::modelName($element) . "[$field]",
            'field' => $field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $label
     * @param       $name
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function passwordChangeField($element, $label, $name, $htmlOptions = array(), $layoutColumns = array())
    {
        $htmlOptions = array_merge(array(
            'label' => $label,
            'password' => 1,
        ), $htmlOptions);

        $this->widget('application.widgets.TextField', array(
            'element' => $element,
            'name' => $name,
            'field' => null,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $htmlOptions
     * @param array  $layoutColumns
     */
    public function checkBox($element, $field, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.CheckBox', array(
            'element' => $element,
            'field' => $field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $labeltext
     * @param       $fields
     * @param array $layoutColumns
     */
    public function checkBoxArray($element, $labeltext, $fields, $layoutColumns = array())
    {
        $this->widget('application.widgets.CheckBoxArray', array(
            'element' => $element,
            'fields' => $fields,
            'labeltext' => $labeltext,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param       $relation
     * @param       $relation_id_field
     * @param       $options
     * @param       $default_options
     * @param array $htmlOptions
     * @param bool  $hidden
     * @param bool  $inline
     * @param null  $noSelectionsMessage
     * @param bool  $showRemoveAllLink
     * @param bool  $sorted
     * @param array $layoutColumns
     * @param null  $model
     */
    public function multiSelectListFreeText(
        $element,
        $field,
        $relation,
        $relation_id_field,
        $options,
        $default_options,
        $htmlOptions = array(),
        $hidden = false,
        $inline = false,
        $noSelectionsMessage = null,
        $showRemoveAllLink = false,
        $sorted = false,
        $layoutColumns = array(),
        $model = null
    ) {
        $this->widget('application.widgets.MultiSelectListFreeText', array(
            'element' => $element,
            'field' => $field,
            'relation' => $relation,
            'relation_id_field' => $relation_id_field,
            'options' => $options,
            'default_options' => $default_options,
            'htmlOptions' => $htmlOptions,
            'hidden' => $hidden,
            'inline' => $inline,
            'noSelectionsMessage' => $noSelectionsMessage,
            'showRemoveAllLink' => $showRemoveAllLink,
            'sorted' => $sorted,
            'layoutColumns' => $layoutColumns,
            'model' => $model,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param       $relation
     * @param       $relation_id_field
     * @param       $options
     * @param       $default_options
     * @param array $htmlOptions
     * @param bool  $hidden
     * @param bool  $inline
     * @param null  $noSelectionsMessage
     * @param bool  $showRemoveAllLink
     * @param bool  $sorted
     * @param array $layoutColumns
     * @param array $through
     */
    public function multiSelectList(
        $element,
        $field,
        $relation,
        $relation_id_field,
        $options,
        $default_options,
        $htmlOptions = array(),
        $hidden = false,
        $inline = false,
        $noSelectionsMessage = null,
        $showRemoveAllLink = false,
        $sorted = false,
        $layoutColumns = array(),
        $through = array(),
        $link = ''
    ) {
        $this->widget('application.widgets.MultiSelectList', array(
            'element' => $element,
            'field' => $field,
            'relation' => $relation,
            'relation_id_field' => $relation_id_field,
            'options' => $options,
            'default_options' => $default_options,
            'htmlOptions' => $htmlOptions,
            'hidden' => $hidden,
            'inline' => $inline,
            'noSelectionsMessage' => $noSelectionsMessage,
            'showRemoveAllLink' => $showRemoveAllLink,
            'sorted' => $sorted,
            'layoutColumns' => $layoutColumns,
            'through' => $through,
            'link' => $link,
        ));
    }

    /**
     * @param       $element
     * @param       $text_field
     * @param       $options
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function dropDownTextSelection($element, $text_field, $options, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.DropDownTextSelection', array(
            'element' => $element,
            'field' => $text_field,
            'options' => $options,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $text_field
     * @param       $options
     * @param       $htmlOptions
     * @param array $layoutColumns
     */
    public function multiDropDownTextSelection($element, $text_field, $options, $htmlOptions, $layoutColumns = array())
    {
        $this->widget('application.widgets.MultiDropDownTextSelection', array(
            'element' => $element,
            'field' => $text_field,
            'options' => $options,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param bool  $value
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function hiddenInput($element, $field, $value = false, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.HiddenField', array(
            'element' => $element,
            'field' => $field,
            'value' => $value,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param       $options
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function slider($element, $field, $options, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.Slider', array(
            'element' => $element,
            'field' => $field,
            'min' => $options['min'],
            'max' => $options['max'],
            'width' => @$options['width'],
            'step' => $options['step'],
            'force_dp' => @$options['force_dp'],
            'prefix_positive' => @$options['prefix_positive'],
            'remap_values' => @$options['remap'],
            'null' => @$options['null'],
            'append' => @$options['append'],
            'layoutColumns' => $layoutColumns,
            'painScale' => @$options['painScale'],
            'htmlOptions' => $htmlOptions,
        ));
    }

    /**
     * @param       $element
     * @param       $field
     * @param       $data
     * @param array $layoutColumns
     */
    public function sliderTable($element, $field, $data, $layoutColumns = array())
    {
        $this->widget('application.widgets.SliderTable', array(
            'element' => $element,
            'field' => $field,
            'data' => $data,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param CModel $element
     * @param string $field
     * @param array  $htmlOptions
     * @param array  $layoutColumns
     */
    public function hiddenField($element, $field, $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.HiddenField', array(
            'element' => $element,
            'field' => $field,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param array $buttonOptions
     * @param array $htmlOptions
     * @param array $layoutColumns
     */
    public function formActions($buttonOptions = array(), $htmlOptions = array(), $layoutColumns = array())
    {
        $this->widget('application.widgets.FormActions', array(
            'buttonOptions' => $buttonOptions,
            'htmlOptions' => $htmlOptions,
            'layoutColumns' => $layoutColumns,
        ));
    }

    /**
     * @param mixed $models
     * @param null  $header
     * @param null  $footer
     * @param array $htmlOptions
     *
     * @return string
     */
    public function errorSummary($models, $header = null, $footer = null, $htmlOptions = array())
    {
        if (!isset($htmlOptions['class'])) {
            $htmlOptions['class'] = 'alert-box alert with-icon';
        }

        return parent::errorSummary($models, $header, $footer, $htmlOptions);
    }

    public function TagsInput($label, $element, $field, $relation, $relation_id_field, $htmlOptions)
    {
        $this->widget('application.widgets.TagsInput', array(
            'label'=>$label,
            'element'=>$element,
            'field'=>$field,
            'relation'=>$relation,
            'relation_id_field' =>$relation_id_field,
            'htmlOptions'=>$htmlOptions
        ));
    }
}
