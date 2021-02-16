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
class DefaultController extends BaseEventTypeController
{
    protected static $action_types = array(
        'loadElementByProcedure' => self::ACTION_TYPE_FORM,
        'getElementsToDelete' => self::ACTION_TYPE_FORM,
        'verifyProcedure' => self::ACTION_TYPE_FORM,
        'getImage' => self::ACTION_TYPE_FORM,
        'getTheatreOptions' => self::ACTION_TYPE_FORM,
        'whiteboard' => self::ACTION_TYPE_VIEW,
    );

    /* @var Element_OphTrOperationbooking_Operation operation that this note is for when creating */
    protected $booking_operation;
    /* @var boolean - indicates if this note is for an unbooked procedure or not when creating */
    protected $unbooked = false;
    /* @var Proc[] - cache of bookings for the booking operation */
    protected $booking_procedures;

    /**
     * Handle the selection of a booking for creating an op note.
     *
     * (non-phpdoc)
     *
     * @see parent::actionCreate()
     */
    public function actionCreate()
    {
        $errors = array();

        if (!empty($_POST)) {
            if (preg_match('/^booking([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                $this->redirect(array('/OphTrOperationnote/Default/create?patient_id=' . $this->patient->id . '&booking_event_id=' . $m[1]));
            } elseif (@$_POST['SelectBooking'] == 'emergency') {
                $this->redirect(array('/OphTrOperationnote/Default/create?patient_id=' . $this->patient->id . '&unbooked=1'));
            }

            $errors = array('Operation' => array('Please select a booked operation'));
        }

        if ($this->booking_operation || $this->unbooked) {
            $this->createOpNote();
        } else {
            // set up form for selecting a booking for the Op note
            $bookings = array();


            $element_enabled = Yii::app()->params['disable_theatre_diary'];
            $theatre_diary_disabled = isset($element_enabled) && $element_enabled == 'on';

            /** @var OphTrOperationbooking_API $api */
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $operations = $api->getOpenOperations($this->patient);
            }


            $this->title = 'Please select booking';
            $this->event_tabs = array(
                array(
                    'label' => 'Select a booking',
                    'active' => true,
                ),
            );
            $cancel_url = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
            $this->event_actions = array(
                EventAction::link(
                    'Cancel',
                    Yii::app()->createUrl($cancel_url),
                    null,
                    array('class' => 'button small warning')
                ),
            );

            $this->render('select_event', array(
                'errors' => $errors,
                'operations' => $operations,
                'theatre_diary_disabled' => $theatre_diary_disabled
            ));
        }
    }

    public function actionWhiteboard($id)
    {
        $this->redirect(Yii::app()->createUrl('/OphTrOperationbooking/whiteboard/view/' . $id));
    }

    protected function createOpNote()
    {
        $this->event->firm_id = $this->selectedFirmId;
        if (!empty($_POST)) {
            // form has been submitted
            if (isset($_POST['cancel'])) {
                $this->redirectToPatientEpisodes();
            }

            // set and validate
            $errors = $this->setAndValidateElementsFromData($_POST);

            // creation
            if (empty($errors)) {
                $transaction = Yii::app()->db->beginTransaction();

                try {
                    $success = $this->saveEvent($_POST);

                    if ($success) {
                        // should this be in the save event as pass through?
                        if ($this->eventIssueCreate) {
                            $this->event->addIssue($this->eventIssueCreate);
                        }

                        // should not be passing event?
                        $this->afterCreateElements($this->event);

                        $this->logActivity('created event.');

                        $this->event->audit('event', 'create');

                        Yii::app()->user->setFlash('success', "{$this->event_type->name} created.");

                        $transaction->commit();

                        $create_prescription = \Yii::app()->request->getParam('auto_generate_prescription_after_surgery');
                        if ($create_prescription) {
                            $transaction = Yii::app()->db->beginTransaction();
                            // create 'post-op' prescription
                            $result = $this->createPrescriptionEvent();
                            if ($result['success'] === true) {
                                $transaction->commit();
                            } else {
                                $transaction->rollback();
                                $this->logEventCreationFail($result['errors'], 'OphDrPrescription', 'Element_OphDrPrescription_Details');
                            }
                        }

                        $create_correspondence = \Yii::app()->request->getParam('auto_generate_gp_letter_after_surgery');
                        if ($create_correspondence) {
                            if ($this->patient->gp_id && $this->patient->practice_id) {
                                                                $macro_name = \SettingMetadata::model()->getSetting('default_post_op_letter');
                                                                $transaction = Yii::app()->db->beginTransaction();
                                                                // create 'post-op' letter
                                                                $result = $this->createCorrespondenceEvent($macro_name);
                                if ($result['success'] === true) {
                                    $transaction->commit();
                                } else {
                                    $transaction->rollback();
                                    $this->logEventCreationFail($result['errors'], 'OphCoCorrespondence', 'ElementLetter');
                                }
                            } else {
                                    Yii::app()->user->setFlash('error', "GP letter could not be created because the patient has no GP");
                                    $this->logEventCreationFail(['Error Message' => 'GP letter could not be created because the patient has no GP', 'gp_id' => $this->patient->gp_id, 'practice_id' => $this->patient->practice_id], 'OphCoCorrespondence', 'Patient');
                            }
                        }

                        $create_optom_correspondence = \Yii::app()->request->getParam('auto_generate_optom_post_op_letter_after_surgery');


                        if ($create_optom_correspondence) {
                            $macro_name = \SettingMetadata::model()->getSetting('default_optom_post_op_letter');
                            $transaction = Yii::app()->db->beginTransaction();
                            // create optometrist 'post-op' letter
                            $result = $this->createCorrespondenceEvent($macro_name);
                            if ($result['success'] === true) {
                                $transaction->commit();
                            } else {
                                $transaction->rollback();
                                $this->logEventCreationFail($result['errors'], 'OphCoCorrespondence', 'ElementLetter');
                            }
                        }

                        if ($this->event->parent_id) {
                            $this->redirect(Yii::app()->createUrl('/' . $this->event->parent->eventType->class_name . '/default/view/' . $this->event->parent_id));
                        } else {
                            $this->redirect(array($this->successUri . $this->event->id));
                        }
                    } else {
                        throw new Exception('could not save event');
                    }
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw $e;
                }
            }
        } else {
            $this->setOpenElementsFromCurrentEvent('create');
            $this->updateHotlistItem($this->patient);
        }

        $this->editable = false;
        $this->event_tabs = array(
            array(
                'label' => 'Create',
                'active' => true,
            ),
        );

        $cancel_url = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
        $this->event_actions = array(
            EventAction::link(
                'Cancel',
                Yii::app()->createUrl($cancel_url),
                array('level' => 'cancel')
            ),
        );

        $this->render('create', array(
            'errors' => @$errors,
        ));
    }


