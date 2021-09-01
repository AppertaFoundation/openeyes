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


class DefaultController extends BaseEventTypeController
{
    private $elementFilterList = [ 'Element_OphTrConsent_Other' ];
    protected static $action_types = array(
        'users' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
    );

    public $booking_event;
    public $booking_operation;
    public $unbooked = false;

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
                $element->eye_id = $this->booking_operation->eye_id;

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

                $element->procedures = $this->booking_operation->procedures;
                $additional = array();
                $additional_ids = array();
                foreach ($element->procedures as $proc) {
                    foreach ($proc->additional as $add) {
                        if (!in_array($add->id, $additional_ids)) {
                            $additional[] = $add;
                            $additional_ids[] = $add->id;
                        }
                    }
                }
                $element->additional_procedures = $additional;
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
        if ($action == 'create') {
            $patient_age = (int)$this->patient->getAge();
            if ($patient_age <= 16) {
                $element->type_id = 2;
            } else {
                $element->type_id = 1;
            }
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
        }
    }

    /**
     * @param $default_view
     */
    public function renderSidebar($default_view)
    {
        if (!$this->booking_event && !$this->unbooked) {
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
            }
            $errors = array('Consent form' => array('Please select a booking or Unbooked procedures'));
        }

        if ($this->booking_event || $this->unbooked) {
            parent::actionCreate();
        } else {
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $bookings = $api->getIncompleteOperationsForEpisode($this->patient);
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
                        array('id' => 'et_cancel', 'class' => 'button small warning')
                    ),
            );
            $this->processJsVars();
            $this->render('select_event', array(
                'errors' => $errors,
                'bookings' => $bookings ? $bookings : [],
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
            throw new Exception('Unable to save consent form: '.print_r($type->getErrors(), true));
        }
        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }
        $event->info = '';
        if (!$event->save()) {
            throw new Exception('Unable to save event: '.print_r($event->getErrors(), true));
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
                throw new Exception('Unable to mark consent form printed: '.print_r($type->getErrors(), true));
            }
        }
    }

    protected function saveComplexAttributes_Element_OphTrConsent_Procedure($element, $data, $index)
    {
        $curr_by_id = array();

        foreach ($element->anaesthetic_type as $type) {
            $curr_by_id[$type->id] = OphTrConsent_Procedure_AnaestheticType::model()->findByAttributes(array(
                'et_ophtrconsent_procedure_id' => $element->id,
                'anaesthetic_type_id' => $type->id
            ));
        }

        if (isset($data['AnaestheticType']) && !empty($data['AnaestheticType'])) {
            foreach ($data['AnaestheticType'] as $type_id) {
                if (!isset($curr_by_id[$type_id])) {
                    $type = new OphTrConsent_Procedure_AnaestheticType();
                    $type->et_ophtrconsent_procedure_id = $element->id;
                    $type->anaesthetic_type_id = $type_id;

                    if (!$type->save()) {
                        throw new Exception('Unable to save anaesthetic agent assignment: '.print_r($type->getErrors(), true));
                    }
                } else {
                    unset($curr_by_id[$type_id]);
                }
            }
        }

        foreach ($curr_by_id as $type) {
            if (!$type->delete()) {
                throw new Exception('Unable to delete anaesthetic agent assignment: '.print_r($type->getErrors(), true));
            }
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
        $elements = parent::getOptionalElements();
        return $this->filterElements($elements);
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
}
