<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class Element_OphTrOperationnote_Mmc extends Element_OnDemand
{
	public function tableName()
	{
		return 'et_ophtroperationnote_mmc';
	}

	public function rules()
	{
		return array(
			array('application_type_id,concentration_id,volume_id,duration,number,washed', 'safe'),
			array('application_type_id,concentration_id', 'required'),
			array('duration', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 5),
			array('number', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 5),
		);
	}

	public function attributeLabels()
	{
		return array(
			'application_type_id' => 'Application',
			'concentration_id' => 'Concentration (mg/ml)',
			'volume_id' => 'Volume (ml)',
			'dose' => 'Dose (mg)',
			'duration' => 'Duration (mins)',
			'washed' => 'Washed with (20ml) BSS / saline',
		);
	}

	public function relations()
	{
		return array(
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'application_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Antimetabolite_Application_Type', 'application_type_id'),
			'concentration' => array(self::BELONGS_TO, 'OphTrOperationnote_Mmc_Concentration', 'concentration_id'),
			'volume' => array(self::BELONGS_TO, 'OphTrOperationnote_Mmc_Concentration', 'volume_id'),
		);
	}

	public function afterValidate()
	{
		switch ($this->application_type_id) {
			case OphTrOperationnote_Antimetabolite_Application_Type::SPONGE:
				if (is_null($this->duration)) $this->addError('duration', '{attribute} cannot be blank');
				if (is_null($this->number)) $this->addError('number', '{attribute} cannot be blank');
				if (is_null($this->washed)) $this->addError('washed', '{attribute} cannot be blank');
				$this->volume_id = null;
				break;
			case OphTrOperationnote_Antimetabolite_Application_Type::INJECTION:
				if (is_null($this->volume_id)) $this->addError('volume_id', '{attribute} cannot be blank');
				$this->duration = null;
				$this->number = null;
				$this->washed = null;
				break;
		}

		parent::afterValidate();
	}

	/**
	 * @return string
	 */
	public function getDose()
	{
		return number_format($this->concentration->value * $this->volume->value, 2);
	}
}