    protected function afterCreateElements($event)
    {
        parent::afterCreateElements($event);
        $this->persistPcrRisk();
    }

    private function createPrescriptionEvent()
    {
        $drug_set_name = \SettingMetadata::model()->getSetting("default_{$this->event->episode->status->key}_drug_set");
        $subspecialty_id = $this->firm->getSubspecialtyID();
        $params = [':subspecialty_id' => $subspecialty_id, ':name' => $drug_set_name];

        $set = MedicationSet::model()->find([
            'condition' => 'subspecialty_id = :subspecialty_id AND name = :name',
            'params' => $params,
            'with' => 'medicationSetRules',
            'together' => true
        ]);

        $success = false;

        if ($set) {
            $procedure_list = Element_OphTrOperationnote_ProcedureList::model()->findByAttributes(['event_id' => $this->event->id]);

            $prescription_creator = new PrescriptionCreator($this->event->episode);
            $prescription_creator->patient = $this->patient;
            $prescription_creator->addMedicationSet($set->id, $procedure_list->eye_id);
            $prescription_creator->save();

            $success = !$prescription_creator->hasErrors();
            $errors = $prescription_creator->getErrors();
        } else {
            $msg = "Unable to create default Prescription because: No drug set named '{$drug_set_name}' was found";
            $errors[] = [$msg];
            $errors[] = $params; // these are only going to the logs and audit, not displayed to the user

            \Yii::app()->user->setFlash('issue.prescription', $msg);
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    /**
     * Ensures that any attached operation booking status is updated after the op note is removed.
     *
     * @param $id
     *
     * @return bool|void
     */
    public function actionDelete($id)
    {
        $this->dont_redirect = true;

        if (parent::actionDelete($id)) {
            $this->redirect((new CoreAPI())->generatePatientLandingPageLink($this->event->episode->patient));
        }
    }

    /**
     * After soft delete
     *
     * @param $yii_event
     * @return bool|void
     */
    public function afterSoftDelete($yii_event)
    {
        $proclist = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($this->event->id));
        if ($proclist && $proclist->booking_event_id) {
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $last_status_id = $api->getLastNonCompleteStatus($proclist->booking_event_id);
                $status = OphTrOperationbooking_Operation_Status::model()->findByPk($last_status_id);
                $api->setOperationStatus($proclist->booking_event_id, $status->name);
            }
        }
    }

    /**
     * Suppress default behaviour - optional elements are managed through the procedure selection.
     *
     * @return array
     */
    public function getOptionalElements()
    {
        return array();
    }

    /**
     * Ajax action to load the required elements for a procedure.
     *
     * @throws SystemException
     * @throws CHttpException
     */
    public function actionLoadElementByProcedure()
    {
        if (!$proc = Procedure::model()->findByPk((int)@$_GET['procedure_id'])) {
            throw new SystemException('Procedure not found: ' . @$_GET['procedure_id']);
        }

        if (!$patient_id = $this->getApp()->request->getParam('patientId')) {
            throw new SystemException('patientId required for procedure element loading.');
        }
        $this->setPatient($patient_id);

        $form = new BaseEventTypeCActiveForm();

        $procedureSpecificElements = $this->getProcedureSpecificElements($proc->id);

        foreach ($procedureSpecificElements as $i => $element) {
            $class_name = $element->element_type->class_name;

            $element = new $class_name();
            $element->patientId = $this->patient->id;

            if ($element->requires_eye) {
                $eye_id = $this->getApp()->request->getParam('eye');
                if (!in_array($eye_id, array(Eye::LEFT, Eye::RIGHT))) {
                    echo 'must-select-eye';
                    return;
                }
                $element->eye = Eye::model()->findByPk($eye_id);
            }

            $element->setDefaultOptions($this->patient);

            $postProcess = ($i == count($procedureSpecificElements) - 1);
            $this->renderElement($element, 'create', $form, array(), array('ondemand' => true), false, $postProcess);
        }

        if (count($procedureSpecificElements) == 0) {
            $element = new Element_OphTrOperationnote_GenericProcedure();
            $element->proc_id = $proc->id;
            $element->setDefaultOptions();
            $this->renderElement($element, 'create', $form, array(), array('ondemand' => true), false, true);
        }
    }

