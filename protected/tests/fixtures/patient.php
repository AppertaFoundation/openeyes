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
    'patient1' => array(
        'id' => 1,
        'pas_key' => '123',
        'dob' => '1970-01-01',
        'gender' => 'M',
        'practice_id' => 1,
        'address_id' => 1,
        'contact_id' => 1,
    ),
    'patient2' => array(
        'id' => 2,
        'pas_key' => '456',
        'dob' => '1972-01-01',
        'gender' => 'M',
        'practice_id' => 2,
        'address_id' => 2,
        'contact_id' => 2,
    ),
    'patient3' => array(
        'id' => 3,
        'pas_key' => '789',
        'dob' => '1960-01-01',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 3,
        'contact_id' => 3,
    ),
    'patient4' => array(
        'pas_key' => '123',
        'dob' => '1977-01-01',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 4,
        'contact_id' => 4,
    ),
    'patient5' => array(
        'pas_key' => '1010',
        'dob' => '1977-01-01',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 4,
        'contact_id' => 5,
    ),
    'patient6' => array(
        'pas_key' => '10107',
        'dob' => '1977-01-01',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 4,
        'contact_id' => 6,
    ),

    'patient7' => array(
        'id' => 7,
        'pas_key' => '1010',
        'dob' => '1977-03-04',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 4,
        'contact_id' => 5,
        'is_local' => 0,
    ),
    'patient8' => array(
        'id' => 8,
        'pas_key' => '10107',
        'dob' => '1977-03-04',
        'gender' => 'F',
        'practice_id' => 3,
        'address_id' => 4,
        'contact_id' => 6,
        'is_local' => 0,
    ),
    'patient9' => array(
        'id' => 9,
        'dob' => '1979-09-08',
        'date_of_death' => '2019-07-10',
        'is_deceased' => '1',
        'title' => 'MR',
        'primary_phone' => '0208 1111111',
        'address_id' => 1,
        'contact_id' => 6,
    ),
    'patient10' => array(
        'id' => 10,
        'dob' => '1979-09-08',
        'date_of_death' => '2019-07-10',
        'is_deceased' => '1',
        'title' => 'MR',
        'primary_phone' => '0208 1111111',
        'address_id' => 1,
        'contact_id' => 7,
    ),
);
