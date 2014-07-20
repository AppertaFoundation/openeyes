<?php

abstract class Measurement extends BaseActiveRecordVersioned
{
	private $patient_measurement;

	public function getMeasurementType()
	{
		return MeasurementType::model()->findByClassName(get_class($this));
	}

	public function getPatientMeasurement()
	{
		if (!isset($this->patient_measurement)) {
			if($this->isNewRecord) {
				$this->patient_measurement = new PatientMeasurement();
				$this->patient_measurement->measurement_type_id = $this->getMeasurementType()->id;
			} else {
				$this->patient_measurement = PatientMeasurement::model()->findByPk($this->patient_measurement_id);
			}
		}
		return $this->patient_measurement;
	}

	public function getPatient_id()
	{
		return $this->getPatientMeasurement()->patient_id;
	}

	public function setPatient_id($id)
	{
		$this->getPatientMeasurement()->patient_id = $id;
	}

	/**
	 * Attach this measurement to an Episode or Event
	 *
	 * @param Episode|Event $entity
	 * @param boolean $origin
	 * @return MeasurementReference
	 */
	public function attach($entity, $origin = false)
	{
		$ref = new MeasurementReference;
		$ref->patient_measurement_id = $this->getPatientMeasurement()->id;
		$ref->origin = $origin;

		if ($entity instanceof Episode) {
			$ref->episode_id = $entity->id;
		} elseif ($entity instanceof Event) {
			$ref->event_id = $entity->id;
		} else {
			throw new Exception("Can only attach measurements to Episodes or Events, was passed an object of type " . get_class($entity));
		}

		$ref->save();
		return $ref;
	}

	protected function afterValidate()
	{
		$this->getPatientMeasurement()->validate();

		foreach ($this->getPatientMeasurement()->getErrors() as $attribute => $errors) {
			foreach ($errors as $error) {
				$this->addError($attribute, $error);
			}
		}

		parent::afterValidate();
	}

	protected function beforeSave()
	{
		if (!parent::beforeSave() || !$this->getPatientMeasurement()->save()) return false;

		$this->patient_measurement_id = $this->getPatientMeasurement()->id;
		return true;
	}
}
