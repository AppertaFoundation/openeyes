<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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

        $admin = new Admin(EventLog::model(), $this);
        $admin->setModelDisplayName('Examination Event Log(s)');
        $admin->setListFields(array(
            'event_id',
            'unique_code',
            'examination_date',
            'status.status_value'
        ));

        $admin->searchAll();
        $admin->getSearch()->addSearchItem('import_success', array(
            'type' => 'dropdown',
            'options' => CHtml::listData(ImportStatus::model()->findAll(),'id', 'status_value'),
        ));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel(false);
    }

    public function actionEdit($id = false)
    {
        if (!EventLog::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }

        if (!empty($_POST)) {
            @$status = $_POST['status'];

            if ($status == 1) {
                $logId = $id;
                $eventQuery = EventLog::model()->findByPk($logId);
                $eventId = $eventQuery->event_id;
                $data = $eventQuery->examination_data;
                $examination = json_decode($data, true);

                $eventType = EventType::model()->find('name = "Examination"');
                $user = new User();
                $portalUser = $user->portalUser();
                if(!$portalUser){
                    throw new Exception('No User found for import');
                }
                $portalUserId = $portalUser->id;
                $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');

                $eyes = Eye::model()->findAll();
                $eyeIds = array();
                foreach ($eyes as $eye) {
                    $eyeIds[strtolower($eye->name)] = $eye->id;
                }

                $uidArray = explode('-', $examination['patient']['unique_identifier']);
                $uniqueCode = $uidArray[1];
                $opNoteEvent = UniqueCodes::model()->eventFromUniqueCode($uniqueCode);

                if (UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode, $eventType['id'])) {
                    $transaction = $opNoteEvent->getDbConnection()->beginInternalTransaction();

                    try {
                        $creator = new ExaminationCreator();
                        $examinationEvent = $creator->saveExamination($opNoteEvent, $portalUserId, $examination, $eventType, $eyeIds, $refractionType);
                    } catch (Exception $e) {
                        $transaction->rollback();
                        throw new CHttpException(500, 'Saving Examination event failed');
                    }
                    $transaction->commit();

                    $changeOtherEvents = new CDbCriteria();
                    $changeOtherEvents->addCondition("unique_code='$uniqueCode'"); // $wall_ids = array ( 1, 2, 3, 4 );
                    EventLog::model()->updateAll(array('import_success' => '3'), $changeOtherEvents);

                    $eventQuery->saveAttributes(array('import_success' => 1, 'event_id' => $examinationEvent->id));

                    $eventIdUpdate = new CDbCriteria();
                    $eventIdUpdate->addCondition("id=$eventId"); // $wall_ids = array ( 1, 2, 3, 4 );
                    Event::model()->updateAll(array('deleted' => '3', 'last_modified_user_id' => $portalUserId), $eventIdUpdate);
                }
            }

            $this->redirect('/oeadmin/eventLog/list/');
        }

        $eventQuery = EventLog::model()->findByPk($id);
        $event = $eventQuery->event;
        $eventUniqueCode = $eventQuery->unique_code;
        $data = $eventQuery->examination_data;

        $this->render('//eventlog/edit', array(
            'log_id' => $id,
            'event' => $event,
            'unique_code' => $eventUniqueCode,
            'data' => json_decode($data, true),
        ));
    }
}
