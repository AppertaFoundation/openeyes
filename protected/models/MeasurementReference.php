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

	protected function beforeSave()
	{
		foreach ($this->findAll('patient_measurement_id = ?', array($this->patient_measurement_id)) as $existing) {
			if ($this->episode_id && $this->episode_id == $existing->episode_id) {
				throw new Exception("Measurement reference already exists from episode {$this->episode_id} to patient measurement {$this->patient_measurement_id}");
			}

			if ($this->event_id && $this->event_id == $existing->event_id) {
				throw new Exception("Measurement reference already exists from event {$this->event_id} to patient measurement {$this->patient_measurement_id}");
			}

			if ($this->origin && $existing->origin) {
				throw new Exception("Origin reference already exists for patient measurement {$this->patient_measurement_id}");
			}
		}

		return parent::beforeSave();
	}
}
