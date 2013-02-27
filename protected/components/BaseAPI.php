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

class BaseAPI {
	public function getElementForLatestEventInEpisode($patient, $element) {
		if (!$event_type = EventType::model()->find('class_name=?',array(preg_replace('/_API$/','',get_class($this))))) {
			throw new Exception("Unknown event type or incorrectly named API class: ".get_class($this));
		}

		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			if ($event = $episode->getMostRecentEventByType($event_type->id)) {
				$criteria = new CDbCriteria;
				$criteria->compare('episode_id',$episode->id);
				$criteria->compare('event_id',$event->id);
				$criteria->order = 'datetime desc';

				return $element::model()
					->with('event')
					->find($criteria);
			}
		}
	}
}
