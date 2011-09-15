<?php

/**
 * A class that all clinical elements should extend from.
 */
class BaseElement extends BaseActiveRecord
{
	public $firm;
	public $userId;
	public $patientId;

	// Used to display the view number set in site_element_type for any particular
	// instance of this element
	public $viewNumber;

	// Used during creation and updating of elements
	public $required;

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
	public function __construct($firm = null, $patientId = null, $userId = null, $viewNumber = null, $required = false)
	{
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
	public function getPhraseBySpecialtyOptions($section)
	{
		$section = Section::Model()->getByType('Exam', $section);
		return array_merge(array('-' => '-'), CHtml::listData(PhraseBySpecialty::Model()->findAll('specialty_id = ? AND section_id = ?', array($this->firm->serviceSpecialtyAssignment->specialty_id, $section->id)), 'id', 'phrase'));
	}

	/**
	 * Stubbed method to set default options
	 * Used by child objects to set defaults for forms on create
	 */
	public function setDefaultOptions()
	{
		return null;
	}
}
