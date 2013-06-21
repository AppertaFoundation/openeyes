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
+----+-----------+-----------------+-----------------------+---------------------+-----------------+---------------------+
| id | name      | subspecialty_id | last_modified_user_id | last_modified_date  | created_user_id | created_date        |
+----+-----------+-----------------+-----------------------+---------------------+-----------------+---------------------+
|  1 | Post op   |              16 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  2 | Follow up |              16 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  3 | Pressure  |              16 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  4 | Post op   |               1 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  5 | Post op   |               2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  6 | Post op   |               3 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  7 | Post op   |               4 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  8 | Post op   |               5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
|  9 | Post op   |               6 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
| 10 | Post op   |               7 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
+----+----------+-----------------------+-----------------+---------------------+---------------------+---------+---------

 */
return array(
	 'drugset1' => array(
		  'id' => 1,
		  'name' => 'Post op',
		  'subspecialty_id' => 16
	 ),
	 'drugset2' => array(
		  'id' => 2,
		  'name' => 'Follow up',
		  'subspecialty_id' => 16
	 ),
	 'drugset3' => array(
		  'id' => 3,
		  'name' => 'Pressure',
		  'subspecialty_id' => 16
	 ),
	 'drugset4' => array(
		  'id' => 4,
		  'name' => 'Post op',
		  'subspecialty_id' => 1
	 ),
	 'drugset5' => array(
		  'id' => 5,
		  'name' => 'Post op',
		  'subspecialty_id' => 2
	 ),
	 'drugset6' => array(
		  'id' => 6,
		  'name' => 'Post op',
		  'subspecialty_id' => 3
	 ),
	 'drugset7' => array(
		  'id' => 7,
		  'name' => 'Post op',
		  'subspecialty_id' => 4
	 ),
	 'drugset8' => array(
		  'id' => 8,
		  'name' => 'Post op',
		  'subspecialty_id' => 5
	 ),
	 'drugset9' => array(
		  'id' => 9,
		  'name' => 'Post op',
		  'subspecialty_id' => 6
	 ),
	 'drugset10' => array(
		  'id' => 10,
		  'name' => 'Post op',
		  'subspecialty_id' => 7
	 ),
);