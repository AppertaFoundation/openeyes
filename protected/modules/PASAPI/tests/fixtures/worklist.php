<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If
 * not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'worklist1' => array(
        'id' => 1,
       'name' => 'test worklist',
        'start' => '2016-05-23 09:00:00',
        'end' => '2016-05-23 17:00:00',
        'scheduled' => true,
        'worklist_definition_id' => 1,
    ),
    'worklist2' => array(
        'id' => 2,
        'name' => 'test worklist 2',
        'start' => '2016-05-23 09:00:00',
        'end' => '2016-05-23 12:00:00',
        'scheduled' => true,
        'worklist_definition_id' => 2,
    ),
    'worklist3' => array(
        'id' => 3,
        'name' => 'test worklist 3',
        'start' => date_format(new Datetime(), 'Y-m-d 09:00:00'),
        'end' => date_format(new Datetime(), 'Y-m-d 17:00:00'),
        'scheduled' => true,
        'worklist_definition_id' => 3,
    ),
    'worklist4' => array(
        'id' => 4,
        'name' => 'test worklist 4',
        'start' => date_format(new Datetime(), 'Y-m-d 09:00:00'),
        'end' => date_format(new Datetime(), 'Y-m-d 17:00:00'),
        'scheduled' => true,
        'worklist_definition_id' => 4,
    ),
    'worklist5' => array(
        'id' => 5,
        'name' => 'test future worklist 4',
        'start' => date_format(new Datetime("+7 days"), 'Y-m-d 09:00:00'),
        'end' => date_format(new Datetime("+7 days"), 'Y-m-d 17:00:00'),
        'scheduled' => true,
        'worklist_definition_id' => 5,
    ),
);
