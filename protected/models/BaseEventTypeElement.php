<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * A class that all clinical elements should extend from.
 */
class BaseEventTypeElement extends BaseElement {

	public $firm;
	public $userId;
	public $patientId;

	// Used to display the view number set in site_element_type for any particular
	// instance of this element
	public $viewNumber;

	// Used during creation and updating of elements
	public $required = false;

	/**
	 * Temporary override to catch any bad constructor calls that may be lurking in the code.
	 * Should now be using {@link setBaseOptions} after construction instead
	 *
	 * @param string $scenario
	 * @fixme This can be removed once we are sure that it is not throwing errors
	 */
	public function __construct($scenario = 'insert', $patientId = null) {
		if($patientId !== null) {
			throw new CException('Element constructor called with bad args, old code needs fixing');
			Yii::log('Element constructor called with bad args, old code needs fixing', 'warning');
		}
		parent::__construct($scenario);
	}

	/**
	 * Clear BaseElement method
	 * @see BaseElement::tableName()
	 */
	public function tableName() {
		return get_class($this);
	}

	/**
	 * Clear BaseElement method
	 * @see BaseElement::relations()
	 */
	public function relations() {
		return array();
	}
	/**
	 * Clear BaseElement method
	 * @see BaseElement::getElement()
	 */
	public function getElement() {
	}

	function getElementType() {
		return ElementType::model()->find('class_name=?', array(get_class($this)));
	}

	function render($action) {
		$this->Controller->renderPartial();
	}

	function getFormOptions($table) {
		$options = array();
		foreach (Yii::app()->db->createCommand()
				->select("$table.*")
				->from($table)
				->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
				->where("element_type_id = ".$this->getElementType()->id)
				->order("display_order asc")
				->queryAll() as $option) {

			$options[$option['id']] = $option['name'];
		}
		return $options;
	}

	/**
	 * Here we need to provide default options for when the element is instantiated
	 * by findByPk in ClinicalService->getElements().
	 *
	 * @param object $firm
	 * @param int $patientId
	 * @param int $userId
	 * @param int $viewNumber
	 * @param boolean $required
	 */
	public function setBaseOptions($firm = null, $patientId = null, $userId = null, $viewNumber = null, $required = false) {
		$this->firm = $firm;
		$this->patientId = $patientId;
		$this->userId = $userId;
		$this->viewNumber = $viewNumber;
		$this->required = $required;
	}

	/**
	 * Returns a list of Exam Phrases to be used by the element form.
	 *
	 * @return array
	 */
	public function getPhraseBySubspecialtyOptions($section) {
		$section = Section::Model()->getByType('Exam', $section);
		return array_merge(array('-' => '-'), CHtml::listData(PhraseBySubspecialty::Model()->findAll('subspecialty_id = ? AND section_id = ?', array($this->firm->serviceSubspecialtyAssignment->subspecialty_id, $section->id)), 'id', 'phrase'));
	}

	/**
	 * Stubbed method to set default options
	 * Used by child objects to set defaults for forms on create
	 */
	public function setDefaultOptions() {
		return null;
	}

	public function getInfoText() {
	}

	public function getCreate_view() {
		return get_class($this);
	}

	public function getUpdate_view() {
		return get_class($this);
	}

	public function getView_view() {
		return get_class($this);
	}

	public function getPrint_view() {
		return get_class($this);
	}

	public function isEditable() {
		return true;
	}

}
