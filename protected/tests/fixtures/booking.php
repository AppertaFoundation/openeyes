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


$operations = $this->getRows('operations');

$sessions = $this->getRows('sessions');

$monthStart = date('Y-m-01');

$bookings = array();

if (!empty($operations)) {
	$sessionId = -1;
	foreach ($operations as $operation) {
		if ($operation['event_id'] == 1 || $operation['event_id'] == 2) {
			if (!empty($sessions)) {
				foreach ($sessions as $session) {
					if ($session['id'] > $sessionId && $session['date'] >= $monthStart) {
						$sessionId = $session['id'];
						break;
					}
				}
				$bookings[] = array(
				'element_operation_id' => $operation['id'],
						'session_id' => $sessionId,
					'display_order' => 1,
					'ward_id' => 1
				);
			}
			$sessionId++;
		}
	}
}

return $bookings;
