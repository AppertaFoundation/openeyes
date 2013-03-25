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

class TransportController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';
	public $items_per_page = 100;
	public $page = 1;
	public $total_items = 0;
	public $pages = 1;

	public function accessRules() {
		return array(
			// Level 2 or below can't change anything or print
			array('deny',
				'actions' => array('confirm', 'print'),
				'expression' => '!BaseController::checkUserLevel(3)',
			),
			// Level 2 or above can do anything else
			array('allow',
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			// Deny anything else (default rule allows authenticated users)
			array('deny'),
		);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if (ctype_digit(@$_GET['page'])) $this->page = $_GET['page'];

		$this->render('index',array('bookings' => $this->getBookings()));
	}

	public function getBookings() {
		if (!empty($_REQUEST)) {
			if (preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/',@$_REQUEST['date_from']) &&
				preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/',@$_REQUEST['date_to'])) {

				$date_from = Helper::convertNHS2MySQL($_REQUEST['date_from'])." 00:00:00";
				$date_to = Helper::convertNHS2MySQL($_REQUEST['date_to'])." 23:59:59";
			}
		} else {
			$_REQUEST['include_bookings'] = 1;
			$_REQUEST['include_reschedules'] = 1;
			$_REQUEST['include_cancellations'] = 1;
		}

		if (!@$_REQUEST['include_bookings'] && !@$_REQUEST['include_reschedules'] && !@$_REQUEST['include_cancellations']) {
			$_REQUEST['include_bookings'] = 1;
		}

		return $this->getTCIEvents(@$date_from, @$date_to, (boolean)@$_REQUEST['include_bookings'], (boolean)@$_REQUEST['include_reschedules'], (boolean)@$_REQUEST['include_cancellations']);
	}

	public function getTCIEvents($from, $to, $include_bookings, $include_reschedules, $include_cancellations) {
		$today = date('Y-m-d');

		if (!$include_bookings && !$include_reschedules && !$include_cancellations) {
			$this->total_items = $this->pages = 0;
			return array('bookings' => array(), 'bookings_all' => array());
		}

		if ($from && $to) {
			$wheresql1 = " and session.date >= '$from' and session.date <= '$to' ";
			$wheresql2 = " and cb.date >= '$from' and cb.date <= '$to' ";
		} else {
			$wheresql1 = $wheresql2 = null;
		}

		$offset = ($this->items_per_page * ($this->page-1));

		$data = array();
		$data_all = array();

		$exclude_sites = array(3,5);
		$exclude_theatres = array();

		// cache theatre->site associations for page load speed
		$sites = array();
		foreach (Yii::app()->db->createCommand()
			->select('theatre.id, site.name, site.short_name, site.id as site_id')
			->from('theatre')
			->join('site','theatre.site_id = site.id')
			->queryAll() as $row) {
			if (in_array($row['site_id'],$exclude_sites)) {
				$exclude_theatres[] = $row['id'];
			} else {
				$sites[$row['id']] = $row['short_name'] ? $row['short_name'] : $row['name'];
			}
		}

		if (!empty($exclude_theatres)) {
			$wheresql3 = ' and session.theatre_id not in ('.implode(',',$exclude_theatres).') ';
			$wheresql4 = ' and cb.theatre_id not in ('.implode(',',$exclude_theatres).') ';
		} else {
			$wheresql3 = $wheresql4 = '';
		}

		foreach (Yii::app()->db->createCommand()
			->selectDistinct('element_operation.id as eoid, element_operation.priority_id, booking.id as booking_id, patient.id as pid, event.id as evid, contact.first_name,
				contact.last_name, patient.hos_num, element_operation.eye_id, firm.pas_code as firm, element_operation.decision_date, subspecialty.ref_spec as subspecialty,
				session.date as session_date, session.start_time as session_time, element_operation.status, transport_list.id as transport, transport_list2.id as transport2,
				coalesce(booking.created_date,cb.created_date) as order_created_date, ward.name as ward_name, cb.id as cancelled_booking_id, cb.date as cancelled_session_date,
				session.theatre_id, coalesce(session.date,cb.date) as order_date, cb.start_time as cancelled_session_time, session.id as session_id, cb.theatre_id as theatre_id2,
				coalesce(session.start_time,cb.start_time) as order_time, cb.date as cancelled_booking_date')
			->from('element_operation')
			->join('event','element_operation.event_id = event.id')
			->join('episode','event.episode_id = episode.id')
			->join('firm','episode.firm_id = firm.id')
			->join('service_subspecialty_assignment','firm.service_subspecialty_assignment_id = service_subspecialty_assignment.id')
			->join('subspecialty','service_subspecialty_assignment.subspecialty_id = subspecialty.id')
			->join('patient','episode.patient_id = patient.id')
			->join('contact',"contact.parent_id = patient.id and contact.parent_class = 'Patient'")
			->leftJoin('booking','booking.element_operation_id = element_operation.id')
			->leftJoin('transport_list',"transport_list.item_table = 'booking' and transport_list.item_id = booking.id")
			->leftJoin('ward','booking.ward_id = ward.id')
			->leftJoin('(select created_date,date,start_time,theatre_id,element_operation_id,max(id) as maxid from cancelled_booking group by element_operation_id) cb2',"element_operation.id = cb2.element_operation_id")
			->leftJoin("cancelled_booking cb","cb.id = cb2.maxid and cb.date >= '$today'")
			->leftJoin('transport_list transport_list2',"transport_list2.item_table = 'cancelled_booking' and transport_list2.item_id = cb.id")
			->leftJoin('session',"booking.session_id = session.id and session.date >= '$today'")
			->where("(event.deleted = 0 or event.deleted is null) and (episode.deleted = 0 or episode.deleted is null) and ((booking.id is not null $wheresql1 $wheresql3) or (booking.id is null and cb.id is not null $wheresql2 $wheresql4 )) and (transport_list.id is null or substr(transport_list.last_modified_date,1,10) = '$today') and (transport_list2.id is null or substr(transport_list2.last_modified_date,1,10) = '$today')")
			->order("order_date asc, order_time asc, order_created_date desc")
			->queryAll() as $i => $row) {

			if ($row['booking_id']) {
				if ($row['status'] == 3) {
					$row['method'] = 'Rescheduled';
				} else {
					$row['method'] = 'Booked';
				}
				$ts = strtotime($row['session_date'].' '.$row['session_time']);
				$row['checkid'] = $row['booking_id'];
				$row['location'] = $sites[$row['theatre_id']];
			} else {
				$row['method'] = 'Cancelled';
				$ts = strtotime($row['cancelled_session_date'].' '.$row['cancelled_session_time']);
				$row['checkid'] = $row['cancelled_booking_id'];
				$row['session_date'] = $row['cancelled_session_date'];
				$row['session_time'] = $row['cancelled_session_time'];
				$row['ward_name'] = 'Unknown';
				$row['location'] = $sites[$row['theatre_id2']];
				$row['transport'] = $row['transport2'];
			}

			if (($include_bookings && $row['method'] == 'Booked') || ($include_reschedules && $row['method'] == 'Rescheduled') || ($include_cancellations && $row['method'] == 'Cancelled')) {
				if (count($data_all) >= $offset && count($data) < $this->items_per_page) {
					while (isset($data[$ts])) $ts++;
					$data[$ts] = $row;
				}
				while (isset($data_all[$ts])) $ts++;
				$data_all[$ts] = $row;
			}
		}

		ksort($data);
		ksort($data_all);

		$this->total_items = count($data_all);
		$this->pages = ceil($this->total_items / $this->items_per_page);

		return array('bookings' => $data, 'bookings_all' => $data_all);
	}

	public function actionDigest() {
		$times = Yii::app()->params['transport_csv_intervals'];

		foreach ($times as $i => $time) {
			if ($_GET['time'] == preg_replace('/:/','',$time)) {
				if ($i == 0) {
					$from = strtotime($_GET['date'].' '.$times[count($times)-1]) - 86400;
					$to = strtotime($_GET['date'].' '.$_GET['time']);
				} else {
					$from = strtotime($_GET['date'].' '.$last_time);
					$to = strtotime($_GET['date'].' '.$_GET['time']);
				}
				break;
			}

			$last_time = $time;
		}

		header("Content-Type: text/plain");
		header("Content-Description: File Transfer");
		header('Content-disposition: attachment; filename="'.$_GET['date'].'_'.$_GET['time'].'.csv"');
		header("Content-Transfer-Encoding: binary");

		$bookings = $this->getTCIEvents(date('Y-m-d H:i:s',$from), date('Y-m-d H:i:s',$to));

		echo "Hospital number,Patient,Session date,Session time,Site,Method,Firm,Subspecialty,Decision date,Priority\n";

		foreach ($bookings['bookings_all'] as $booking) {
			echo '"'.$booking['hos_num'].'","'.$booking['last_name'].', '.$booking['first_name'].'","'.$booking['session_date'].'","'.$booking['session_time'].'","'.$booking['location'].'","'.$booking['method'].'","'.$booking['firm'].'","'.$booking['subspecialty'].'","'.$booking['decision_date'].'","'.$booking['priority'].'"'."\n";
		}

		Yii::app()->end();
	}

	/**
	 * Print transport letters for bookings
	 */
	public function actionPrint() {
		$booking_ids = (isset($_REQUEST['booked'])) ? $_REQUEST['booked'] : null;
		if(!is_array($booking_ids)) {
			throw new CHttpException('400', 'Invalid booking list');
		}
		$bookings = Booking::model()->findAllByPk($booking_ids);
		
		// Print a letter for booking, separated by a page break
		$break = false;
		foreach($bookings as $booking) {
			if($break) {
				$this->renderPartial("/letters/break");
			} else {
				$break = true;
			}
			$patient = $booking->elementOperation->event->episode->patient;
			$transport = array(
				'request_to' => 'FIXME: REQUEST TO',
				'request_from' => 'FIXME: REQUEST FROM',
				'escort' => '', // FIXME: No source yet
				'mobility' => '', // FIXME: No source yet
				'oxygen' => '', // FIXME: No source yet
				'contact_name' => 'FIXME: CONTACT NAME',
				'contact_number' => 'FIXME: CONTACT NUMBER',
				'comments' => '', // FIXME: No source yet
			);
			$this->renderPartial("/transport/transport_form", array(
				'booking' => $booking, 
				'patient' => $patient,
				'transport' => $transport,
			));
		}
	}

	public function actionConfirm() {
		if (isset($_REQUEST['booked']) && is_array($_REQUEST['booked'])) {
			foreach ($_REQUEST['booked'] as $booking_id) {
				$c = TransportList::Model()->find('item_table = ? and item_id = ? and status = ?',array('booking',$booking_id,1));

				if (!$c) {
					$c = new TransportList;
					$c->item_table = 'booking';
					$c->item_id = $booking_id;
					$c->status = 1;
					if (!$c->save()) {
						throw new SystemException('Unable to save transport_list item: '.print_r($c->getErrors(),true));
					}
				} else {
					/*
					if (!$c->delete()) {
						throw new SystemException('Unable to delete transport_list item: '.print_r($c->getErrors(),true));
					}
					*/
				}
			}
		}

		if (isset($_REQUEST['cancelled']) && is_array($_REQUEST['cancelled'])) {
			foreach ($_REQUEST['cancelled'] as $cancelled_booking_id) {
				if ($cancelled_booking = CancelledBooking::model()->findByPk($cancelled_booking_id)) {
					foreach (CancelledBooking::model()->findAll('element_operation_id=?',array($cancelled_booking->element_operation_id)) as $cb) {
						$c = TransportList::Model()->find('item_table = ? and item_id = ? and status = ?',array('cancelled_booking',$cb->id,1));

						if (!$c) {
							$c = new TransportList;
							$c->item_table = 'cancelled_booking';
							$c->item_id = $cb->id;
							$c->status = 1;
							if (!$c->save()) {
								throw new SystemException('Unable to save transport_list item: '.print_r($c->getErrors(),true));
							}
						} else {
							/*
							if (!$c->delete()) {
								throw new SystemException('Unable to delete transport_list item: '.print_r($c->getErrors(),true));
							}
							*/
						}
					}
				}
			}
		}

		die("1");
	}

	public function actionDownloadcsv() {
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=transport.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "Hospital number,First name,Last name,TCI date,Admission time,Site,Ward,Method,Firm,Specialty,DTA,Priority\n";

		$data = $this->getBookings();

		foreach ($data['bookings_all'] as $row) {
			echo '"'.$row['hos_num'].'","'.trim($row['first_name']).'","'.trim($row['last_name']).'","'.$row['order_date'].'","'.$row['order_time'].'","'.$row['location'].'","'.$row['ward_name'].'","'.$row['method'].'","'.$row['firm'].'","'.$row['subspecialty'].'","'.$row['decision_date'].'","'.($row['priority_id'] == 1 ? 'Routine' : 'Urgent').'"'."\n";
		}
	}
	
	public function getUriAppend() {
		$return = array();
		foreach(array(	'date_from' => '', 'date_to' => '', 'include_bookings' => 0, 'include_reschedules' => 0, 'include_cancellations' => 0) as $token => $value) {
			if(isset($_REQUEST[$token])) {
				$return[] = $_REQUEST[$token];
			} else {
				$return[] = $value;
			}
		}
		return '/' . implode('/', $return);
	}
	
}
