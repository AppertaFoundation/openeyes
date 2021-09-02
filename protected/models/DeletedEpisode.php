<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "episode".
 *
 * The followings are the available columns in table 'episode':
 *
 * @property int $id
 * @property int $patient_id
 * @property int $firm_id
 * @property string $start_date
 * @property string $end_date
 * @property boolean $support_services
 * @property boolean $change_tracker
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property Firm $firm
 * @property Event[] $events
 * @property EpisodeStatus $status
 */
class DeletedEpisode extends Episode
{
    private $defaultScopeDisabled = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return DeletedEpisode the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
