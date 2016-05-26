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

/**
 * Class UniqueCodesController
 */
class EventLogController extends BaseAdminController {

    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Lists procedures
     *
     * @throws CHttpException
     */
    public function actionList() {


        $criteria = new CDbCriteria;
        if (isset($_REQUEST['search']))
            $criteria->compare('event_id', $_REQUEST['search'], true);
        $pagination = $this->initPagination(Drug::model(), $criteria);
        $this->render('/eventLog/list', array(
            'eventLogs' => EventLog::model()->findAll($criteria),
            'pagination' => $pagination,
        ));

        /* $admin = new Admin(EventLog::model(), $this);


          $admin->setModelDisplayName('Examination Event Log(s)');

          $admin->setListFields(array(
          'event_id',
          'unique_code',
          'examination_date',
          'import_success'
          ));


          $admin->searchAll();
          $admin->getSearch()->addActiveFilter();
          $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
          $admin->listModel(false); */
    }

    /**
     * Edits or adds a Procedure
     *
     * @param bool|int $id
     * @throws CHttpException
     */
    public function actionEdit($id = false) {

        if (!$eventlog = EventLog::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }


        if (!empty($_POST)) {
            @$status = $_POST['status'];

            if ($status == 1) {
                $logId = $_POST['logId'];
                $eventQuery = EventLog::model()->findByPk($logId);
                $eventId = $eventQuery->event_id;
                $eventUniqueCode = $eventQuery->unique_code;
                ;
                $data = $eventQuery->examination_data;
                $examination = json_decode($data, true);

                $eventType = EventType::model()->find('name = "Examination"');
                $portalUserId = 1; //todo get portal user
                $refractionType = \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->find('name = "Ophthalmologist"');

                $eyes = Eye::model()->findAll();
                $eyeIds = array();
                foreach ($eyes as $eye) {
                    $eyeIds[strtolower($eye->name)] = $eye->id;
                }




                $uidArray = explode('-', $examination['patient']['unique_identifier']);
                $uniqueCode = $uidArray[1];
                $opNoteEvent = UniqueCodes::model()->eventFromUniqueCode($uniqueCode);

                /*if (UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode, $eventType['id'])) {




                    $transaction = $opNoteEvent->getDbConnection()->beginInternalTransaction();

                    try {
                        $examinationEvent = Event::model()->findByPk($eventId);
                        //$examinationEvent->id =  $eventQuery->event_id;
                        $examinationEvent->episode_id = $opNoteEvent->episode_id;
                        $examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = $portalUserId;
                        $examinationEvent->event_date = date('Y-m-d H:i:s', strtotime($examination['examination_date']));
                        $examinationEvent->event_type_id = $eventType['id'];
                        $examinationEvent->is_automated = 1;
                        $examinationEvent->automated_source = json_encode($examination['op_tom']);
                        if ($examinationEvent->save())
                            {
                            
                            $examinationEvent->refresh();
                            $refraction = \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::model()->find('event_id = '.$eventId);
                            
                            $refraction->event_id = $examinationEvent->id;
                            $refraction->created_user_id = $refraction->last_modified_user_id = $portalUserId;

                            $iop = \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::model()->find('event_id = '.$eventId);
                           // $iop->created_date = date('Y-m-d H:i:s', strtotime($iop->examination_date));
                            $iop->last_modified_date = date('Y-m-d H:i:s', strtotime($iop->last_modified_date));
                            $iop->event_id = $eventId;
                            $iop->last_modified_user_id = $portalUserId;
                            $iop->eye_id = $eyeIds['both'];
                            $iop->left_comments = 'Portal Add';
                            $iop->right_comments = 'Portal Add';
                            
                            
                            if (!$iop->save()) {
                                
                                throw new CDbException('iop failed: ' . print_r($iop->getErrors(), true));
                            }
                            $iop->refresh();
                            
                            $complications = \OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications::model()->find('event_id = '.$eventId);
                            $complications->event_id = $examinationEvent->id;
                            $complications->created_user_id = $complications->last_modified_user_id = $portalUserId;
                            $complications->eye_id = $eyeIds['both'];
                            if (!$complications->save()) {
                                throw new CDbException('Complications failed: ' . print_r($complications->getErrors(), true));
                            }
                            $complications->refresh();
                            if (count($examination['patient']['eyes'][0]['reading'][0]['visual_acuity'])) {
                                //create VisualFunction, required for visual acuity to show.
                                $visualFunction = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction::model()->find('event_id = '.$eventId);
                                $visualFunction->event_id = $examinationEvent->id;
                                $visualFunction->eye_id = $eyeIds['both'];
                                $visualFunction->left_rapd = 0;
                                $visualFunction->right_rapd = 0;
                                $visualFunction->created_user_id = $visualFunction->last_modified_user_id = $portalUserId;
                                if (!$visualFunction->save()) {
                                    throw new CDbException('Visual Function failed: ' . print_r($visualFunction->getErrors(), true));
                                }

                                $measure = $examination['patient']['eyes'][0]['reading'][0]['visual_acuity'][0]['measure'];
                                $unit = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->find('name = :measure', array('measure' => $measure));
                                //Create visual acuity
                                $visualAcuity = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->find('event_id = '.$eventId);
                                $visualAcuity->event_id = $examinationEvent->id;
                                $visualAcuity->created_user_id = $visualAcuity->last_modified_user_id = $portalUserId;
                                $visualAcuity->eye_id = $eyeIds['both'];
                                $visualAcuity->unit_id = $unit->id;
                                if (!$visualAcuity->save(false)) {
                                    throw new CDbException('Visual Acuity failed: ' . print_r($visualAcuity->getErrors(), true));
                                }
                                $visualAcuity->refresh();
                            }

                            foreach ($examination['patient']['eyes'] as $eye) {
                                
                                $eyeLabel = strtolower($eye['label']);
                                $refractionReading = $eye['reading'][0]['refraction'];
                                $typeSide = $eyeLabel . '_type_id';
                                $sphereSide = $eyeLabel . '_sphere';
                                $cylinderSide = $eyeLabel . '_cylinder';
                                $axisSide = $eyeLabel . '_axis';
                                $refraction->$typeSide = $refractionType['id'];
                                $refraction->$sphereSide = $refractionReading['sphere'];
                                $refraction->$cylinderSide = $refractionReading['cylinder'];
                                $refraction->$axisSide = $refractionReading['axis'];

                                foreach ($eye['reading'][0]['visual_acuity'] as $vaData) {
                                    $vaReading = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::model()->find('element_id = '.$visualAcuity->id);
                                    $vaReading->element_id = $visualAcuity->id;
                                    $baseValue = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue::model()->getBaseValue($unit->id, $vaData['reading']);
                                    $vaReading->value = $baseValue;
                                    $vaReading->method_id = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->find('name = :name', array('name' => $vaData['method']))->id;
                                    $vaReading->side = ($eyeLabel === 'left') ? \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::LEFT : \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::RIGHT;
                                    $vaReading->created_user_id = $vaReading->last_modified_user_id = $portalUserId;
                                    if (!$vaReading->save()) {
                                        throw new CDbException('Visual Acuity Reading failed: ' . print_r($vaReading->getErrors(), true));
                                    }
                                }

                                $iopReading = $eye['reading'][0]['iop'];
                                $iopValue = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value::model()->find('element_id = '.$visualAcuity->id);
                                $iopValue->element_id = $iop->id;
                                $iopValue->eye_id = $eyeIds[$eyeLabel];
                                $iopReadingValue = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading::model()->find('value = ?', array($iopReading['mm_hg']));
                                $instrument = \OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->find('name = ?', array($iopReading['instrument']));
                                $iopValue->reading_id = $iopReadingValue['id'];
                                $iopValue->instrument_id = $instrument['id'];
                                if (!$iopValue->save()) {
                                    throw new CDbException('iop value failed: ' . print_r($iop->getErrors(), true));
                                }
                                if (array_key_exists('complications', $eye)) {
                                    if (count($eye['complications'])) {
                                        foreach ($eye['complications'] as $complicationArray) {
                                            $eyeComplication =  \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications::model()->find('element_id = '.$visualAcuity->id);
                                            $eyeComplication->element_id = $complications->id;
                                            $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "' . $complicationArray['complication'] . '"');
                                            $eyeComplication->complication_id = $complicationToAdd->id;
                                            $eyeComplication->operation_note_id = $opNoteEvent->id;
                                            $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                                            $eyeComplication->save();
                                        }
                                    } else {
                                        $eyeComplication = \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications::model()->find('element_id = '.$visualAcuity->id);
                                        $eyeComplication->element_id = $complications->id;
                                        $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "none"');
                                        $eyeComplication->complication_id = $complicationToAdd->id;
                                        $eyeComplication->operation_note_id = $opNoteEvent->id;
                                        $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                                        $eyeComplication->save();
                                    }
                                }
                            }

                            $refraction->eye_id = $eyeIds['both'];


                            if (!$refraction->save()) {
                                throw new CDbException('Refraction failed: ' . print_r($iop->getErrors(), true));
                            }
                        } else {
                            echo 'Examination save failed: ' . PHP_EOL;
                            foreach ($examinationEvent->getErrors() as $key => $error) {
                                echo $key . ' invalid: ' . implode(', ', $error) . PHP_EOL;
                            }
                        }
                    } catch (Exception $e) {
                        echo $e->getTraceAsString();
                        exit;
                        $transaction->rollback();
                        echo 'Failed for examination ' . $examination['patient']['unique_identifier'] . ' with exception: ' . $e->getMessage() . 'on line ' . $e->getLine() . ' in file ' . $e->getFile() . PHP_EOL . $e->getTraceAsString();
                    }

                    $transaction->commit();
                    echo 'Examination imported: ' . $examinationEvent->id . PHP_EOL;


                    $changeOtherEvents = new CDbCriteria;
                    $changeOtherEvents->addCondition("event_id=$eventId"); // $wall_ids = array ( 1, 2, 3, 4 );
                    EventLog::model()->updateAll(array('import_success' => '3'), $changeOtherEvents);


                    $eventQuery->saveAttributes(array('import_success' => 1));
                } 
                
                  */
                  if (UniqueCodes::model()->examinationEventCheckFromUniqueCode($uniqueCode, $eventType['id'])) {
                $transaction = $opNoteEvent->getDbConnection()->beginInternalTransaction();

                try {
                    //Create main examination event
                    $examinationEvent = new Event();
                    $examinationEvent->episode_id = $opNoteEvent->episode_id;
                    $examinationEvent->created_user_id = $examinationEvent->last_modified_user_id = $portalUserId;
                    $examinationEvent->event_date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $examination['examination_date'])->format('Y-m-d');
                    $examinationEvent->event_type_id = $eventType['id'];
                    $examinationEvent->is_automated = 1;
                    $examinationEvent->automated_source = json_encode($examination['op_tom']);

                    if ($examinationEvent->save()) {
                        $examinationEvent->refresh();
                        $refraction = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction();
                        $refraction->event_id = $examinationEvent->id;
                        $refraction->created_user_id = $refraction->last_modified_user_id = $portalUserId;

                        $iop = new \OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure();
                        $iop->event_id = $examinationEvent->id;
                        $iop->created_user_id = $iop->last_modified_user_id = $portalUserId;
                        $iop->eye_id = $eyeIds['both'];
                        $iop->left_comments = 'Portal Add';
                        $iop->right_comments = 'Portal Add';
                        if (!$iop->save()) {
                            throw new CDbException('iop failed: '.print_r($iop->getErrors(), true));
                        }
                        $iop->refresh();

                        $complications = new \OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications();
                        $complications->event_id = $examinationEvent->id;
                        $complications->created_user_id = $complications->last_modified_user_id = $portalUserId;
                        $complications->eye_id = $eyeIds['both'];
                        if (!$complications->save()) {
                            throw new CDbException('Complications failed: '.print_r($complications->getErrors(), true));
                        }
                        $complications->refresh();
                        if (count($examination['patient']['eyes'][0]['reading'][0]['visual_acuity'])) {
                            //create VisualFunction, required for visual acuity to show.
                            $visualFunction = new \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction();
                            $visualFunction->event_id = $examinationEvent->id;
                            $visualFunction->eye_id = $eyeIds['both'];
                            $visualFunction->left_rapd = 0;
                            $visualFunction->right_rapd = 0;
                            $visualFunction->created_user_id = $visualFunction->last_modified_user_id = $portalUserId;
                            if (!$visualFunction->save()) {
                                throw new CDbException('Visual Function failed: '.print_r($visualFunction->getErrors(), true));
                            }

                            $measure = $examination['patient']['eyes'][0]['reading'][0]['visual_acuity'][0]['measure'];
                            $unit = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->find('name = :measure', array('measure' => $measure));
                            //Create visual acuity
                            $visualAcuity = new \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity();
                            $visualAcuity->event_id = $examinationEvent->id;
                            $visualAcuity->created_user_id = $visualAcuity->last_modified_user_id = $portalUserId;
                            $visualAcuity->eye_id = $eyeIds['both'];
                            $visualAcuity->unit_id = $unit->id;
                            if (!$visualAcuity->save(false)) {
                                throw new CDbException('Visual Acuity failed: '.print_r($visualAcuity->getErrors(), true));
                            }
                            $visualAcuity->refresh();
                        }

                        foreach ($examination['patient']['eyes'] as $eye) {
                            $eyeLabel = strtolower($eye['label']);
                            $refractionReading = $eye['reading'][0]['refraction'];
                            $typeSide = $eyeLabel.'_type_id';
                            $sphereSide = $eyeLabel.'_sphere';
                            $cylinderSide = $eyeLabel.'_cylinder';
                            $axisSide = $eyeLabel.'_axis';
                            $refraction->$typeSide = $refractionType['id'];
                            $refraction->$sphereSide = $refractionReading['sphere'];
                            $refraction->$cylinderSide = $refractionReading['cylinder'];
                            $refraction->$axisSide = $refractionReading['axis'];

                            foreach ($eye['reading'][0]['visual_acuity'] as $vaData) {
                                $vaReading = new \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading();
                                $vaReading->element_id = $visualAcuity->id;
                                $baseValue = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue::model()->getBaseValue($unit->id, $vaData['reading']);
                                $vaReading->value = $baseValue;
                                $vaReading->method_id = \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->find('name = :name', array('name' => $vaData['method']))->id;
                                $vaReading->side = ($eyeLabel === 'left') ? \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::LEFT : \OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::RIGHT;
                                $vaReading->created_user_id = $vaReading->last_modified_user_id = $portalUserId;
                                if (!$vaReading->save()) {
                                    throw new CDbException('Visual Acuity Reading failed: '.print_r($vaReading->getErrors(), true));
                                }
                            }

                            $iopReading = $eye['reading'][0]['iop'];
                            $iopValue = new \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value();
                            $iopValue->element_id = $iop->id;
                            $iopValue->eye_id = $eyeIds[$eyeLabel];
                            $iopReadingValue = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading::model()->find('value = ?', array($iopReading['mm_hg']));
                            $instrument = \OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->find('name = ?', array($iopReading['instrument']));
                            $iopValue->reading_id = $iopReadingValue['id'];
                            $iopValue->instrument_id = $instrument['id'];
                            if (!$iopValue->save()) {
                                throw new CDbException('iop value failed: '.print_r($iop->getErrors(), true));
                            }
                            if (array_key_exists('complications', $eye)) {
                                if (count($eye['complications'])) {
                                    foreach ($eye['complications'] as $complicationArray) {
                                        $eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
                                        $eyeComplication->element_id = $complications->id;
                                        $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "'.$complicationArray['complication'].'"');
                                        $eyeComplication->complication_id = $complicationToAdd->id;
                                        $eyeComplication->operation_note_id = $opNoteEvent->id;
                                        $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                                        $eyeComplication->save();
                                    }
                                } else {
                                    $eyeComplication = new \OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications();
                                    $eyeComplication->element_id = $complications->id;
                                    $complicationToAdd = \OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications::model()->find('name = "none"');
                                    $eyeComplication->complication_id = $complicationToAdd->id;
                                    $eyeComplication->operation_note_id = $opNoteEvent->id;
                                    $eyeComplication->eye_id = $eyeIds[$eyeLabel];
                                    $eyeComplication->save();
                                }
                            }
                        }

                        $refraction->eye_id = $eyeIds['both'];
                        if (!$refraction->save()) {
                            throw new CDbException('Refraction failed: '.print_r($iop->getErrors(), true));
                        }
                    } else {
                        echo 'Examination save failed: '.PHP_EOL;
                        foreach ($examinationEvent->getErrors() as $key => $error) {
                            echo $key.' invalid: '.implode(', ', $error).PHP_EOL;
                        }
                    }
                } 
                catch (Exception $e) {
                    $transaction->rollback();
                    echo 'Failed for examination '.$examination['patient']['unique_identifier'].' with exception: '.$e->getMessage().'on line '.$e->getLine().' in file '.$e->getFile().PHP_EOL.$e->getTraceAsString();
                   
                }
                $transaction->commit();
                echo 'Examination imported: '.$examinationEvent->id.PHP_EOL;
                
                
                
                    $changeOtherEvents = new CDbCriteria;
                    $changeOtherEvents->addCondition("unique_code='$uniqueCode'"); // $wall_ids = array ( 1, 2, 3, 4 );
                    EventLog::model()->updateAll(array('import_success' => '3'), $changeOtherEvents);
                    
                    
                    $eventQuery->saveAttributes(array('import_success' => 1,'event_id'=>$examinationEvent->id));
                    
                    
                    
                    
                    $eventIdUpdate = new CDbCriteria;
                    $eventIdUpdate->addCondition("id=$eventId"); // $wall_ids = array ( 1, 2, 3, 4 );
                    Event::model()->updateAll(array('deleted' => '3','last_modified_user_id'=>$portalUserId), $eventIdUpdate);
                    
                        
                    
                
                
            } 
                
                
                
                
            }
            

            $this->redirect('/eventLog/list/');
        }
 
        $eventQuery = EventLog::model()->findByPk($id);
        $eventId = $eventQuery->event_id;
        $eventUniqueCode = $eventQuery->unique_code;
        ;
        $data = $eventQuery->examination_data;
        $decode = json_decode($data, true);
        //  print_r($data);
        $this->render('//eventlog/edit', array(
            'logId' => $id,
            'eventId' => $eventId,
            'unique_code' => $eventUniqueCode,
            'data' => $decode
        ));
    }

}
