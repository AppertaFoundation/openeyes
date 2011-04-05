<?php

/**
 * A class that all clinical elements should extend from.
 */
class BaseElement extends CActiveRecord
{
	public $firm;
	public $userId;
	public $patientId;
	public $event;

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
	 * @param int viewNumber
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

	public function getSpecialtyId()
	{
		if (isset($this->firm)) {
			return $this->firm->serviceSpecialtyAssignment->specialty_id;
		} elseif (isset($this->event_id)) {
			$event = $this->getEvent();
			return $event->episode->firm->serviceSpecialtyAssignment->specialty_id;
		} else {
			// @todo - change to one of our exceptions
			throw new Exception('Unable to return specialty_id.');
		}
	}

	// @todo - should there be getPatientId and getUserId methods too?
	// There's nothing to fetch userId from so it's hard to imagine that one...
	// patientId can be fetched from the event.

	public function getEvent()
	{
		if (isset($this->event)) {
			return $this->event;
		} elseif (isset($this->event_id)) {
			$this->event = Event::model()->findByPk($this->event_id);
			return $this->event;
		} else {
			throw new Exception('Unable to return event model in getEvent().');
		}
	}

	/**
	 * Returns a list of Exam Phrases to be used by the element form.
	 *
	 * @return array
	 */
	public function getExamPhraseOptions($part)
	{
		return array_merge(array('-' => '-'), CHtml::listData(ExamPhrase::Model()->findAll(
			'specialty_id = ? AND part = ?', array($this->getSpecialtyId(), $part)
		), 'id', 'phrase'));
	}
}
