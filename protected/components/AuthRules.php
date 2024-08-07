<?php
/**
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Core authorisation business rules.
 */
class AuthRules
{
    /**
     * @param Episode $episode
     *
     * @return bool
     */
    public function canEditEpisode($data, Episode $episode)
    {
        if ($episode->change_tracker) {
            // firm/subspecialty  is irrelevant for change tracking episodes.
            return true;
        }
        if ($episode->legacy) {
            return false;
        }

        return true;
    }

    /**
     * @param Firm|null $firm
     * @param Episode|null $episode
     * @param EventType|null $event_type
     * @param bool $has_pgdpsd_assignments
     * @return bool
     */
    public function canCreateEvent($data, Firm $firm = null, Episode $episode = null, EventType $event_type = null, bool $has_pgdpsd_assignments = false)
    {
        if ($event_type) {
            if ($event_type->disabled) {
                return false;
            }

            if (!$event_type->support_services && !$firm->getSubspecialtyID()) {
                // Can't create a non-support service event for a support-service firm
                return false;
            }

            if ($event_type->rbac_operation_suffix) {
                $oprn = "OprnCreate" . $event_type->rbac_operation_suffix;
                if (!Yii::app()->user->checkAccess($oprn)) return false;
            }

            if ($event_type->class_name === 'OphDrPGDPSD' && !Yii::app()->user->checkAccess('OprnCreateDA') && !$has_pgdpsd_assignments) {
                return false;
            }
        }

        if ($episode) {
            return $this->canEditEpisode(null, $episode);
        }

        return true;
    }

    /**
     * @param Firm  $firm
     * @param Event $event
     *
     * @return bool
     */
    public function canEditEvent($data, Event $event)
    {
        if ($event->delete_pending) {
            return false;
        }

        if ($event->episode->patient->isDeceased()) {
            return false;
        }

        if (!$this->canModifyEvent($event)) {
            return false;
        }
        if (!$this->isEventUnlocked($event)) {
            return false;
        }

        return true;
    }

    /**
     * @param OEWebUser|User $user
     * @param Event $event
     *
     * @return bool
     */
    public function canDeleteEvent($data, $user, Event $event)
    {
        if (!(Yii::app()->user->checkAccess('admin'))) {
            return false;
        }

        if (!$this->canModifyEvent($event)) {
            return false;
        }
        if (!$this->isEventUnlocked($event)) {
            return false;
        }

        return true;
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    public function canRequestEventDeletion($data, Event $event)
    {
        if ($event->delete_pending) {
            return false;
        }
        if ($event->showDeleteIcon() === false) {
            return false;
        }

        if (!$this->canModifyEvent($event)) {
            return false;
        }

        return true;
    }

    public function hasTeamAssignment($assigned_team_ids, $requested_team_id)
    {
        return in_array($requested_team_id, $assigned_team_ids);
    }

    public function canShowAllUsersInTeamUserAdder($data)
    {
        return Yii::app()->user->checkAccess('admin');
    }

    /**
     * Common check for all rules that involve editing/deleting events.
     *
     * @param Event $event
     *
     * @return bool
     */
    private function canModifyEvent(Event $event)
    {
        return $this->canEditEpisode(null, $event->episode);
    }

    /**
     * Event locking check.
     *
     * @param Event $event
     *
     * @return bool
     */
    private function isEventUnlocked(Event $event)
    {
        if (SettingMetadata::model()->getSetting('event_lock_disable') || Yii::app()->user->checkAccess('admin')) {
            return true;
        }

        if (($module_allows_editing = $event->moduleAllowsEditing()) !== null) {
            return $module_allows_editing;
        }

        // request: When I am logged in as a user that has edit rights (but NOT admin rights) for the biometry event,
        // I need to be able to edit the event at any time (i.e., the usual 24 hour limit does not apply)
        if (isset($event->eventType->name) && $event->eventType->name == 'Biometry') {
            return true;
        } else {
            return date('Ymd') < date('Ymd',
                    strtotime($event->created_date) + (86400 * (SettingMetadata::model()->getSetting('event_lock_days') + 1)));
        }
    }
}
