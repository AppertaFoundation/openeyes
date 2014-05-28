<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "medication".
 *
 * The followings are the available columns in table 'medication':
 * @property integer $id
 * @property integer $patient_id
 * @property integer $route_id
 * @property integer $drug_id
 * @property integer $option_id
 * @property integer $frequency_id
 * @property string $start_date
 * @property string $end_date
 */
class Medication extends BaseActiveRecordVersioned
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'medication';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('drug_id, route_id, option_id, dose, frequency_id, start_date, end_date, stop_reason_id', 'safe'),
			array('drug_id, route_id, frequency_id, start_date', 'required'),
			array('option_id', 'validateOptionId'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'drug' => array(self::BELONGS_TO, 'Drug', 'drug_id'),
			'route' => array(self::BELONGS_TO, 'DrugRoute', 'route_id'),
			'option' => array(self::BELONGS_TO, 'DrugRouteOption', 'option_id'),
			'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
			'stop_reason' => array(self::BELONGS_TO, 'MedicationStopReason', 'stop_reason_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'drug_id' => 'Medication',
			'route_id' => 'Route',
			'option_id' => 'Option',
			'frequency_id' => 'Frequency',
			'stop_reason_id' => 'Reason for stopping',
		);
	}

	public function validateOptionId()
	{
		if (!$this->option_id && $this->route && $this->route->options) {
			$this->addError('option_id', "Must specify an option for route '{$this->route->name}'");
		}
	}

	public function beforeSave()
	{
		if (!$this->end_date) $this->stop_reason_id = null;
		return parent::beforeSave();
	}
}
