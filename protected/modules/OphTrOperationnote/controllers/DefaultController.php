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
        'getUserSettingsValues' => self::ACTION_TYPE_FORM,
        'verifyProcedure' => self::ACTION_TYPE_FORM,
        'getImage' => self::ACTION_TYPE_FORM,
        'getTheatreOptions' => self::ACTION_TYPE_FORM,
        'whiteboard' => self::ACTION_TYPE_VIEW,
        'findTemplatesFor' => self::ACTION_TYPE_FORM,
    );

    /* @var Element_OphTrOperationbooking_Operation operation that this note is for when creating */
    protected $booking_operation;
    /* @var boolean - indicates if this note is for an unbooked procedure or not when creating */
    protected $unbooked = false;
    /* @var Proc[] - cache of bookings for the booking operation */
    protected $booking_procedures;
    /* @var boolean - indicates if this note is a outpatient minor op or not when creating */
    protected $outpatient_minor_op = false;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'afterCreateEventGenerate' => [
                'class' => 'CreateEventsAfterEventSavedBehavior',
                'determine_eye_from_element' => 'Element_OphTrOperationnote_ProcedureList'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function afterCreateEvent($event)
    {
        // implemented in CreateEventsAfterEventSavedBehavior
        $this->checkAndCreatePrescriptionEvent();
        $this->checkAndCreateCorrespondenceEvent();
        $this->checkAndCreateOptomCorrespondenceEvent();

        parent::afterCreateEvent($event);
    }

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
                $this->redirect(
                    '/OphTrOperationnote/Default/create?patient_id='
                        . $this->patient->id
                        . '&booking_event_id=' . $m[1]
                        . ($_POST['template_id'] ? ('&template_id=' . $_POST['template_id']) : '')
                );
            } elseif (@$_POST['SelectBooking'] == 'emergency') {
                $this->redirect(array('/OphTrOperationnote/Default/create?patient_id=' . $this->patient->id . '&unbooked=1&unbooked_type=emergency'));
            } elseif (@$_POST['SelectBooking'] == 'outpatient-minor-op') {
                $this->redirect(array('/OphTrOperationnote/Default/create?patient_id=' . $this->patient->id . '&unbooked=1&unbooked_type=outpatient_minor_op'));
            }

            $errors = array('Operation' => array('Please select a booked operation'));
        }

        if ($this->booking_operation || $this->unbooked) {
            $this->createOpNote();
        } else {
            // set up form for selecting a booking for the Op note
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

    public function actionUpdate($id)
    {
        if (empty($_GET['template_id']) && empty($_GET['template_clear']) && $this->event->template_id) {
            $this->redirect(
                '/OphTrOperationnote/Default/update/' . $this->event->id
                    . '?template_id=' . $this->event->template_id
            );
        } else {
            if (!empty($_GET['template_clear'])) {
                $this->event->template_id = null;
            } elseif (!empty($_POST) && $this->template && $this->template->id !== $this->event->template_id) {
                $this->event->template_id = $this->template->id;
            }

            return parent::actionUpdate($id);
        }
    }

    public function actionWhiteboard($id)
    {
        $this->redirect(Yii::app()->createUrl('/OphTrOperationbooking/whiteboard/view/' . $id));
    }

    protected function setOpenElementsFromCurrentEvent($action)
    {
        if ($action === 'create' || ($this->template && $this->template->id !== $this->event->template_id)) {
            $template_data = json_decode($this->template ? $this->template->getDetailRecord()->template_data : '{}', true);

            $eye = null;

            if ($this->unbooked) {
                $eye_id = $this->getApp()->request->getParam('eye');
                $eye = Eye::model()->findByPk($eye_id);
            }

            $this->open_elements = $this->buildEventElements($template_data, $this->getEventElements(), $eye);
        } else {
            if (!empty($_POST) && $this->template && $this->template->id !== $this->event->template_id) {
                $this->event->template_id = $this->template->id;
            }

            parent::setOpenElementsFromCurrentEvent($action);
        }
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
            if ($this->external_errors) {
                $errors = array_merge($errors, $this->external_errors);
            }

            // creation
            if (empty($errors)) {
                $transaction = Yii::app()->db->beginTransaction();

                try {
                    $this->event->template_id = $this->template->id ?? null;
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
                        /*
                         * After event saved and transaction is committed
                         * here we can generate additional events with their own transactions
                         */
                        $this->afterCreateEvent($this->event);

                        if ($this->event->parent_id) {
                            $this->redirect(Yii::app()->createUrl('/' . $this->event->parent->eventType->class_name . '/default/view/' . $this->event->parent_id));
                        } else {
                            if (!empty($this->template)) {
                                $existing_template_data = json_decode($this->template->getDetailRecord()->template_data, true);

                                $template_status = $this->event->getTemplateUpdateStatusForEvent($existing_template_data);

                                if ($template_status !== 'UNNEEDED') {
                                    $this->redirect(array($this->successUri . $this->event->id . '?template=' . $template_status));
                                } else {
                                    $this->redirect(array($this->successUri . $this->event->id));
                                }
                            } else {
                                $this->redirect(array($this->successUri . $this->event->id . '?template=' . EventTemplate::UPDATE_CREATE_ONLY));
                            }
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

        if ($this->unbooked) {
            $templates_for_unbooked = OphTrOperationnote_Template::model()
                ->forUserId(\Yii::app()->user->id)
                ->findAll();
        }

        $this->render('create', array(
            'errors' => @$errors,
            'outpatient_minor_op' => $this->outpatient_minor_op,
            'templates_for_unbooked' => $templates_for_unbooked ?? []
        ));
    }

    public function getDefaultsContextData()
    {
        return array(
            'patient' => $this->patient,
            'booking' => $this->booking_operation,
            'event' => $this->event,
            'booking_procedures' => $this->getBookingProcedures(),
            'site' => Yii::app()->session['selected_site_id'],
            'firm' => $this->firm,
            'action' => $this->action->id,
            'controller' => $this
        );
    }


    protected function afterCreateElements($event)
    {
        parent::afterCreateElements($event);
        $this->persistPcrRisk();
    }

    private function createPrescriptionEvent()
    {
        $drug_set_name = SettingMetadata::model()->getSetting("default_{$this->event->episode->status->key}_drug_set");
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
            $prescription_creator->elements['Element_OphDrPrescription_Details']->draft = !Yii::app()->user->checkAccess('OprnCreatePrescription');
            $prescription_creator->save();

            $success = !$prescription_creator->hasErrors();
            $errors = $prescription_creator->getErrors();
        } else {
            $msg = "Unable to create default Prescription because: No drug set named '{$drug_set_name}' was found";
            $errors[] = [$msg];
            $errors[] = $params; // these are only going to the logs and audit, not displayed to the user

            Yii::app()->user->setFlash('issue.prescription', $msg);
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
     * @throws Exception
     */
    public function actionLoadElementByProcedure()
    {
        if (!$proc = Procedure::model()->findByPk((int)@$_GET['procedure_id'])) {
            throw new SystemException('Procedure not found: ' . @$_GET['procedure_id']);
        }

        if (!$patient_id = $this->getApp()->request->getParam('patient_id')) {
            throw new SystemException('patient_id required for procedure element loading.');
        }
        $this->setPatient($patient_id);

        $form = new BaseEventTypeCActiveForm();

        $procedureSpecificElements = $this->getProcedureSpecificElements($proc->id);

        $template_data = array();

        if ($this->template) {
            $template_detail = $this->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        } elseif ($this->event && $this->event->template) {
            $template_detail = $this->event->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        } elseif ($template_id = \Yii::app()->request->getParam('template_id')) {
            if ($this->template = \EventTemplate::model()->findByPk($template_id)) {
                $template_detail = $this->template->getDetailRecord();
                $template_data = json_decode($template_detail->template_data, true);
            }
        }

        if (count($procedureSpecificElements) === 0) {
            $element = new Element_OphTrOperationnote_GenericProcedure();
            $element->proc_id = $proc->id;

            if (!empty($template_data)) {
                $processed_elements = $this->buildEventElements($template_data, [$element]);
            } else {
                $element->setDefaultOptions($this->patient);

                $processed_elements = [$element];
            }
        } elseif (!empty($template_data)) {
            $processed_elements = $this->buildEventElements($template_data, $procedureSpecificElements);
        } else {
            $processed_elements = array_map(function ($element) {
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

                return $element;
            }, $procedureSpecificElements);
        }

        foreach ($processed_elements as $i => $element) {
            $postProcess = ($i == count($processed_elements) - 1);

            $element_class = $element->elementType->class_name;
            $template_data_exists = !empty($template_data) && array_key_exists($element_class, $template_data);
            $element_template_data = $template_data_exists ? $template_data[$element_class] : [];

            $this->renderElement(
                $element,
                'create',
                $form,
                array(),
                $element_template_data,
                array('ondemand' => true),
                false,
                $postProcess
            );
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
            if (
                empty($procedures)
                || !OphTrOperationnote_ProcedureListOperationElement::model()->find(
                    'procedure_id in (' . implode(',', $procedures) . ') and element_type_id = ' . $element->element_type->id
                )
            ) {
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
        $template_data = array();
        if ($this->template) {
            $template_detail = $this->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        } elseif ($this->event && $this->event->template) {
            $template_detail = $this->event->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        }
        foreach ($this->open_elements as $el) {
            if (is_subclass_of($el, 'Element_OnDemand')) {
                $element_class = $el->elementType->class_name;
                $template_data_exists = !empty($template_data) && array_key_exists($element_class, $template_data);
                $element_template_data = $template_data_exists ? $template_data[$element_class] : [];
                $this->renderElement(
                    $el,
                    $action,
                    $form,
                    $data,
                    $element_template_data
                );
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
            $procedures = $this->getBookingProcedures();

            if (empty($procedures) && $this->unbooked) {
                $procedures = $this->getTemplateProcedures();
            }

            if ($procedures) {
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
        return array();
    }

    /**
     * returns list of procudures for the template set on the controller.
     *
     * @return Proc[]
     */
    protected function getTemplateProcedures()
    {
        if ($this->template) {
            return $this->template->opnote_templates->procedure_set->procedures;
        }

        return [];
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

    public function getElementDefaultOptions_Element_OphTrOperationnote_ProcedureList($element, $action)
    {
        $fields = array();
        if ($action == 'create' && $procedures = $this->getBookingProcedures()) {
            $fields['procedures'] = array_map(
                static function ($item) {
                    return $item->id;
                },
                $procedures
            );

            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $fields['eye_id'] = $api->getEyeForOperation($this->booking_operation->event_id)->id;
            $fields['booking_event_id'] = $this->booking_operation->event_id;
        }
        return $fields;
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

    public function getElementDefaultOptions_Element_OphTrOperationnote_Anaesthetic($element, $action)
    {
        $fields = array();
        if ($action == 'create') {
            if ($this->booking_operation) {
                $fields['anaesthetic_type'] = $this->booking_operation->anaesthetic_type;
            } else {
                $key = $this->patient->isChild() ? 'ophtroperationnote_default_anaesthetic_child' : 'ophtroperationnote_default_anaesthetic';

                if (isset(Yii::app()->params[$key])) {
                    if ($at = AnaestheticType::model()->find('code=?', array(Yii::app()->params[$key]))) {
                        $fields['anaesthetic_type'] = array($at);
                    }
                }
            }
            $fields['anaesthetic_agents'] = $this->getAnaestheticAgentsBySiteAndSubspecialty('siteSubspecialtyAssignmentDefaults');
        }
        return $fields;
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

    public function getElementDefaultOptions_Element_OphTrOperationnote_PostOpDrugs($element, $action)
    {
        $fields = array();
        if ($action == 'create') {
            $efields['drugs'] = $this->getPostOpDrugsBySiteAndSubspecialty(true);
        }
        return $fields;
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

    public function getElementDefaultOptions_Element_OphTrOperationnote_Cataract($element, $action)
    {
        $fields = array();
        if ($action == 'create') {
            $fields['operative_devices'] = $this->getOperativeDevicesBySiteAndSubspecialty(true);
        }
        return $fields;
    }

    protected function getUserSettings($surgeon_id): array
    {
        $element_type = ElementType::model()->find('class_name = :class_name', array(':class_name' => 'Element_OphTrOperationnote_Cataract'));
        $user_settings = SettingUser::model()->findAll('user_id = :user_id AND element_type_id = :element_type_id', array(':user_id' => $surgeon_id, ':element_type_id' => $element_type->id));
        $settings = [];

        foreach ($user_settings as $key => $user_setting) {
            $settings[$user_setting->key] = $user_setting->value;
        }

        return $settings;
    }

    protected function setOpNoteSettings($op_note_user_settings)
    {
        if ($op_note_user_settings) {
            $this->jsVars['number_of_ports'] = ($op_note_user_settings['number_of_ports'] ?? 2);
            $this->jsVars['surgeon_position'] = [0 => $op_note_user_settings['surgeon_position_right_eye'], 1 => $op_note_user_settings['surgeon_position_left_eye']];
            $this->jsVars['incision_centre_position'] = [0 => $op_note_user_settings['incision_centre_position_right_eye'], 1 => $op_note_user_settings['incision_centre_position_left_eye']];
            parent::processJsVars();
        }
    }

    public function actionGetUserSettingsValues($surgeon_id)
    {
        echo json_encode($this->getUserSettings($surgeon_id));
    }

    public function actionFindTemplatesFor()
    {
        $surgeon_id = \Yii::app()->request->getParam('surgeon_id');
        $procedures = \Yii::app()->request->getParam('procedures');

        $procedure_set = ProcedureSet::findForProcedures($procedures);

        if ($procedure_set) {
            $templates_criteria = new CDbCriteria();

            $templates_criteria->join = 'JOIN ophtroperationnote_template ont ON ont.event_template_id = t.id JOIN event_template_user etu ON etu.event_template_id = t.id';
            $templates_criteria->addCondition('user_id = :user_id');
            $templates_criteria->addCondition('proc_set_id = :procedure_set_id');
            $templates_criteria->params = [':user_id' => $surgeon_id, ':procedure_set_id' => $procedure_set->id];

            $procedures = array_map(static function ($procedure) {
                return $procedure->term;
            }, $procedure_set->procedures);
            $templates = CHtml::listData(EventTemplate::model()->findAll($templates_criteria), 'id', 'name');

            $this->renderJSON(['procedures' => $procedures, 'templates' => $templates]);
        } else {
            $this->renderJSON(null);
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
        $surgeon_id = Yii::app()->user->id;

        if (\Yii::app()->request->isPostRequest) {
            $surgeoun_element_array = \Yii::app()->request->getParam('Element_OphTrOperationnote_Surgeon');
            $surgeon_id = $surgeoun_element_array['surgeon_id'] ?? $surgeon_id;
        }

        if (isset($_GET['booking_event_id'])) {
            if (!$api) {
                throw new Exception('invalid request for booking event');
            }
            if (!$this->booking_operation = $api->getOperationForEvent($_GET['booking_event_id'])) {
                throw new Exception('booking event not found');
            }
        } elseif (isset($_GET['unbooked'])) {
            $this->unbooked = true;

            if (isset($_GET['unbooked_type']) && $_GET['unbooked_type'] === 'outpatient_minor_op') {
                $this->outpatient_minor_op = true;
            }
        }

        $this->initEdit();
        $op_note_user_settings = $this->getUserSettings($surgeon_id);
        $this->setOpNoteSettings($op_note_user_settings);
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
     * Initialise a dummy event for loading elements
     *
     */
    protected function initActionLoadElementByProcedure()
    {
        if (isset($_GET['event_id'])) {
            $this->initWithEventId($_GET['event_id']);
        } else {
            parent::initActionCreate();
        }

        if (isset($_GET['template_id'])) {
            $this->template = EventTemplate::model()->findByPk($_GET['template_id']);
        }
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
            $element->setEye(Eye::model()->findByPk($data['Element_OphTrOperationnote_ProcedureList']['eye_id']));
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

        $anaesthetic_GA_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('anaesthetic_type')
            ->where('name=:name', array(':name' => 'GA'))
            ->queryScalar();
        if (count($element->anaesthetic_type_assignments) == 1 && $element->anaesthetic_type_assignments[0]->anaesthetic_type_id == $anaesthetic_GA_id) {
            $data['AnaestheticDelivery'] = array(
                Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('anaesthetic_delivery')
                    ->where('name=:name', array(':name' => 'Other'))
                    ->queryScalar()
            );

            $element->anaesthetist_id = Yii::app()->db->createCommand()
                ->select('id')
                ->from('anaesthetist')
                ->where('name=:name', array(':name' => 'Anaesthetist'))
                ->queryScalar();
        }

        $anaesthetic_NoA_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('anaesthetic_type')
            ->where('code=:code', array(':code' => 'NoA'))
            ->queryScalar();
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
            $macro_name = SettingMetadata::model()->getSetting("default_{$this->event->episode->status->key}_letter");
        }
        $macro = $correspondence_api->getDefaultMacroByEpisodeStatus($this->event->episode, $firm, Yii::app()->session['selected_site_id'], $macro_name);

        $success = false;

        if ($macro) {
            $correspondence_creator = new CorrespondenceCreator($this->event->episode, $macro, $macro->letter_type_id);
            $correspondence_creator->save();

            $success = !$correspondence_creator->hasErrors();
            $errors = $correspondence_creator->getErrors();
        } else {
            $msg = "Unable to create default Letter because: No macro named '{$macro_name}' was found";
            $errors[] = [$msg];

            Yii::app()->user->setFlash('issue.correspondence', $msg);
        }

        return [
            'success' => $success,
            'errors' => $errors
        ];
    }

    /**
     * We set the validation scenario for the models based on whether the user is creating a minor outpatient operation
     * note or not.
     *
     * @param $element
     */
    protected function setValidationScenarioForElement($element)
    {
        if ($this->outpatient_minor_op) {
            switch (get_class($element)) {
                case 'Element_OphTrOperationnote_SiteTheatre':
                    $element->setScenario('outpatient_minor_operation');
                    break;
            }
        }
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

            if ($element_type->class_name === "Element_OphTrOperationnote_Cataract"  && in_array($element_type, $elements)) {
                $elements[] = new Element_OphTrOperationnote_Biometry();
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

    private function buildEventElements($data, $elements, $eye = null)
    {
        $context = $this->getDefaultsContextData();

        if ($eye) {
            $context['unbooked_eye'] = $eye;
        }

        $event_defaults =
            Yii::app()
            ->eventDefaults
            ->forEventType($this->event_type)
            ->forElements($elements)
            ->withContext($context)
            ->getDefaults();

        $unprocessed_elements =
            Yii::app()
            ->eventBuilder
            ->forEventType($this->event_type)
            ->forElements($elements)
            ->getElements();

        $priorities = \EventTemplate::getPrefillablePriorities($unprocessed_elements);

        $built_elements =
            Yii::app()
            ->eventBuilder
            ->setPriorities($priorities)
            ->addData($event_defaults, \EventTemplate::PRIORITY_PATIENT)
            ->addData($data, \EventTemplate::PRIORITY_TEMPLATE)
            ->applyData()
            ->getElements();

        return $built_elements;
    }
}
