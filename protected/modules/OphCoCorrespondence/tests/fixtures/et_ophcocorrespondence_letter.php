<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

return array(
	 array(
		'id' => 1,
		'event_id' => 6,
		'date' => '2014-09-15 11:13:01',
		'address' => '1 road,
town,
city
A11 1BB',
		'introduction' => 'Hi there,',
		're' => 'blah',
		'body' => 'Hi there,
you have been diagnosed with blah condition

please return to the clinic on xx date',
		'footer' => 'Yours sincerely,
Clinician',
		'draft' => 1,
		'site_id' => 1,
		'created_date' => '2014-09-15 11:11:11',
		'last_modified_date' => '2014-09-15 11:11:11',
		'created_user_id' => 2,
	),
	 array(
		'id' => 2,
		'event_id' => 7,
		'date' => '2014-09-15 11:13:01',
		'address' => '1 road,
town,
city
A11 1BB',
		'introduction' => 'Hi there,',
		're' => 'blah',
		'body' => 'Hi there,
you have been discharged with the following conditions:

please return to the clinic on xx date',
		'footer' => 'Yours sincerely,
Clinician',
		'draft' => 1,
		'site_id' => 1,
		'created_date' => '2014-01-15 11:11:11',
		'last_modified_date' => '2014-01-15 11:11:11',
		'created_user_id' => 3,
	),
);
