<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'oct1' => array(
        'id' => 1,
        'event_id' => 1,
        'eye_id' => 2,
        'left_method_id' => 1,
        'right_method_id' => 2,
        'right_crt' => 20,
        'left_crt' => 33,
    ),
    'oct2' => array(
        'id' => 2,
        'event_id' => 2,
        'eye_id' => 1,
        'left_method_id' => 1,
        'right_method_id' => null,
        'right_crt' => null,
        'left_crt' => 20,
    ),
    'oct3' => array(
        'id' => 3,
        'event_id' => 15,
        'eye_id' => 2,
        'left_method_id' => 1,
        'right_method_id' => null,
        'right_crt' => 20,
        'left_crt' => null,
    ),
    'oct4' => array(
        'id' => 4,
        'event_id' => 29,
        'eye_id' => Eye::BOTH,
        'left_method_id' => 1,
        'right_method_id' => 1,
        'right_crt' => 50,
        'left_crt' => 50,
    ),
);
