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
  +----+-----------------------------------------------+-----------------------+---------------------+-----------------+---------------------+---------+---------+------------+--------------+------------------+----------------------+---------------------+-------------------+---------+-----------------------------------------------+--------------+
  | id | name                                          | last_modified_user_id | last_modified_date  | created_user_id | created_date        | type_id | form_id | dose_unit  | default_dose | default_route_id | default_frequency_id | default_duration_id | preservative_free | aliases | tallman                                       | discontinued |
  +----+-----------------------------------------------+-----------------------+---------------------+-----------------+---------------------+---------+---------+------------+--------------+------------------+----------------------+---------------------+-------------------+---------+-----------------------------------------------+--------------+
  |  1 | Abidec drops                                  |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      34 |       4 | mL         | 1            |               10 |                    0 |                   0 |                 0 | ABIDEC  | ABIDEC drops                                  |            0 |
  |  2 | Acetazolamide 250mg modified release capsules |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      17 |       1 | capsule(s) | 1            |               10 |                    3 |                   0 |                 0 | DIAMOX  | acetazolamide 250mg modified release capsules |            0 |
  |  3 | Acetazolamide 250mg tablets                   |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      17 |      32 | tablet(s)  | 1            |               10 |                    0 |                   0 |                 0 | DIAMOX  | acetazolamide 250mg tablets                   |            0 |
  |  4 | Acetazolamide 250mg in 5ml suspension         |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      17 |      20 | mg         |              |               10 |                    0 |                   0 |                 0 |         | acetazolamide 250mg in 5mL suspension         |            0 |
  |  5 | Acetazolamide 500mg injection                 |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      17 |      12 | mg         |              |                7 |                    0 |                   0 |                 0 | DIAMOX  | acetazolamide 500mg injection                 |            0 |
  |  6 | Acetylcholine 1% intraocular irrigation       |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      23 |      13 | mL         |              |                1 |                    0 |                   0 |                 1 |         | acetylCHOline 1% intraocular irrigation       |            0 |
  |  7 | Acetylcysteine 10% eye drops                  |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      30 |       4 | drop(s)    | 1            |                1 |                    7 |                   5 |                 0 |         | acetylCYSteine 10% eye drops                  |            0 |
  |  8 | Acetylcysteine 10% eye drops                  |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      30 |       4 | drop(s)    | 1            |                1 |                    7 |                   5 |                 1 |         | acetylCYSteine 10% eye drops                  |            0 |
  |  9 | Acetylcysteine 20% eye drops                  |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      30 |       4 | drop(s)    | 1            |                1 |                    7 |                   5 |                 0 |         | acetylCYSteine 20% eye drops                  |            0 |
  | 10 | Acetylcysteine 5% eye drops                   |                     1 | 2012-10-12 10:20:24 |               1 | 1900-01-01 00:00:00 |      30 |       4 | drop(s)    | 1            |                1 |                    7 |                   5 |                 0 | ILUBE   | acetylCYSteine 5% eye drops                   |            0 |
  +----+-----------------------------------------------+-----------------------+---------------------+-----------------+---------------------+---------+---------+------------+--------------+------------------+----------------------+---------------------+-------------------+---------+-----------------------------------------------+--------------+


 */
return array(
	 'drug1' => array(
		  'id' => 1,
		  'name' => 'Abidec drops',
		  'type_id' => 34,
		  'form_id' => 4,
		  'dose_unit' => 'mL',
		  'default_dose' => 1,
		  'default_route_id' => 10,
		  'default_frequency_id' => 0,
		  'default_duration_id' => 0,
		  'preservative_free' => 0,
		  'aliases' => 'ABIDEC',
		  'tallman' => 'ABIDEC drops',
		  'discontinued' => 0,
	 ),
	 'drug2' => array(
		  'id' => 2,
		  'name' => 'Acetazolamide 250mg modified release capsules',
		  'type_id' => 17,
		  'form_id' => 1,
		  'dose_unit' => 'capsule(s)',
		  'default_dose' => 1,
		  'default_route_id' => 10,
		  'default_frequency_id' => 3,
		  'default_duration_id' => 0,
		  'preservative_free' => 0,
		  'aliases' => 'DIAMOX',
		  'tallman' => 'acetazolamide 250mg modified release capsules',
		  'discontinued' => 0,
	 ),
	 'drug3' => array(
		  'id' => 3,
		  'name' => 'Acetazolamide 250mg tablets',
		  'type_id' => 17,
		  'form_id' => 32,
		  'dose_unit' => 'tablet(s)',
		  'default_dose' => 1,
		  'default_route_id' => 10,
		  'default_frequency_id' => 0,
		  'default_duration_id' => 0,
		  'preservative_free' => 0,
		  'aliases' => 'DIAMOX',
		  'tallman' => 'acetazolamide 250mg tablets',
		  'discontinued' => 0,
	 ), 
);