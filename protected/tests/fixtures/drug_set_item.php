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
  +----+---------+-------------+----------------------+---------------------+-----------------------+---------------------+-----------------+---------------------+
  | id | drug_id | drug_set_id | default_frequency_id | default_duration_id | last_modified_user_id | last_modified_date  | created_user_id | created_date        |
  +----+---------+-------------+----------------------+---------------------+-----------------------+---------------------+-----------------+---------------------+
  |  1 |     176 |           1 |                    8 |                   2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  2 |     278 |           1 |                    8 |                   5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  3 |     264 |           1 |                    8 |                   2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  4 |     264 |           2 |                    3 |                   5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  5 |      60 |           3 |                    9 |                   5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  6 |     573 |           3 |                    3 |                   5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  7 |     176 |           4 |                    8 |                   2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  8 |     278 |           4 |                    8 |                   5 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  |  9 |     264 |           4 |                    8 |                   2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  | 10 |     176 |           5 |                    8 |                   2 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |
  +----+----------+-----------------------+-----------------+---------------------+---------------------+---------------------------------------------------------+

 */
return array(
	 'drugsetitem1' => array(
		  'id' => 1,
		  'drug_id' => 176,
		  'drug_set_id' => 1,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 2,
	 ),
	 'drugsetitem2' => array(
		  'id' => 2,
		  'drug_id' => 278,
		  'drug_set_id' => 1,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 5,
	 ),
	 'drugsetitem3' => array(
		  'id' => 3,
		  'drug_id' => 264,
		  'drug_set_id' => 1,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 2,
	 ),
	 'drugsetitem4' => array(
		  'id' => 4,
		  'drug_id' => 264,
		  'drug_set_id' => 2,
		  'default_frequency_id' => 3,
		  'default_duration_id' => 5,
	 ),
	 'drugsetitem5' => array(
		  'id' => 5,
		  'drug_id' => 60,
		  'drug_set_id' => 3,
		  'default_frequency_id' => 9,
		  'default_duration_id' => 5,
	 ),
	 'drugsetitem6' => array(
		  'id' => 6,
		  'drug_id' => 573,
		  'drug_set_id' => 3,
		  'default_frequency_id' => 3,
		  'default_duration_id' => 5,
	 ),
	 'drugsetitem7' => array(
		  'id' => 7,
		  'drug_id' => 176,
		  'drug_set_id' => 4,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 2,
	 ),
	 'drugsetitem8' => array(
		  'id' => 8,
		  'drug_id' => 278,
		  'drug_set_id' => 4,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 5,
	 ),
	 'drugsetitem9' => array(
		  'id' => 9,
		  'drug_id' => 264,
		  'drug_set_id' => 4,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 2,
	 ),
	 'drugsetitem10' => array(
		  'id' => 10,
		  'drug_id' => 176,
		  'drug_set_id' => 5,
		  'default_frequency_id' => 8,
		  'default_duration_id' => 2,
	 ),
);