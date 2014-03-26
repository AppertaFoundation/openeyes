<?php

class PatientMeasurement extends BaseActiveRecordVersioned
{
	public function tableName()
	{
		return 'patient_measurement';
	}

	public function relations()
	{
		return array(
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
			'type' => array(self::BELONGS_TO, 'MeasurementType', 'measurement_type_id'),
			'originReference' => array(self::HAS_ONE, 'MeasurementReference', 'patient_measurement_id', 'on' => 'originReference.origin = true'),
			'references' => array(self::HAS_MANY, 'MeasurementReference', 'patient_measurement_id'),
		);
	}
}
