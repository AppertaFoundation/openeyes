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

use Eye;
use OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures;
use OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision;
use OEModule\OphTrConsent\models\OphTrConsent_BestInterestDecision_Attachment;

class DefaultController extends BaseEventTypeController
{
    public const PATIENT_CONTACTS_TYPE = 1;
    public const OPENEYES_USERS_TYPE = 2;
    public const PATIENT_TYPE = 3;

    protected static $action_types = array(
        'users' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
        'withdraw' => self::ACTION_TYPE_FORM,
        'removeWithdraw' => self::ACTION_TYPE_FORM,
        'confirm' => self::ACTION_TYPE_FORM,
        'removeConfirm' => self::ACTION_TYPE_FORM,
        'benefits' => self::ACTION_TYPE_FORM,
        'complications' => self::ACTION_TYPE_FORM,
        'createEventImages' => self::ACTION_TYPE_PRINT,
        'saveCapturedSignature' => self::ACTION_TYPE_FORM,
        'getSignatureByUsernameAndPin' => self::ACTION_TYPE_FORM,
        'postSignRequest' => self::ACTION_TYPE_FORM,
        'sign' => self::ACTION_TYPE_EDIT,
        'contactPage' => self::ACTION_TYPE_FORM,
        'getDeleteConsentPopupContent' => self::ACTION_TYPE_FORM,
        'uploadFile' => self::ACTION_TYPE_FORM,
        'saveWithdrawal' => self::ACTION_TYPE_FORM,
    );

