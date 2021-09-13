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

use OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures;

class DefaultController extends BaseEventTypeController
{
    private $elementFilterList = [ 'Element_OphTrConsent_Other' ];
    protected static $action_types = array(
        'users' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
        'benefits' => self::ACTION_TYPE_FORM,
        'complications' => self::ACTION_TYPE_FORM,
        'createEventImages' => self::ACTION_TYPE_PRINT,
        'saveCapturedSignature' => self::ACTION_TYPE_FORM,
        'getSignatureByUsernameAndPin' => self::ACTION_TYPE_FORM,
        'postSignRequest' => self::ACTION_TYPE_FORM,
        'sign' => self::ACTION_TYPE_EDIT,
        'contactPage' => self::ACTION_TYPE_FORM,
        'getDeleteConsentPopupContent' => self::ACTION_TYPE_FORM,
    );

    public $booking_event;
    public $booking_operation;
    public $unbooked = false;
    public $type_id = null;
    public $template;

    public function actions()
    {
        return [
            'saveCapturedSignature' => [
                'class' => \SaveCapturedSignatureAction::class,
            ],
            'getSignatureByUsernameAndPin' => [
                'class' => \GetSignatureByUsernameAndPinAction::class,
            ],
            'postSignRequest' => [
                'class' => PostSignRequestAction::class,
            ],
        ];
    }

    protected function beforeAction($action)
    {
        //adding Anaestethic JS
        $url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphTrOperationnote.assets.js'), true);
        Yii::app()->clientScript->registerScriptFile($url . '/OpenEyes.UI.OphTrOperationnote.Anaesthetic.js');
        Yii::app()->clientScript->registerScript(
            'AnaestheticController',
            'new OpenEyes.OphTrOperationnote.AnaestheticController({ typeSelector: \'#Element_OphTrConsent_Procedure_AnaestheticType\'});',
            CClientScript::POS_END
        );

        return parent::beforeAction($action);
    }

