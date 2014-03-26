<?php

class MeasurementReference extends BaseActiveRecordVersioned
{
	public function tableName()
	{
		return 'measurement_reference';
	}

	public function relations()
	{
		return array(
			'episode' => array(self::BELONGS_TO, 'Episode', 'episode_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'patientMeasurement' => array(self::BELONGS_TO, 'PatientMeasurement', 'patient_measurement_id'),
		);
	}
}
