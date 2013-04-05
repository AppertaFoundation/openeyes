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
	'sequence1' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+1 day')),
		'end_date' => null,
		'start_time' => '09:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_1WEEK,
		'weekday' => date('N', strtotime('+1 day')),
		'week_selection' => 0,
	),
	'sequence2' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+2 days')),
		'end_date' => null,
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_2WEEKS,
		'weekday' => date('N', strtotime('+2 days')),
		'week_selection' => 0,
	),
	'sequence3' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('-1 month')),
		'end_date' => date('Y-m-d', strtotime('-1 day')),
		'start_time' => '10:00:00',
		'end_time' => '13:00:00',
		'repeat_interval' => Sequence::FREQUENCY_1WEEK,
		'weekday' => date('N', strtotime('-1 month')),
		'week_selection' => 0,
	),
	'sequence4' => array(
		'theatre_id' => 1,
		'start_date' => date('Y-m-d', strtotime('+5 days')),
		'end_date' => null,
		'start_time' => '13:30:00',
		'end_time' => '18:00:00',
		'repeat_interval' => 0,
		'weekday' => date('N', strtotime('+5 days')),
		'week_selection' => Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK,
	)
);