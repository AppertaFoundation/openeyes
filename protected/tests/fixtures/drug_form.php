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
 *+----+--------------------+-----------------------+-----------------+---------------------+---------------------+
| id | name               | last_modified_user_id | created_user_id | last_modified_date  | created_date        |
+----+--------------------+-----------------------+-----------------+---------------------+---------------------+
|  1 | Capsule            |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  2 | Cream              |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  3 | Device             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  4 | Drop               |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  5 | Eye  Ointment      |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  6 | Gas                |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  7 | Gel                |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  8 | Implant            |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
|  9 | Infusion           |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 10 | Inhalation         |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 11 | Inhaler            |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 12 | Injection          |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 13 | Irrigation         |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 14 | Lotion             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 15 | Lotion             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 16 | Lozenge            |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 17 | Mouthwash          |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 18 | Nebuliser Solution |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 19 | Ointment           |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 20 | Oral Liquid        |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 21 | Pack               |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 22 | Pack               |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 23 | Patch              |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 24 | Pessary            |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 25 | Powder             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 26 | Sachet             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 27 | Solution           |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 28 | Sponge             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 29 | Spray              |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 30 | Strip              |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 31 | Suppository        |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 32 | Tablet             |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 33 | Tube               |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
| 34 | Wafer              |                     1 |               1 | 1900-01-01 00:00:00 | 1900-01-01 00:00:00 |
+----+----------+-----------------------+-----------------+---------------------+---------------------+---------+

 */
return array(
                         'drugform1' => array(
                                                  'id'=> 1,
                                                  'name' => 'Capsule',
                         ),
                         'drugform2' => array(
                                                  'id'=> 2,
                                                  'name' => 'Cream' ,
                         ),
                         'drugform3' => array(
                                                  'id'=> 3,
                                                  'name' => 'Device',
                         ),
	  'drugform4' => array(
                                                  'id'=> 4,
                                                  'name' => 'Drop',
                         ),
                         'drugform5' => array(
                                                  'id'=> 5,
                                                  'name' => 'Eye  Ointment' ,
                         ),
                         'drugform6' => array(
                                                  'id'=> 6,
                                                  'name' => 'Gas',
                         ),
	 'drugform7' => array(
                                                  'id'=> 7,
                                                  'name' => 'Gel',
                         ),
                         'drugform8' => array(
                                                  'id'=> 8,
                                                  'name' => 'Implant' ,
                         ),
                         'drugform9' => array(
                                                  'id'=> 9,
                                                  'name' => 'Infusion',
                         ),
	  'drugform10' => array(
                                                  'id'=> 10,
                                                  'name' => 'Inhalation', 
                         ), 
);