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
class BookingController extends OphTrOperationbookingEventController
{
    protected static $action_types = array(
        'schedule' => self::ACTION_TYPE_SCHEDULE,
        'reschedule' => self::ACTION_TYPE_SCHEDULE,
        'rescheduleLater' => self::ACTION_TYPE_EDIT,
    );

    public $reschedule = false;
    protected $operation_required = false;
    /** @var Element_OphTrOperation_Operation $operation */
    protected $operation = null;

    protected function beforeAction($action)
    {
        Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/booking.js');
        Yii::app()->assetManager->registerScriptFile('js/jquery.validate.min.js');
        Yii::app()->assetManager->registerScriptFile('js/additional-validators.js');

        return parent::beforeAction($action);
    }

    /**
     * (non-phpdoc).
     *
     * @see BaseEventTypeController::initAction($action)
     */
    protected function initAction($action)
    {
        parent::initAction($action);

        if (!$this->event && in_array(strtolower($action), array('schedule', 'reschedule', 'reschedulelater'))) {
            $this->initWithEventId(@$_GET['id']);
            $this->operation_required = true;
        }

        // setup the Operation that we are concerned with
        if ($this->operation_required) {
            if (!$this->operation = Element_OphTrOperationbooking_Operation::model()->find('event_id = ?', array($this->event->id))) {
                throw new Exception('Operation not found');
            };
        }
    }

    /**
     * Action to schedule an event operation.
     *
     * @throws Exception
     */
    public function actionSchedule()
    {
        if (!$this->title) {
            $this->title = 'Schedule operation';
        }

        $operation = $this->operation;
        $schedule_options = Element_OphTrOperationbooking_ScheduleOperation::model()->find('event_id = ?', array($this->event->id));

        if ($operation->status->name == 'Cancelled') {
            return $this->redirect(array('default/view/'.$this->event->id));
        }

        if (@$_GET['firm_id']) {
            if ($_GET['firm_id'] == 'EMG') {
                $firm = new Firm();
                $firm->name = 'Emergency List';
            } else {
                if (!$firm = Firm::model()->findByPk(@$_GET['firm_id'])) {
                    throw new Exception('Unknown firm id: '.$_GET['firm_id']);
                }
            }
        } else {
            $firm = $this->firm;
        }

        // allowing the referral to be updated
        if (@$_GET['referral_id']) {
            if ($referral = Referral::model()->findByPk($_GET['referral_id'])) {
                $operation->referral_id = $_GET['referral_id'];
                $operation->referral = $referral;
            }
        }

        if (preg_match('/^([0-9]{4})([0-9]{2})$/', @$_GET['date'], $m)) {
            $date = mktime(0, 0, 0, $m[2], 1, $m[1]);
        } else {
            $date = $operation->minDate;
        }

        if (ctype_digit(@$_GET['day'])) {
            $selectedDate = date('Y-m-d', mktime(0, 0, 0, date('m', $date), $_GET['day'], date('Y', $date)));
            $theatres = $operation->getTheatres($selectedDate, $firm->id);

            if ($session = OphTrOperationbooking_Operation_Session::model()->findByPk(@$_GET['session_id'])) {
                $criteria = new CDbCriteria();
                $criteria->compare('session_id', $session->id);
                $criteria->addCondition('`t`.booking_cancellation_date is null');
                $criteria->addCondition('event.deleted = 0');
                $criteria->order = 'display_order ASC';
                //FIXME: this should be retrieved by a method on the operation
                $bookings = OphTrOperationbooking_Operation_Booking::model()->with(array('operation' => array('with' => 'event')))->findAll($criteria);

                foreach ($theatres as $theatre) {
                    foreach ($theatre->sessions as $_session) {
                        if ($session->id == $_session->id) {
                            $bookable = $_session->operationBookable($operation);
                            $there_is_place_for_complex_booking = $_session->isTherePlaceForComplexBooking($operation);
                        }
                    }
                }

                if (!empty($_POST['Booking']['element_id'])) {
                    if (!$operation = Element_OphTrOperationbooking_Operation::model()->findByPk($_POST['Booking']['element_id'])) {
                        throw new Exception('Operation not found: '.$_POST['Booking']['element_id']);
                    }

                    $transaction = Yii::app()->db->beginTransaction();

                    try {
                        $cancellation_data = array(
                            'submitted' => isset($_POST['cancellation_reason']),
                            'reason_id' => @$_POST['cancellation_reason'],
                            'comment' => @$_POST['cancellation_comment'],
                        );

                        $booking = new OphTrOperationbooking_Operation_Booking();
                        $booking->attributes = $_POST['Booking'];

                        // referral might have been altered in scheduling form, so should update the operation here
                        // (different from the GET changes above which handle the selection down to the session)
                        if ($operation->canChangeReferral()) {
                            $operation->referral_id = $_POST['Operation']['referral_id'];
                        }

                        $result = $operation->schedule(
                            $booking,
                            $_POST['Operation']['comments'],
                            $_POST['Session']['comments'],
                            $_POST['Operation']['comments_rtt'],
                            $this->reschedule,
                            $cancellation_data,
                            $schedule_options
                        );

                        if ($result !== true) {
                            foreach ($result as $attribute => $message) {
                                $errors[$operation->getAttributeLabel($attribute)] = $message;
                            }
                        } else {
                            $transaction->commit();
                            $this->redirect(array('default/view/'.$operation->event_id));
                        }
                    } catch (RaceConditionException $e) {
                        $transaction->rollback();
                        Yii::app()->user->setFlash('notice', $e->getMessage());
                        $this->redirect(array('default/view/'.$operation->event_id));
                    } catch (Exception $e) {
                        // no handling of this at the moment
                        $transaction->rollback();
                        throw $e;
                    }
                } else {
                    $_POST['Booking']['admission_time'] = substr($session['default_admission_time'], 0, 5);
                    $_POST['Booking']['ward_id'] = key($operation->getWardOptions($session));
                    $_POST['Session']['comments'] = $session['comments'];
                    $_POST['Operation']['referral_id'] = $operation->referral_id;
                    $_POST['Operation']['comments'] = $operation->comments;
                    $_POST['Operation']['comments_rtt'] = $operation->comments_rtt;
                }
            }
        } elseif ($operation->booking) {
            $selectedDate = $operation->booking->session->date;
        }

        $this->processJsVars();

        $criteria = new \CDbCriteria();
        $criteria->addCondition('active = 1');
        $criteria->compare('institution_id', Yii::app()->session['selected_institution_id']);
        $criteria->distinct = true;
        $criteria->join = "JOIN ophtroperationbooking_operation_session session ON t.id = session.firm_id";

        $booked_firm_list = \Firm::model()->findAll($criteria);

        $this->render('schedule', array(
            'event' => $this->event,
            'operation' => $operation,
            'schedule_options' => $schedule_options,
            'firm' => $firm,
            'firm_list' => $booked_firm_list,
            'date' => $date,
            'selectedDate' => isset($selectedDate) ? $selectedDate : null,
            'sessions' => $operation->getFirmCalendarForMonth($firm, $date, $schedule_options),
            'theatres' => isset($theatres) ? $theatres : null,
            'session' => isset($session) ? $session : null,
            'bookings' => isset($bookings) ? $bookings : null,
            'bookable' => isset($bookable) ? $bookable : null,
            'there_is_place_for_complex_booking' => isset($there_is_place_for_complex_booking) ?
                $there_is_place_for_complex_booking : null,
            'errors' => isset($errors) ? $errors : null,
        ));
    }

