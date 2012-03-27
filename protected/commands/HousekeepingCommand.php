<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class HousekeepingCommand extends CConsoleCommand {

	public function getName() {
		return 'Housekeeping Command.';
	}

	public function getHelp() {
		return "Various housekeeping procedures.\n";
	}

	public function run($args) {
		$this->deceasedPatients();
	}

	// Check for operations where patient is deceased and cancel them
	protected function deceasedPatients() {

		// TODO: This needs to be made more robust
		$cancellation_reason = CancellationReason::model()->find("text = 'Patient has died'");
		if(!$cancellation_reason) {
			throw new CException('Cannot find cancellation code for "patient has died"');
		}

		// We only want to cancel operations that have not been booked, or have a current booking
		$operations = ElementOperation::model()->findAll(array(
			'condition' => "(session.date > NOW() OR session.date IS NULL) AND t.status != :cancelled AND patient.date_of_death IS NOT NULL AND patient.date_of_death < NOW()",
			'with' => array('event', 'event.episode', 'event.episode.patient', 'booking', 'booking.session'),
			'params' => array(
				':cancelled' => ElementOperation::STATUS_CANCELLED,
		),
		));
		foreach($operations as $operation) {
			$operation->cancel($cancellation_reason->id, 'Booking cancelled automatically');
		}

	}

}
