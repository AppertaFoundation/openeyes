<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'event1' => array(
        'id' => 1,
        'episode_id' => 1,
        'created_user_id' => 1,
        'event_type_id' => 1,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => date('Y-m-d 00:00:00'),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event2' => array(
        'id' => 2,
        'episode_id' => 2,
        'created_user_id' => 1,
        'event_type_id' => 1,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 01:00:00'),
        'created_date' => date('Y-m-d 02:00:00'),
        'event_date' => date('Y-m-d 00:00:00', strtotime('-2 days')),
        'info' => 'someinfo2',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event3' => array(
        'id' => 3,
        'episode_id' => 2,
        'created_user_id' => 1,
        'event_type_id' => 1,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => date('Y-m-d 01:00:00'),
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo3',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event4' => array(
        'id' => 4,
        'episode_id' => 3,
        'created_user_id' => 1,
        'event_type_id' => 1,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => date('Y-m-d 00:00:00'),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event5' => array(
        'id' => 5,
        'episode_id' => 4,
        'created_user_id' => 1,
        'event_type_id' => 1,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => date('Y-m-d 00:00:00'),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event6' => array(
        'id' => 6,
        'episode_id' => 4,
        'created_user_id' => 1,
        'event_type_id' => 1001,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:01'),
        'created_date' => date('Y-m-d 00:00:01', strtotime('-3 months')),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event7' => array(
        'id' => 7,
        'episode_id' => 3,
        'created_user_id' => 1,
        'event_type_id' => 1007,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:02'),
        'created_date' => date('Y-m-d 00:00:02', strtotime('-45 days')),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event8' => array(
        'id' => 8,
        'episode_id' => 3,
        'created_user_id' => 1,
        'event_type_id' => 1012,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:03'),
        'created_date' => date('Y-m-d 00:00:03', strtotime('-17 days')),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event9' => array(
        'id' => 9,
        'episode_id' => 4,
        'created_user_id' => 1,
        'event_type_id' => 1012,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:04'),
        'created_date' => date('Y-m-d 00:00:04', strtotime('-3 days')),
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event10' => array(
        'episode_id' => 6,
        'created_user_id' => 1,
        'event_type_id' => 1001,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:30',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event11' => array(
        'episode_id' => 6,
        'created_user_id' => 1,
        'event_type_id' => 1001,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:40',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event12' => array(
        'episode_id' => 6,
        'created_user_id' => 1,
        'event_type_id' => 1001,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event13' => array(
        'episode_id' => 6,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event14' => array(
        'episode_id' => 6,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event15' => array(
        'episode_id' => 1,
        'created_user_id' => 1,
        'event_type_id' => 1002,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => null,
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),

        /* **/

    'event16' => array(
        'episode_id' => 7,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event17' => array(
        'episode_id' => 7,
        'created_user_id' => 1,
        'event_type_id' => 1002,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),

    'event18' => array(
        'episode_id' => 8,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event19' => array(
        'episode_id' => 8,
        'created_user_id' => 1,
        'event_type_id' => 1002,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),

    'event20' => array(
        'episode_id' => 9,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event21' => array(
        'episode_id' => 9,
        'created_user_id' => 1,
        'event_type_id' => 1002,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),

    'event22' => array(
        'episode_id' => 10,
        'created_user_id' => 1,
        'event_type_id' => 1003,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event23' => array(
        'episode_id' => 10,
        'created_user_id' => 1,
        'event_type_id' => 1002,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),
    'event24' => array(
        'episode_id' => 10,
        'created_user_id' => 1,
        'event_type_id' => 1008,
        'last_modified_user_id' => 1,
        'last_modified_date' => date('Y-m-d 00:00:00'),
        'created_date' => '2014-01-01 00:00:50',
        'event_date' => date('Y-m-d 00:00:00'),
        'info' => 'someinfo',
        'deleted' => false,
        'delete_reason' => null,
        'delete_pending' => false,
    ),

);
