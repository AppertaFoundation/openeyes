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
    'audit1' => array(
        'action' => 'action1',
        'action_id' => 1,
        'type_id' => 3,
        'target_type' => 'targettype1',
        'patient_id' => 1,
        'episode_id' => 1,
        'event_id' => 1,
        'user_id' => 1,
        'data' => 'Data 1',
        'remote_addr' => '1.1.1.1',
        'http_user_agent' => 'HTTP User Agent 1',
        'server_name' => 'servername1',
        'request_uri' => 'request/uri1',
        'site_id' => 1,
        'firm_id' => 1,
    ),
    'audit2' => array(
        'action' => 'action2',
        'action_id' => 2,
        'type_id' => 3,
        'target_type' => 'targettype2',
        'patient_id' => 2,
        'episode_id' => 2,
        'event_id' => 2,
        'user_id' => 1,
        'data' => 'Data 2',
        'remote_addr' => '2.2.2.2',
        'http_user_agent' => 'HTTP User Agent 2',
        'server_name' => 'servername2',
        'request_uri' => 'request/uri2',
        'site_id' => 2,
        'firm_id' => 2,
    ),
    'audit3' => array(
        'action' => 'action3',
        'action_id' => 3,
        'type_id' => 3,
        'target_type' => 'targettype3',
        'patient_id' => 3,
        'episode_id' => 3,
        'event_id' => 3,
        'user_id' => 1,
        'data' => 'Data 3',
        'remote_addr' => '3.3.3.3',
        'http_user_agent' => 'HTTP User Agent 3',
        'server_name' => 'servername3',
        'request_uri' => 'request/uri3',
        'site_id' => 3,
        'firm_id' => 3,
    ),
);
