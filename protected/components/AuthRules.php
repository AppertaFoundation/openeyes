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
		// Get current logged in firm's subspecialty id (null for support services firms)
		$current_subspecialty_id = $firm->getSubspecialtyID();
		if (!$episode->firm) {
			// Episode has no firm, so it's either a legacy episode or a support services episode
			if ($episode->support_services) {
				// Support services episode, so are you logged in as a support services firm
				return ($current_subspecialty_id == null);
			} else {
				// Legacy episode
				return false;
			}
		} else {
			// Episode is normal (has a firm)
			if (!$current_subspecialty_id) {
				// Logged in as a support services firm
				return false;
			} else {
				// Logged in as a normal firm, so does episode subspecialty match
				return ($episode->getSubspecialtyID() == $current_subspecialty_id);
			}
		}

	}

	/**
	 * @param Firm $firm
	 * @param EventType $event_type
	 * @return boolean
	 */
	public function canCreateEvent(Firm $firm, EventType $event_type)
	{
		if ($event_type->disabled) return false;

		if (!$event_type->support_services && !$firm->getSubspecialtyID()) {
			// Can't create a non-support service event for a support-service firm
			return false;
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

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($firm->getSubspecialtyID() != $event->episode->getSubspecialtyID()) {
			//The firm you are using is not associated with the subspecialty of the episode
			return false;
		}

		return true;
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