    /**
     * @param $procedure_id
     *
     * @return OphTrOperationnote_ProcedureListOperationElement[]
     */
    public function getProcedureSpecificElements($procedure_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('procedure_id', $procedure_id);
        $criteria->order = 'display_order asc';

        return OphTrOperationnote_ProcedureListOperationElement::model()->findAll($criteria);
    }

    /**
     * Ajax function that works out what elements are no longer needed when a procedure has been removed.
     *
     * @throws SystemException
     */
    public function actionGetElementsToDelete()
    {
        if (!$proc = Procedure::model()->findByPk((int)@$_POST['procedure_id'])) {
            throw new SystemException('Procedure not found: ' . @$_POST['procedure_id']);
        }

        $procedures = @$_POST['remaining_procedures'] ? explode(',', $_POST['remaining_procedures']) : array();

        $elements = array();

        foreach ($this->getProcedureSpecificElements($proc->id) as $element) {
            if (empty($procedures) || !OphTrOperationnote_ProcedureListOperationElement::model()->find('procedure_id in (' . implode(',', $procedures) . ') and element_type_id = ' . $element->element_type->id)) {
                $elements[] = $element->element_type->class_name;
            }
        }

        die(json_encode($elements));
    }

    /**
     * Renders procedure specific elements - wrapper for rendering child elements of the procedure list element.
     *
     * @param $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     * @throws Exception
     */
    public function renderAllProcedureElements($action, $form = null, $data = null)
    {
        foreach ($this->open_elements as $el) {
            if (is_subclass_of($el, 'Element_OnDemand')) {
                $this->renderElement($el, $action, $form, $data);
            }
        }
    }

    /**
     * Overloads BaseEventTypeController::renderOpenElements() to not render the event date as a separate element
     * The event date element is instead rendered as part of the Location element
     */
    public function renderOpenElements($action, $form = null, $data = null)
    {
        if ($action === 'renderEventImage') {
            $action = 'view';
        }
        $this->renderTiledElements($this->getElements($action), $action, $form, $data);
    }

