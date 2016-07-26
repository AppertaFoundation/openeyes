<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

class EventCviRecord extends \CviRecord
{
	protected $event;

	public function __construct(\Event $event)
	{
		parent::__construct();
		$this->event = $event;
	}

	public function getStatusDate()
	{
		return $this->event->event_date;
	}

	public function getStatusText()
	{
		\OELog::log('progress of sorts' . $this->event->id);

		$info = Element_OphCoCvi_EventInfo::model()->findByAttributes(array('event_id' => $this->event->id));

		$clinical = Element_OphCoCvi_ClinicalInfo::model()->findByAttributes(array('event_id' => $this->event->id));

		return $clinical->getStatus() . ' (' . ($info->is_draft ? 'Draft' : 'Issued') . ')';
	}
}