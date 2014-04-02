<?php

/**
 * This is the model class for table "patient_measurement".
 *
 * The followings are the available columns in table 'patient_measurement':
 * @property string $id
 * @property string $patient_id
 * @property string $measurement_type_id
 * @property integer $deleted
 *
 * The followings are the available model relations:
 * @property MeasurementReference[] $measurementReferences
 * @property OphinvisualfieldsFieldMeasurement[] $ophinvisualfieldsFieldMeasurements
 * @property OphinvisualfieldsFieldMeasurementVersion[] $ophinvisualfieldsFieldMeasurementVersions
 * @property MeasurementType $measurementType
 * @property Patient $patient
 */
class BasePatientMeasurement extends BaseActiveRecord {

	public $patient_measurement_id;
	public $patientMeasurement;
	public $patient_id;
	public $measurement_type_id;
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PatientMeasurement the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function beforeSave() {
		$this->patientMeasurement = new PatientMeasurement;
		$this->patientMeasurement->patient_id = $this->patient_id;
		$this->patientMeasurement->measurement_type_id = $this->measurement_type_id;
		return $this->patientMeasurement->save();
	}

}
