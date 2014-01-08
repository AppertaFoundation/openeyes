<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Core authorisation business rules
 */
class AuthRules
{
	/**
	 * @param Firm $firm
	 * @param Episode $episode
	 * @return boolean
	 */
	public function canEditEpisode(Firm $firm, Episode $episode)
	{
		if ($episode->legacy) return false;

		if ($episode->support_services) return $firm->isSupportServicesFirm();

		return ($firm->getSubspecialtyID() === $episode->getSubspecialtyID());
	}

	/**
	 * @param Firm $firm
	 * @param Episode $episode
	 * @param EventType $event_type
	 * @return boolean
	 */
	public function canCreateEvent(Firm $firm = null, Episode $episode = null, EventType $event_type = null)
	{
		if ($event_type) {
			if ($event_type->disabled) return false;

			if (!$event_type->support_services && !$firm->getSubspecialtyID()) {
				// Can't create a non-support service event for a support-service firm
				return false;
			}
		}

		if ($firm && $episode) {
			return $this->canEditEpisode($firm, $episode);
		}

		return true;
	}

	/**
	 * @param Firm $firm
	 * @param Event $event
	 * @return boolean
	 */
	public function canEditEvent(Firm $firm, Event $event)
	{
		if ($event->episode->patient->date_of_death) return false;

		return $this->canEditEpisode($firm, $event->episode);
	}

	/**
	 * @param User $user
	 * @param Firm $firm
	 * @param Event $event
	 * @return boolean
	 */
	public function canDeleteEvent(User $user, Firm $firm, Event $event)
	{
		// Only the event creator can delete the event, and only 24 hours after its initial creation
		if (!($event->created_user_id == $user->id && (time() - strtotime($event->created_date)) <= 86400)) {
			return false;
		}

		return $this->canEditEvent($firm, $event);
	}
}