    /**
     * Reschedule an operation for the given event.
     *
     * @param $id
     */
    public function actionReschedule($id)
    {
        $this->title = 'Reschedule operation';
        $this->reschedule = true;
        $this->operation->reschedule = true;

        return $this->actionSchedule($id);
    }

    /**
     * Cancels and reschedules an operation.
     *
     * @throws Exception
     */
    public function actionRescheduleLater()
    {
        $this->show_element_sidebar = false;
        $operation = $this->operation;

        if (in_array($operation->status->name, array('Requires scheduling', 'Requires rescheduling', 'Cancelled'))) {
            return $this->redirect(array('default/view/'.$this->event->id));
        }

        $this->patient = $operation->event->episode->patient;
        $this->title = 'Reschedule later';

        $errors = array();

        if (!empty($_POST)) {
            if (strlen($_POST['cancellation_comment']) > 200) {
                $errors[] = 'Comments must be 200 characters max';
            }
            if (!$reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->findByPk($_POST['cancellation_reason'])) {
                $errors[] = 'Please select a rescheduling reason';
            } else {
                $comment = isset($_POST['cancellation_comment']) ? $_POST['cancellation_comment'] : null;

                $is_cancelled = $operation->cancel($_POST['cancellation_reason'], $comment)['result'];

                if ($is_cancelled) {
                    $operation->setStatus('Requires rescheduling');

                    if (!$operation->event->hasIssue('Operation requires scheduling')) {
                        $operation->event->addIssue('Operation requires scheduling');
                    }
                }

                $this->redirect(array('default/view/'.$this->event->id));
            }
        }

        $this->processJsVars();

        $this->render('reschedule_later', array(
            'operation' => $operation,
            'date' => $operation->minDate,
            'patient' => $operation->event->episode->patient,
            'errors' => $errors,
        ));
    }
}
