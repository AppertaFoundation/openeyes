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
 * +----+----------+-----------------------+-----------------+---------------------+---------------------+---------------+
| id | name     | last_modified_user_id | created_user_id | last_modified_date  | created_date        | display_order |
+----+----------+-----------------------+-----------------+---------------------+---------------------+---------------+
|  1 | 5 days   |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |             6 |
|  2 | 7 days   |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |             7 |
|  3 | 10 days  |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |             8 |
|  4 | 14 days  |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |             9 |
|  5 | 1 month  |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |            11 |
|  6 | 24 hours |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |             1 |
|  7 | 48 hours |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |             2 |
|  8 | 1 day    |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |             3 |
|  9 | 3 days   |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |             4 |
| 10 | 4 days   |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |             5 |
| 11 | 6 weeks  |                     1 |               1 | 2012-10-31 12:27:33 | 2012-10-31 12:27:33 |            10 |
+----+----------+-----------------------+-----------------+---------------------+---------------------+---------------+

 */
return array(
                         'drugduration1' => array(
                                                  'id'=> 1,
                                                  'name' => '5 days',
                                                  'display_order' => 6
                         ),
                         'drugduration2' => array(
                                                  'id'=> 2,
                                                  'name' => '7 days' ,
                                                  'display_order' => 7
                         ),
                         'drugduration3' => array(
                                                  'id'=> 3,
                                                  'name' => '10 days',
                                                  'display_order' => 8
                         ),
	  'drugduration4' => array(
                                                  'id'=> 4,
                                                  'name' => '14 days',
                                                  'display_order' => 9
                         ),
                         'drugduration5' => array(
                                                  'id'=> 5,
                                                  'name' => '1 month' ,
                                                  'display_order' => 11
                         ),
                         'drugduration6' => array(
                                                  'id'=> 6,
                                                  'name' => '24 hours',
                                                  'display_order' => 1
                         ),
	 'drugduration7' => array(
                                                  'id'=> 7,
                                                  'name' => '48 hours',
                                                  'display_order' => 2
                         ),
                         'drugduration8' => array(
                                                  'id'=> 8,
                                                  'name' => '1 day' ,
                                                  'display_order' => 3
                         ),
                         'drugduration9' => array(
                                                  'id'=> 9,
                                                  'name' => '3 days',
                                                  'display_order' => 4
                         ),
	  'drugduration10' => array(
                                                  'id'=> 10,
                                                  'name' => '4 days',
                                                  'display_order' => 5
                         ),
                         'drugduration11' => array(
                                                  'id'=> 11,
                                                  'name' => '6 weeks' ,
                                                  'display_order' => 10
                         ),
);