    /**
     * Set up procedures from booking event.
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrConsent_Procedure($element, $action)
    {
        if ($action == 'create' && $this->booking_event) {
            $element->booking_event_id = $this->booking_event->id;
            if ($this->booking_operation) {
                $eye_id = $this->booking_operation->eye_id ?? null;
                $type_assessments_by_id = array();
                foreach ($element->anaesthetic_type_assignments as $type_assignments) {
                    $type_assessments_by_id[$type_assignments->anaesthetic_type_id] = $type_assignments;
                }

                //$element->anaesthetic_type_id = $this->booking_operation->anaesthetic_type_id;
                $anaesthetic_types = array();
                if ($this->booking_operation->anaesthetic_type) {
                    foreach ($this->booking_operation->anaesthetic_type as $anaesthetic_type) {
                        if ( !array_key_exists($anaesthetic_type->id, $type_assessments_by_id) ) {
                            $anaesthetic_type_assesment = new OphTrConsent_Procedure_AnaestheticType();
                        } else {
                            $anaesthetic_type_assesment = $type_assessments_by_id[$anaesthetic_type->id];
                        }

                        $anaesthetic_type_assesment->et_ophtrconsent_procedure_id = $element->id;
                        $anaesthetic_type_assesment->anaesthetic_type_id = $anaesthetic_type->id;

                        $type_assessments[] = $anaesthetic_type_assesment;
                        $anaesthetic_types[] = $anaesthetic_type;
                    }

                    $element->anaesthetic_type_assignments = $type_assessments;
                    $element->anaesthetic_type = $anaesthetic_types;
                }
                $assigned_procedures = array();
                foreach ($this->booking_operation->procedureItems as $booked_proc) {
                    $assigned_proc = new OphtrconsentProcedureProceduresProcedures();
                    $assigned_proc->proc_id = $booked_proc->proc_id;
                    $assigned_proc->eye_id = $booked_proc->eye_id ?? $eye_id;
                    $assigned_procedures[] = $assigned_proc;
                    // regard the additional proc as normal one
                    foreach ($booked_proc->procedure->additional as $additional_proc) {
                        $add_proc = new OphtrconsentProcedureProceduresProcedures();
                        $add_proc->proc_id = $additional_proc->id;
                        $add_proc->eye_id = $booked_proc->eye_id ?? $eye_id;
                        $assigned_procedures[] = $add_proc;
                    }
                }
                $element->procedure_assignments = $assigned_procedures;
            }
        }
    }

    /**
     * Set up benefits and risks from booking event procedures.
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrConsent_BenefitsAndRisks($element, $action)
    {
        if ($action == 'create' && $this->booking_operation) {
            $element->setBenefitsAndRisksFromProcedures($this->booking_operation->procedures);
        } elseif ($action == 'create' && $this->template) {
            $element->setBenefitsAndRisksFromProcedures($this->template->procedures);
        }
    }

    /**
     * Set the consultant id.
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrConsent_Other($element, $action)
    {
        if ($action == 'create') {
            if ($this->firm->consultant) {
                $element->consultant_id = $this->firm->consultant->id;
            }
        }
    }

    /**
     * Set the consent type from the child status of the patient.
     *
     * @param $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphTrConsent_Type($element, $action)
    {
        if (is_null($this->type_id)) {
            $element->type_id = Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID;
        } else {
            $element->type_id = $this->type_id;
        }

        if ($action == 'create' && $this->template) {
            $element->type_id = $this->template->type_id;
        } elseif ($action == 'create') {
            $patient_age = (int)$this->patient->getAge();
            if ($patient_age <= 16) {
                $element->type_id = 2;
            }
        }
    }

    protected function setElementDefaultOptions_Element_OphTrConsent_AdditionalSignatures($element, $action)
    {
        if ($action == 'create') {
            $patient_age = (int)$this->patient->getAge();
            if ($patient_age <= 16) {
                $element->cf_type_id = 2;
            } else {
                $element->cf_type_id = $this->type_id;
            }
        } else {
            $element->cf_type_id = $this->type_id;
        }
    }

    /**
     * Process the booking event value setting.
     *
     * @throws Exception
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();

        if (isset($_GET['booking_event_id'])) {
            if (!$api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                throw new Exception('invalid request for booking event');
            }

            if (!($this->booking_event = Event::model()->findByPk($_GET['booking_event_id']))
                || (!$this->booking_operation = $api->getOperationForEvent($_GET['booking_event_id']))) {
                throw new Exception('booking event not found');
            }
        } elseif (isset($_GET['unbooked'])) {
            $this->unbooked = true;
        } elseif (isset($_GET['template_id'])) {
            if (!($this->template = OphTrConsent_Template::model()->findByPk($_GET['template_id']))) {
                throw new Exception('booking event not found');
            }
        }

        if (is_null(Yii::app()->request->getParam("type_id"))) {
            $this->type_id = Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID;
            if ((Yii::app()->request->isPostRequest) && (Yii::app()->request->getPost('Element_OphTrConsent_Type'))) {
                $this->type_id = \Yii::app()->request->getPost('Element_OphTrConsent_Type')['type_id'];
            }
        }

        if (is_null(Yii::app()->request->getParam("type_id"))) {
            $this->type_id = Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID;
        } else {
            $this->type_id = Yii::app()->request->getParam("type_id");
        }
    }

    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        if (is_null(Yii::app()->request->getParam("type_id"))) {
            if ($et = $this->event->getElementByClass(Element_OphTrConsent_Type::class)) {
                $this->type_id = $et->type_id;
            }
        } else {
            $this->type_id = Yii::app()->request->getParam("type_id");
        }
    }

    protected function initActionView()
    {
        parent::initActionView();
        if ($et = $this->event->getElementByClass(Element_OphTrConsent_Type::class)) {
            $this->type_id = $et->type_id;
        }

        if ($et = $this->event->getElementByClass(Element_OphTrConsent_AdditionalSignatures::class)) {
            $et->cf_type_id = $this->type_id;
        }
    }

    /**
     * @param $default_view
     */
    public function renderSidebar($default_view)
    {
        if (!$this->booking_event && !$this->unbooked && !$this->template) {
            $this->show_element_sidebar = false;
        }
        parent::renderSidebar($default_view);
    }

