<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class BaseEventTypeCActiveForm extends FormLayout
{
	public function dropDownList($model, $field, $data, $htmlOptions=array(), $hidden=false, $layoutColumns=array())
	{
		$this->widget('application.widgets.DropDownList', array(
			'element' => $model,
			'field' => $field,
			'data' => $data,
			'htmlOptions' => $htmlOptions,
			'hidden' => $hidden,
			'layoutColumns' => $layoutColumns
		));
	}

	public function dropDownListRow($model, $fields, $datas, $htmlOptions=array(), $layoutColumns=array())
	{
		if (!isset($layoutColumns['field'])) {
			$layoutColumns['field'] = 12;
		}
		$this->widget('application.widgets.DropDownListRow', array(
			'element' => $model,
			'fields' => $fields,
			'datas' => $datas,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function dropDownListNoPost($id, $data, $selected_value, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.DropDownListNoPost', array(
			'id' => $id,
			'data' => $data,
			'selected_value' => $selected_value,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function radioButtons($element, $field, $table=null, $selected_item=null, $maxwidth=false, $hidden=false, $no_element=false, $label_above=false, $htmlOptions=array(), $layoutColumns=array())
	{
		$data = $element->getFormOptions($table);
		$this->widget('application.widgets.RadioButtonList', array(
			'element' => $element,
			'name' => get_class($element)."[$field]",
			'field' => $field,
			'data' => $data,
			'selected_item' => $selected_item,
			'maxwidth' => $maxwidth,
			'hidden' => $hidden,
			'no_element' => $no_element,
			'label_above' => $label_above,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function radioBoolean($element, $field, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.RadioButtonList', array(
			'element' => $element,
			'name' => get_class($element)."[$field]",
			'field' => $field,
			'data' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'selected_item' => $element->$field,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function datePicker($element, $field, $options=array(), $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.DatePicker', array(
			'element' => $element,
			'name' => get_class($element)."[$field]",
			'field' => $field,
			'options' => $options,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function textArea($element, $field, $options=array(), $hidden=false, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.TextArea', array_merge(array(
			'element' => $element,
			'field' => $field,
			'hidden' => $hidden,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns,
		), $options));
	}

	public function textField($element, $field, $htmlOptions=array(), $links=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.TextField', array(
			'element' => $element,
			'name' => get_class($element)."[$field]",
			'field' => $field,
			'htmlOptions' => $htmlOptions,
			'links' => $links,
			'layoutColumns' => $layoutColumns
		));
	}

	public function passwordField($element, $field, $htmlOptions=array(), $layoutColumns=array())
	{
		$htmlOptions['password'] = 1;

		$this->widget('application.widgets.TextField', array(
			'element' => $element,
			'name' => get_class($element)."[$field]",
			'field' => $field,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function passwordConfirmField($element, $label, $name, $htmlOptions=array(), $layoutColumns=array())
	{
		$htmlOptions = array_merge(array(
			'label' => $label,
			'password' => 1
		), $htmlOptions);

		$this->widget('application.widgets.TextField', array(
			'element' => $element,
			'name' => $name,
			'field' => null,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function checkBox($element, $field, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.CheckBox', array(
			'element' => $element,
			'field' => $field,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function checkBoxArray($element, $labeltext, $fields, $layoutColumns=array())
	{
		$this->widget('application.widgets.CheckBoxArray', array(
			'element' => $element,
			'fields' => $fields,
			'labeltext' => $labeltext,
			'layoutColumns' => $layoutColumns
		));
	}

	public function multiSelectList($element, $field, $relation, $relation_id_field, $options, $default_options, $htmlOptions=array(), $hidden=false, $inline=false, $noSelectionsMessage=null, $showRemoveAllLink=false, $sorted=false, $layoutColumns=array())
	{
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
			'layoutColumns' => $layoutColumns
		));
	}

	public function dropDownTextSelection($element, $text_field, $options, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.DropDownTextSelection', array(
			'element' => $element,
			'field' => $text_field,
			'options' => $options,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function multiDropDownTextSelection($element, $text_field, $options, $htmlOptions, $layoutColumns=array())
	{
		$this->widget('application.widgets.MultiDropDownTextSelection', array(
			'element' => $element,
			'field' => $text_field,
			'options' => $options,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function hiddenInput($element, $field, $value=false, $htmlOptions=array(), $layoutColumns=array())
	{
		$this->widget('application.widgets.HiddenField', array(
			'element' => $element,
			'field' => $field,
			'value' => $value,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function slider($element, $field, $options, $htmlOptions=array(), $layoutColumns=array())
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

	public function sliderTable($element, $field, $data, $layoutColumns=array())
	{
		$this->widget('application.widgets.SliderTable', array(
			'element' => $element,
			'field' => $field,
			'data' => $data,
			'layoutColumns' => $layoutColumns
		));
	}

	public function hiddenField($element, $field, $htmlOptions=array(), $layoutColumns=array()) {
		$this->widget('application.widgets.HiddenField', array(
			'element' => $element,
			'field' => $field,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function formActions($buttonOptions=array(), $htmlOptions=array(), $layoutColumns=array()) {
		$this->widget('application.widgets.FormActions', array(
			'buttonOptions' => $buttonOptions,
			'htmlOptions' => $htmlOptions,
			'layoutColumns' => $layoutColumns
		));
	}

	public function errorSummary($models,$header=null,$footer=null,$htmlOptions=array()) {
		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = 'alert-box alert with-icon';
		}
		return parent::errorSummary($models,$header,$footer,$htmlOptions);
	}
}
