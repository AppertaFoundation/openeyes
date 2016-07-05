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
 * @property boolean $useContainerView When rendering the element, wrap the element
 * in a container view?
 */
class BaseEventTypeElement extends BaseElement
{
	public $firm;
	public $userId;
	public $patientId;
	public $useContainerView = true;

	protected $_element_type;
	protected $_children;
	protected $frontEndErrors = array();
	protected $errorExceptions = array(
		'Element_OphTrOperationbooking_Operation_procedures' => 'select_procedure_id_procs',
		'Element_OphDrPrescription_Details_items' => 'prescription_items',
		'Element_OphTrConsent_Procedure_procedures' => 'typeProcedure',
		'Element_OphTrLaser_Treatment_left_procedures' => 'treatment_left_procedures',
		'Element_OphTrLaser_Treatment_right_procedures' => 'treatment_right_procedures',
		'OEModule_OphCiExamination_models_Element_OphCiExamination_Dilation_left_treatments' => 'dilation_drug_left',
		'OEModule_OphCiExamination_models_Element_OphCiExamination_Dilation_right_treatments' => 'dilation_drug_right',
		'OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_right_values.reading_id' => 'OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_right_values',
		'OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_left_values.reading_id' => 'OEModule_OphCiExamination_models_OphCiExamination_IntraocularPressure_Value_left_values',
	);

	private $settings = array();

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
	 * @return boolean
	 */
	public function canViewPrevious()
	{
		return false;
	}

	/**
	 * Is this a required element?
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->elementType->required;
	}

	/**
	 * Is this element required in the UI? (Prevents the user from being able
	 * to remove the element.)
	 * @return boolean
	 */
	public function isRequiredInUI()
	{
		return $this->isRequired();
	}

	/**
	 * Is this element to be hidden in the UI? (Prevents the elements from
	 * being displayed on page load.)
	 * @return boolean
	 */
	public function isHiddenInUI()
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
			if( isset($element->$attribute) ){
				$this->$attribute = $element->$attribute;
			}
		}
	}

	public function render($action)
	{
		$this->Controller->renderPartial();
	}

	public function getSetting($key)
	{
		$setting = SettingMetadata::model()->getSetting($key, ElementType::model()->find('class_name=?',array(get_class($this))));
		if(!$setting){
			$setting = SettingMetadata::model()->getSetting($key, ElementType::model()->find('class_name=?',array(get_parent_class($this))));
		}

		return $setting;
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
		$kls = explode('\\', get_class($this));
		return end($kls);
	}

	public function getCreate_view()
	{
		return $this->getForm_View();
	}

	public function getUpdate_view()
	{
		return $this->getForm_View();
	}

	public function getView_view()
	{
		return 'view_'.$this->getDefaultView();
	}

	public function getPrint_view()
	{
		return $this->getView_View();
	}

	public function getForm_View()
	{
		return 'form_'.$this->getDefaultView();
	}

	public function getDefaultContainerView()
	{
		return '//patient/element_container_view';
	}

	public function getContainer_view_view()
	{
		return '//patient/element_container_view';
	}

	public function getContainer_print_view()
	{
		return '//patient/element_container_print';
	}

	public function getContainer_form_view()
	{
		return '//patient/element_container_form';
	}

	public function getContainer_create_view()
	{
		return $this->getContainer_form_view();
	}

	public function getContainer_update_view()
	{
		return $this->getContainer_form_view();
	}

	public function isEditable()
	{
		return true;
	}

	public function addError($attribute, $message)
	{
		$this->frontEndErrors[] = $this->errorAttributeException(str_replace('\\', '_', get_class($this)) . '_' . $attribute);
		$message = '<a class="errorlink" onClick="scrollToElement($(\'.' . str_replace('\\', '_',
				get_class($this)) . '\'))">' . $message . '</a>';
		parent::addError($attribute, $message);
	}

	/**
	 * Allows for exceptions where the element displayed is not the one required. eg for ajax control elements.
	 *
	 * @param $attribute
	 * @return mixed
	 */
	protected function errorAttributeException($attribute)
	{
		if(array_key_exists($attribute, $this->errorExceptions)){
			return $this->errorExceptions[$attribute];
		}

		return $attribute;
	}

	/**
	 *  returns the front-end attributes with errors
	 */
	public function getFrontEndErrors()
	{
		echo json_encode($this->frontEndErrors);
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

	/**
	 * stub method to allow elements to carry out actions related to being a part of a soft deleted event
	 */
	public function softDelete()
	{

	}

	/**
	 * Returns true if the specified multiselect relation has the value $value_string
	 */
	public function hasMultiSelectValue($relation, $value_string) {
		foreach ($this->$relation as $item) {
			if ($item->name == $value_string) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Updates multiselect items in the database, deleting items not passed in $ids
	 */
	public function updateMultiSelectData($model, $ids, $relation_field)
	{
		$_ids = array();

		foreach ($ids as $id) {
			if (!$assignment = $model::model()->find("element_id=? and $relation_field=?",array($this->id,$id))) {
				$assignment = new $model;
				$assignment->element_id = $this->id;
				$assignment->$relation_field = $id;

				if (!$assignment->save()) {
					throw new Exception("Unable to save assignment: ".print_r($assignment->getErrors(),true));
				}
			}

			$_ids[] = $assignment->id;
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('element_id = :element_id');
		$criteria->params[':element_id'] = $this->id;

		!empty($_ids) && $criteria->addNotInCondition('id',$_ids);

		$model::model()->deleteAll($criteria);
	}
}
