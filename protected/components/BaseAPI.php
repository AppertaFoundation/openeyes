<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class BaseAPI
{
	/*
	 * gets the event type for the api instance
	 *
	 * @return EventType $event_type
	 */
	protected function getEventType()
	{
		if (!$event_type = EventType::model()->find('class_name=?',array(preg_replace('/_API$/','',get_class($this))))) {
			throw new Exception("Unknown event type or incorrectly named API class: ".get_class($this));
		}
		return $event_type;
	}

	/*
	 * gets the element of type $element for the given patient in the given episode
	 *
	 * @param Patient $patient - the patient
	 * @param Episode $episode - the episode
	 * @param string $element - the element class
	 *
	 * @return unknown - the element type requested, or null
	 */
	public function getElementForLatestEventInEpisode($patient, $episode, $element)
	{
		$event_type = $this->getEventType();

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

	/*
	 * gets all the events in the episode for the event type this API is for, for the given patient, most recent first.
	 *
	 * @param Patient $patient - the patient
	 * @param Episode $episode - the episode
	 *
	 * @return array - list of events of the type for this API instance
	 */
	public function getEventsInEpisode($patient, $episode)
	{
		$event_type = $this->getEventType();

		if ($episode) {
			return $episode->getAllEventsByType($event_type->id);
		}
		return array();
	}

	public function getMostRecentEventInEpisode($episode_id, $event_type_id)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type_id);
		$criteria->compare('episode_id',$episode_id);
		$criteria->order = 'created_date desc';

		return Event::model()->find($criteria);
	}

	/*
	 * gets the most recent instance of a specific element in the current episode
	 *
	 */
	public function getMostRecentElementInEpisode($episode_id, $event_type_id, $model)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type_id);
		$criteria->compare('episode_id',$episode_id);
		$criteria->order = 'created_date desc';

		foreach (Event::model()->findAll($criteria) as $event) {
			if ($element = $model::model()->find('event_id=?',array($event->id))) {
				return $element;
			}
		}

		return false;
	}

	/*
	 * Stub method to prevent crashes when getEpisodeHTML() is called for every installed module
	 *
	 */
	public function getEpisodeHTML($episode_id)
	{
	}
}
