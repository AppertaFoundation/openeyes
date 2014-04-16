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
