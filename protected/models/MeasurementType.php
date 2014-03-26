<?php

class MeasurementType extends BaseActiveRecordVersioned
{
	public function tableName()
	{
		return 'measurement_type';
	}

	/**
	 * @param string $class_name
	 * @return MeasurementType
	 */
	public function findByClassName($class_name)
	{
		return $this->find('class_name = ?', array($class_name));
	}
}