    /**
     * Get the open elements for the event that are not children.
     *
     * @return array
     */
    public function getElements($action = 'edit')
    {
        $elements = array();
        if (is_array($this->open_elements)) {
            foreach ($this->open_elements as $element) {
                if ($element->getElementType()) {
                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Ajax method for checking whether a procedure requires the eye to be set.
     */
    public function actionVerifyprocedure()
    {
        if (!empty($_GET['name'])) {
            $proc = Procedure::model()->findByAttributes(array('term' => $_GET['name']));
            if ($proc) {
                if ($this->procedure_requires_eye($proc->id)) {
                    echo 'no';
                } else {
                    echo 'yes';
                }
            }
        } else {
            $i = 0;
            $result = true;
            $procs = array();
            while (isset($_GET['proc' . $i])) {
                if ($this->procedure_requires_eye($_GET['proc' . $i])) {
                    $result = false;
                    $procs[] = Procedure::model()->findByPk($_GET['proc' . $i])->term;
                }
                ++$i;
            }
            if ($result) {
                echo 'yes';
            } else {
                echo implode("\n", $procs);
            }
        }
    }

    /**
     * returns true if the passed procedure id requires the selection of 'left' or 'right' eye.
     *
     * @param $procedure_id
     *
     * @return bool
     */
    public function procedure_requires_eye($procedure_id)
    {
        foreach ($this->getProcedureSpecificElements($procedure_id) as $plpa) {
            $element_type = ElementType::model()->findByPk($plpa->element_type_id);

            if (in_array($element_type->class_name, array('Element_OphTrOperationnote_Cataract', 'Element_OphTrOperationnote_Buckle', 'Element_OphTrOperationnote_Vitrectomy'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Works out the eye that should be used for an eyedraw.
     *
     * @FIXME: This should be a property on the element, or a variable passed to render.
     *
     * @return Eye
     *
     * @throws SystemException
     * @throws CHttpException
     */
    public function getSelectedEyeForEyedraw()
    {
        $eye = null;

        if (!empty($_POST['Element_OphTrOperationnote_ProcedureList']['eye_id'])) {
            $eye = Eye::model()->findByPk($_POST['Element_OphTrOperationnote_ProcedureList']['eye_id']);
        } elseif ($this->event && $this->event->id) {
            $eye = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($this->event->id))->eye;
        } elseif (!empty($_GET['eye'])) {
            $eye = Eye::model()->findByPk($_GET['eye']);
        } elseif ($this->action->id == 'create') {
            if (!$this->patient) {
                $this->setPatient($this->getApp()->request->getParam('patient_id'));
            }

            $booking_event_id = $this->getApp()->request->getParam('booking_event_id');

            if ($booking_event_id) {
                $operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($booking_event_id));
                $eye = $operation ? $operation->eye : null;
            }
        }

        if ($eye && $eye->name == 'Both') {
            $eye = Eye::model()->find('name=?', array('Right'));
        }

        //return Right if eye isn't set
        return $eye ? $eye : Eye::model()->find('name=?', array('Right'));
    }

    /**
     * Return the anaesthetic agent list.
     *
     * @param Element_OphTrOperationnote_Anaesthetic $element
     *
     * @return array
     */
    public function getAnaesthetic_agent_list($element)
    {
        $agents = $this->getAnaestheticAgentsBySiteAndSubspecialty();
        $list = CHtml::listData($agents, 'id', 'name');
        $curr_list = CHtml::listData($element->anaesthetic_agents, 'id', 'name');
        if ($missing = array_diff($curr_list, $list)) {
            foreach ($missing as $id => $name) {
                $list[$id] = $name;
            }
        }

        return $list;
    }

    /**
     * Retrieve AnaestheticAgent instances relevant to the current site and subspecialty. The relation flag indicates
     * whether we are retrieve the full list of defaults.
     *
     * @param string $relation
     *
     * @return array
     */
    protected function getAnaestheticAgentsBySiteAndSubspecialty($relation = 'siteSubspecialtyAssignments')
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('site_id = :siteId and subspecialty_id = :subspecialtyId');
        $criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];
        $criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
        $criteria->order = 'name';

        return AnaestheticAgent::model()
            ->active()
            ->with(array(
                $relation => array(
                    'joinType' => 'JOIN',
                ),
            ))
            ->findAll($criteria);
    }

    /**
     * Return the list of possible operative devices for the given element.
     *
     * @param Element_OphTrOperationnote_Cataract $element
     *
     * @return array
     */
    public function getOperativeDeviceList($element)
    {
        $curr_list = CHtml::listData($element->operative_devices, 'id', 'name');
        $devices = $this->getOperativeDevicesBySiteAndSubspecialty(false, array_keys($curr_list));

        return CHtml::listData($devices, 'id', 'name');
    }

    /**
     * Retrieve OperativeDevice instances relevant to the current site and subspecialty. The default flag indicates
     * whether we are retrieve the full list of defaults.
     *
     * @param bool $default
     *
     * @return OperativeDevice[]
     */
    protected function getOperativeDevicesBySiteAndSubspecialty($default = false, $include_ids = null)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('subspecialty_id = :subspecialtyId and site_id = :siteId');
        $criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
        $criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];

        if ($default) {
            $criteria->addCondition('siteSubspecialtyAssignments.default = :one');
            $criteria->params[':one'] = 1;
        }

        $criteria->order = 'name asc';

        return OperativeDevice::model()
            ->activeOrPk($include_ids)
            ->with(array(
                'siteSubspecialtyAssignments' => array(
                    'joinType' => 'JOIN',
                ),
            ))
            ->findAll($criteria);
    }

    /**
     * Get the ids of the default anaesthetic agents for the current site and subspecialty.
     *
     * @return array
     */
    public function getOperativeDeviceDefaults()
    {
        $ids = array();
        foreach ($this->getOperativeDevicesBySiteAndSubspecialty(true) as $operative_device) {
            $ids[] = $operative_device->id;
        }

        return $ids;
    }

    /**
     * Get the drug options for the element for the controller state.
     *
     * @param Element_OphTrOperationnote_PostOpDrugs $element
     *
     * @return array
     */
    public function getPostOpDrugList($element)
    {
        $drug_ids = array();
        foreach ($element->drugs as $drug) {
            $drug_ids[] = $drug->id;
        }

        $drugs = $this->getPostOpDrugsBySiteAndSubspecialty(false, $drug_ids);

        return CHtml::listData($drugs, 'id', 'name');
    }

    /**
     * Return the post op drugs for the current site and subspecialty.
     *
     * @param bool $default
     *
     * @return OphTrOperationnote_PostopDrug[]
     */
    protected function getPostOpDrugsBySiteAndSubspecialty($default = false, $include_ids = null)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('subspecialty_id = :subspecialtyId and site_id = :siteId');
        $criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
        $criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];

        if ($default) {
            $criteria->addCondition('siteSubspecialtyAssignments.default = :one');
            $criteria->params[':one'] = 1;
        }

        $criteria->order = 'name asc';

        return OphTrOperationnote_PostopDrug::model()
            ->with(array(
                'siteSubspecialtyAssignments' => array(
                    'joinType' => 'JOIN',
                ),
            ))
            ->activeOrPk($include_ids)
            ->findAll($criteria);
    }

    /**
     * Helper method to get the site for the operation booking on this event.
     *
     * (currently only supports events that have been saved)
     */
    public function findBookingSite()
    {
        if ($pl = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($this->event->id))) {
            if ($pl->bookingEvent) {
                if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                    return $api->findSiteForBookingEvent($pl->bookingEvent);
                }
            }
        }

