<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class TransportController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$bookings = $this->getTCIEvents(date('Y-m-d')." 00:00:00", date('Y-m-d')." 23:59:59");

		$this->render('index',array('bookings' => $bookings));
	}

	public function actionList() {
		$bookings = $this->getTCIEvents(date('Y-m-d',strtotime($_POST['date']))." 00:00:00", date('Y-m-d',strtotime($_POST['date']))." 23:59:59");

		$this->renderPartial('/transport/_list',array('bookings' => $bookings));
	}

	public function getTCIEvents($from, $to) {
		$sql = "select element_operation.id as eoid, booking.id as checkid, patient.id as pid, event.id as evid, patient.first_name, patient.last_name, patient.hos_num, site.short_name as location, element_operation.eye, firm.pas_code as firm, element_operation.decision_date, element_operation.urgent, specialty.ref_spec as specialty, session.date as session_date, session.start_time as session_time, element_operation.status, 'Booked' as method, transport_list.id as transport from booking
			join session on booking.session_id = session.id
			join sequence on session.sequence_id = sequence.id
			join theatre on sequence.theatre_id = theatre.id
			join site on theatre.site_id = site.id
			join element_operation on element_operation.id = booking.element_operation_id
			join event on element_operation.event_id = event.id
			join episode on event.episode_id = episode.id
			join firm on episode.firm_id = firm.id
			join service_specialty_assignment on firm.service_specialty_assignment_id = service_specialty_assignment.id
			join specialty on service_specialty_assignment.specialty_id = specialty.id
			join patient on episode.patient_id = patient.id
			left join transport_list on (transport_list.item_table = 'booking' and transport_list.item_id = booking.id)
			where booking.created_date >= '$from' and booking.created_date <= '$to'
			UNION
				select element_operation.id as eoid, booking.id as checkid, patient.id as pid, event.id as evid, patient.first_name, patient.last_name, patient.hos_num, site.short_name as location, element_operation.eye, firm.pas_code as firm, element_operation.decision_date, element_operation.urgent, specialty.ref_spec as specialty, session.date as session_date, session.start_time as session_time, element_operation.status, 'Rescheduled' as method, transport_list.id as transport from booking
			join session on booking.session_id = session.id
			join cancelled_booking on cancelled_booking.element_operation_id = booking.element_operation_id
			join theatre on cancelled_booking.theatre_id = theatre.id
			join site on theatre.site_id = site.id
			join element_operation on element_operation.id = cancelled_booking.element_operation_id
			join event on element_operation.event_id = event.id
			join episode on event.episode_id = episode.id
			join firm on episode.firm_id = firm.id
			join service_specialty_assignment on firm.service_specialty_assignment_id = service_specialty_assignment.id
			join specialty on service_specialty_assignment.specialty_id = specialty.id
			join patient on episode.patient_id = patient.id
			left join transport_list on (transport_list.item_table = 'booking' and transport_list.item_id = booking.id)
			where cancelled_booking.created_date >= '$from' and cancelled_booking.created_date <= '$to' and element_operation.status = 3
			UNION
				select element_operation.id as eoid, cancelled_booking.id as checkid, patient.id as pid, event.id as evid, patient.first_name, patient.last_name, patient.hos_num, site.short_name as location, element_operation.eye, firm.pas_code as firm, element_operation.decision_date, element_operation.urgent, specialty.ref_spec as specialty, cancelled_booking.date as session_date, cancelled_booking.start_time as session_time, element_operation.status, 'Cancelled' as method, transport_list.id as transport from cancelled_booking
			join theatre on cancelled_booking.theatre_id = theatre.id
			join site on theatre.site_id = site.id
			join element_operation on element_operation.id = cancelled_booking.element_operation_id
			join event on element_operation.event_id = event.id
			join episode on event.episode_id = episode.id
			join firm on episode.firm_id = firm.id
			join service_specialty_assignment on firm.service_specialty_assignment_id = service_specialty_assignment.id
			join specialty on service_specialty_assignment.specialty_id = specialty.id
			join patient on episode.patient_id = patient.id
			left join transport_list on (transport_list.item_table = 'cancelled_booking' and transport_list.item_id = cancelled_booking.id)
			where cancelled_booking.created_date >= '$from' and cancelled_booking.created_date <= '$to' and element_operation.status != 3
			ORDER BY session_date, session_time asc";

		return Yii::app()->db->createCommand($sql)->query();
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

		echo "Hospital number,Patient,Session date,Session time,Site,Method,Firm,Specialty,Decision date,Priority\n";

		foreach ($bookings as $booking) {
			echo '"'.$booking['hos_num'].'","'.$booking['last_name'].', '.$booking['first_name'].'","'.$booking['session_date'].'","'.$booking['session_time'].'","'.$booking['location'].'","'.$booking['method'].'","'.$booking['firm'].'","'.$booking['specialty'].'","'.$booking['decision_date'].'","'.($booking['urgent'] ? 'Urgent' : 'Routine').'"'."\n";
		}

		exit;
	}

	public function actionConfirm() {
		if (isset($_POST['booked']) && is_array($_POST['booked'])) {
			foreach ($_POST['booked'] as $booking_id) {
				if (!$c = TransportList::Model()->find('item_table = ? and item_id = ? and status = ?',array('booking',$booking_id,1))) {
					$c = new TransportList;
					$c->item_table = 'booking';
					$c->item_id = $booking_id;
					$c->status = 1;
					$c->save();
				}
			}
		}

		if (isset($_POST['cancelled']) && is_array($_POST['cancelled'])) {
			foreach ($_POST['cancelled'] as $cancelled_booking_id) {
				if (!$c = TransportList::Model()->find('item_table = ? and item_id = ? and status = ?',array('cancelled_booking',$cancelled_booking_id,1))) {
					$c = new TransportList;
					$c->item_table = 'cancelled_booking';
					$c->item_id = $cancelled_booking_id;
					$c->status = 1;
					$c->save();
				}
			}
		}
	}
	
	/**
	 * Print pending transport letters
	 */
	public function actionPrint() {		
		$transport_list_ids = (isset($_REQUEST['transport_lists'])) ? $_REQUEST['transport_lists'] : null;
		if(!is_array($operation_ids)) {
			throw new CHttpException('400', 'Invalid transport list');
		}
		$transport_lists = TransportList::model()->findAllByPk($transport_list_ids);
		
		// Print a letter for each transport requirement, separated by a page break
		$break = false;
		foreach($transport_lists as $transport_list) {
			if($break) {
				$this->renderPartial("/letters/break");
			} else {
				$break = true;
			}
			$patient = $transport_list->patient;
			$this->renderPartial("/transport/transport_form", array(
				'transport' => $transport_list, 
				'patient' => $patient,
			));
		}
	}
}
