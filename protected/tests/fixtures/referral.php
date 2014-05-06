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
	'referral1' => array(
		'id' => 1,
		'refno' => 234,
		'patient_id' => 1,
		'referral_type_id' => 1,
		'received_date' => date('Y-m-d', strtotime('-20 days')),
		'referrer' => 'TEST'
	),
	'referral2' => array(
			'id' => 2,
			'refno' => 134,
			'patient_id' => 2,
			'referral_type_id' => 1,
			'received_date' => date('Y-m-d', strtotime('-15 days')),
			'referrer' => 'TEST'
	),
	'referral3' => array(
			'id' => 3,
			'refno' => 534,
			'patient_id' => 1,
			'referral_type_id' => 1,
			'received_date' => date('Y-m-d', strtotime('-5 days')),
			'referrer' => 'TEST'
	),
		'referral4' => array(
				'id' => 4,
				'refno' => 634,
				'patient_id' => 1,
				'referral_type_id' => 1,
				'received_date' => date('Y-m-d', strtotime('-25 days')),
				'closed_date' => date('Y-m-d', strtotime('-13 days')),
				'referrer' => 'TEST'
		),
);