<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

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
