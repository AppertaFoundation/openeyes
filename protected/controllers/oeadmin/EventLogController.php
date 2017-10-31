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

/**
 * Class UniqueCodesController.
 */
class EventLogController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(AutomaticExaminationEventLog::model(), $this);
        $admin->setModelDisplayName('Examination Event Log(s)');
        $admin->setListFields(array(
            'event_id',
            'unique_code',
            'examination_date',
            'import_status.status_value',
        ));

        $admin->searchAll();
        $admin->getSearch()->addSearchItem('import_success', array(
            'type' => 'dropdown',
            'options' => CHtml::listData(ImportStatus::model()->findAll(), 'id', 'status_value'),
        ));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel(false);
    }

    /**
     * @param bool $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionEdit($id = false)
    {
        $eventQuery = AutomaticExaminationEventLog::model()->findByPk($id);
        if (!$eventQuery) {
            throw new CHttpException(404, "Event not found: $id");
        }

        if (!empty($_POST)) {
            if ($eventQuery->import_status->status_value === 'Duplicate Event') {
                $this->replaceEvent($eventQuery);
            }
            if ($eventQuery->import_status->status_value === 'Unfound Event') {
                $this->assignEvent($eventQuery);
            }
            $this->redirect('/oeadmin/eventLog/list/');
        }

        $event = $eventQuery->event;
        $eventUniqueCode = $eventQuery->unique_code;
        $buttons = array();

        switch ($eventQuery->import_status->status_value) {
            case 'Success Event':
            case 'Dismissed Event':
            case 'Import Success':
                $buttons = array(
                    'cancel' => false,
                    'submit' => 'Ok',
                );
                break;
            case 'Duplicate Event':
                $buttons = array(
                    'cancel' => 'Dismiss New',
                    'cancel-uri' => '/oeadmin/eventLog/dismiss/'.$id,
                    'submit' => 'Accept New',
                );
                break;
        }

        $this->render('//eventlog/edit', array(
            'log_id' => $id,
            'event' => $event,
            'unique_code' => $eventUniqueCode,
            'status' => $eventQuery->import_status->status_value,
            'data' => json_decode($eventQuery->examination_data, true),
            'previous' => $this->previousEventLogData($eventQuery),
            'buttons' => $buttons,
        ));
    }

    /**
     * @param $id
     */
    public function actionDismiss($id)
    {
        $eventQuery = AutomaticExaminationEventLog::model()->findByPk($id);
        if (!$eventQuery) {
            throw new CHttpException(404, "Event not found: $id");
        }

        $eventQuery->import_success = ImportStatus::model()->find('status_value = "Dismissed Event"')->id;
        $eventQuery->save();

        $this->redirect('/oeadmin/eventLog/list/');
    }

    /**
     * @param $id
     *
     * @return array
     *
     * @throws CHttpException
     * @throws Exception
     */
    protected function replaceEvent($eventQuery)
    {
        $creator = new \OEModule\OphCiExamination\components\ExaminationCreator();
        $data = $eventQuery->examination_data;
        $examination = json_decode($data, true);
        $eventType = EventType::model()->find('name = "Examination"');
        $portalUserId = $creator->getPortalUser();
        $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');
        $eyeIds = $creator->getEyes();

        $uidArray = explode('-', $examination['patient']['unique_identifier']);
        $uniqueCode = $uidArray[1];
        $opNoteEvent = UniqueCodes::model()->eventFromUniqueCode($uniqueCode);

        if (UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode, $eventType['id'])) {
            $this->createExamination($eventQuery, $opNoteEvent->episode_id, $creator, $portalUserId, $examination, $eventType, $eyeIds, $refractionType, $opNoteEvent->id);
        }
    }

    /**
     * @param $eventQuery
     *
     * @throws CHttpException
     * @throws Exception
     */
    protected function assignEvent($eventQuery)
    {
        $creator = new \OEModule\OphCiExamination\components\ExaminationCreator();
        $data = $eventQuery->examination_data;
        $examination = json_decode($data, true);
        $eventType = EventType::model()->find('name = "Examination"');
        $portalUserId = $creator->getPortalUser();
        $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');
        $eyeIds = $creator->getEyes();
        $patientId = Yii::app()->request->getPost('patient_id');
        $patient = Patient::model()->findByPk($patientId);
        $episodeId = $patient->getCataractEpisodeId();

        if (!$episodeId) {
            throw new CHttpException(400, 'Patient has no cataract episode');
        }

        $this->createExamination($eventQuery, $episodeId, $creator, $portalUserId, $examination, $eventType, $eyeIds, $refractionType);
    }

    /**
     * @param $eventLog
     *
     * @return mixed|string
     */
    protected function previousEventLogData($eventLog)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('event_id', $eventLog->event_id);
        $criteria->addCondition('id <> '.$eventLog->id);
        $criteria->order = 'created_date DESC, id ASC';
        $previous = AutomaticExaminationEventLog::model()->find($criteria);

        if (!$previous) {
            return '';
        }

        return json_decode($previous->examination_data, true);
    }

    /**
     * @param $eventQuery
     * @param $opNoteEvent
     * @param $creator
     * @param $portalUserId
     * @param $examination
     * @param $eventType
     * @param $eyeIds
     * @param $refractionType
     *
     * @throws CHttpException
     */
    protected function createExamination($eventQuery, $episodeId, $creator, $portalUserId, $examination, $eventType, $eyeIds, $refractionType, $opNoteId = null)
    {
        $transaction = $eventQuery->getDbConnection()->beginInternalTransaction();

        try {
            $examinationEvent = $creator->saveExamination($episodeId, $portalUserId, $examination, $eventType, $eyeIds, $refractionType, $opNoteId);
            if ($eventQuery->event) {
                //delete old event
                $eventQuery->event->deleted = 1;
                $eventQuery->event->save();
            }
            //update log for new event
            $eventQuery->import_success = ImportStatus::model()->find('status_value = "Success Event"')->id;
            $eventQuery->event_id = $examinationEvent->id;
            $eventQuery->save();
        } catch (Exception $e) {
            $transaction->rollback();
            throw new CHttpException(500, 'Saving Examination event failed');
        }

        $transaction->commit();
    }
}
