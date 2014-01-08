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

/**
 * A class that all clinical elements should extend from.
 */
class BaseEventTypeElement extends BaseElement
{
	public $firm;
	public $userId;
	public $patientId;

	protected $_element_type;
	protected $_children;

	/**
	 * Get the ElementType for this element
	 *
	 * @return ElementType
	 */
	public function getElementType()
	{
		if (!$this->_element_type) {

			$this->_element_type = ElementType::model()->find('class_name=?', array(get_class($this)));
		}
		return $this->_element_type;
	}

	/**
	 * Return the element type name
	 *
	 * @return string $name
	 */
	public function getElementTypeName()
	{
		return $this->getElementType()->name;
	}

	/**
	 * Can this element be copied (cloned/duplicated)
	 * Override to return true if you want an element to be copyable
	 * @return boolean
	 */
	public function canCopy()
	{
		return false;
	}

	/**
	 * Can we view the previous version of this element
	 */
	public function canViewPrevious()
	{
		return false;
	}

	/**
	 * get the child element types for this BaseEventElementType
	 *
	 * @return ElementType[]
	 */
	public function getChildElementTypes()
	{
		return ElementType::model()->findAll('parent_element_type_id = :element_type_id', array(':element_type_id' => $this->getElementType()->id));
	}
	/**
	 * set the children for this element - allows external definition of what the children should
	 * be (for workflows determined by controllers and the like.
	 *
	 * @param BaseEventTypeElement[] $children
	 */
	public function setChildren($children)
	{
		$this->_children = $children;
	}

	/**
	 * Return this elements children
	 * @return array
	 */
	public function getChildren()
	{
		if ($this->_children === null) {
			$this->_children = array();
			foreach ($this->getChildElementTypes() as $child_element_type) {
				if ($this->event_id) {
					if ($element = self::model($child_element_type->class_name)->find('event_id = ?', array($this->event_id))) {
						$this->_children[] = $element;
					}
				}
				else {
					// set the children to be based on the standard defaults - can be overridden by setting the children
					// with setChildren method outside of the element model
					if ($child_element_type->default) {
						$this->_children[] = new $child_element_type->class_name;
					}
				}
			}
		}
		return $this->_children;
	}

	/**
	 * Fields which are copied by the loadFromExisting() method
	 * By default these are taken from the "safe" scenario of the model rules, but
	 * should be overridden for more complex requirements
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
		$no_copy = array('event_id','id');
		foreach ($fields as $index => $field) {
			if (in_array($field,$no_copy)) {
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
	 * overridden to allow for more complex relationships
	 * @param BaseEventTypeElement $element
	 */
	public function loadFromExisting($element)
	{
		foreach ($this->copiedFields() as $attribute) {
			$this->$attribute = $element->$attribute;
		}
	}

	public function render($action)
	{
		$this->Controller->renderPartial();
	}

	public function getFormOptions($table)
	{
		$options = array();

		$table_exists = false;

		foreach (Yii::app()->db->createCommand("show tables;")->query() as $_table) {
			foreach ($_table as $key => $value) {
				if ("element_type_$table" == $value) {
					$table_exists = true;
					break;
				}
			}
		}

		if ($table_exists) {
			foreach (Yii::app()->db->createCommand()
					->select("$table.*")
					->from($table)
					->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
					->where("element_type_id = ".$this->getElementType()->id)
					->order("display_order asc")
					->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
		} else {
			foreach (Yii::app()->db->createCommand()
					->select("$table.*")
					->from($table)
					->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
		}

		return $options;
	}

	public function getSetting($key)
	{
		$element_type = ElementType::model()->find('class_name=?',array(get_class($this)));

		if (!$metadata = SettingMetadata::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return false;
		}

		if ($setting = SettingUser::model()->find('user_id=? and element_type_id=? and `key`=?',array(Yii::app()->session['user']->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if ($setting = SettingFirm::model()->find('firm_id=? and element_type_id=? and `key`=?',array($firm->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}


		if ($subspecialty_id = $firm->getSubspecialtyID()) {
			if ($setting = SettingSubspecialty::model()->find('subspecialty_id=? and element_type_id=? and `key`=?',array($subspecialty_id,$element_type->id,$key))) {
				return $this->parseSetting($setting, $metadata);
			}
		}

		if ($specialty = $firm->getSpecialty()) {
			if ($setting = SettingSpecialty::model()->find('specialty_id=? and element_type_id=? and `key`=?',array($specialty->id,$element_type->id,$key))) {
				return $this->parseSetting($setting, $metadata);
			}
		}

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		if ($setting = SettingSite::model()->find('site_id=? and element_type_id=? and `key`=?',array($site->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingInstitution::model()->find('institution_id=? and element_type_id=? and `key`=?',array($site->institution_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingInstallation::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		return $metadata->default_value;
	}

	public function parseSetting($setting, $metadata)
	{
		if (@$data = unserialize($metadata->data)) {
			if (isset($data['model'])) {
				$model = $data['model'];
				return $model::model()->findByPk($setting->value);
			}
		}

		return $setting->value;
	}

	/**
	 * Stubbed method to set default options
	 * Used by child objects to set defaults for forms on create
	 */
	public function setDefaultOptions()
	{
	}

	/**
	 * Stubbed method to set update options
	 * Used by child objects to override null values for forms on update
	 */
	public function setUpdateOptions()
	{
	}

	public function getInfoText()
	{
	}

	public function getDefaultView()
	{
		return get_class($this);
	}

	public function getCreate_view()
	{
		return $this->getDefaultView();
	}

	public function getUpdate_view()
	{
		return $this->getDefaultView();
	}

	public function getView_view()
	{
		return $this->getDefaultView();
	}

	public function getPrint_view()
	{
		return $this->getDefaultView();
	}

	public function isEditable()
	{
		return true;
	}

	public function requiredIfSide($attribute, $params)
	{
		if (($params['side'] == 'left' && $this->eye_id != 2) || ($params['side'] == 'right' && $this->eye_id != 1)) {
			if ($this->$attribute == null) {
				if (!@$params['message']) {
					$params['message'] = ucfirst($params['side']) . " {attribute} cannot be blank.";
				}
				$params['{attribute}'] = $this->getAttributeLabel($attribute);

				$this->addError($attribute, strtr($params['message'], $params));
			}
		}
	}

	public function textWithLineBreaks($field) {
		return str_replace("\n","<br/>",$this->$field);
	}

	/**
	 * stub method to allow elements to carry out actions related to being a part of a soft deleted event
	 */
	public function softDelete()
	{

	}
}
