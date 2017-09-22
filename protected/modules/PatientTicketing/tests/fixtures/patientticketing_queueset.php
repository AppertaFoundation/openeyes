<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'queueset1' => array(
        'id' => 1,
        'name' => 'QueueSet 1',
        'active' => true,
        'initial_queue_id' => 1,
        'category_id' => 1,
        'filter_priority' => 1,
        'filter_subspecialty' => 1,
        'filter_firm' => 1,
        'filter_my_tickets' => 1,
        'filter_closed_tickets' => 1,
    ),
    'queueset2' => array(
        'id' => 2,
        'name' => 'QueueSet 2',
        'active' => 1,
        'initial_queue_id' => 12,
        'category_id' => 2,
        'filter_priority' => 0,
        'filter_subspecialty' => 0,
        'filter_firm' => 0,
        'filter_my_tickets' => 0,
        'filter_closed_tickets' => 0,
    ),
);
