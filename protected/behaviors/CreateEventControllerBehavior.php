<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphDrPGDPSD\models\{
    OphDrPGDPSD_AssignedUser,
    OphDrPGDPSD_AssignedTeam
};

class CreateEventControllerBehavior extends CBehavior
{
    protected $current_episode;

    /**
     * Caching wrapper for the current episode lookup
     *
     * @return array|mixed|null|void
     * @throws Exception
     */
    public function getOwnerCurrentEpisode()
    {
        if (!$this->current_episode) {
            $this->current_episode = $this->owner->current_episode ? : Episode::getCurrentEpisodeByFirm($this->owner->patient->id, $this->owner->firm);
        }

        return $this->current_episode;
    }

    /**
     * @return bool
     */
    public function getUserHasPGDPSDAssignments(): bool
    {
        if (\Yii::app()->user->checkAccess('Prescribe') || \Yii::app()->user->checkAccess('Med Administer')) {
            return true;
        }
        // Short circuit if the user is directly assigned to at least 1 PSD/PGD (more efficient).
        if (OphDrPGDPSD_AssignedUser::model()->exists('user_id = :user_id', [':user_id' => $this->owner->getApp()->user->id])) {
            return true;
        }
        $user_teams = Yii::app()->db->createCommand()
            ->select('team_id')
            ->from('team_user_assign')
            ->where('user_id = :user_id')
            ->bindValues([':user_id' => $this->owner->getApp()->user->id])
            ->queryColumn();
        if (count($user_teams) <= 0) {
            return false;
        }
        return OphDrPGDPSD_AssignedTeam::model()->exists('team_id IN (' . implode(', ', $user_teams) . ')');
    }

    /**
     * Supports the more complex RBAC rules for newer event types by providing a structure for
     * specifying the operation and arguments that should be used to check create access for an event.
     *
     * It's not necessary to replicate this for editing at the moment, as all editing routes are reached
     * through the relevant module controllers for any given event type.
     *
     * The $skip_args array allows us to manipulate the args that are generated so that wider conditions
     * can be checked. This was introduced so that creating events permission could be checked without the
     * episode context, as this is not really relevant in the SEM model. As the reach of the SEM changes
     * expands to the lower levels of behaviour this should no longer be necessary.
     *
     * @param $event_type
     * @param array $skip_args
     * @return array
     * @throws Exception
     */
    public function getCreateArgsForEventTypeOprn($event_type, array $skip_args = array())
    {
        $create_oprn = 'OprnCreateEvent';
        $args = array('firm', 'episode');

        if ($api = $event_type->getApi()) {
            if (property_exists($api, 'createOprn')) {
                $create_oprn = $api->createOprn;
            }
            if (property_exists($api, 'createOprnArgs')) {
                $args = $api->createOprnArgs;
            }
        }

        $create_args = array($create_oprn);
        foreach ($args as $arg) {
            if (in_array($arg, $skip_args)) {
                $create_args[] = null;
                continue;
            }
            switch ($arg) {
                case 'user_id':
                    $create_args[] = $this->owner->getApp()->user->id;
                    break;
                case 'firm':
                    $create_args[] = $this->owner->firm;
                    break;
                case 'episode':
                    $create_args[] = $this->getOwnerCurrentEpisode();
                    break;
                case 'event_type':
                    $create_args[] = $event_type;
                    break;
                case 'has_pgdpsd_assignments':
                    $create_args[] = $this->getUserHasPGDPSDAssignments();
                    break;
                default:
                    $create_args[] = $arg;
                    break;
            }
        }

        return $create_args;
    }

}
