<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PatientEventController extends BaseController
{
    /**
     * @var Firm
     */
    public $firm;
    /**
     * @var Patient
     */
    public $patient;

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('create'),
                'users' => array('@'),
            )
        );
    }

    public function behaviors()
    {
        return array(
            'CreateEventBehavior' => array(
                'class' => 'application.behaviors.CreateEventControllerBehavior',
            ),
        );
    }

    /**
     * @param $request
     * @return Patient
     * @throws CHttpException
     */
    protected function resolvePatient($request)
    {
        $patient_id = $request->getQuery('patient_id');
        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(404, 'Patient not found.');
        }
        $this->patient = $patient;
        return $patient;
    }

    /**
     * @param $request
     * @return Firm
     * @throws CHttpException
     */
    protected function resolveContext($request)
    {
        if (!$context_id = $request->getQuery('context_id')) {
            throw new CHttpException(400, 'Invalid request.');
        }
        if (!$context = Firm::model()->findByPk($context_id)) {
            throw new CHttpException(404, 'Context firm not found.');
        }

        // set the firm attribute for compatibility with arguments for perm checking
        $this->firm = $context;

        return $context;
    }

    /**
     * @param $request
     * @return EventType
     * @throws CHttpException
     */
    protected function resolveEventType($request)
    {
        if (!$event_type_id = $request->getQuery('event_type_id')) {
            throw new CHttpException(400, 'Invalid request.');
        }
        if (!$event_type = EventType::model()->findByPk($event_type_id)) {
            throw new CHttpException(404, 'Event type not found.');
        }
        $args = $this->getCreateArgsForEventTypeOprn($event_type);
        if (!call_user_func_array(array($this, 'checkAccess'), $args)) {
            throw new CHttpException(403, 'Permission denied for creating event type.');
        }

        return $event_type;
    }

    /**
     * @param Patient $patient
     * @param $request
     * @return Episode
     * @throws CHttpException
     */
    protected function resolveEpisode(Patient $patient, $request)
    {
        if ($episode_id = $request->getQuery('episode_id')) {
            if (!$episode = Episode::model()->findByPk($episode_id)) {
                throw new CHttpException(404, 'Episode not found.');
            }
            return $episode;
        } else {
            if (!$service_id = $request->getQuery('service_id')) {
                throw new CHttpException(404, 'Invalid request.');
            }
            if (!$service = Firm::model()->findByPk($service_id)) {
                throw new CHttpException(404, 'Service firm not found.');
            }
            if ($episode = $patient->getOpenEpisodeOfSubspecialty($service->getSubspecialtyID())) {
                if ($episode->firm_id != $service->id) {
                    throw new CHttpException(404, 'Service mismatch with existing Episode for patient.');
                }
                return $episode;
            } else {
                if (!$this->checkAccess('OprnCreateEpisode')) {
                    throw new CHttpException(403, 'Permissioned denied for creating a new Episode.');
                }
                return $patient->addEpisode($service);
            }
        }
    }

    /**
     * @param Firm $context
     * @throws CHttpException
     */
    protected function setContext(Firm $context)
    {
        // TODO: tidy this up (some kind of abstraction that SiteAndFirmWidget can use consistently)
        // get the user
        $user_id = $this->getApp()->user->id;
        $user = User::model()->findByPk($user_id);

        // set the firm on the user (process taken from SiteAndFirmWidget)
        $user->changeFirm($context->id);
        if (!$user->save(false)) {
            throw new CHttpException(404, 'Unexpected error setting user context.');
        }
        $user->audit('user', 'change-firm', $user->last_firm_id);
        $this->getApp()->session['selected_firm_id'] = $context->id;
    }

    /**
     * Handles the request to carry out required back end actions before redirecting to the appropriate controller
     * action for the event to be created.
     *
     * @throws CHttpException
     */
    public function actionCreate()
    {
        $app = $this->getApp();
        $request = $app->getRequest();

        if ($request->getQuery('step_id')) {
            Yii::app()->session['active_worklist_patient_id'] = $request->getQuery('worklist_patient_id');
            Yii::app()->session['active_step_id'] = $request->getQuery('step_id');
            $step = PathwayStep::model()->findByPk($request->getQuery('step_id'));
            if (!$step) {
                throw new CHttpException(404, 'Unable to retrieve associated worklist step.');
            }
            Yii::app()->session['active_step_state_data'] = json_decode(
                $step->state_data,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        $patient = $this->resolvePatient($request);
        $context = $this->resolveContext($request);
        $event_type = $this->resolveEventType($request);

        $episode = $this->resolveEpisode($patient, $request);
        if ($episode->getSubspecialtyID() != $context->getSubspecialtyID()) {
            throw new CHttpException(400, 'Episode/Context mismatch');
        }
        $this->setContext($context);

        $this->redirect(
            $app->createUrl($event_type->class_name . '/Default/create') . '?patient_id=' . $patient->id
        );
    }
}