    /**
     * Manage picking an extant booking for setting consent form defaults.
     *
     * (non-phpdoc)
     *
     * @see BaseEventTypeController::actionCreate()
     */
    public function actionCreate()
    {
        $errors = array();
        if (!empty($_POST)) {
            // Save and print clicked, stash print flag
            if (isset($_POST['saveprint'])) {
                Yii::app()->session['printConsent'] = 1;
            }
            if (@$_POST['SelectBooking'] == 'unbooked') {
                $this->redirect(array('/OphTrConsent/Default/create?patient_id='.$this->patient->id.'&unbooked=1'));
            } elseif (preg_match('/^booking([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                $this->redirect(array('/OphTrConsent/Default/create?patient_id='.$this->patient->id.'&booking_event_id='.$m[1]));
            } elseif (preg_match('/^template([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                $this->redirect(array('/OphTrConsent/Default/create?patient_id='.$this->patient->id.'&template_id='.$m[1]));
            }
            $errors = array('Consent form' => array('Please select a booking or Unbooked procedures'));
        }

        if ($this->booking_event || $this->unbooked || $this->template) {
            parent::actionCreate();
        } else {
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $bookings = $api->getIncompleteOperationsForEpisode($this->patient);
            }

            $criteria = new \CDbCriteria();
            $criteria->join = "JOIN `et_ophtrconsent_procedure` pr ON pr.`event_id` = t.`id`
                               JOIN `episode` ep ON t.`episode_id` = ep.`id`";
            $criteria->addCondition('pr.booking_event_id IS NULL');
            $criteria->addCondition('ep.patient_id = ' . $this->patient->id);
            $criteria->addCondition('t.event_type_id = ' . $this->event_type->id);
            $criteria->addCondition('ep.deleted = 0');

            if ($this->event->institution_id) {
                $criteria->addCondition('t.institution_id = ' . $this->event->institution_id);
            }

            if ($this->event->site_id) {
                $criteria->addCondition('t.site_id = ' . $this->event->site_id);
            }

            $no_operation_booking = Event::model()->findAll($criteria);

            $criteria = new \CDbCriteria();

            if ($this->event->institution_id) {
                $institution = $this->event->institution_id;
                if ($institution == NULL) {
                    $criteria->addCondition('institution_id IS NULL');
                } else {
                    $criteria->addCondition('institution_id = ' . $institution . ' OR institution_id IS NULL');
                }
            }

            if ($this->event->site_id) {
                $site = $this->event->site_id;
                if ($site == NULL) {
                    $criteria->addCondition('site_id IS NULL');
                } else {
                    $criteria->addCondition('site_id = ' . $site . ' OR site_id IS NULL');
                }
            }

            if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id) {
                $subspecialty = $this->firm->serviceSubspecialtyAssignment->subspecialty_id;
                if ($subspecialty == NULL) {
                    $criteria->addCondition('subspecialty_id IS NULL');
                } else {
                    $criteria->addCondition('subspecialty_id = ' . $subspecialty . ' OR subspecialty_id IS NULL');
                }
            }
            $templates = OphTrConsent_Template::model()->findAll($criteria);

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
                    array('id' => 'et_cancel', 'class' => 'button small warning')
                ),
            );
            $this->processJsVars();
            $this->render('select_event', array(
                'errors' => $errors,
                'bookings' => $bookings ? $bookings : [],
                'templates' => $templates ? $templates : [],
                'no_operation_booking' => $no_operation_booking ? $no_operation_booking : [],
            ), false, true);
        }
    }

    public function actionUpdate($id)
    {
        parent::actionUpdate($id);
    }

    public function actionView($id)
    {
        $this->extraViewProperties['print'] = Yii::app()->session['printConsent'];
        unset(Yii::app()->session['printConsent']);
        parent::actionView($id);
    }

    /**
     * Print action.
     *
     * @param int $id event id
     */
    public function actionPrint($id)
    {
        $this->printInit($id);
        $this->layout = '//layouts/print';

        $elements = array();

        foreach ($this->getEventElements() as $element) {
            $elements[get_class($element)] = $element;
        }

        preg_match('/^([0-9]+)/', $elements['Element_OphTrConsent_Type']->type->name, $m);
        $template_id = $m[1];

        $template = "print{$template_id}_English";

        $this->render($template, array('elements' => $elements, 'css_class' => isset($_GET['vi']) && $_GET['vi'] ? 'impaired' : 'normal'));
    }

    public function actionPDFPrint($id)
    {
        if (@$_GET['vi']) {
            $this->pdf_print_suffix = 'vi';
        }

        return parent::actionPDFPrint($id);
    }

    /**
     * Create images for "print" version of consent event
     *
     * @param int $id event id
     * @return void
     */
    public function actionCreateEventImages($id)
    {
        $procedure = Element_OphTrConsent_Procedure::model()->find('booking_event_id=?', [$id]);

        // Generate a pdf file for the event
        $pdf_route = $this->setPDFprintData($procedure->event_id, false);

        $pf = ProtectedFile::createFromFile($procedure->event->imageDirectory . '/event_' . $pdf_route . '.pdf');
        $pf->title = 'event_'.$pdf_route.'.pdf';

        if ($pf->save()) {
            // Create preview images of generated pdf file
            $this->createPdfPreviewImages($pf->getPath());
            // Delete pdf file after creating images
            $pf->delete();
        }
    }

    /**
     * Ajax action for getting list of users (json-encoded).
     */
    public function actionUsers()
    {
        $users = array();

        $criteria = new CDbCriteria();

        $criteria->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) LIKE :term"));

        $params[':term'] = '%'.strtolower(strtr($_GET['term'], array('%' => '\%'))).'%';

        $criteria->params = $params;
        $criteria->order = 'first_name, last_name';

        $consultant = null;
        // only want a consultant for medical firms
        if ($specialty = $this->firm->getSpecialty()) {
            if ($specialty->medical) {
                $consultant = $this->firm->consultant;
            }
        }

        foreach (User::model()->findAll($criteria) as $user) {
            if ($contact = $user->contact) {
                $consultant_name = false;

                // if we have a consultant for the firm, and its not the matched user, attach the consultant name to the entry
                if ($consultant && $user->id != $consultant->id) {
                    $consultant_name = trim($consultant->contact->title.' '.$consultant->contact->first_name.' '.$consultant->contact->last_name);
                }

                $users[] = array(
                    'id' => $user->id,
                    'value' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications).' ('.$user->role.')',
                    'fullname' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications),
                    'role' => $user->role,
                    'consultant' => $consultant_name,
                );
            }
        }

        $this->renderJSON($users);
    }

    public function actionDoPrint($id)
    {
        if (!$type = Element_OphTrConsent_Type::model()->find('event_id=?', array($id))) {
            throw new Exception("Consent form not found for event id: $id");
        }

        $type->print = 1;
        $type->draft = 0;

        if (!$type->save()) {
            throw new Exception('Unable to save consent form: ' . print_r($type->getErrors(), true));
        }
        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }
        $event->info = '';
        if (!$event->save()) {
            throw new Exception('Unable to save event: ' . print_r($event->getErrors(), true));
        }
        Yii::app()->session['printConsent'] = isset($_GET['vi']) ? 2 : 1;
        echo '1';
    }

    public function actionMarkPrinted($id)
    {
        if ($type = Element_OphTrConsent_Type::model()->find('event_id=?', array($id))) {
            $type->print = 0;
            $type->draft = 0;
            if (!$type->save()) {
                throw new Exception('Unable to mark consent form printed: ' . print_r($type->getErrors(), true));
            }
        }
    }

    protected function setComplexAttributes_Element_OphTrConsent_Procedure($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element_data = $data[$model_name] ?? array();
        $anaesthetic_data = $data['AnaestheticType'] ?? array();
        $procedures_data = $element_data['procedure_assignments'] ?? array();
        $assigned_anaesthetic_types = array();
        $procedure_assignments = array();
        foreach ($anaesthetic_data as $type_id) {
            $type = AnaestheticType::model()->findByPk($type_id);
            $assigned_anaesthetic_types[] = $type;
        }

        foreach ($procedures_data as $proc) {
            $procedure = new OphtrconsentProcedureProceduresProcedures();
            $procedure->eye_id = $proc['eye_id'];
            $procedure->proc_id = $proc['proc_id'];
            $procedure_assignments[] = $procedure;
        }
        $element->anaesthetic_type = $assigned_anaesthetic_types;
        $element->procedure_assignments = $procedure_assignments;
    }

    protected function setComplexAttributes_Element_OphTrConsent_ExtraProcedures($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $element_data = $data[$model_name] ?? array();
        $procedures_data = $element_data['extra_procedure_assignments'] ?? array();
        $procedure_assignments = array();
        foreach ($procedures_data as $proc) {
            $procedure = new OphTrConsent_Procedure_Extra_Assignment();
            $procedure->extra_proc_id = $proc['proc_id'];
            $procedure_assignments[] = $procedure;
        }
        $element->extra_procedure_assignments = $procedure_assignments;
    }

    protected function saveComplexAttributes_Element_OphTrConsent_PatientAttorneyDeputy($element, $data, $index)
    {
        $patient = \Patient::model()->findByPk($this->patient->id);

        if (
            isset($data['OEModule_OphTrConsent_models_Element_OphTrConsent_PatientAttorneyDeputy']) &&
            isset($data["OEModule_OphTrConsent_models_Element_OphTrConsent_PatientAttorneyDeputy"]['contact_id'])
        ) {
            $contact_ids = $data["OEModule_OphTrConsent_models_Element_OphTrConsent_PatientAttorneyDeputy"]['contact_id'];
            $entries = $data["OEModule_OphTrConsent_models_Element_OphTrConsent_PatientAttorneyDeputy"]['entries'];
        } else {
            $contact_ids = [];
        }
        $criteria = new \CDbCriteria();
        $gp = $this->patient->gp;
        $criteria->addCondition('t.patient_id = ' . $patient->id);
        $criteria->addCondition('t.event_id = ' . $element->event_id);
        $patientContactAssignments = \PatientAttorneyDeputyContact::model()->findAll($criteria);

        foreach ($contact_ids as $key => $contact_id) {
            $foundExistingAssignment = false;
            foreach ($patientContactAssignments as $patientContactAssignment) {
                if ($patientContactAssignment->contact_id == $contact_id) {
                    $patientContactAssignment->authorised_decision_id = $entries[$key]['authorised_decision_id'];
                    $patientContactAssignment->considered_decision_id = $entries[$key]['considered_decision_id'];
                    $patientContactAssignment->event_id = $this->event->id;
                    $patientContactAssignment->save();
                    $foundExistingAssignment = true;
                    break;
                }
            }
            if (!$foundExistingAssignment) {
                $patientContactAssignment = new \PatientAttorneyDeputyContact();
                $patientContactAssignment->patient_id = $patient->id;
                $patientContactAssignment->contact_id = $contact_id;
                $patientContactAssignment->authorised_decision_id = isset($entries[$key]['authorised_decision_id']) ? $entries[$key]['authorised_decision_id'] : null;
                $patientContactAssignment->considered_decision_id = isset($entries[$key]['considered_decision_id']) ? $entries[$key]['considered_decision_id'] : null;
                $patientContactAssignment->event_id = $this->event->id;
                $patientContactAssignment->save();
            }
        }

        $patientContactAssignments = array_filter($patientContactAssignments, function ($assignment) use ($contact_ids) {
            return !in_array($assignment->contact_id, $contact_ids);
        });

        foreach ($patientContactAssignments as $patientContactAssignment) {
            $patientContactAssignment->delete();
        }
    }

    /**
     * Filter oprional elements
     * remove retired element(s)
     *
     * @return array
     */
    public function getOptionalElements()
    {
        return [];
    }

    /**
     * Filters elements based on coded dependencies.
     *
     * @param \BaseEventTypeElement[] $elements
     * @return \BaseEventTypeElement[]
     */
    protected function filterElements($elements)
    {
        $final = array();
        foreach ($elements as $el) {
            if (!in_array(get_class($el), $this->elementFilterList)) {
                $final[] = $el;
            }
        }
        return $final;
    }

    public function actionGetDeleteConsentPopupContent()
    {
        $old_consent_id = Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :old_consent_id');
        $criteria->params = [
            ':old_consent_id' => $old_consent_id
        ];
        $old_consent_event = Event::model()->findAll($criteria);
        if (count($old_consent_event)===0) {
            $response = null;
        } else {
            $response = [
                'html' => $this->renderPartial(
                    'select_event_with_consent_delete',
                    array(
                        'old_consent_event' => $old_consent_event,
                    ),
                    true
                ),
            ];
        }

        $this->renderJSON($response);
    }

    protected function setAndValidateElementsFromData($data)
    {
        $errors = [];
        $elements = [];
        $element_types = $this->getElementTypesForConsentFormType();

        foreach ($element_types as $element_type) {
            $from_data = $this->getElementsForElementType($element_type, $data);
            if (count($from_data) > 0) {
                $elements = array_merge($elements, $from_data);
            } elseif ($element_type->required) {
                $elements[] = $element_type->getInstance();
            }
        }

        if (!count($elements)) {
            $errors[$this->event_type->name][] = 'Cannot create an event without at least one element';
        }

        $this->open_elements = $elements;

        foreach ($this->open_elements as $element) {
            $element->validate();
            if (method_exists($element, 'eventScopeValidation')) {
                $element->eventScopeValidation($this->open_elements);
            }

            if ($element->hasErrors()) {
                $name = $element->getElementTypeName();
                foreach ($element->getErrors() as $errormsgs) {
                    foreach ($errormsgs as $error) {
                        $errors[$name][] = $error;
                    }
                }
            }
        }

        if (isset($data['Event']['event_date'])) {
            $this->setEventDate($data['Event']['event_date']);
            $event = $this->event;
            if (isset($data['Event']['parent_id'])) {
                $event->parent_id = $data['Event']['parent_id'];
            }
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
     * Get event elements
     * @return array
     */
    protected function getEventElements(): array
    {
        if ($this->event && !$this->event->isNewRecord) {
            $used_elements = $this->event->getElements();
            $missing_elements = $this->getMissingElementsToLayout($used_elements);
            $elements = array_merge($used_elements, $missing_elements);
        } else {
            $elements = $this->getElementsForConsentFormType();
        }

        return $elements;
    }

    /**
     * Get used elements name
     * @param $elements
     * @return array
     */
    private function getUsedElementNamesInEvent($elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $result[] = get_class($element);
        }
        return $result;
    }

    /**
     * Create new instance from element to edit mode if element is missing
     * @param $elements
     * @return array
     */
    protected function getMissingElementsToLayout($elements): array
    {
        $missing_elements = [];
        $result = [];
        $used_elements = $this->getUsedElementNamesInEvent($elements);
        if ($consent_assessments = $this->getElementsByConsentFormTypes()) {
            foreach ($consent_assessments as $assessment) {
                if (!in_array($assessment->element->class_name, $used_elements)) {
                    $missing_elements[] = $assessment->element->class_name;
                }
            }
        }

        if (!empty($missing_elements)) {
            foreach ($missing_elements as $missing) {
                $element = new $missing();
                /** @var BaseEventTypeElement $element */
                if ($element->hasAttribute('type_id')) {
                    $element->type_id = $this->type_id;
                }
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * Create instance layout elements
     * @return array
     */
    private function getElementsForConsentFormType(): array
    {
        $elements = [];
        if ($consent_assessments = $this->getElementsByConsentFormTypes()) {
            foreach ($consent_assessments as $assessment) {
                $element = new $assessment->element->class_name();
                /** @var BaseEventTypeElement $element */
                if ($element->hasAttribute('type_id')) {
                    $element->type_id = $this->type_id;
                }
                $elements[] = $element;
            }
        }

        return $elements;
    }


    /**
     * Required element to selected Consent Type
     * @return array
     */
    public function getElementsByConsentFormTypes(): array
    {
        return OphTrConsent_Type_Assessment::model()
            ->with('element')
            ->findAllByAttributes(
                [
                    'type_id' => $this->type_id
                ],
                [
                    'order' => 't.display_order ASC'
                ]
            );
    }

    private function getElementTypesForConsentFormType(): array
    {
        if ($consent_assessments = $this->getElementsByConsentFormTypes()) {
            foreach ($consent_assessments as $assessment) {
                $element_classes[] = $assessment->element->class_name;
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition("class_name", $element_classes);
            $criteria->addSearchCondition("event_type_id", $this->getEvent_type()->id);
            $criteria->order = "display_order ASC";
            return (ElementType::model()->findAll($criteria));
        }

        return [];
    }

    public function actionBenefits($id)
    {
        $extra_proc = OphTrConsent_Extra_Procedure::model()->findByPk($id);
        if (!$extra_proc) {
            throw new Exception("Unknown procedure: $id");
        }

        $benefits = array_map(function ($benefit) {
            return $benefit->name;
        }, $extra_proc->benefits);
        $this->renderJSON($benefits);
    }

    public function actionComplications($id)
    {
        $extra_proc = OphTrConsent_Extra_Procedure::model()->findByPk($id);
        if (!$extra_proc) {
            throw new Exception("Unknown procedure: $id");
        }

        $complications = array_map(function ($complication) {
            return $complication->name;
        }, $extra_proc->complications);
        $this->renderJSON($complications);
    }

    public function initActionSign()
    {
        $this->initWithEventId($this->request->getParam('id'));
    }

    public function actionSign($id)
    {
        if (!$element_type = \ElementType::model()->findByPk(\Yii::app()->request->getParam("element_type_id"))) {
            throw new \CHttpException(500, "Element type not found");
        }
        if (!$element = $this->event->getElementByClass($element_type->class_name)) {
            throw new \CHttpException(500, "Element not found");
        }
        $this->redirect("/OphCoCvi/default/print/$id?html=1&auto_print=0&sign=1".
            "&element_type_id=".\Yii::app()->request->getParam("element_type_id").
            "&signature_type=".\Yii::app()->request->getParam("signature_type").
            "&signatory_role=".\Yii::app()->request->getParam("signatory_role").
            "&signature_name=".\Yii::app()->request->getParam("signatory_name").
            "&element_id=".$element->id.
            "&initiator_element_type_id=".\Yii::app()->request->getParam("initiator_element_type_id").
            "&initiator_row_id=".\Yii::app()->request->getParam("initiator_row_id")
        );
    }

    public function actionContactPage()
    {
        $selected_contact_type = null;
        $params = [];

        if (isset($_GET['selected_contact_type_id'])) {
            $selected_contact_type_id = $_GET['selected_contact_type_id'];
            $selected_contact_type = \OphTrConsent_PatientRelationship::model()->findByPk($selected_contact_type_id);
            $params = array(
                'selected_relationship_type_id' => $selected_contact_type_id,
                'selected_relationship_type' => $selected_contact_type->name,
            );
        }

        $this->renderPartial(
            '_add_new_contact',
            $params,
            false,
            true
        );
    }

    protected function setComplexAttributes_Element_OphTrConsent_OthersInvolvedDecisionMakingProcess($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $post_data = \Yii::app()->request->getPost($model_name, []);
        $existing_items = $element->consentContact;
        $existing_contacts = [];
        $deleted_item_ids = array_column($existing_items, 'id');
        $new_items = [];

        foreach ($existing_items as $existintg_item) {
            $existing_contacts[$existintg_item->id] = $existintg_item;
        }

        foreach ($post_data['jsonData'] as $idx => $jsonStr) {
            if (strlen($jsonStr) === 0) {
                continue;
            }

            $data = json_decode(htmlspecialchars_decode($jsonStr), true);

            $existing_id = isset($data['existing_id']) ? $data['existing_id'] : null;

            if (!$existing_id) {
                $contact = new \Ophtrconsent_OthersInvolvedDecisionMakingProcessContact();
                $contact->setAttributes($data);

                $contact->comment = $post_data['comment'][$idx];
                $contact->signature_required = $post_data['signature_required'][$idx];

                $new_items[] = $contact;
            } else {
                $existing_contact = \Ophtrconsent_OthersInvolvedDecisionMakingProcessContact::model()->findByPk($existing_id);
                $existing_contact->comment = $post_data['comment'][$idx];
                $existing_contact->signature_required = $post_data['signature_required'][$idx];

                $existing_contacts[$existing_id] = $existing_contact;
                $key = array_search($existing_id, $deleted_item_ids);
                unset($deleted_item_ids[$key]);
            }
        }

        foreach ($existing_contacts as $existing_item) {
            if (in_array($existing_item->id, $deleted_item_ids)) {
                unset($existing_contacts[$existing_item->id]);
            }
        }
        $element->consentContact = array_merge($existing_contacts, $new_items);
    }
}
