<?php

/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DefaultController extends BaseEventTypeController
{
    protected static $action_types = array(
        'step' => self::ACTION_TYPE_EDIT,
        'deleteNote' => self::ACTION_TYPE_FORM,
    );

    // if set to true, we are advancing the current event step
    protected $set;
    private $step = false;
    public $editable = false;
    protected $show_element_sidebar = false;
    protected $render_optional_elements = false;
    protected string $customErrorHeaderMessage;

    /* @var Element_OphTrOperationbooking_Operation operation that this operation checklists is for when creating */
    protected $booking_operation;
    /* @var boolean - indicates if this operation checklists is for an unbooked procedure or not when creating */
    protected $unbooked = false;
    /* @var Procedure[] - cache of procedures for the booking operation */
    protected $booking_procedures;
    /* @var AnaestheticType[] - cache of anaesthetic types for the booking operation */
    protected $booking_anaesthetic_types;
    /* @var Disorder - cache of disorder for the booking operation */
    protected $booking_disgnosis_disorder;

    protected $isDraft;

    public function getTitle()
    {
        $title = parent::getTitle();
        $current = $this->step ?: $this->getCurrentStep();
        if ($this->action->id === 'step') {
            $title .= ' (' . $current->name . ')';
        }
        if ($this->action->id === 'update') {
            $title .= ' (' . $this->step->name . ')';
        }

        if ($this->action->id === 'view' && !$this->isDraft) {
            $title .= ' (' . $this->event->info . ')';
        }
        return $title;
    }

    /**
     * Edit actions common initialisation.
     */
    protected function initEdit()
    {
        $this->moduleStateCssClass = 'edit';
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

    public function initActionStep()
    {
        $this->initActionUpdate();
    }

    /**
     * Call editInit to setup jsVars.
     */
    public function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit();
    }

    /**
     * @throws \CException
     */
    protected function setCurrentSet()
    {
        $element_assignment = $this->getElementSetAssignment();
        if (!$this->set) {
            /*@TODO: probably the getNextStep() should be able to recognize if there were no steps completed before and return the first step
              Note: getCurrentStep() will return firstStep if there were no steps before */
            $this->set = $element_assignment && $this->action->id != 'update' ? $this->getNextStep() : $this->getCurrentStep();
        }

        if (!$element_assignment && $this->event) {
            \OELog::log("Assignment not found for event id: {$this->event->id}");
        }

        if ($this->action->id == 'update' && (!isset($element_assignment))) {
            $this->step = $this->getCurrentStep();
        }
    }

    public function actionCreate()
    {
        $this->isDraft = $_POST['isDraft'] ?? null;
        $this->setCurrentSet();
        $this->step = $this->getCurrentStep();

        $errors = array();

        if (!empty($_POST)) {
            if (preg_match('/^booking([0-9]+)$/', @$_POST['SelectBooking'], $m)) {
                $this->redirect(array('/OphTrOperationchecklists/Default/create?patient_id=' . $this->patient->id . '&booking_event_id=' . $m[1]));
            } elseif (@$_POST['SelectBooking'] === 'emergency') {
                $this->redirect(array('/OphTrOperationchecklists/Default/create?patient_id=' . $this->patient->id . '&unbooked=1'));
            }

            $errors = array('Operation' => array('Please select a booked operation'));
        }

        if ($this->booking_operation || $this->unbooked) {
            parent::actionCreate();
        } else {
            // set up form for selecting a booking for the Op Operation checklists
            $bookings = array();

            $element_enabled = Yii::app()->params['disable_theatre_diary'];
            $theatre_diary_disabled = isset($element_enabled) && $element_enabled == 'on';

            /** @var OphTrOperationbooking_API $api */
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $operations = $api->getOpenOperations($this->patient);
            }

            $this->setTitle('Please select booking');
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

    /**
     * Updates the event info based on the current step.
     * @param $id
     * @throws Exception
     */
    protected function updateEventInfoByStep($id)
    {
        $info_text = '';
        if ($id === '1') {
            $info_text = 'Admitted';
        } elseif ($this->getCurrentStep()->id === '2') {
            $info_text = 'Ward to Theatre Checklist Complete';
        } elseif ($this->getCurrentStep()->id === '3') {
            $info_text = 'Theatre Checklist 2 Completed';
        } elseif ($this->getCurrentStep()->id === '4') {
            $info_text = 'Pending Discharge';
        } elseif ($this->getCurrentStep()->id === '5') {
            $info_text = 'Discharged';
        }
        $this->event->info = $info_text;
        $this->event->save();
    }

    public function actionUpdate($id)
    {
        $this->isDraft = $_POST['isDraft'] ?? null;
        $step_id = \Yii::app()->request->getParam('step_id');

        if ($step_id) {
            $this->step = OphTrOperationchecklists_ElementSet::model()->findByPk($step_id);
        } else {
            $this->step = $this->getCurrentStep()->getNextStep();
        }

        $this->setCurrentSet();
        parent::actionUpdate($id);
    }

    public function actionView($id)
    {
        $model = OphTrOperationchecklists_Event::model()
            ->findBySql('SELECT * FROM ophtroperationchecklists_event WHERE event_id = :id', [':id' => $id]);

        if (isset($model) && $model->draft) {
            $this->isDraft = true;
            $this->editable = true;
            Yii::app()->user->setFlash('alert.draft', 'This operation checklists event is a draft and can still be edited');
        }

        parent::actionView($id);
    }

    public function actionPrint($id)
    {
        parent::actionPrint($id);
    }

    /**
     * For new operation checklists for a specific operation, initialise procedure list with relevant procedures.
     *
     * @param Element_OphTrOperationchecklists_ProcedureList $element
     * @param $data
     * @param $index
     */
    protected function setElementDefaultOptions_Element_OphTrOperationchecklists_ProcedureList($element, $action)
    {
        $procedures = $this->getBookingProcedures();
        $anaesthetic_types = $this->getBookingAnaestheticTypes();
        $disorder = $this->getBookingDiagnosis();
        if ($action === 'create' && !empty($procedures) && $disorder && !empty($anaesthetic_types)) {
            $element->procedures = $procedures;
            $element->anaesthetic_type = $anaesthetic_types;
            $element->disorder_id = $disorder->id;
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $element->eye = $api->getEyeForOperation($this->booking_operation->event_id);
            $element->priority = $api->getPriorityForOperation($this->booking_operation->event_id);
            $element->booking_event_id = $this->booking_operation->event_id;
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_ProcedureList $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_ProcedureList($element, $data, $index)
    {
        $procs = array();
        if (isset($data['Procedures_procs'])) {
            foreach ($data['Procedures_procs'] as $idx => $proc_id) {
                $procs[] = Procedure::model()->findByPk($proc_id);
            }
        }
        $element->procedures = $procs;

        //AnaestheticType
        $type_assessments = array();
        if (isset($data['AnaestheticType']) && is_array($data['AnaestheticType'])) {
            $type_assessments_by_id = array();
            foreach ($element->anaesthetic_type_assignments as $type_assignments) {
                $type_assessments_by_id[$type_assignments->anaesthetic_type_id] = $type_assignments;
            }

            foreach ($data['AnaestheticType'] as $anaesthetic_type_id) {
                if (!array_key_exists($anaesthetic_type_id, $type_assessments_by_id)) {
                    $anaesthetic_type_assesment = new \OphTrOperationchecklists_AnaestheticAnaestheticType();
                } else {
                    $anaesthetic_type_assesment = $type_assessments_by_id[$anaesthetic_type_id];
                }

                $anaesthetic_type_assesment->procedurelist_id = $element->id;
                $anaesthetic_type_assesment->anaesthetic_type_id = $anaesthetic_type_id;

                $type_assessments[] = $anaesthetic_type_assesment;
            }
        }

        $element->anaesthetic_type_assignments = $type_assessments;
    }

    /**
     * @param Element_OphTrOperationchecklists_ProcedureList $element
     * @param array $data
     * @param int $index
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_ProcedureList($element, $data, $index)
    {
        $element->updateProcedures(isset($data['Procedures_procs']) ? $data['Procedures_procs'] : array());
        $element->updateAnaestheticType(isset($data['AnaestheticType']) ? $data['AnaestheticType'] : array());
    }

    /**
     * @param Element_OphTrOperationchecklists_Admission $element
     * @param $action
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_Admission($element, $action)
    {
        $checklist_result_records = array();
        foreach ($element->checklistResults as $checklist_result) {
            $checklist_result_records[$checklist_result->question_id] = $checklist_result;
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_Admission $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_Admission($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['checklistResults'])) {
            foreach ($data[$model_name]['checklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                $dilation = null;
                $observations = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_AdmissionResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_AdmissionResults();
                }
                if ($dilation == null) {
                    $dilation = new OphTrOperationchecklists_Dilation();
                }

                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;
                if (@$checklist_results['dilation']) {
                    $treatment_records = [];
                    $dilation->attributes = $checklist_results['dilation'];
                    if (isset($checklist_results['dilation']['treatments'])) {
                        foreach ($checklist_results['dilation']['treatments'] as $t) {
                            $treatment = null;
                            $treatment = new OphTrOperationchecklists_DilationTreatment();
                            $treatment->attributes = $t;
                            $treatment_records[] = $treatment;
                        }
                        $dilation->treatments = $treatment_records;
                    }
                    $checklist_result->dilation = $dilation;
                }
                if (isset($checklist_results['observations'])) {
                    $observation = new OphTrOperationchecklists_Observations();
                    $observation->attributes = $checklist_results['observations'];
                    $checklist_result->observations = $observation;
                }

                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
            }
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_Admission $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_Admission($element, $data, $index)
    {
        $element->saveData();
    }

    /**
     * @param Element_OphTrOperationchecklists_Note $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_Note($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        if (isset($data[$model_name]['notes'])) {
            $notes = new OphTrOperationchecklists_Notes();
            $notes->notes = $data[$model_name]['notes']['notes'];
            $element->notes = $notes;
        }
    }

    /**
     * @param $element Element_OphTrOperationchecklists_Note
     * @param $data
     * @param $index
     */
    public function saveComplexAttributes_Element_OphTrOperationchecklists_Note($element, $data, $index)
    {
        $data = $data['Element_OphTrOperationchecklists_Note']['notes']['notes'];
        if (!empty($data)) {
            $element->saveNote($data);
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_Documentation $element
     * @param $action
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_Documentation($element, $action)
    {
        if ($action != 'view') {
            // get the results from the saved event.
            $documentation = Element_OphTrOperationchecklists_Documentation::model()->with('checklistResults')->find(
                'event_id = :event_id AND checklistResults.set_id = :set_id',
                array(':event_id' => $this->event->id, ':set_id' => $this->getNextStep()->id)
            );
            if (isset($documentation)) {
                $checklist_result_records = array();
                foreach ($documentation->checklistResults as $checklist_result) {
                    $checklist_result_records[$checklist_result->question_id] = $checklist_result;
                }
                $checklistResults = $checklist_result_records;
            } else {
                $checklistResults = [];
                $questions = OphTrOperationchecklists_Questions::model()->findAll(array(
                    'order' => 'display_order',
                    'condition' => 'element_type_id = :element_type_id',
                    'params' => array(':element_type_id' => $element->getElementType()->id)
                ));
                for ($i = 0; $i < count($questions); $i++) {
                    $checklistResults[] = new OphTrOperationchecklists_DocumentationResults();
                }
            }
            $element->checklistResults = $checklistResults;
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_Documentation $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_Documentation($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['checklistResults'])) {
            foreach ($data[$model_name]['checklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_DocumentationResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_DocumentationResults();
                }
                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;
                $checklist_result->set_id = $this->getNextStep()->id;
                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
            }
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_Documentation $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_Documentation($element, $data, $index)
    {
        $element->saveDocumentationData();
    }

    /**
     * @param Element_OphTrOperationchecklists_ClinicalAssessment $element
     * @param $data
     * @param $index
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_ClinicalAssessment($element, $action)
    {
        if ($action != 'view') {
            // get the results from the saved event.
            $clinicalAssessment = Element_OphTrOperationchecklists_ClinicalAssessment::model()->with('checklistResults')->find(
                'event_id = :event_id AND checklistResults.set_id = :set_id',
                array(':event_id' => $this->event->id, ':set_id' => $this->getNextStep()->id)
            );
            if (isset($clinicalAssessment)) {
                $checklist_result_records = array();
                foreach ($clinicalAssessment->checklistResults as $checklist_result) {
                    $checklist_result_records[$checklist_result->question_id] = $checklist_result;
                }
                $checklistResults = $checklist_result_records;
            } else {
                $checklistResults = [];
                $questions = OphTrOperationchecklists_Questions::model()->findAll(array(
                    'order' => 'display_order',
                    'condition' => 'element_type_id = :element_type_id',
                    'params' => array(':element_type_id' => $element->getElementType()->id)
                ));
                for ($i = 0; $i < count($questions); $i++) {
                    $checklistResults[] = new OphTrOperationchecklists_ClinicalResults();
                }
            }
            $element->checklistResults = $checklistResults;
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_ClinicalAssessment $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_ClinicalAssessment($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['checklistResults'])) {
            foreach ($data[$model_name]['checklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_ClinicalResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_ClinicalResults();
                }
                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;
                $checklist_result->set_id = $this->getNextStep()->id;
                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
            }
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_ClinicalAssessment $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_ClinicalAssessment($element, $data, $index)
    {
        $element->saveClinicalData();
    }

    /**
     * @param Element_OphTrOperationchecklists_NursingAssessment $element
     * @param $data
     * @param $index
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_NursingAssessment($element, $action)
    {
        if ($action != 'view') {
            // get the results from the saved event.
            $nursingAssessment = Element_OphTrOperationchecklists_NursingAssessment::model()->with('checklistResults')->find(
                'event_id = :event_id AND checklistResults.set_id = :set_id',
                array(':event_id' => $this->event->id, ':set_id' => $this->getNextStep()->id)
            );
            if (isset($nursingAssessment)) {
                $checklist_result_records = array();
                foreach ($nursingAssessment->checklistResults as $checklist_result) {
                    $checklist_result_records[$checklist_result->question_id] = $checklist_result;
                }
                $checklistResults = $checklist_result_records;
            } else {
                $checklistResults = [];
                $questions = OphTrOperationchecklists_Questions::model()->findAll(array(
                    'order' => 'display_order',
                    'condition' => 'element_type_id = :element_type_id',
                    'params' => array(':element_type_id' => $element->getElementType()->id)
                ));
                for ($i = 0; $i < count($questions); $i++) {
                    $checklistResults[] = new OphTrOperationchecklists_NursingResults();
                }
            }
            $element->checklistResults = $checklistResults;
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_NursingAssessment $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_NursingAssessment($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['checklistResults'])) {
            foreach ($data[$model_name]['checklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_NursingResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_NursingResults();
                }
                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;
                $checklist_result->set_id = $this->getNextStep()->id;
                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
            }
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_NursingAssessment $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_NursingAssessment($element, $data, $index)
    {
        $element->saveNursingData();
    }

    /**
     * @param Element_OphTrOperationchecklists_PatientSupport $element
     * @param $data
     * @param $index
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_PatientSupport($element, $action)
    {
        if ($action != 'view') {
            // get the results from the saved event.
            $patientSupport = Element_OphTrOperationchecklists_PatientSupport::model()->with('checklistResults')->find(
                'event_id = :event_id AND checklistResults.set_id = :set_id',
                array(':event_id' => $this->event->id, ':set_id' => $this->getNextStep()->id)
            );
            if (isset($patientSupport)) {
                $checklist_result_records = array();
                foreach ($patientSupport->checklistResults as $checklist_result) {
                    $checklist_result_records[$checklist_result->question_id] = $checklist_result;
                }
                $checklistResults = $checklist_result_records;
            } else {
                $checklistResults = [];
                $questions = OphTrOperationchecklists_Questions::model()->findAll(array(
                    'order' => 'display_order',
                    'condition' => 'element_type_id = :element_type_id',
                    'params' => array(':element_type_id' => $element->getElementType()->id)
                ));
                for ($i = 0; $i < count($questions); $i++) {
                    $checklistResults[] = new OphTrOperationchecklists_PatientSupportResults();
                }
            }
            $element->checklistResults = $checklistResults;
        }
    }

    /**
     * @param Element_OphTrOperationchecklists_PatientSupport $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_PatientSupport($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['checklistResults'])) {
            foreach ($data[$model_name]['checklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_PatientSupportResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_PatientSupportResults();
                }
                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;
                $checklist_result->set_id = $this->getNextStep()->id;
                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
            }
        }
        $element->checklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_PatientSupport $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_PatientSupport($element, $data, $index)
    {
        $element->savePatientSupportData();
    }

    /**
     * @param Element_OphTrOperationchecklists_Discharge $element
     * @param $action
     */
    public function setElementDefaultOptions_Element_OphTrOperationchecklists_Discharge($element, $action)
    {
        $checklist_result_records = array();
        foreach ($element->dischargeChecklistResults as $checklist_result) {
            $checklist_result_records[$checklist_result->question_id] = $checklist_result;
        }
        $element->dischargeChecklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_Discharge $element
     * @param $data
     * @param $index
     */
    public function setComplexAttributes_Element_OphTrOperationchecklists_Discharge($element, $data, $index)
    {
        // get the id for the first question for this element
        $firstQuestionIdDischargeElement = OphTrOperationchecklists_Questions::model()->find('element_type_id=:element_type_id', array(':element_type_id' => $element->getElementType()->id))->id;
        $model_name = \CHtml::modelName($element);
        $checklist_result_records = array();
        if (isset($data[$model_name]['dischargeChecklistResults'])) {
            foreach ($data[$model_name]['dischargeChecklistResults'] as $idx => $checklist_results) {
                $checklist_result = null;
                if (@$checklist_results['id']) {
                    $checklist_result = OphTrOperationchecklists_DischargeResults::model()->findByPk($checklist_results['id']);
                }
                if ($checklist_result == null) {
                    $checklist_result = new OphTrOperationchecklists_DischargeResults();
                }

                $checklist_result->question_id = $checklist_results['question_id'];
                $checklist_result->answer_id = $checklist_results['answer_id'] ?? null;
                $checklist_result->answer = $checklist_results['answer'] ?? null;
                $checklist_result->comment = $checklist_results['comment'] ?? null;

                $checklist_result_records[$checklist_result->question_id] = $checklist_result;
                if ($idx == $firstQuestionIdDischargeElement) {
                    // if the response of the first question for the discharge checklist is 'Yes',
                    // then do not create anymore instances of the model.
                    if ($checklist_results['answer_id'] == OphTrOperationchecklists_Answers::model()->find('answer=:answer', array(':answer' => 'Yes'))->id) {
                        break;
                    }
                }
            }
        }
        $element->dischargeChecklistResults = $checklist_result_records;
    }

    /**
     * @param Element_OphTrOperationchecklists_Discharge $element
     * @param $data
     * @param $index
     * @throws Exception
     */
    protected function saveComplexAttributes_Element_OphTrOperationchecklists_Discharge($element, $data, $index)
    {
        $element->saveDischargeData();
    }

    /**
     * returns list of procedures for the booking operation set on the controller.
     *
     * @return Procedure[]
     */
    protected function getBookingProcedures(): array
    {
        $result = [];
        if (isset($this->booking_operation) && !isset($this->booking_procedures)) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $this->booking_procedures = $api->getProceduresForOperation($this->booking_operation->event_id);
            $result = $this->booking_procedures;
        }
        return $result;
    }

    /**
     * returns list of anaesthetic types for the booking operation set on the controller.
     *
     * @return AnaestheticType[]
     */
    protected function getBookingAnaestheticTypes(): array
    {
        $result = [];
        if (isset($this->booking_operation) && !isset($this->booking_anaesthetic_types)) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $this->booking_anaesthetic_types = $api->getAnaestheticTypesForOperation($this->booking_operation->event_id);
            $result = $this->booking_anaesthetic_types;
        }
        return $result;
    }

    /**
     * returns diagnosis for the booking operation set on the controller.
     *
     * @return Disorder
     */
    protected function getBookingDiagnosis()
    {
        if ($this->booking_operation) {
            if (!$this->booking_disgnosis_disorder) {
                $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
                $this->booking_disgnosis_disorder = $api->getDisorderForDiagnosis($this->booking_operation->event_id);
            }

            return $this->booking_disgnosis_disorder;
        }
        return null;
    }

    protected function customSetAndValidateElementsFromData($data)
    {
        $errors = array();
        // only process data for elements that are part of the element type set for the controller event type
        $elements = array();
        // get data
        foreach ($this->getEventElements() as $element) {
            $elementClassName = $element->getElementType()->class_name;
            if (isset($data[$elementClassName])) {
                $this->setElementAttributesFromData($element, $data, null);
            }
            $elements[] = $element;
        }

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

        //event date and parent validation
        if (isset($data['Event']['event_date'])) {
            $event = $this->event;
            $event_date = Helper::convertNHS2MySQL($data['Event']['event_date']);
            $current_event_date = substr($event->event_date, 0, 10);

            if ($event_date !== $current_event_date) {
                $event->event_date = $event_date;
            }

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
     * Unpacks any data that has been sent in JSON form.
     *
     * @param array $data
     * @return array $data
     */
    protected function unpackJSONAttributes($data)
    {
        foreach ($data as $elementName => $elementData) {
            if (is_array($elementData) && array_key_exists('JSON_string', $elementData)) {
                $data[$elementName] = json_decode(
                    str_replace("'", '"', $data[$elementName]['JSON_string']),
                    true
                );
            }
        }
        return $data;
    }

    /**
     * Custom validation for the operation checklists event
     *
     * @param array $data
     * @return array|mixed
     */
    protected function setAndValidateElementsFromData($data)
    {
        $data = $this->unpackJSONAttributes($data);
        $errors = $this->customSetAndValidateElementsFromData($data);
        $currentStep = $this->getCurrentStep();
        $nextStep = $this->getNextStep();
        if (isset($data['Element_OphTrOperationchecklists_Documentation']['checklistResults'])) {
            $errors = $this->setAndValidateDocumentationElement($data['Element_OphTrOperationchecklists_Documentation']['checklistResults'], $currentStep, $nextStep, $errors);
        }
        if (isset($data['Element_OphTrOperationchecklists_ClinicalAssessment']['checklistResults'])) {
            $errors = $this->setAndValidateClinicalElement($data['Element_OphTrOperationchecklists_ClinicalAssessment']['checklistResults'], $currentStep, $nextStep, $errors);
        }
        if (isset($data['Element_OphTrOperationchecklists_NursingAssessment']['checklistResults'])) {
            $errors = $this->setAndValidateNursingAssessmentElement($data['Element_OphTrOperationchecklists_NursingAssessment']['checklistResults'], $currentStep, $nextStep, $errors);
        }
        if (isset($data['Element_OphTrOperationchecklists_PatientSupport']['checklistResults'])) {
            $errors = $this->setAndValidatePatientSupportElement($data['Element_OphTrOperationchecklists_PatientSupport']['checklistResults'], $currentStep, $nextStep, $errors);
        }
        return $errors;
    }

    /**
     * Custom validation on Observations in Admission element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateObservationsFromData($data, $question_id, $errors)
    {
        $et_class_name = $this->getOpenElementByClassName('Element_OphTrOperationchecklists_Admission');
        $et_name = $et_class_name->getElementTypeName();
        $observations = new OphTrOperationchecklists_Observations();
        $observations->attributes = $data;
        if (!$observations->validate()) {
            $observationsErrors = $observations->getErrors();
            foreach ($observationsErrors as $observationsErrorAttributeName => $observationsErrorMessages) {
                foreach ($observationsErrorMessages as $observationsErrorMessage) {
                    $et_class_name->setFrontEndError('checklistResults_' . $question_id . '_observations_' . $observationsErrorAttributeName);
                    $errors[$et_name][] = $observationsErrorMessage;
                }
            }
        }
        return $errors;
    }

    /**
     * Custom validation on Documentation element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateDocumentationElement($data, $currentStep, $nextStep, $errors)
    {
        $show_error = false;
        $et_class_name = $this->getOpenElementByClassName('Element_OphTrOperationchecklists_Documentation');
        $et_name = $et_class_name->getElementTypeName();
        $nextStepName = $nextStep->name;
        if ((($nextStepName === 'Reception Practitioner') || ($nextStepName === 'Theatre Practitioner')) && ($_POST['isDocumentationResponseDifferent'] !== '1')) {
            // get the current step
            $et = Element_OphTrOperationchecklists_Documentation::model()
                ->with('checklistResults')
                ->find('event_id = :event_id AND checklistResults.set_id = :set_id', array(':event_id' => $this->event->id, ':set_id' => $currentStep->id));

            $i = 0;
            //compare the current responses
            foreach ($data as $idx => $checklist_results) {
                if ($et->checklistResults[$i]['question_id'] === $checklist_results['question_id']) {
                    $savedAnswerId = $et->checklistResults[$i]['answer_id'];
                    $savedAnswer = $et->checklistResults[$i]['answer'];
                    $currentAnswerId = $checklist_results['answer_id'] !== '' ? $checklist_results['answer_id'] : null;
                    // this can be empty, so if it is changing it to null
                    $currentAnswer = $checklist_results['answer'] !== '' ? $checklist_results['answer'] : null;
                    if (($savedAnswerId !== $currentAnswerId) || ($savedAnswer !== $currentAnswer)) {
                        // setting the post variable to true so that the warning message is not shown to the user
                        // on next save.
                        $show_error = true;
                        $_POST['isDocumentationResponseDifferent'] = true;
                        $et_class_name->setFrontEndError('checklistResults_' . $checklist_results['question_id'] . '_answer');
                    }
                }
                $i++;
            }
            if ($show_error) {
                $this->customErrorHeaderMessage = "Please review the following responses:";
                $errors[$et_name][] = "Highlighted questions do not match with the " . $this->getCurrentStep()->name . " responses. Please review them. Responses can be saved by pressing the Save/Save draft button again.";
            }
        }

        return $errors;
    }

    /**
     * Custom validation on Clinical element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateClinicalElement($data, $currentStep, $nextStep, $errors)
    {
        $show_error = false;
        $et_class_name = $this->getOpenElementByClassName('Element_OphTrOperationchecklists_ClinicalAssessment');
        $et_name = $et_class_name->getElementTypeName();
        $nextStepName = $nextStep->name;
        if ((($nextStepName === 'Reception Practitioner') || ($nextStepName === 'Theatre Practitioner')) && ($_POST['isClinicalResponseDifferent'] !== '1')) {
            // get the current step
            $et = Element_OphTrOperationchecklists_ClinicalAssessment::model()
                ->with('checklistResults')
                ->find('event_id = :event_id AND checklistResults.set_id = :set_id', array(':event_id' => $this->event->id, ':set_id' => $currentStep->id));

            $i = 0;
            //compare the current responses
            foreach ($data as $idx => $checklist_results) {
                if ($et->checklistResults[$i]['question_id'] === $checklist_results['question_id']) {
                    $savedAnswerId = $et->checklistResults[$i]['answer_id'];
                    $savedAnswer = $et->checklistResults[$i]['answer'];
                    $currentAnswerId = $checklist_results['answer_id'] !== '' ? $checklist_results['answer_id'] : null;
                    ;
                    // this can be empty, so if it is changing it to null
                    $currentAnswer = $checklist_results['answer'] !== '' ? $checklist_results['answer'] : null;
                    if (($savedAnswerId !== $currentAnswerId) || ($savedAnswer !== $currentAnswer)) {
                        // setting the post variable to true so that the warning message is not shown to the user
                        // on next save.
                        $show_error = true;
                        $_POST['isClinicalResponseDifferent'] = true;
                        $et_class_name->setFrontEndError('checklistResults_' . $checklist_results['question_id'] . '_answer');
                    }
                }
                $i++;
            }
            if ($show_error) {
                $this->customErrorHeaderMessage = "Please review the following responses:";
                $errors[$et_name][] = "Highlighted questions do not match with the " . $this->getCurrentStep()->name . " responses. Please review them. Responses can be saved by pressing the Save/Save draft button again.";
            }
        }

        return $errors;
    }

    /**
     * Custom validation on Nursing Assessment element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateNursingAssessmentElement($data, $currentStep, $nextStep, $errors)
    {
        $show_error = false;
        $et_class_name = $this->getOpenElementByClassName('Element_OphTrOperationchecklists_NursingAssessment');
        $et_name = $et_class_name->getElementTypeName();
        $nextStepName = $nextStep->name;
        if ((($nextStepName === 'Reception Practitioner') || ($nextStepName === 'Theatre Practitioner')) && ($_POST['isNursingResponseDifferent'] !== '1')) {
            // get the current step
            $et = Element_OphTrOperationchecklists_NursingAssessment::model()
                ->with('checklistResults')
                ->find('event_id = :event_id AND checklistResults.set_id = :set_id', array(':event_id' => $this->event->id, ':set_id' => $currentStep->id));

            $i = 0;
            //compare the current responses
            foreach ($data as $idx => $checklist_results) {
                if ($et->checklistResults[$i]['question_id'] === $checklist_results['question_id']) {
                    $savedAnswerId = $et->checklistResults[$i]['answer_id'];
                    $savedAnswer = $et->checklistResults[$i]['answer'];
                    $currentAnswerId = $checklist_results['answer_id'] !== '' ? $checklist_results['answer_id'] : null;
                    ;
                    // this can be empty, so if it is changing it to null
                    $currentAnswer = $checklist_results['answer'] !== '' ? $checklist_results['answer'] : null;
                    if (($savedAnswerId !== $currentAnswerId) || ($savedAnswer !== $currentAnswer)) {
                        // setting the post variable to true so that the warning message is not shown to the user
                        // on next save.
                        $show_error = true;
                        $_POST['isNursingResponseDifferent'] = true;
                        $et_class_name->setFrontEndError('checklistResults_' . $checklist_results['question_id'] . '_answer');
                    }
                }
                $i++;
            }
            if ($show_error) {
                $this->customErrorHeaderMessage = "Please review the following responses:";
                $errors[$et_name][] = "Highlighted questions do not match with the " . $this->getCurrentStep()->name . " responses. Please review them. Responses can be saved by pressing the Save/Save draft button again.";
            }
        }

        return $errors;
    }

    /**
     * Custom validation on Patient Support element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidatePatientSupportElement($data, $currentStep, $nextStep, $errors)
    {
        $show_error = false;
        $et_class_name = $this->getOpenElementByClassName('Element_OphTrOperationchecklists_PatientSupport');
        $et_name = $et_class_name->getElementTypeName();
        $nextStepName = $nextStep->name;
        if ((($nextStepName === 'Reception Practitioner') || ($nextStepName === 'Theatre Practitioner')) && ($_POST['isPatientSupportResponseDifferent'] !== '1')) {
            // get the current step
            $et = Element_OphTrOperationchecklists_PatientSupport::model()
                ->with('checklistResults')
                ->find('event_id = :event_id AND checklistResults.set_id = :set_id', array(':event_id' => $this->event->id, ':set_id' => $currentStep->id));

            $i = 0;
            //compare the current responses
            foreach ($data as $idx => $checklist_results) {
                if ($et->checklistResults[$i]['question_id'] === $checklist_results['question_id']) {
                    $savedAnswerId = $et->checklistResults[$i]['answer_id'];
                    $savedAnswer = $et->checklistResults[$i]['answer'];
                    $currentAnswerId = $checklist_results['answer_id'] !== '' ? $checklist_results['answer_id'] : null;
                    ;
                    // this can be empty, so if it is changing it to null
                    $currentAnswer = $checklist_results['answer'] !== '' ? $checklist_results['answer'] : null;
                    if (($savedAnswerId !== $currentAnswerId) || ($savedAnswer !== $currentAnswer)) {
                        // setting the post variable to true so that the warning message is not shown to the user
                        // on next save.
                        $show_error = true;
                        $_POST['isPatientSupportResponseDifferent'] = true;
                        $et_class_name->setFrontEndError('checklistResults_' . $checklist_results['question_id'] . '_answer');
                    }
                }
                $i++;
            }
            if ($show_error) {
                $this->customErrorHeaderMessage = "Please review the following responses:";
                $errors[$et_name][] = "Highlighted questions do not match with the " . $this->getCurrentStep()->name . " responses. Please review them. Responses can be saved by pressing the Save/Save draft button again.";
            }
        }

        return $errors;
    }

    /**
     * Delete Note
     *
     * @throws Exception
     */
    public function actionDeleteNote()
    {
        if (!$note = OphTrOperationchecklists_Notes::model()->find('id=?', array(@$_POST['case-note-id']))) {
            throw new \Exception('Note not found: ' . @$_POST['case-note-id']);
        }

        if (!$note->delete()) {
            throw new \Exception('Unable to delete note: ' . print_r($note->getErrors(), true));
        }

        $msg = 'Note ' . $note->primaryKey . " is deleted.";
        Audit::add('note', 'delete', $msg, null, array(
            'module' => (is_object($this->module)) ? $this->module->id : 'core',
            'model' => 'OphTrOperationchecklists_Notes',
        ));

        echo "1";
    }

    public function isRequiredInUI(\BaseEventTypeElement $element)
    {
        return true;
    }

    /**
     * Override action value when action is step to be update.
     *
     * @param BaseEventTypeElement $element
     * @param string $action
     * @param BaseEventTypeCActiveForm $form
     * @param ?array $data
     * @param array $template_data
     * @param array $view_data
     * @param bool $return
     * @param bool $processOutput
     * @throws Exception
     */
    protected function renderElement(
        $element,
        $action,
        $form,
        ?array $data = null,
        ?array $template_data = [],
        ?array $view_data = [],
        bool $return = false,
        bool $processOutput = false
    ) {
        if (($action === 'step') || ($action === 'update')) {
            $view_data = array_merge(array(
                'isCollapsable' => true,
            ), $view_data);

            $class_names = [];

            if ($action === 'update') {
                $assignment = OphTrOperationchecklists_EventElementSetAssignment::model()->find('event_id = :event_id', array(':event_id' => $this->event->id));
                if (isset($assignment)) {
                    $current_step = $this->getNextStep();
                    foreach ($current_step->items as $item) {
                        $class_names[] = $item->element_type->class_name;
                    }

                    if (in_array(get_class($element), $class_names)) {
                        $action = 'update';
                    } else {
                        $action = 'view';
                    }
                } else {
                    $current_step = $this->getNextStep();
                    foreach ($current_step->items as $item) {
                        $class_names[] = $item->element_type->class_name;
                    }

                    if (in_array(get_class($element), $class_names)) {
                        $action = 'view';
                    } else {
                        $action = 'update';
                    }
                }
            } elseif ($action === 'step') {
                $action = 'view';
                $current_step = $this->getNextStep();
                foreach ($current_step->items as $item) {
                    if ($item->element_type->class_name === get_class($element)) {
                        $action = 'update';
                    }
                }
            }

            // Note element should always be in the editable mode.
            if (get_class($element) === 'Element_OphTrOperationchecklists_Note') {
                $action = 'update';
            }
        }

        parent::renderElement($element, $action, $form, $data, $template_data, $view_data, $return, $processOutput);
    }

    public function renderOpenElements($action, $form = null, $date = null)
    {
        if ($action === 'renderEventImage') {
            $action = 'view';
        }
        $step_id = \Yii::app()->request->getParam('step_id');

        $elements = $this->getElements($action);

        if ($action !== 'view' && $action !== 'createImage') {
            parent::renderOpenElements($action, $form, $date);

            return;
        }

        $this->renderElements($elements, $action, $form, $date);
    }

    /**
     * Action to move the workflow forward a step on the given event.
     *
     * @param $id
     */
    public function actionStep($id)
    {
        // This is the same as update.
        $this->actionUpdate($id);
    }

    /**
     * Advance the workflow step for the event if requested.
     *
     * @param Event $event
     *
     * @throws \CException
     */
    protected function afterUpdateElements($event)
    {
        parent::afterUpdateElements($event);
        if ($this->step && !$this->isDraft) {
            // Advance the workflow
            if (!$assignment = OphTrOperationchecklists_EventElementSetAssignment::model()->find('event_id = ?', array($event->id))) {
                // Create initial workflow assignment if event hasn't already got one
                $assignment = new OphTrOperationchecklists_EventElementSetAssignment();
                $assignment->event_id = $event->id;
            }

            $assignment->step_id = $this->step->id;
            if (!$assignment->save()) {
                throw new \CException('Cannot save assignment' . print_r($assignment->getErrors(), true));
            }

            // update the information attribute on the event
            $this->updateEventInfoByStep($this->getCurrentStep()->id);
        }
    }

    protected function afterCreateElements($event)
    {
        parent::afterCreateElements($event);
        if ($this->step && !$this->isDraft) {
            // Advance the workflow
            if (!$assignment = OphTrOperationchecklists_EventElementSetAssignment::model()->find('event_id = ?', array($event->id))) {
                // Create initial workflow assignment if event hasn't already got one
                $assignment = new OphTrOperationchecklists_EventElementSetAssignment();
                $assignment->event_id = $event->id;
            }

            $assignment->step_id = $this->step->id;
            if (!$assignment->save()) {
                throw new \CException('Cannot save assignment' . print_r($assignment->getErrors(), true));
            }

            // update the information attribute on the event
            $this->updateEventInfoByStep($this->getCurrentStep()->id);
        }
    }

    /**
     * Get the array of elements for the current site, subspecialty, episode status and workflow position
     *
     * @param OphTrOperationchecklists_ElementSet $set
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getElementsByWorkflow($set = null)
    {
        $elements = array();
        if (!$set) {
            $set = OphTrOperationchecklists_ElementSet::model()->find('position = 1');
        }

        if ($set) {
            $element_types = $set->DefaultElementTypes;
            foreach ($element_types as $element_type) {
                $elements[$element_type->id] = $element_type->getInstance();
            }
        }

        $this->set = $set;

        return $elements;
    }

    public function getElements($action = 'edit')
    {
        ksort($this->open_elements);
        return $this->open_elements;
    }

    /**
     * Applies workflow and filtering to the element retrieval.
     * @return \BaseEventTypeElement[]
     * @throws \CException
     */
    protected function getEventElements()
    {
        if (!$this->event || $this->event->isNewRecord) {
            $elements = $this->getElementsByWorkflow(null);
        } else {
            $elements = $this->event->getElements();
            if ($this->step) {
                $elements = $this->mergeNextStep($elements);
            }
        }

        // move note element to the end of the elements array
        $notesElement = null;
        foreach ($elements as $key => $element) {
            if (get_class($element) === 'Element_OphTrOperationchecklists_Note') {
                $notesElement = $element;
                unset($elements[$key]);
                break;
            }
        }
        if (isset($notesElement)) {
            array_push($elements, $notesElement);
        }

        return $elements;
    }

    /**
     * Merge workflow next step elements into existing elements.
     *
     * @param array $elements
     *
     * @return array
     * @throws \CException
     *
     */
    protected function mergeNextStep($elements)
    {
        if (!$event = $this->event) {
            throw new \CException('No event set for step merging');
        }

        $extra_elements = $this->getElementsByWorkflow($this->step);
        $extra_by_etid = array();
        foreach ($extra_elements as $extra) {
            $extra_by_etid[$extra->getElementType()->id] = $extra;
        }

        $merged_elements = array();
        foreach ($elements as $element) {
            $merged_elements[$element->getElementType()->id] = $element;
        }
        foreach ($extra_by_etid as $extra_element) {
            $extra_element->setDefaultOptions($this->patient);
            // Precache Element Type to avoid bug in usort
            $extra_element->getElementType();
            // if this extra_element already exists in the merged elements array, don't push it to the array.
            if (!array_key_exists($extra_element->getElementType()->id, $merged_elements)) {
                $merged_elements[] = $extra_element;
            }
        }

        ksort($merged_elements);
        return $merged_elements;
    }

    /**
     * Returns Element Set Assignment
     * @param null $event
     * @return mixed|null
     */
    public function getElementSetAssignment($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        if ($event && !$event->isNewRecord && $assignment = OphTrOperationchecklists_EventElementSetAssignment::model()->find('event_id = ?', array($event->id))) {
            return $assignment;
        }

        return null;
    }

    /**
     * @param null $event
     * @return null|OphTrOperationchecklists_ElementSet
     */
    protected function getCurrentStep($event = null)
    {
        if (!$event) {
            $event = $this->event;
        }

        $assignment = $this->getElementSetAssignment($event);
        return $assignment ? $assignment->step : OphTrOperationchecklists_ElementSet::model()->find('position = 1');
    }

    /**
     * Get the next workflow step.
     *
     * @param Event $event
     *
     * @return OphTrOperationchecklists_ElementSet
     */
    protected function getNextStep($event = null)
    {
        $step = $this->getCurrentStep();
        return $step->getNextStep();
    }

    /**
     * Iterates through the open elements and calls the custom methods for saving complex data attributes to them.
     *
     * Custom method is of the name format saveComplexAttributes_[element_class_name]($element, $data, $index)
     *
     * @param $data
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        $counter_by_cls = array();

        foreach ($this->open_elements as $element) {
            $el_cls_name = get_class($element);
            if (isset($data[$el_cls_name])) {
                $element_method = 'saveComplexAttributes_' . Helper::getNSShortname($element);
                if (method_exists($this, $element_method)) {
                    // there's custom behaviour for setting additional relations on this element class
                    if (!isset($counter_by_cls[$el_cls_name])) {
                        $counter_by_cls[$el_cls_name] = 0;
                    } else {
                        ++$counter_by_cls[$el_cls_name];
                    }
                    $this->$element_method($element, $data, $counter_by_cls[$el_cls_name]);
                }
            }
        }
    }

    /**
     * Save the event for this controller - will create or update the event, create and update the elements.
     *
     * @param $data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function saveEvent($data)
    {
        if ($this->event->isNewRecord) {
            if (!$this->event->save()) {
                OELog::log("Failed to create new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
                throw new Exception('Unable to save event.');
            }
            OELog::log("Created new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);

            $operationChecklistEvent = new OphTrOperationchecklists_Event();
            $operationChecklistEvent->event_id = $this->event->id;
            // check if the event is a draft event.
            if ($this->isDraft) {
                $operationChecklistEvent->draft = 1;
            } else {
                $operationChecklistEvent->draft = 0;
            }
            if (!$operationChecklistEvent->save()) {
                OELog::log("Failed to save the operation checklist event info");
                throw new Exception('Unable to save event info.');
            }
        } else {
            $operationChecklistEvent = OphTrOperationchecklists_Event::model()->find('event_id = :event_id', array(':event_id' => $this->event->id));
            // check if the event is a draft event.
            if ($this->isDraft) {
                $operationChecklistEvent->draft = '1';
            } else {
                $operationChecklistEvent->draft = '0';
            }
            if (!$operationChecklistEvent->save()) {
                OELog::log("Failed to save the operation checklist event info");
                throw new Exception('Unable to save event info.');
            }
        }

        foreach ($this->open_elements as $element) {
            $element->event_id = $this->event->id;
            // No need to validate as it has already been validated and the event id was just generated.
            if (!$element->save(false)) {
                throw new Exception('Unable to save element ' . get_class($element) . '.');
            }
        }

        // ensure any complex data is saved to the elements
        $this->saveEventComplexAttributesFromData($data);

        return true;
    }

    /**
     * Render the given errors with the standard template.
     *
     * @param $errors
     * @param bool $bottom
     * @param string $errorHeaderMessage
     * @throws CException
     */
    public function displayErrors($errors, $bottom = false, $errorHeaderMessage = '')
    {
        $this->renderPartial('//elements/form_errors', array(
            'errors' => $errors,
            'bottom' => $bottom,
            'elements' => $this->open_elements,
            'errorHeaderMessage' => $errorHeaderMessage,
        ));
    }
}
