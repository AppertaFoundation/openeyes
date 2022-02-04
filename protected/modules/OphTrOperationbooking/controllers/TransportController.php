<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class TransportController extends BaseModuleController
{
    public $layout = '//layouts/main';
    public $items_per_page = 100;
    public $page = 1;
    public $total_items = 0;
    public $pages = 1;
    public $renderPatientPanel = false;

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'TCIs'),
                'roles' => array('OprnViewClinical'),
            ),
            array('allow',
                'actions' => $this->printActions(),
                'roles' => array('OprnPrint'),
            ),
            array('allow',
                'actions' => array('confirm'),
                'roles' => array('OprnConfirmTransport'),
            ),
        );
    }

    /**
     * @return array
     *               (non-phpdoc)
     *
     * @see parent::printActions()
     */
    public function printActions()
    {
        return array(
            'print', 'printList', 'downloadCsv',
        );
    }

    /**
     * initial view loads framework for TCI list - actual list is loaded through ajax request.
     */
    public function actionIndex()
    {
        !isset($_GET['include_bookings']) and $_GET['include_bookings'] = 1;
        !isset($_GET['include_reschedules']) and $_GET['include_reschedules'] = 1;
        !isset($_GET['include_cancellations']) and $_GET['include_cancellations'] = 1;

        $this->render('index');
    }

    /**
     * Ajax action to load the list of TCIs based on the request criteria.
     */
    public function actionTCIs()
    {
        if (ctype_digit(@$_GET['page'])) {
            $this->page = $_GET['page'];
        }
        $this->renderPartial('_list', array('operations' => $this->getTransportList($_GET)));
    }

    /**
     * Get all the operations that have TCI requirements based on the given criteria.
     *
     * @param $data
     * @param bool $all
     *
     * @return Element_OphTrOperationbooking_Operation[]
     */
    public function getTransportList($data, $all = false)
    {
        if (!empty($data)) {
            if (preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/', @$data['date_from']) && preg_match('/^[0-9]+ [a-zA-Z]{3} [0-9]{4}$/', @$data['date_to'])) {
                $date_from = Helper::convertNHS2MySQL($data['date_from']).' 00:00:00';
                $date_to = Helper::convertNHS2MySQL($data['date_to']).' 23:59:59';
            }
        }

        !isset($data['include_bookings']) and $data['include_bookings'] = 1;
        !isset($data['include_reschedules']) and $data['include_reschedules'] = 1;
        !isset($data['include_cancellations']) and $data['include_cancellations'] = 1;

        if (!@$data['include_bookings'] && !@$data['include_reschedules'] && !@$data['include_cancellations']) {
            $data['include_bookings'] = 1;
        }

        $criteria = new CDbCriteria();

        $criteria->addCondition('transport_arranged = :zero or transport_arranged_date = :today');
        $criteria->params[':zero'] = 0;
        $criteria->params[':today'] = date('Y-m-d');
        $criteria->params[':six'] = 6;

        if (@$date_from && @$date_to) {
            $criteria->addCondition('session_date >= :fromDate and session_date <= :toDate');
            $criteria->params[':fromDate'] = $date_from;
            $criteria->params[':toDate'] = $date_to;
        }

        if (!$data['include_bookings']) {
            $criteria->addCondition('latestBooking.booking_cancellation_date is not null or status_id != :two');
            $criteria->params[':two'] = 2;
        }

        if (!$data['include_reschedules']) {
            $criteria->addCondition('latestBooking.booking_cancellation_date is not null or status_id = :two');
            $criteria->params[':two'] = 2;
        }

        if (!$data['include_cancellations']) {
            $criteria->addCondition('latestBooking.booking_cancellation_date is null');
        }

        if (!empty(Yii::app()->params['transport_exclude_sites'])) {
            $criteria->addNotInCondition('site.id', Yii::app()->params['transport_exclude_sites']);
        }

        if (!empty(Yii::app()->params['transport_exclude_theatres'])) {
            $criteria->addNotInCondition('theatre_id', Yii::app()->params['transport_exclude_theatres']);
        }

        $criteria->addCondition('session.date >= :today');

        $criteria->addCondition('event.deleted = 0 and episode.deleted = 0');

        $this->total_items = Element_OphTrOperationbooking_Operation::model()
            ->with(array(
                'latestBooking' => array(
                    'with' => array(
                        'session' => array(
                            'with' => array(
                                'theatre' => array(
                                    'with' => 'site',
                                ),
                            ),
                        ),
                    ),
                ),
                'event' => array(
                    'joinType' => 'JOIN',
                    'with' => array(
                        'episode' => array(
                            'joinType' => 'JOIN',
                        ),
                    ),
                ),
            ))
            ->count($criteria);

        $this->pages = ceil($this->total_items / $this->items_per_page);

        if (!$all) {
            $criteria->limit = $this->items_per_page;
            $criteria->offset = ($this->items_per_page * ($this->page - 1));
        }

        $criteria->order = 'session_date, session_start_time, decision_date';

        Yii::app()->event->dispatch('start_batch_mode');

        return Element_OphTrOperationbooking_Operation::model()
            ->with(array(
                'latestBooking' => array(
                    'with' => array(
                        'session',
                        'theatre' => array(
                            'with' => 'site',
                        ),
                        'ward',
                    ),
                ),
                'event' => array(
                    'joinType' => 'JOIN',
                    'with' => array(
                        'episode' => array(
                            'joinType' => 'JOIN',
                            'with' => array(
                                'patient' => array(
                                    'with' => 'contact',
                                ),
                                'firm' => array(
                                    'with' => array(
                                        'serviceSubspecialtyAssignment' => array(
                                            'with' => 'subspecialty',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'priority',
            ))
            ->findAll($criteria);
    }

    /**
     * Print the list based on given criteria.
     */
    public function actionPrintList()
    {
        if (ctype_digit(@$_GET['page'])) {
            $this->page = $_GET['page'];
        }
        $this->renderPartial('_printList', array('operations' => $this->getTransportList($_GET, true)));
    }

    /**
     * Print transport letters for bookings.
     */
    public function actionPrint($id)
    {
        $operation_ids = (isset($_GET['operations'])) ? $_GET['operations'] : null;
        if (!is_array($booking_ids)) {
            throw new CHttpException('400', 'Invalid operation list');
        }
        $bookings = OphTrOperationbooking_Operation_Booking::model()->findAllByPk($booking_ids);

        // Print a letter for booking, separated by a page break
        $break = false;
        foreach ($bookings as $booking) {
            if ($break) {
                $this->renderPartial('letters/break');
            } else {
                $break = true;
            }
            $patient = $booking->operation->event->episode->patient;
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
            $this->renderPartial('transport/transport_form', array(
                'booking' => $booking,
                'patient' => $patient,
                'transport' => $transport,
            ));
        }
    }

    /**
     * Ajax method to mark the given operations with transport arranged.
     *
     * @throws Exception
     */
    public function actionConfirm()
    {
        if (is_array(@$_POST['operations'])) {
            foreach ($_POST['operations'] as $operation_id) {
                if (!$operation = Element_OphTrOperationbooking_Operation::model()->with('latestBooking')->findByPk($operation_id)) {
                    throw new Exception('Operation not found: '.$operation_id);
                }

                $booking = $operation->latestBooking;

                if (!$booking->transport_arranged) {
                    $booking->transport_arranged = 1;
                    $booking->transport_arranged_date = date('Y-m-d');

                    if (!$booking->save(true, null, true)) {
                        throw new Exception('Unable to save booking: '.print_r($booking->getErrors(), true));
                    }
                }
            }
        }

        echo '1';
    }

    /**
     * Download a CSV of the operations requiring transport changes.
     */
    public function actionDownloadcsv()
    {
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=transport.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $institution_id = Institution::model()->getCurrent()->id;
        $site_id = Yii::app()->session['selected_site_id'];

        echo PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(Yii::app()->params['display_primary_number_usage_code'], $institution_id, $site_id) . ",First name,Last name,TCI date,Admission time,Site,Ward,Method,Firm,Specialty,DTA,Priority\n";

        $operations = $this->getTransportList($_POST, true);

        foreach ($operations as $operation) {
            echo '"' . PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $operation->event->episode->patient->id, $institution_id, $site_id)) . '","' .
                trim($operation->event->episode->patient->first_name) . '","' .
                trim($operation->event->episode->patient->last_name) . '","' .
                date('j-M-Y', strtotime($operation->latestBooking->session_date)) . '","' .
                substr($operation->latestBooking->session_start_time, 0, 5) . '","' .
                $operation->latestBooking->theatre->site->shortName . '","' .
                ($operation->latestBooking->ward ? $operation->latestBooking->ward->name : 'N/A') . '","' .
                $operation->transportStatus . '","' .
                $operation->event->episode->firm->pas_code . '","' .
                $operation->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->ref_spec . '","' .
                $operation->NHSDate('decision_date') . '","' .
                $operation->priority->name . '"' . "\n";
        }
    }

    public function getUriAppend()
    {
        $return = '';
        foreach (array('date_from', 'date_to', 'include_bookings' => 0, 'include_reschedules' => 0, 'include_cancellations' => 0) as $token) {
            if (isset($_GET[$token])) {
                $return .= '&'.$token.'='.$_GET[$token];
            }
        }

        return $return;
    }
}
