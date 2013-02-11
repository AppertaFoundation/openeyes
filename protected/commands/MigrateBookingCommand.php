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

class MigrateBookingCommand extends CConsoleCommand {
	public function run($args) {
		Yii::import('application.modules.OphTrOperation.models.*');

		echo "Migrating element_diagnosis ... ";

		foreach (Yii::app()->db->createCommand("select * from element_diagnosis")->queryAll() as $ed) {
			unset($ed['id']);

			$diagnosis = new Element_OphTrOperation_Diagnosis;

			foreach ($ed as $key => $value) {
				$diagnosis->{$key} = $value;
			}

			if (!$diagnosis->save(true,null,true)) {
				echo "Unable to save diagnosis:\n\n".print_r($diagnosis->getErrors(),true)."\n";
				print_r($ed);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating cancellation_reason ... ";

		foreach (Yii::app()->db->createCommand("select * from cancellation_reason order by id asc")->queryAll() as $cr) {
			$reason = new OphTrOperation_Operation_Cancellation_Reason;

			foreach ($cr as $key => $value) {
				$reason->{$key} = $value;
			}

			if (!$reason->save(true,null,true)) {
				echo "Unable to save cancellation reason:\n\n".print_r($reason->getErrors(),true)."\n";
				print_r($cr);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating element_operation ... ";

		foreach (Yii::app()->db->createCommand("select * from element_operation order by id asc")->queryAll() as $eo) {
			$operation = new Element_OphTrOperation_Operation;

			$eo['status_id'] = $eo['status']+1;

			foreach (array('consultant_required','anaesthetist_required','overnight_stay','schedule_timeframe') as $field) {
				if ($eo[$field] === null) {
					$eo[$field] = 0;
				}
			}

			foreach ($eo as $key => $value) {
				if (!in_array($key,array('schedule_timeframe','status'))) {
					$operation->{$key} = $value;
				}
			}

			if ($co = Yii::app()->db->createCommand("select * from cancelled_operation where element_operation_id = {$eo['id']}")->queryRow()) {
				$operation->cancellation_date = $co['cancelled_date'];
				$operation->cancellation_reason_id = $co['cancelled_reason_id'];
				$operation->cancellation_comment = $co['cancellation_comment'];
				$operation->cancellation_user_id = $co['created_user_id'];
			}

			if (!$operation->save(true,null,true)) {
				echo "Unable to save operation:\n\n".print_r($operation->getErrors(),true)."\n";
				print_r($eo);
				exit;
			}

			$element = Yii::app()->db->createCommand("select * from et_ophtroperation_operation order by id desc limit 1")->queryRow();

			foreach (Yii::app()->db->createCommand("select * from operation_procedure_assignment where operation_id = {$eo['id']}")->queryAll() as $opa) {
				$nopa = new OphTrOperation_Operation_Procedures;
				$nopa->element_id = $element['id'];

				foreach (array('proc_id','display_order','last_modified_user_id','last_modified_date','created_user_id','created_date') as $field) {
					$nopa->{$field} = $opa[$field];
				}

				if (!$nopa->save(true,null,true)) {
					echo "Unable to save procedure assignment:\n\n".print_r($nopa->getErrors(),true)."\n";
					print_r($opa);
					exit;
				}
			}

			$schedule = new Element_OphTrOperation_ScheduleOperation;
			$schedule->event_id = $operation->event_id;
			$schedule->schedule_options_id = 1;
			foreach (array('created_date','created_user_id','last_modified_date','last_modified_user_id') as $field) {
				$schedule->{$field} = $operation->{$field};
			}
			if (!$schedule->save(true,null,true)) {
				echo "Unable to save schedule element:\n\n".print_r($schedule->getErrors(),true)."\n";
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating date_letter_sent ... ";

		foreach (Yii::app()->db->createCommand("select * from date_letter_sent order by id asc")->queryAll() as $dls) {
			$letter = new OphTrOperation_Operation_Date_Letter_Sent;

			$dls['element_id'] = $dls['element_operation_id'];
			unset($dls['element_operation_id']);

			foreach ($dls as $key => $value) {
				$letter->{$key} = $value;
			}

			if (!$letter->save(true,null,true)) {
				echo "Unable to save date_letter_sent:\n\n".print_r($letter->getErrors(),true)."\n";
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating theatre ... ";

		foreach (Yii::app()->db->createCommand("select * from theatre order by id asc")->queryAll() as $t) {
			$theatre = new OphTrOperation_Operation_Theatre;
			$theatre->name = $t['name'];
			$theatre->site_id = $t['site_id'];
			$theatre->code = $t['code'];
			if (!$theatre->save(true,null,true)) {
				echo "Unable to save theatre:\n\n".print_r($theatre->getErrors(),true)."\n";
				print_r($t);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating ward ... ";

		foreach (Yii::app()->db->createCommand("select * from ward order by id asc")->queryAll() as $w) {
			$ward = new OphTrOperation_Operation_Ward;
			$ward->site_id = $w['site_id'];
			$ward->name = $w['name'];
			$ward->restriction = $w['restriction'];
			$ward->code = $w['code'];

			if ($twa = Yii::app()->db->createCommand("select * from theatre_ward_assignment where ward_id = {$w['id']}")->queryRow()) {
				$ward->theatre_id = $twa['theatre_id'];
			}

			if ($w['id'] == 3) {
				$ward->long_name = "Richard Desmond's Children's Eye Centre (RDCEC)";
				$ward->directions = "the Main Reception in the RDCEC";
			}

			if ($w['id'] == 5) {
				$ward->long_name = "Refractive waiting room - Cumberledge Wing 4th Floor";
			}

			if ($w['id'] == 9) {
				$ward->directions = "the Jungle Ward on level 5 of the Lanesborough wing";
			}

			if (!$ward->save(true,null,true)) {
				echo "Unable to save ward:\n\n".print_r($ward->getErrors(),true)."\n";
				print_r($w);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating sequence ... ";

		foreach (Yii::app()->db->createCommand("select * from sequence order by id asc")->queryAll() as $seq) {
			$sequence = new OphTrOperation_Operation_Sequence;

			if ($seq['id'] == 160) {
				$seq['start_date'] = '2011-11-29';
			}

			foreach ($seq as $key => $value) {
				if ($key == 'repeat_interval') {
					$sequence->interval_id = $value+1;
				} else {
					$sequence->{$key} = $value;
				}
			}

			if ($sfa = Yii::app()->db->createCommand("select * from sequence_firm_assignment where sequence_id = {$seq['id']}")->queryRow()) {
				$sequence->firm_id = $sfa['firm_id'];
			}

			if (!$sequence->save(true,null,true)) {
				echo "Unable to save sequence:\n\n".print_r($sequence->getErrors(),true)."\n";
				print_r($seq);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating session ... ";

		foreach (Yii::app()->db->createCommand("select * from session order by id asc")->queryAll() as $ses) {
			$session = new OphTrOperation_Operation_Session;

			foreach ($ses as $key => $value) {
				if ($key == 'status') {
					$session->available = ($value == 0);
				} else {
					$session->{$key} = $value;
				}
			}

			if ($sfa = Yii::app()->db->createCommand("select * from session_firm_assignment where session_id = {$ses['id']}")->queryRow()) {
				$session->firm_id = $sfa['firm_id'];
			}

			if (!$session->save(true,null,true)) {
				echo "Unable to save session:\n\n".print_r($session->getErrors(),true)."\n";
				print_r($ses);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating element_operation_erod ... ";

		foreach (Yii::app()->db->createCommand("select * from element_operation_erod order by id asc")->queryAll() as $er) {
			$erod = new OphTrOperation_Operation_EROD;

			$er['element_id'] = $er['element_operation_id'];
			unset($er['element_operation_id']);

			foreach ($er as $key => $value) {
				$erod->{$key} = $value;
			}

			if (!$erod->save(true,null,true)) {
				echo "Unable to save erod:\n\n".print_r($erod->getErrors(),true)."\n";
				print_r($er);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating erod_rule ... ";

		foreach (Yii::app()->db->createCommand("select * from erod_rule order by id asc")->queryAll() as $er) {
			$erod = new OphTrOperation_Operation_EROD_Rule;

			foreach ($er as $key => $value) {
				$erod->{$key} = $value;
			}

			if (!$erod->save(true,null,true)) {
				echo "Unable to save erod rule:\n\n".print_r($erod->getErrors(),true)."\n";
				print_r($er);
				exit;
			}
		}

		echo "ok\n";

		echo "Migrating erod_rule_item ... ";

		foreach (Yii::app()->db->createCommand("select * from erod_rule_item order by id asc")->queryAll() as $er) {
			$erod = new OphTrOperation_Operation_EROD_Rule_Item;

			foreach ($er as $key => $value) {
				$erod->{$key} = $value;
			}

			if (!$erod->save(true,null,true)) {
				echo "Unable to save erod rule item:\n\n".print_r($erod->getErrors(),true)."\n";
				print_r($er);
				exit;
			}
		}

		echo "ok\n";

		echo "Collecting data from booking ... ";

		$bookings = array();

		$operation_ids = array();

		foreach (Yii::app()->db->createCommand("select * from booking order by id asc")->queryAll() as $b) {
			$booking = new OphTrOperation_Operation_Booking;

			$b['element_id'] = $b['element_operation_id'];
			unset($b['element_operation_id']);

			if (!in_array($b['element_id'],$operation_ids)) {
				$operation_ids[] = $b['element_id'];
			}

			foreach ($b as $key => $value) {
				$booking->{$key} = $value;
			}

			$session = Yii::app()->db->createCommand("select * from session where id = {$b['session_id']}")->queryRow();

			$booking->session_date = $session['date'];
			$booking->session_start_time = $session['start_time'];
			$booking->session_end_time = $session['end_time'];
			$booking->session_theatre_id = $session['theatre_id'];

			if ($tl = Yii::app()->db->createCommand("select id,last_modified_date from transport_list where item_table = 'booking' and item_id = {$b['id']}")->queryRow()) {
				$booking->transport_arranged = 1;
				$booking->transport_arranged_date = $tl['last_modified_date'];
			}

			$bookings[$b['element_id']][$booking->created_date][] = $booking;
		}

		echo "ok\n";

		echo "Collecting data from cancelled_booking ... ";

		foreach (Yii::app()->db->createCommand("select * from cancelled_booking order by id asc")->queryAll() as $cb) {
			if (Yii::app()->db->createCommand("select * from element_operation where id = {$cb['element_operation_id']}")->queryRow()) {
				$cancelled = new OphTrOperation_Operation_Booking;

				$cancelled->element_id = $cb['element_operation_id'];

				if (!in_array($cancelled->element_id,$operation_ids)) {
					$operation_ids[] = $cancelled->element_id;
				}

				if ($session = $this->findSessionForCancelledBooking($cb)) {
					// map cancelled_booking rows to a session
					$cancelled->session_id = $session['id'];
				} else {
					$cancelled->session_id = null;
				}

				// map to ward
				$cancelled->ward_id = $this->getWardForOperationAndTheatre($cb['element_operation_id'],$cb['theatre_id']);

				$cancelled->admission_time = $cb['start_time'];
				$cancelled->session_date = $cb['date'];
				$cancelled->session_start_time = $cb['start_time'];
				$cancelled->session_end_time = $cb['end_time'];
				$cancelled->session_theatre_id = $cb['theatre_id'];
				$cancelled->cancellation_date = $cb['cancelled_date'];
				$cancelled->cancellation_reason_id = $cb['cancelled_reason_id'];
				$cancelled->cancellation_comment = $cb['cancellation_comment'];
				$cancelled->cancellation_user_id = $cb['created_user_id'];
				$cancelled->created_user_id = $cb['created_user_id'];
				$cancelled->created_date = $cb['created_date'];
				$cancelled->last_modified_user_id = $cb['last_modified_user_id'];
				$cancelled->last_modified_date = $cb['last_modified_date'];

				if ($tl = Yii::app()->db->createCommand("select id,last_modified_date from transport_list where item_table = 'cancelled_booking' and item_id = {$cb['id']}")->queryRow()) {
					$cancelled->transport_arranged = 1;
					$cancelled->transport_arranged_date = $tl['last_modified_date'];
				}

				$bookings[$cancelled->element_id][$cancelled->created_date][] = $cancelled;
			}
		}

		echo "ok\n";

		echo "Sorting bookings ... ";

		ksort($bookings);

		foreach ($bookings as $element_id => $dates) {
			foreach ($dates as $date => $bookings_for_date) {
				ksort($bookings[$element_id][$date]);
			}
		}

		echo "ok\n";

		echo "Storing bookings ... ";

		$id = 1;

		foreach ($bookings as $element_id => $dates) {
			$live_booking = false;

			foreach ($dates as $date => $bookings_for_date) {
				foreach ($bookings_for_date as $booking) {
					if ($booking->cancellation_date) {
						$booking->id = $id++;
						if (!$booking->save()) {
							echo "Unable to save booking: ".print_r($booking->getErrors(),true)."\n";
							print_r($booking);
							exit;
						}
					} else {
						$live_booking = $booking;
					}
				}
			}

			if ($live_booking) {
				$live_booking->id = $id++;
				if (!$live_booking->save()) {
					echo "Unable to save booking: ".print_r($live_booking->getErrors(),true)."\n";
					print_r($live_booking);
					exit;
				}
			}
		}

		echo "ok\n";
	}

	public function findSessionForCancelledBooking($cb) {
		$sessions = array();

		foreach (Yii::app()->db->createCommand("select * from session where date = '{$cb['date']}' and start_time = '{$cb['start_time']}' and end_time = '{$cb['end_time']}' and theatre_id = '{$cb['theatre_id']}'")->queryAll() as $session) {
			$sessions[] = $session;
		}

		if (count($sessions) == 1) {
			return $sessions[0];
		}

		$sessions = array();

		foreach (Yii::app()->db->createCommand("select * from session where date = '{$cb['date']}' and start_time = '{$cb['start_time']}' and theatre_id = '{$cb['theatre_id']}'")->queryAll() as $session) {
			$sessions[] = $session;
		}

		if (count($sessions) == 1) {
			return $sessions[0];
		}

		//echo "Unable to infer correct session for cancelled_booking:\n\n".print_r($cb,true)."\n\n";

		foreach ($sessions as $session) {
			echo $session['id']."\n";
		}

		return false;
	}

	public function getWardForOperationAndTheatre($eo_id, $theatre_id) {
		$operation = Yii::app()->db->createCommand("select * from element_operation where id = $eo_id")->queryRow();

		if (!$theatre = Yii::app()->db->createCommand("select * from theatre where id = $theatre_id")->queryRow()) {
			echo "Unable to find theatre: $theatre_id\n";
			exit;
		}

		$wards = $this->getWardOptions($operation, $theatre['site_id'], $theatre['id']);

		return key($wards);
	}

	public function getWardOptions($operation, $siteId, $theatreId = null)
	{
		if (empty($siteId)) {
			throw new Exception('Site id is required.');
		}
		$results = array();
		// if we have a theatre id, see if it has an associated ward
		if (!empty($theatreId)) {
			$ward = Yii::app()->db->createCommand()
				->select('t.ward_id AS id, w.name')
				->from('theatre_ward_assignment t')
				->join('ward w', 't.ward_id = w.id')
				->where('t.theatre_id = :id', array(':id' => $theatreId))
				->queryRow();

			if (!empty($ward)) {
				$results[$ward['id']] = $ward['name'];
			}
		}

		if (empty($results)) {
			// otherwise select by site and patient age/gender
			$event = Yii::app()->db->createCommand("select * from event where id = {$operation['event_id']}")->queryRow();
			$episode = Yii::app()->db->createCommand("select * from episode where id = {$event['episode_id']}")->queryRow();
			$patient = Yii::app()->db->createCommand("select * from patient where id = {$episode['patient_id']}")->queryRow();

			$genderRestrict = $ageRestrict = 0;
			$genderRestrict = ('M' == $patient['gender']) ? 1 : 2;
			$ageRestrict = $this->isChild($patient) ? 4 : 8;

			$whereSql = 's.id = :id AND
				(w.restriction & :r1 > 0) AND (w.restriction & :r2 > 0)';
			$whereParams = array(
				':id' => $siteId,
				':r1' => $genderRestrict,
				':r2' => $ageRestrict
			);

			$wards = Yii::app()->db->createCommand()
				->select('w.id, w.name')
				->from('ward w')
				->join('site s', 's.id = w.site_id')
				->where($whereSql, $whereParams)
				->queryAll();
			$results = array();

			foreach ($wards as $ward) {
				$results[$ward['id']] = $ward['name'];
			}
		}

		return $results;
	}

	function isChild($patient) {
		$age_limit = (isset(Yii::app()->params['child_age_limit'])) ? Yii::app()->params['child_age_limit'] : 16;
		$age = Helper::getAge($patient['dob'], $patient['date_of_death']);
		return ($age < $age_limit);
	}
}