    protected static array $accepted_file_types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
    ];

    protected static int $max_filesize = 2_097_152;

    public $booking_event;
    public $booking_operation;
    public $unbooked = false;
    public $type_id = null;
    public $template;
    public $template_eye;

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
                        if (!array_key_exists($anaesthetic_type->id, $type_assessments_by_id)) {
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
        } elseif ($action == 'create' && $this->template) {
            if ($this->template_eye) {
                $eye_id = $this->template_eye->id;
                $assigned_procedures = array();
                foreach ($this->template->procedures as $booked_proc) {
                    $assigned_proc = new OphtrconsentProcedureProceduresProcedures();
                    $assigned_proc->proc_id = $booked_proc->id;
                    $assigned_proc->eye_id = $eye_id;
                    $assigned_procedures[] = $assigned_proc;
                    // regard the additional proc as normal one
                    foreach ($booked_proc->additional as $additional_proc) {
                        $add_proc = new OphtrconsentProcedureProceduresProcedures();
                        $add_proc->proc_id = $additional_proc->id;
                        $add_proc->eye_id = $eye_id;
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
            if($this->type_id) {
                $element->type_id = $this->type_id;
            }
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

    public function actionElementForm($id, $patient_id, $previous_id = null, $event_id = null)
    {
        // first prevent invalid requests
        $element_type = ElementType::model()->findByPk($id);
        if (!$element_type) {
            throw new CHttpException(404, 'Unknown ElementType');
        }
        $patient = Patient::model()->findByPk($patient_id);
        if (!$patient) {
            throw new CHttpException(404, 'Unknown Patient');
        }

        // Clear script requirements as all the base css and js will already be on the page
        Yii::app()->assetManager->reset();

        $this->patient = $patient;

        $this->setFirmFromSession();

        $this->episode = $this->getEpisode();

        // allow additional parameters to be defined by module controllers
        // TODO: Should valid additional parameters be a property of the controller?
        $additional = array();
        foreach (array_keys($_GET) as $key) {
            if (!in_array($key, array('id', 'patient_id', 'previous_id'))) {
                $additional[$key] = $_GET[$key];
            }
        }

        // retrieve the element
        $element = $this->getElementForElementForm($element_type, $previous_id, $additional);

        $this->open_elements = array($element);

        $form = Yii::app()->getWidgetFactory()->createWidget($this, 'BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('class' => 'sliding'),
        ));

        $this->renderElement($element, 'create', $form, null, array(
            'previous_parent_id' => $previous_id,
        ), false, true);
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

            if (
                !($this->booking_event = Event::model()->findByPk($_GET['booking_event_id']))
                || (!$this->booking_operation = $api->getOperationForEvent($_GET['booking_event_id']))
            ) {
                throw new Exception('booking event not found');
            }
        } elseif (isset($_GET['unbooked'])) {
            $this->unbooked = true;
        } elseif (isset($_GET['template_id'])) {
            if (!($this->template = OphTrConsent_Template::model()->findByPk($_GET['template_id']))) {
                throw new Exception('booking event not found');
            }

            if(isset($_GET['template_eye_id'])) {
                if (!($this->template_eye = Eye::model()->findByPk($_GET['template_eye_id']))) {
                    throw new Exception('eye not found');
                }
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
                $this->redirect(array('/OphTrConsent/Default/create?patient_id=' . $this->patient->id . '&unbooked=1'));
            } elseif (preg_match('/^booking([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                $this->redirect(array('/OphTrConsent/Default/create?patient_id=' . $this->patient->id . '&booking_event_id=' . $m[1]));
            } elseif (preg_match('/^template([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                if(!isset($_POST["template".$m[1]]["right_eye"]) && !isset($_POST["template".$m[1]]["left_eye"])) {
                    $errors = array('Consent form' => array('Please select laterality to add procedures for the template'));
                } else {
                    $template_eye_id = \Helper::getEyeIdFromArray($_POST["template".$m[1]]);
                    $template = OphTrConsent_Template::model()->findByPk($m[1]);
                    $this->redirect(array('/OphTrConsent/Default/create?patient_id=' . $this->patient->id . '&template_id=' . $m[1] . '&type_id='.$template->type_id . '&template_eye_id='.$template_eye_id));
                }
            }
            if(!isset($errors)) {
                $errors = array('Consent form' => array('Please add Laterality when a template is selected'));
            }
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
                if (is_null($institution)) {
                    $criteria->addCondition('institution_id IS NULL');
                } else {
                    $criteria->addCondition('institution_id = ' . $institution . ' OR institution_id IS NULL');
                }
            }

            if ($this->event->site_id) {
                $site = $this->event->site_id;
                if (is_null($site)) {
                    $criteria->addCondition('site_id IS NULL');
                } else {
                    $criteria->addCondition('site_id = ' . $site . ' OR site_id IS NULL');
                }
            }

            if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id) {
                $subspecialty = $this->firm->serviceSubspecialtyAssignment->subspecialty_id;
                if (is_null($subspecialty)) {
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
            $this->print_args = '?vi=1';
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
        $pf->title = 'event_' . $pdf_route . '.pdf';

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

        $params[':term'] = '%' . strtolower(strtr($_GET['term'], array('%' => '\%'))) . '%';

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
                    $consultant_name = trim($consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name);
                }

                $users[] = array(
                    'id' => $user->id,
                    'value' => trim($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name . ' ' . $contact->qualifications) . ' (' . $user->role . ')',
                    'fullname' => trim($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name . ' ' . $contact->qualifications),
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

    public function actionWithdraw()
    {
        $event_id = $this->request->getParam('event_id');
        if ($event_id === null) {
            $this->getEvent()->id;
        }
        $this->initWithEventId($event_id);

        $trans = Yii::app()->db->beginTransaction();

        $withdrawal_element_criteria = new CDbCriteria();
        $withdrawal_element_criteria->compare('t.event_id', $event_id);
        $withdrawal = Element_OphTrConsent_Withdrawal::model()->find($withdrawal_element_criteria);

        if ($withdrawal === null) {
            $withdrawal = new Element_OphTrConsent_Withdrawal();
        }

        if (($withdrawal_reason = Yii::app()->request->getPost(CHtml::modelName(Element_OphTrConsent_Withdrawal::class) . '_withdrawal_reason')) === null) {
            $withdrawal_reason = Yii::app()->request->getPost(CHtml::modelName(Element_OphTrConsent_Esign::class) . '_withdrawal_reason');
        }

        $withdrawal->withdrawn = 1;
        $withdrawal->withdrawal_reason = $withdrawal_reason;
        $withdrawal->event_id = $event_id;

        if (!$withdrawal->isUnableToConsent()) {
            $other_relationship = \OphTrConsent_PatientRelationship::model()->find("LOWER(name) = 'other'");
            $contact_type_id = self::PATIENT_TYPE;

            $withdrawal->contact_type_id = $contact_type_id;
            $withdrawal->contact_user_id = null; // not a User
            $withdrawal->first_name = $this->patient->first_name;
            $withdrawal->last_name = $this->patient->last_name;
            $withdrawal->email = $this->patient->getEmail();
            $withdrawal->phone_number = $this->patient->primary_phone;
            $withdrawal->mobile_number = $this->patient->contact->mobile_phone;
            $withdrawal->address_line1 = $this->patient->contact->address->address1;
            $withdrawal->address_line2 = $this->patient->contact->address->address2;
            $withdrawal->city = $this->patient->contact->address->city;
            $withdrawal->country_id = $this->patient->contact->address->country_id;
            $withdrawal->postcode = $this->patient->contact->address->postcode;
            $withdrawal->consent_patient_relationship_id = $other_relationship->id;
            $withdrawal->other_relationship = 'Patient';
        }

        if ($withdrawal_reason !== null) {
            $this->event->addIssue('Consent Withdrawn');
        } else {
            $this->event->deleteIssue('Consent Withdrawn');
        }

        if ($withdrawal->save()) {
            if ($type = $this->event->getElementByClass(Element_OphTrConsent_Type::class)) {
                $type->draft = 0;
                $type->save();
            }
            $trans->commit();
        } else {
            $trans->rollback();
        }

        $this->redirect('/OphTrConsent/default/view/' . $event_id);
    }

    public function actionRemoveWithdraw()
    {
        $event_id = $this->request->getParam('event_id');

        $transaction = Yii::app()->db->beginTransaction();

        $withdrawal_element_criteria = new CDbCriteria();
        $withdrawal_element_criteria->compare('t.event_id', $event_id);
        $withdrawals = Element_OphTrConsent_Withdrawal::model()->findAll($withdrawal_element_criteria);

        try {
            foreach ($withdrawals as $withdrawal) {
                if (!$withdrawal->delete()) {
                    throw new Exception("Could not delete withdrawal {$withdrawal->id}");
                };
            }

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw new Exception($e->getMessage());
        }

        $this->redirect('/OphTrConsent/default/view/' . $event_id);
    }

    public function actionConfirm()
    {
        $event_id = $this->request->getParam('event_id');
        if ($event_id === null) {
            $this->getEvent()->id;
        }
        $this->initWithEventId($event_id);

        $trans = Yii::app()->db->beginTransaction();

        $confirm_element_criteria = new CDbCriteria();
        $confirm_element_criteria->compare('t.event_id', $event_id);
        $confirm = Element_OphTrConsent_Confirm::model()->find($confirm_element_criteria);

        if ($confirm === null) {
            $confirm = new Element_OphTrConsent_Confirm();
        }

        $confirm->confirmed = 1;
        $confirm->event_id = $event_id;

        if ($confirm->save()) {
            if ($type = $this->event->getElementByClass(Element_OphTrConsent_Type::class)) {
                $type->draft = 0;
                $type->save();
            }
            $trans->commit();
        } else {
            $trans->rollback();
        }

        $this->redirect('/OphTrConsent/default/view/' . $event_id);
    }

    public function actionRemoveConfirm()
    {
        $event_id = $this->request->getParam('event_id');

        $transaction = Yii::app()->db->beginTransaction();

        $confirm_element_criteria = new CDbCriteria();
        $confirm_element_criteria->compare('t.event_id', $event_id);
        $confirms = Element_OphTrConsent_Confirm::model()->findAll($confirm_element_criteria);

        try {
            foreach ($confirms as $confirm) {
                if (!$confirm->delete()) {
                    throw new Exception("Could not delete confirm {$confirm->id}");
                };
            }

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }
        }

        $this->redirect('/OphTrConsent/default/view/' . $event_id);
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

    protected function saveComplexAttributes_Element_OphTrConsent_SupplementaryConsent($element, $data, $index)
    {
        $ele_qs = $data['Element_OphTrConsent_SupplementaryConsent']['element_question'] ?? [];

        //for each element question id posted
        foreach ($ele_qs as $ele_q_id => $ele_q_data) {
            // check if question exists in this element
            $new = true;
            $element_question = new Ophtrconsent_SupplementaryConsentElementQuestion();

            foreach ($element->element_question as $ele_ques) {
                if ((int)$ele_q_id === (int)$ele_ques->question_id) {
                    $new = false;
                    $element_question = $ele_ques;
                    break;
                }
            }
            if ($new) {//if new instantiate the question
                $element_question->element_id = $element->id;
                $element_question->question_id = $ele_q_id;
            }

            // check if value is dirty
            $element_question->save();

            foreach ($ele_q_data as $data_type => $ele_a_data) {
                $new_a = true;
                $element_answer = new Ophtrconsent_SupplementaryConsentElementQuestionAnswer();

                // check each type of value posted to figure out how to handle them
                if ($data_type === 'text' || $data_type === 'textarea') { // text
                    foreach ($element_question->element_answers as $ele_ans) {
                        // check if answer is the same as the data we got
                        if ((int)$ele_ans->answer_text === (int)$ele_a_data) {
                            $new_a = false;
                            $element_answer = $ele_ans;
                        } else { // cleanup extra answers to this question.
                            $ele_ans->delete();
                        }
                    }
                    if ($new_a) { //if new instantiate the answer
                        $element_answer->element_question_id = $element_question->id;
                        $element_answer->answer_text = $ele_a_data;
                    }
                    // if old but not up to date
                    if ($ele_a_data != $element_answer->answer_text) {
                        $element_answer->answer_text = $ele_a_data;
                    }
                    $element_answer->save();
                } elseif ($data_type === 'dropdown' || $data_type === 'radio') { // one choice
                    foreach ($element_question->element_answers as $ele_ans) {
                        // check if answer is the same as the data we got
                        if ($ele_a_data === (int)$ele_ans->answer_id) {
                            $new = false;
                            $element_answer = $ele_ans;
                        } else { // cleanup extra answers to this question.
                            $ele_ans->delete();
                        }
                    }
                    if ($new_a) { //if new instantiate the question
                        $element_answer->element_question_id = $element_question->id;
                        $element_answer->answer_id = $ele_a_data;
                    } // if old but not up to date
                    if ($ele_a_data !== (int)$element_answer->answer_id) {
                        $element_answer->answer_id = $ele_a_data;
                    }
                    $element_answer->save();
                } elseif ($data_type === 'check') { // multiple choice
                    $ans_choice_check = [];

                    //cleanup old data by removing any answer that is not in the current list (has to loop twice)
                    foreach ($element_question->element_answers as $ele_ans) {
                        // check if answer is the same as the data we got
                        if (in_array($ele_ans->answer_id, $ele_a_data)) {
                            array_push($ans_choice_check, $ele_ans);
                        }
                    }
                    foreach ($element_question->element_answers as $ele_ans) {
                        // check if answer was previously confirmed and delete extra answers to this question.
                        if (!in_array($ele_ans, $ans_choice_check)) {
                            $ele_ans->delete();
                        }
                    }

                    //for each of the answers we have
                    foreach ($ele_a_data as $data_answer_id) {
                        $new = true;
                        foreach ($ans_choice_check as $ans_choice_check_item) {
                            if ($data_answer_id === (int)$ans_choice_check_item->answer_id) {
                                $new = false;
                            }
                        }
                        //check to see if it is new
                        if ($new) {
                            $element_answer = new Ophtrconsent_SupplementaryConsentElementQuestionAnswer();
                            $element_answer->element_question_id = $element_question->id;
                            $element_answer->answer_id = $data_answer_id;
                            $element_answer->save();
                        }
                    }
                }
            }
        }
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
        if (count($old_consent_event) === 0) {
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
            $elements = $this->event->getElements();
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
        $this->redirect("/OphCoCvi/default/print/$id?html=1&auto_print=0&sign=1" .
            "&element_type_id=" . \Yii::app()->request->getParam("element_type_id") .
            "&signature_type=" . \Yii::app()->request->getParam("signature_type") .
            "&signatory_role=" . \Yii::app()->request->getParam("signatory_role") .
            "&signature_name=" . \Yii::app()->request->getParam("signatory_name") .
            "&element_id=" . $element->id .
            "&initiator_element_type_id=" . \Yii::app()->request->getParam("initiator_element_type_id") .
            "&initiator_row_id=" . \Yii::app()->request->getParam("initiator_row_id"));
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

    private function checkFileError(int $error): void
    {
        switch ($error) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('Exceeded filesize limit.');
            default:
                throw new Exception('Unknown error.');
        }
    }

    private function checkMimeType(string $file_path): void
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($file_path);
        if (
            !array_search(
                $type,
                self::$accepted_file_types,
                true
            )
        ) {
            throw new Exception("File type $type not allowed.");
        }
    }

    private function sanitizeFileName(string $orig_name): string
    {
        return preg_replace('/[^\da-zA-Z\.\-_]/i', '_', $orig_name);
    }

    private function checkFileSize(int $size): void
    {
        if ($size > static::$max_filesize) {
            throw new Exception("File too large.");
        }
    }

    public function actionUploadFile()
    {
        if (isset($_FILES["file"])) {
            $file = $_FILES["file"];
            try {
                $this->checkFileError($file["error"]);
                $this->checkFileSize($file["size"]);
                $this->checkMimeType($file["tmp_name"]);
            } catch (Exception $e) {
                $this->renderJSON([
                    "success" => false,
                    "message" => $e->getMessage(),
                ]);
            }
            $file_name = $this->sanitizeFileName($file["name"]);
            $pf = ProtectedFile::createForWriting($file_name);
            $pf->title = "Best Interest Decision support document";
            if (!move_uploaded_file($file["tmp_name"], $pf->getPath())) {
                $this->renderJSON([
                    "success" => false,
                    "message" => "Error uploading file.",
                ]);
            }
            $pf->save();
            $this->renderJSON([
                "success" => true,
                "protected_file_id" => $pf->id,
            ]);
        } else {
            throw new CHttpException(400, "Bad request");
        }
    }

    protected function setComplexAttributes_Element_OphTrConsent_BestInterestDecision(
        Element_OphTrConsent_BestInterestDecision $element,
                                                  $data,
                                                  $index = null
    )
    {
        $data = $data["OEModule_OphTrConsent_models_Element_OphTrConsent_BestInterestDecision"];
        $items = [];
        if (isset($data["attachments"]) && is_array($data["attachments"])) {
            foreach ($data["attachments"] as $attachment) {
                if ($attachment["id"] === "new") {
                    $item = new OphTrConsent_BestInterestDecision_Attachment();
                    $pf_id = $attachment["protected_file_id"];
                    $item->protected_file_id = $pf_id;
                    if (array_key_exists("tmp_name", $attachment)) {
                        $item->tmp_name = $attachment["tmp_name"];
                    }
                } else {
                    $item = OphTrConsent_BestInterestDecision_Attachment::model()->findByPk($attachment["id"]);
                }
                $items[] = $item;
            }
        }
        $element->attachments = $items;
    }

    protected function saveComplexAttributes_Element_OphTrConsent_BestInterestDecision(
        Element_OphTrConsent_BestInterestDecision $element,
                                                  $data,
                                                  $index = null
    )
    {
        $existing_ids = Yii::app()->db->createCommand(
            "SELECT id FROM " . OphTrConsent_BestInterestDecision_Attachment::model()->tableName()
            . " WHERE element_id = :element_id"
        )->queryColumn([":element_id" => $element->id]);
        $ids_to_keep = [];
        foreach ($element->attachments as $attachment) {
            if ($attachment->isNewRecord) {
                $attachment->element_id = $element->id;
                if (!$attachment->save()) {
                    throw new Exception("Could not save attachment " . print_r($attachment->errors, 1));
                }
            } else {
                $ids_to_keep[] = $attachment->id;
            }
        }

        // Remove those that the user marked as deleted
        foreach (array_diff($existing_ids, $ids_to_keep) as $id) {
            OphTrConsent_BestInterestDecision_Attachment::model()->deleteByPk($id);
        }
    }

    public function actionContactPage()
    {
        $request = \Yii::app()->getRequest();
        $params = [];

        $selected_contact_method = $request->getQuery('selected_contact_method');
        $selected_relationship = $request->getQuery('selected_relationship');

        $params = array(
            'selected_contact_method' => $selected_contact_method,
            'selected_relationship' => $selected_relationship,
        );

        $this->renderPartial('_add_new_contact', $params, false, true);
    }

    public function actionSaveWithdrawal()
    {
        $code = 1;
        $message = '';

        $request = Yii::app()->request;
        if ($request->isPostRequest) {
            $data = $request->getRestParams();
            try {
                $withdrawal = \Element_OphTrConsent_Withdrawal::model()->find("event_id = ?", [$data['event_id']]);
                if ($withdrawal) {
                    $withdrawal->setAttributes($data);
                    $withdrawal->withdrawn = 0;
                    $withdrawal->withdrawal_reason = null;
                    if (!$withdrawal->save()) {
                        throw new Exception("Could not save withdrawal. Please contact support for assistance.");
                    }
                } else {
                    throw new Exception('Something went wrong trying to add the withdrawal. Please try again or contact support for assistance');
                }
            } catch (Exception $e) {
                $code = 0;
                $message = $e->getMessage();
            }
        }
        $this->renderJSON(['code' => $code, 'message' => $message]);
    }
}
