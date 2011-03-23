<?php

/**
 * A class that all clinical elements should extend from.
 */
class BaseElement extends CActiveRecord
{
	public $userFirm;
	public $userId;
	public $patientId;
	public $event;

	public function __construct($userId = null, $firm = null, $patientId = null)
	{
		$this->userId = $userId;
		$this->userFirm = $firm;
		$this->patientId = $patientId;
	}

	public function getSpecialtyId()
	{
		if (isset($this->userFirm)) {
			return $this->userFirm->serviceSpecialtyAssignment->specialty_id;
		} elseif (isset($this->event_id)) {
			$event = $this->getEvent();
			return $event->episode->firm->serviceSpecialtyAssignment->specialty_id;
		} else {
			// @todo - change to one of our exceptions
			throw new Exception('Unable to return specialty_id.');
		}
	}

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