        return;
    }

    public function actionGetImage()
    {
        preg_match('/data\:image\/png;base64,(.*)$/', $_POST['image'], $m);

        file_put_contents('/tmp/image.png', base64_decode($m[1]));
    }

    public function getBookingOperation()
    {
        if ($this->booking_operation) {
            return $this->booking_operation;
        } else {
            return false;
        }
    }

    public function actionGetTheatreOptions()
    {
        $siteId = $this->request->getParam('siteId');
        if ($siteId > 0) {
            $optionValues = OphTrOperationbooking_Operation_Theatre::model()->findAll(array(
                'condition' => 'active=1 and site_id=' . $siteId,
                'order' => 'name',
            ));

            if (count($optionValues) == 1) {
                echo CHtml::dropDownList(
                    'theatre_id',
                    false,
                    CHtml::listData($optionValues, 'id', 'name')
                );
            } else {
                echo CHtml::dropDownList(
                    'theatre_id',
                    false,
                    CHtml::listData($optionValues, 'id', 'name'),
                    array('empty' => 'Select',)
                );
            }
        }
    }

    public function formatAconst($aconst)
    {
        /* based on the requirements:
        Valid results*
        * 118.0
        * 118.1*
        * 118.12*
        * 118.123*
        * 118.102
        * 118.001*

        *Invalid results*
        * 118
        * 118.000
        * 118.100
        * 118.120

        */
        $formatted = (float)$aconst;
        if ($formatted == (int)$formatted) {
            $formatted .= '.0';
        }

        return $formatted;
    }

    protected function beforeAction($action)
    {
        Yii::app()->clientScript->registerScriptFile($this->assetPath . '/js/eyedraw.js');
        Yii::app()->clientScript->registerScriptFile($this->assetPath . '/js/OpenEyes.UI.OphTrOperationnote.Anaesthetic.js');

        return parent::beforeAction($action);
    }

    /**
     * Creates the procedure elements for the procedures selected in the procedure list element.
     *
     * @return BaseEventTypeElement[]
     */
    protected function getEventElements()
    {
        if ($this->event && !$this->event->isNewRecord) {
            return $this->event->getElements();
            //TODO: check for missing elements for procedures
        } else {
            $elements = $this->event_type->getDefaultElements();
            if ($procedures = $this->getBookingProcedures()) {
                // Splice the elements array to place the extra elements in the correct order
                // As it is when operation note has no booked procedures
                $elements_before_procedures = [];
                $elements_after_procedures = [];
                $procedure_list_element_found = false;

                foreach ($elements as $element) {
                    if ($procedure_list_element_found) {
                        $elements_after_procedures[] = $element;
                    } else {
                        $elements_before_procedures[] = $element;
                        if ($element instanceof Element_OphTrOperationnote_ProcedureList) {
                            $procedure_list_element_found = true;
                        }
                    }
                }
                // need to add procedure elements for the booking operation
                $extra_elements = array();

                foreach ($procedures as $proc) {
                    $procedure_elements = $this->getProcedureSpecificElements($proc->id);
                    foreach ($procedure_elements as $element) {
                        $kls = $element->element_type->class_name;
                        // only have one of any given procedure element
                        if (!in_array($kls, $extra_elements)) {
                            $extra_elements[] = $kls;
                            $elements_before_procedures[] = new $kls();
                        }
                    }

                    if (count($procedure_elements) == 0) {
                        // no specific element for procedure, use generic
                        $element = new Element_OphTrOperationnote_GenericProcedure();
                        $element->proc_id = $proc->id;
                        $elements_before_procedures[] = $element;
                    }
                }

                return array_merge($elements_before_procedures, $elements_after_procedures);
            }

            return $elements;
        }
    }

    /**
     * returns list of procudures for the booking operation set on the controller.
     *
     * @return Proc[]
     */
    protected function getBookingProcedures()
    {
        if ($this->booking_operation) {
            if (!$this->booking_procedures) {
                $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
                $this->booking_procedures = $api->getProceduresForOperation($this->booking_operation->event_id);
            }

            return $this->booking_procedures;
        }
    }

    /**
     * @param BaseEventTypeElement $element
     * @param string $action
     * @inheritdoc
     */
    protected function setElementDefaultOptions($element, $action)
    {
        if ($action == 'create' && $this->getBookingProcedures()) {
            // we are loading procedure elements directly, so if they need the
            // eye setting, we must take care of this first.
            if (is_a($element, 'Element_OnDemandEye')) {
                $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
                $element->setEye($api->getEyeForOperation($this->booking_operation->event_id));
            }
        }
        parent::setElementDefaultOptions($element, $action);
    }

    /**
     * For new notes for a specific operation, initialise procedure list with relevant procedures.
     *
     * @param Element_OphTrOperationnote_ProcedureList $element
     * @param string $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationnote_ProcedureList($element, $action)
    {
        if ($action == 'create' && $procedures = $this->getBookingProcedures()) {
            $element->procedures = $procedures;

            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $element->eye = $api->getEyeForOperation($this->booking_operation->event_id);
            $element->booking_event_id = $this->booking_operation->event_id;
        }
    }

    /**
     * Determine if the witness field is required, and set various defaults from the patient and related booking.
     *
     * @param Element_OphTrOperationnote_Anaesthetic $element
     * @param string $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationnote_Anaesthetic($element, $action)
    {
        if ($action == 'create') {
            if ($this->booking_operation) {
                $element->anaesthetic_type = $this->booking_operation->anaesthetic_type;
            } else {
                $key = $this->patient->isChild() ? 'ophtroperationnote_default_anaesthetic_child' : 'ophtroperationnote_default_anaesthetic';

                if (isset(Yii::app()->params[$key])) {
                    if ($at = AnaestheticType::model()->find('code=?', array(Yii::app()->params[$key]))) {
                        $element->anaesthetic_type = array($at);
                    }
                }
            }
            $element->anaesthetic_agents = $this->getAnaestheticAgentsBySiteAndSubspecialty('siteSubspecialtyAssignmentDefaults');
        }
    }

    /**
     * Set the default drugs from site and subspecialty.
     *
     * @param Element_OphTrOperationnote_PostOpDrugs $element
     * @param string $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationnote_PostOpDrugs($element, $action)
    {
        if ($action == 'create') {
            $element->drugs = $this->getPostOpDrugsBySiteAndSubspecialty(true);
        }
    }

    /**
     * Set the default operative devices from the site and subspecialty.
     *
     * @param Element_OphTrOperationnote_Cataract $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrOperationnote_Cataract($element, $action)
    {
        if ($action == 'create') {
            $element->operative_devices = $this->getOperativeDevicesBySiteAndSubspecialty(true);
        }
    }

    /**
     * Set up the controller properties for booking relationship.
     *
     * @throws Exception
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();

        /** @var OphTrOperationbooking_API $api */
        $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');

        if (isset($_GET['booking_event_id'])) {
            if (!$api) {
                throw new Exception('invalid request for booking event');
            }
            if (!$this->booking_operation = $api->getOperationForEvent($_GET['booking_event_id'])) {
                throw new Exception('booking event not found');
            }
        } elseif (isset($_GET['unbooked'])) {
            $this->unbooked = true;
        }

        $this->initEdit();
    }

    /**
     * Edit actions common initialisation.
     */
    protected function initEdit()
    {
        $this->jsVars['eyedraw_iol_classes'] = Yii::app()->params['eyedraw_iol_classes'];
        $this->moduleStateCssClass = 'edit';
    }

    /**
     * Call the core edit action initialisation.
     *
     * (non-phpdoc)
     *
     * @see parent::initActionUpdate()
     */
    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit();
    }

    /**
     * @param Element_OphTrOperationnote_ProcedureList $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphTrOperationnote_ProcedureList($element, $data, $index)
    {
        $procs = array();
        if (isset($data['Procedures_procs'])) {
            foreach ($data['Procedures_procs'] as $proc_id) {
                $procs[] = Procedure::model()->findByPk($proc_id);
            }
        }
        $element->procedures = $procs;
    }

    /**
     * @param Element_OphTrOperationnote_ProcedureList $element
     * @param array $data
     * @param int $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationnote_ProcedureList($element, $data, $index)
    {
        $element->updateProcedures(isset($data['Procedures_procs']) ? $data['Procedures_procs'] : array());
    }

    /**
     * Update the anaesthetic agents and complications.
     *
     * @param Element_OphTrOperationnote_Anaesthetic $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationnote_Anaesthetic($element, $data, $index)
    {
        $element->updateAnaestheticAgents(isset($data['AnaestheticAgent']) ? $data['AnaestheticAgent'] : array());
        $element->updateComplications(isset($data['OphTrOperationnote_AnaestheticComplications']) ? $data['OphTrOperationnote_AnaestheticComplications'] : array());

        $element->updateAnaestheticType(isset($data['AnaestheticType']) ? $data['AnaestheticType'] : array());
        $element->updateAnaestheticDelivery(isset($data['AnaestheticDelivery']) ? $data['AnaestheticDelivery'] : array());
    }

    /**
     * Set complications and operative devices for validation.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphTrOperationnote_Cataract($element, $data, $index)
    {
        $complications = array();
        if (isset($data['OphTrOperationnote_CataractComplications']) && is_array($data['OphTrOperationnote_CataractComplications'])) {
            foreach ($data['OphTrOperationnote_CataractComplications'] as $c_id) {
                $complications[] = OphTrOperationnote_CataractComplications::model()->findByPk($c_id);
            }
        }

        if (isset($data['Element_OphTrOperationnote_ProcedureList']['eye_id'])) {
            $element->setEye(\Eye::model()->findByPk($data['Element_OphTrOperationnote_ProcedureList']['eye_id']));
        }

        $element->complications = $complications;

        $devices = array();
        if (isset($data['OphTrOperationnote_CataractOperativeDevices']) && is_array($data['OphTrOperationnote_CataractOperativeDevices'])) {
            foreach ($data['OphTrOperationnote_CataractOperativeDevices'] as $oa_id) {
                $devices[] = OphTrOperationnote_CataractOperativeDevice::model()->findByPk($oa_id);
            }
        }
        $element->operative_devices = $devices;
    }

    /**
     * Update the complications and the operative devices.
     *
     * @param Element_OphTrOperationnote_Cataract $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationnote_Cataract($element, $data, $index)
    {
        $element->updateComplications(isset($data['OphTrOperationnote_CataractComplications']) ? $data['OphTrOperationnote_CataractComplications'] : array());
        $element->updateOperativeDevices(isset($data['OphTrOperationnote_CataractOperativeDevices']) ? $data['OphTrOperationnote_CataractOperativeDevices'] : array());
                $procedure_list  = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', [$element->event_id]);
                $eye = $procedure_list->eye;
                $this->patient->removeBiologicalLensDiagnoses($eye);
    }

    /**
     * Set the drugs for the element.
     *
     * @param Element_OphTrOperationnote_PostOpDrugs $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphTrOperationnote_PostOpDrugs($element, $data, $index)
    {
        $drugs = array();
        if (isset($data['Drug']) && (!empty($data['Drug']))) {
            foreach ($data['Drug'] as $d_id) {
                $drugs[] = OphTrOperationnote_PostopDrug::model()->findByPk($d_id);
            }
        }
        $element->drugs = $drugs;
    }

    /**
     * Update the drug assignments.
     *
     * @param Element_OphTrOperationnote_PostOpDrugs $element
     * @param $data
     * @param $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationnote_PostOpDrugs($element, $data, $index)
    {
        $element->updateDrugs(isset($data['Drug']) ? $data['Drug'] : array());
    }

    /**
     * Set the complications for the Element_OphTrOperationote_Trabectome element.
     *
     * @param $element
     * @param $data
     * @param $index
     */
    protected function setComplexAttributes_Element_OphTrOperationnote_Trabectome($element, $data, $index)
    {
        $model_name = CHtml::modelName($element);
        $complications = array();
        if (@$data[$model_name]['complications']) {
            foreach ($data[$model_name]['complications'] as $id) {
                $complications[] = OphTrOperationnote_Trabectome_Complication::model()->findByPk($id);
            }
        }
        $element->complications = $complications;
    }

    protected function saveComplexAttributes_Element_OphTrOperationnote_Trabectome($element, $data, $index)
    {
        $model_name = CHtml::modelName($element);
        $element->updateComplications(isset($data[$model_name]['complications']) ? $data[$model_name]['complications'] : array());
    }

    protected function setComplexAttributes_Element_OphTrOperationnote_Trabeculectomy($element, $data, $index)
    {
        $difficulties = array();

        if (!empty($data['MultiSelect_Difficulties'])) {
            foreach ($data['MultiSelect_Difficulties'] as $difficulty_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Difficulties();
                $assignment->difficulty_id = $difficulty_id;

                $difficulties[] = $assignment;
            }
        }

        $element->difficulties = $difficulties;

        $complications = array();

        if (!empty($data['MultiSelect_Complications'])) {
            foreach ($data['MultiSelect_Complications'] as $complication_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Complications();
                $assignment->complication_id = $complication_id;

                $complications[] = $assignment;
            }
        }

        $element->complications = $complications;
    }

    protected function setComplexAttributes_Element_OphTrOperationnote_Anaesthetic($element, $data, $index)
    {
        //AnaestheticType
        $type_assessments = array();
        if (isset($data['AnaestheticType']) && is_array($data['AnaestheticType'])) {
            $type_assessments_by_id = array();
            foreach ($element->anaesthetic_type_assignments as $type_assignments) {
                $type_assessments_by_id[$type_assignments->anaesthetic_type_id] = $type_assignments;
            }

            foreach ($data['AnaestheticType'] as $anaesthetic_type_id) {
                if (!array_key_exists($anaesthetic_type_id, $type_assessments_by_id)) {
                    $anaesthetic_type_assesment = new OphTrOperationnote_OperationAnaestheticType();
                } else {
                    $anaesthetic_type_assesment = $type_assessments_by_id[$anaesthetic_type_id];
                }

                $anaesthetic_type_assesment->et_ophtroperationnote_anaesthetic_id = $element->id;
                $anaesthetic_type_assesment->anaesthetic_type_id = $anaesthetic_type_id;

                $type_assessments[] = $anaesthetic_type_assesment;
            }
        }

        $element->anaesthetic_type_assignments = $type_assessments;

        $anaesthetic_GA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'GA'))->queryScalar();
        if (count($element->anaesthetic_type_assignments) == 1 && $element->anaesthetic_type_assignments[0]->anaesthetic_type_id == $anaesthetic_GA_id) {
            $data['AnaestheticDelivery'] = array(
                Yii::app()->db->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Other'))->queryScalar()
            );

            $element->anaesthetist_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetist')->where('name=:name', array(':name' => 'Anaesthetist'))->queryScalar();
        }

        $anaesthetic_NoA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('code=:code', array(':code' => 'NoA'))->queryScalar();
        if (count($element->anaesthetic_type_assignments) == 1 && $element->anaesthetic_type_assignments[0]->anaesthetic_type_id == $anaesthetic_NoA_id) {
            $data['AnaestheticDelivery'] = array();
            $element->anaesthetist_id = null;
        }

        //AnaestheticDelivery
        $delivery_assessments = array();
        if (isset($data['AnaestheticDelivery']) && is_array($data['AnaestheticDelivery'])) {
            $delivery_assessments_by_id = array();
            foreach ($element->anaesthetic_delivery_assignments as $delivery_assignments) {
                $delivery_assessments_by_id[$delivery_assignments->anaesthetic_delivery_id] = $delivery_assignments;
            }

            foreach ($data['AnaestheticDelivery'] as $anaesthetic_delivery_id) {
                if (!array_key_exists($anaesthetic_delivery_id, $delivery_assessments_by_id)) {
                    $anaesthetic_delivery_assesment = new OphTrOperationnote_OperationAnaestheticDelivery();
                } else {
                    $anaesthetic_delivery_assesment = $delivery_assessments_by_id[$anaesthetic_delivery_id];
                }

                $anaesthetic_delivery_assesment->et_ophtroperationnote_anaesthetic_id = $element->id;
                $anaesthetic_delivery_assesment->anaesthetic_delivery_id = $anaesthetic_delivery_id;

                $delivery_assessments[] = $anaesthetic_delivery_assesment;
            }
        }

        $element->anaesthetic_delivery_assignments = $delivery_assessments;
    }

    protected function saveComplexAttributes_Element_OphTrOperationnote_Trabeculectomy($element, $data, $index)
    {
        $element->updateMultiSelectData('OphTrOperationnote_Trabeculectomy_Difficulties', empty($data['MultiSelect_Difficulties']) ? array() : $data['MultiSelect_Difficulties'], 'difficulty_id');
        $element->updateMultiSelectData('OphTrOperationnote_Trabeculectomy_Complications', empty($data['MultiSelect_Complications']) ? array() : $data['MultiSelect_Complications'], 'complication_id');
    }

    protected function afterUpdateElements($event)
    {
        parent::afterUpdateElements($event);
        $this->persistPcrRisk();
    }

    private function createCorrespondenceEvent($macro_name = null)
    {
        $correspondence_api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        if (empty($macro_name)) {
            $macro_name = \SettingMetadata::model()->getSetting("default_{$this->event->episode->status->key}_letter");
        }
        $macro = $correspondence_api->getDefaultMacroByEpisodeStatus($this->event->episode, $firm, Yii::app()->session['selected_site_id'], $macro_name);

        $success = false;

        if ($macro) {
            $name = addcslashes($this->event->episode->status->name, '%_'); // escape LIKE's special characters
            $criteria = new CDbCriteria(array(
                'condition' => "name LIKE :name",
                'params'    => array(':name' => "$name%")
            ));

            $letter_type = \LetterType::model()->find($criteria);
            $letter_type_id = $letter_type ? $letter_type->id : null;

            $correspondence_creator = new CorrespondenceCreator($this->event->episode, $macro, $letter_type_id);
            $correspondence_creator->save();

            $success = !$correspondence_creator->hasErrors();
            $errors = $correspondence_creator->getErrors();
        } else {
            $msg = "Unable to create default Letter because: No macro named '{$macro_name}' was found";
            $errors[] = [$msg];

            \Yii::app()->user->setFlash('issue.correspondence', $msg);
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    /**
     * @inheritdoc
     */

    protected function setAndValidateElementsFromData($data)
    {
        $errors = array();
        $elements = array();

        // only process data for elements that are part of the element type set for the controller event type
        foreach ($this->event_type->getAllElementTypes() as $element_type) {
            $from_data = $this->getElementsForElementType($element_type, $data);
            if (count($from_data) > 0) {
                $elements = array_merge($elements, $from_data);
            } elseif ($element_type->required && (!method_exists($element_type->getInstance(), "isEnabled") || $element_type->getInstance()->isEnabled())) {
                $errors[$this->event_type->name][] = $element_type->name . ' is required';
                $elements[] = $element_type->getInstance();
            }
        }

        // Filter disabled elements from validation

        $elements = array_filter($elements, function ($e) {
            return !method_exists($e, "isEnabled") || $e->isEnabled();
        });

        if (!count($elements)) {
            $errors[$this->event_type->name][] = 'Cannot create an event without at least one element';
        }

        // assign
        $this->open_elements = $elements;

        // validate
        foreach ($this->open_elements as $element) {
            $this->setValidationScenarioForElement($element);
            if (!$element->validate()) {
                $name = $element->getElementTypeName();
                foreach ($element->getErrors() as $errormsgs) {
                    foreach ($errormsgs as $error) {
                        $errors[$name][] = $error;
                    }
                }
            }
        }

        //event date
        if (isset($data['Event']['event_date'])) {
            $this->setEventDate($data['Event']['event_date']);
            $event = $this->event;
            if (!$event->validate()) {
                foreach ($event->getErrors() as $errormsgs) {
                    foreach ($errormsgs as $error) {
                        $errors[$this->event_type->name][] = $error;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @inheritdoc
     */
    public function getExtraTitleInfo()
    {
        if ($this->getAction()->id === 'view') {
            /* @var Element_OphTrOperationnote_SiteTheatre */
            $element = $this->event->getElementByClass('Element_OphTrOperationnote_SiteTheatre');

            if (!$element) {
                return null;
            }

            return '<div class="extra-info">' .
                '<small class="fade">Site: </small><small>' .
                $element->site->name . ', ' . ($element->theatre->name ?? 'None') . '</small>' .
                '</div>';
        }
        return null;
    }

    /**
     * @param $proc_id
     * @return OphTrOperationnote_Attribute[]
     *
     * Returns attributes that belong
     * to the given procedure
     *
     * @param $proc_id
     * @return mixed
     */
    protected function getAttributesForProcedure($proc_id)
    {
        $crit = new CDbCriteria();
        $crit->compare('proc_id', $proc_id);
        $crit->order = "display_order";

        return OphTrOperationnote_Attribute::model()->findAll($crit);
    }

    protected function logEventCreationFail($errors, $module, $model)
    {
        $log = print_r($errors, true);
        \Audit::add('event', 'create-failed', 'Automatic Event creation Failed<pre>' . $log . '</pre>', $log, [
            'module' => $module,
            'episode_id' => $this->event->episode->id,
            'patient_id' => $this->patient->id,
            'model' => $model
        ]);
    }
}
