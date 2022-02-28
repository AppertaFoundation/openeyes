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
class WorklistController extends BaseController
{
    public $layout = 'worklist';
    /**
     * @var WorklistManager
     */
    protected $manager;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('OprnWorklist')));
    }

    public function behaviors()
    {
        return array(
            'SetupPathwayStepPicker' => ['class' => 'application.behaviors.SetupPathwayStepPickerBehavior',],
        );
    }

    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
        if ($action->getId() === "print") {
            $newblue_path = 'application.assets.newblue';
            Yii::app()->assetManager->registerCssFile('/dist/css/style_oe_print.3.css', $newblue_path, null);
        }

        $this->manager = new WorklistManager();

        return parent::beforeAction($action);
    }
    protected function prescriberDomData($require_preset = true)
    {
        $ret = array(
            'preset_orders' => array(),
            'is_prescriber' => false,
            'popup' => null,
            'assign_preset_btn' => null,
        );
        if ($is_prescriber = $this->checkAccess('Prescribe')) {
            if ($require_preset) {
                $preset_criteria = new CDbCriteria();
                $preset_criteria->compare('active', true);
                $preset_orders = OphDrPGDPSD_PGDPSD::model()->findAll($preset_criteria) ? : array();
                $preset_orders_json = array_map(
                    static function ($item) {
                        return array('id' => $item->id, 'name' => 'preset_order', 'label' => $item->name);
                    },
                    $preset_orders
                );
                $ret['preset_orders'] = $preset_orders_json;
            }
            $ret['is_prescriber'] = $is_prescriber;
            $ret['assign_preset_btn'] = "<div class='button-stack'><button disabled class='green hint' id='js-worklist-psd-add'>Assign Preset Order to selected patients</button></div>";
        }
        return $ret;
    }
    public function actionView()
    {
        $date_from = Yii::app()->request->getQuery('date_from');
        $date_to = Yii::app()->request->getQuery('date_to');
        $redirect = false;

        if (!isset(Yii::app()->session['worklist'])) {
            Yii::app()->session['worklist'] = [];
        }

        if ($date_from || $date_to) {
            foreach (['date_from', 'date_to'] as $date) {
                ${$date} = is_numeric(str_replace([" ", "/"], "", ${$date})) ? str_replace([" ", "/"], "-", ${$date}) : str_replace(['/'], " ", ${$date});
            }
            Yii::app()->session['worklist'] = ['date_from' => $date_from, 'date_to' => $date_to];
        }

        if (count(Yii::app()->session['worklist']) > 0) {
            foreach (['date_from', 'date_to'] as $date) {
                if (Yii::app()->session['worklist'][$date] && !${$date}) {
                    ${$date} = str_replace(" ", "+", Yii::app()->session['worklist'][$date]);
                    $redirect = true;
                }
            }
        }

        if ($redirect) {
            $this->redirect(array('/worklist/view?date_from=' . $date_from . '&date_to=' . $date_to));
        }

        $filter = WorklistFilterQuery::getLastUsedFilterFromSession();
        $filter = $filter['filter'];

        $date_from = $filter->getFrom() ?? $date_from;
        $date_to = $filter->getTo() ?? $date_to;

        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);

        if (WorklistFilter::model()->countForCurrentUser() !== 0 || WorklistRecentFilter::model()->countForCurrentUser() !== 0) {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.InputFieldValidation.js'), ClientScript::POS_END);
            $worklist_js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', true);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($worklist_js, ClientScript::POS_END);

            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.UI.WorklistFilterPanel.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.UI.WorklistQuickFilterPanel.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.WorklistFilter.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.WorklistFiltersController.js'), ClientScript::POS_END);

            $sync_interval_setting_key = 'worklist_auto_sync_interval';
            $sync_interval_settings = \SettingMetadata::model()->find("`key` = 'worklist_auto_sync_interval'");
            $sync_interval_options = unserialize($sync_interval_settings->data, ['allowed_classes' => true]);
            $sync_interval_value = $sync_interval_settings->getSetting();
            $prescriber_dom_data = $this->prescriberDomData(false);

            $picker_setup = $this->setupPicker();
            $path_step_type_ids = json_encode($this->getPathwayStepTypesRequirePicker());
            $this->render(
                'index',
                array(
                    'worklists' => $worklists,
                    'picker_setup' => $picker_setup,
                    'path_step_type_ids' => $path_step_type_ids,
                    'path_steps' => PathwayStepType::getPathTypes(),
                    'pathways' => PathwayType::model()->findAll(),
                    'standard_steps' => PathwayStepType::getStandardTypes(),
                    'custom_steps' => PathwayStepType::getCustomTypes(),
                    'sync_interval_options' => $sync_interval_options,
                    'sync_interval_value' => $sync_interval_value,
                    'sync_interval_setting_key' => $sync_interval_setting_key,
                    'is_prescriber' => $prescriber_dom_data['is_prescriber'],
                    'preset_orders' => $prescriber_dom_data['preset_orders'],
                    'assign_preset_btn' => $prescriber_dom_data['assign_preset_btn'],
                )
            );
        } else {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.UI.WorklistFilterPanel.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.WorklistFilter.js'), ClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/worklist/OpenEyes.WorklistFiltersController.js'), ClientScript::POS_END);

            $this->render(
                'landing_page',
                array('worklists' => $worklists)
            );
        }
    }

    public function actionGetPresetDrugs($id)
    {
        $preset = OphDrPGDPSD_PGDPSD::model()->findByPk($id);
        $laterality = Yii::app()->request->getQuery('laterality');

        if ($preset) {
            $json = array_map(
                static function ($medication) use ($laterality) {
                    return array(
                        'id' => $medication->id,
                        'drug_name' => $medication->medication->preferred_term,
                        'dose' => $medication->dose . ' ' . $medication->dose_unit_term,
                        'route' => $medication->route->has_laterality ? false : $medication->route->term,
                        'laterality' => (bool)$medication->route->has_laterality,
                        'right_eye' => $laterality && ($laterality & MedicationLaterality::RIGHT),
                        'left_eye' => $laterality && ($laterality & MedicationLaterality::LEFT),
                    );
                },
                $preset->assigned_meds
            );
            $this->renderJSON($json);
        }
    }

    /**
     * @throws Exception
     */
    public function actionChangePathwayStatus()
    {
        $pathway_id = Yii::app()->request->getPost('pathway_id');
        $new_status = Yii::app()->request->getPost('new_status');
        $step_action = Yii::app()->request->getPost('step_action');
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway) {
            switch ($step_action) {
                case 'remove':
                    $success = $pathway->removeIncompleteSteps();
                    break;
                case 'mark_done':
                    $success = $pathway->completeIncompleteSteps();
                    break;
                default:
                    $success = true;
            }
            $pathway->refresh();
            if ($success) {
                switch ($new_status) {
                    case 'discharged':
                        // This comparison caters for quick-completed pathways being re-activated.
                        if (count($pathway->started_steps) > 0) {
                            $pathway->status = Pathway::STATUS_ACTIVE;
                        } elseif (count($pathway->requested_steps) > 0) {
                            $pathway->status = Pathway::STATUS_WAITING;
                        } else {
                            $pathway->status = Pathway::STATUS_DISCHARGED;
                        }

                        if ($pathway->end_time) {
                            $pathway->end_time = null;
                        }
                        break;
                    case 'done':
                        $pathway->status = Pathway::STATUS_DONE;
                        $pathway->end_time = date('Y-m-d H:i:s');
                        break;
                    case 'wait':
                        $pathway->status = Pathway::STATUS_WAITING;
                        break;
                    case 'long-wait':
                        $pathway->status = Pathway::STATUS_DELAYED;
                        break;
                    case 'stuck':
                        $pathway->status = Pathway::STATUS_STUCK;
                        break;
                    case 'break':
                        $pathway->status = Pathway::STATUS_BREAK;
                        break;
                    case 'active':
                        $pathway->status = Pathway::STATUS_ACTIVE;
                        break;
                    default:
                        $pathway->status = Pathway::STATUS_LATER;
                        break;
                }
                $pathway->save();
                $pathway->refresh();
                $this->renderJSON(
                    [
                        'status' => $pathway->getStatusString(),
                        'step_html' => $this->renderPartial('_clinical_pathway', ['pathway' => $pathway], true),
                        'status_html' => $pathway->getPathwayStatusHTML(),
                        'waiting_time_html' => $pathway->getTotalDurationHTML(true),
                    ]
                );
                Yii::app()->end();
            }
            throw new CHttpException(500, 'Unable to transition pathway steps.');
        }
        throw new CHttpException(404, 'Unable to locate pathway');
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionChangeStepStatus()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $direction = Yii::app()->request->getPost('direction');
        $step = PathwayStep::model()->findByPk($step_id);
        if ($step) {
            if ($direction === 'next') {
                $extra_form_data = Yii::app()->request->getPost('extra_form_data');
                if ($extra_form_data && array_key_exists('YII_CSRF_TOKEN', $extra_form_data)) {
                    unset($extra_form_data['YII_CSRF_TOKEN']);
                }
                $step->nextStatus($extra_form_data);
            } else {
                if ($step->short_name === "Discharge") {
                    $hl7_a13 = new \OEModule\PASAPI\resources\HL7_A13();
                    $hl7_a13->setDataFromEvent(\Event::model()->find("worklist_patient_id = ?", array($step->pathway->worklist_patient->id))->id);
                    Yii::app()->event->dispatch('emergency_care_update',
                            $hl7_a13
                    );
                }
                $step->prevStatus();
            }
            $step->refresh();

            $pathway = $step->pathway;

            $pathway->updateStatus();

            if ((int)$step->status === PathwayStep::STEP_STARTED) {
                Yii::app()->event->dispatch('step_started', ['step' => $step]);
            } elseif ((int)$step->status === PathwayStep::STEP_COMPLETED) {
                Yii::app()->event->dispatch('step_completed', ['step' => $step]);
            }

            $this->renderJSON(
                [
                    'step' => $step->toJSON(),
                    'pathway_status' => $pathway->getStatusString(),
                    'pathway_status_html' => $pathway->getPathwayStatusHTML(),
                    'wait_time_details' => json_encode($pathway->getWaitTimeSinceLastAction()),
                ]
            );
            Yii::app()->end();
        }
        throw new CHttpException(404, 'Unable to retrieve step for processing.');
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionCheckIn()
    {
        $visit_id = Yii::app()->request->getPost('visit_id');
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);
        /**
         * @var $wl_patient WorklistPatient
         */
        $pathway = $wl_patient->pathway;

        if ($pathway) {
            $pathway->startPathway();
            $this->renderJSON(
                [
                    'end_time' => DateTime::createFromFormat('Y-m-d H:i:s', $pathway->start_time)->format('H:i'),
                    'pathway_status_html' => $pathway->getPathwayStatusHTML(),
                ]
            );
        }
        throw new CHttpException(404, 'Unable to retrieve step for processing or step is not a checkin step.');
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionDidNotAttend()
    {
        $visit_id = Yii::app()->request->getPost('visit_id');
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);
        /**
         * @var $wl_patient WorklistPatient
         */
        $pathway = $wl_patient->pathway;

        if ($pathway) {
            $pathway->start_time = date('Y-m-d H:i:s');
            if (count($pathway->requested_steps) === 0) {
                $pathway->status = Pathway::STATUS_DONE;
                $pathway->end_time = date('Y-m-d H:i:s');
            } else {
                $pathway->status = Pathway::STATUS_DISCHARGED;
            }

            $pathway->did_not_attend = true;
            $pathway->save();
            // Create and save a Did Not Attend event.
            $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            $event_type_id = EventType::model()->find(
                'class_name = :class_name',
                [':class_name' => 'OphCiDidNotAttend']
            )->id;
            $service = Firm::model()->find(
                'service_subspecialty_assignment_id = :id AND can_own_an_episode = 1',
                [':id' => $firm->service_subspecialty_assignment_id]
            );
            $service_id = $service->id;

            $params = [
                'patient_id' => $pathway->worklist_patient->patient_id,
                'context_id' => $firm->id,
                'service_id' => $service_id,
                'event_type_id' => $event_type_id
            ];

            $this->renderJSON(
                [
                    'redirect_url' => '/patientEvent/create?' . http_build_query($params),
                    'pathway_status_html' => $pathway->getPathwayStatusHTML(),
                ]
            );
        }
        throw new CHttpException(404, 'Unable to retrieve step for processing or step is not a checkin step.');
    }

    /**
     * @throws CDbException
     */
    public function actionDeleteStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $step = PathwayStep::model()->findByPk($step_id);
        if ($step) {
            Yii::app()->event->dispatch('step_deleted', ['step' => $step]);
            $step->delete();
            echo '1';
        }
    }

    /**
     * @throws CHttpException
     */
    public function actionReorderStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $direction = Yii::app()->request->getPost('direction');
        $step = PathwayStep::model()->findByPk($step_id);
        $altered_steps = array();

        if ($step) {
            $old_order = $step->order;
            $new_order = $direction === 'left' ? $old_order - 1 : $old_order + 1;

            // As we're only moving one step, we should only have to reorder at most a single step.
            $step_to_reorder = PathwayStep::model()->find(
                "pathway_id = :pathway_id AND (status IN (-1, 0) OR status IS NULL) AND id != :id AND `order` = :order",
                [
                    'pathway_id' => $step->pathway_id,
                    ':id' => $step->id,
                    ':order' => $new_order
                ]
            );

            if ($step_to_reorder) {
                $step_to_reorder->order = $old_order;
                $step_to_reorder->save();
                $step_to_reorder->refresh();
                $altered_steps[$step_to_reorder->id] = $step_to_reorder;
            }
            $step->order = $new_order;
            if (!$step->save()) {
                throw new CHttpException('Unable to reorder step.');
            }
            $step->refresh();
            $altered_steps[$step->id] = $step;
        }

        $this->renderJSON($altered_steps);
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionGetVfPresetData($id)
    {
        $preset = VisualFieldTestPreset::model()->findByPk($id);
        if ($preset) {
            $this->renderJSON(
                array(
                    'test_type_id' => $preset->test_type_id,
                    'test_type_name' => $preset->testType->short_name,
                    'test_option_id' => $preset->option_id,
                    'option_name' => $preset->option->short_name,
                )
            );
        }
        throw new CHttpException(404, 'Unable to retrieve Fields preset.');
    }

    /**
     * @param $partial
     * @param $pathstep_id
     * @param $patient_id
     * @param $red_flag
     * @throws CException
     * @throws CHttpException
     */
    public function actionGetPathStep($partial, $pathstep_id, $patient_id, $red_flag = false, $interactive = 1)
    {
        switch ($pathstep_id) {
            case 'checkin':
                $wl_patient = WorklistPatient::model()->find('patient_id = :id', [':id' => $patient_id]);
                if ($wl_patient) {
                    $dom = $this->renderPartial('/worklist/steps/checkin', array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => Patient::model()->findByPk($patient_id),
                        'partial' => $partial
                    ), true);
                    $this->renderJSON($dom);
                } else {
                    throw new CHttpException('Unable to retrieve pathway for patient.');
                }
                break;
            case 'comment':
                $wl_patient = WorklistPatient::model()->find('patient_id = :id', [':id' => $patient_id]);
                if ($wl_patient) {
                    $dom = $this->renderPartial('//worklist/comment', array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => Patient::model()->findByPk($patient_id),
                        'partial' => $partial
                    ), true);
                    $this->renderJSON($dom);
                } else {
                    throw new CHttpException('Unable to retrieve pathway for patient.');
                }
                break;
            case 'wait':
                $wl_patient = WorklistPatient::model()->find('patient_id = :id', [':id' => $patient_id]);
                $view_file = 'callout';
                $dom = $this->renderPartial(
                    '//worklist/steps/' . $view_file,
                    array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => Patient::model()->findByPk($patient_id),
                        'partial' => $partial
                    ),
                    true
                );
                $this->renderJSON($dom);
                break;
            case 'finished':
                $wl_patient = WorklistPatient::model()->find('patient_id = :id', [':id' => $patient_id]);
                $view_file = 'finished';
                $dom = $this->renderPartial(
                    '//worklist/steps/' . $view_file,
                    array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => Patient::model()->findByPk($patient_id),
                        'partial' => $partial
                    ),
                    true
                );
                $this->renderJSON($dom);
                break;
            default:
                $step = PathwayStep::model()->findByPk($pathstep_id);
                if ($step && $step->type->short_name === 'drug admin') {
                    $psd_assignment_id = $step->getState('assignment_id');

                    if (!$psd_assignment_id) {
                        throw new CHttpException('Unable to retrieve PSD id');
                    }

                    $psd_assignment = OphDrPGDPSD_Assignment::model()->findByPk($psd_assignment_id);

                    if (!$psd_assignment) {
                        throw new CHttpException(404, 'Unable to retrieve PSD.');
                    }

                    if (intval($interactive)) {
                        $interactive = $psd_assignment->getAppointmentDetails()['date'] === 'Today' ? 1 : 0;
                    }
                    $can_remove_psd = \Yii::app()->user->checkAccess('Prescribe') && (int)$step->status === PathwayStep::STEP_REQUESTED && !$psd_assignment->elements ? '' : 'disabled';
                    $dom = $this->renderPartial(
                        'application.modules.OphDrPGDPSD.views.pathstep.pathstep_view',
                        array(
                            'assignment' => $psd_assignment,
                            'step' => $step,
                            'partial' => (int)$partial,
                            'patient_id' => $patient_id,
                            'for_administer' => 0,
                            'is_prescriber' => Yii::app()->user->checkAccess('Prescribe'),
                            'can_remove_psd' => $can_remove_psd,
                            'interactive' => (bool)$interactive,
                        ),
                        true
                    );
                    $this->renderJSON($dom);
                    Yii::app()->end();
                }

                if ($step) {
                    $view_file = $step->type->widget_view ?? 'generic_step';
                    $dom = $this->renderPartial(
                        '//worklist/steps/' . $view_file,
                        array(
                            'step' => $step,
                            'patient' => Patient::model()->findByPk($patient_id),
                            'partial' => $partial,
                            'red_flag' => $red_flag
                        ),
                        true
                    );
                    $this->renderJSON($dom);
                }
                break;
        }
    }

    /**
     * Redirect to a suitable worklist default action.
     */
    public function actionIndex()
    {
        return $this->redirect(array('/worklist/manual'));
    }

    /**
     * Manage User's manual worklists.
     */
    public function actionManual()
    {
        $current_worklists = $this->manager->getCurrentManualWorklistsForUser(Yii::app()->user);
        $available_worklists = $this->manager->getAvailableManualWorklistsForUser(Yii::app()->user);

        $this->render('//worklist/manual/index', array(
            'current_worklists' => $current_worklists,
            'available_worklists' => $available_worklists,
        ));
    }

    public function actionManualAdd()
    {
        $worklist = new Worklist();

        if (!empty($_POST)) {
            $worklist->attributes = $_POST['Worklist'];
            if ($this->manager->createWorklistForUser($worklist)) {
                Audit::add('Manual-Worklist', 'add', $worklist->id);
                $this->redirect('/worklist/manual');
            } else {
                $errors = $worklist->getErrors();
            }
        }

        $this->render('//worklist/manual/add', array(
            'worklist' => $worklist,
            'errors' => @$errors,
        ));
    }

    /**
     * Update the worklist display order for the current user based on the submitted ids.
     */
    public function actionManualUpdateDisplayOrder()
    {
        $worklist_ids = @$_POST['item_ids'] ? explode(',', $_POST['item_ids']) : array();

        if (!$this->manager->setWorklistDisplayOrderForUser(Yii::app()->user, $worklist_ids)) {
            OELog::log(print_r($this->manager->getErrors(), true));
            throw new Exception('Unable to save new display order for worklists');
        }

        $this->redirect('/worklist/manual');
    }

    public function actionPrint($date_from = null, $date_to = null, $list_id = null)
    {
        $this->layout = '//layouts/print';

        $filter = WorklistFilterQuery::getLastUsedFilterFromSession();
        $filter = $filter['filter'];

        $date_from = $filter->getFrom() ?? $date_from;
        $date_to = $filter->getTo() ?? $date_to;

        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);

        if ($list_id) {
            $worklists = array_filter($worklists, function ($e) use ($list_id) {
                return (int)$e->id === (int)$list_id;
            });
        }


        $this->render('//worklist/print', array('worklists' => $worklists, 'filter' => $filter));
    }

    public function actionClearDates()
    {
        Yii::app()->session->remove('worklist');
        return $this->redirect(array('/worklist/view'));
    }

    public function actionRenderPopup()
    {
        if (isset($_POST['patientId'])) {
            $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
            $patientId = $_POST['patientId'];
            $patient = Patient::model()->findByPk($patientId);

            $deceased = $patient->isDeceased();
            $institution = Institution::model()->getCurrent();
            $selected_site_id = Yii::app()->session['selected_site_id'];
            $display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];
            $display_secondary_number_usage_code = Yii::app()->params['display_secondary_number_usage_code'];
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
            $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
            $patientIdentifiers = null;
            foreach ($patient->identifiers as $patientIdentifier) {
                $patientIdentifiers[] = [
                    'longTitle' => $patientIdentifier->patientIdentifierType->long_title,
                    'shortTitle' => $patientIdentifier->patientIdentifierType->short_title,
                    'value' => $patientIdentifier->value,
                    'valueDisplayPrefix' => $patientIdentifier->patientIdentifierType->value_display_prefix,
                    'valueDisplaySuffix' => $patientIdentifier->patientIdentifierType->value_display_suffix,
                    'patientIdentifierStatus' => $patientIdentifier->patientIdentifierStatus,
                    'description' => $patientIdentifier->patientIdentifierStatus ? $patientIdentifier->patientIdentifierStatus->description : null,
                    'iconBannerClassName' => $patientIdentifier->patientIdentifierStatus ? $patientIdentifier->patientIdentifierStatus->icon->banner_class_name : null,
                ];
            }

            $patientDeletedIdentifiers = null;
            foreach (PatientIdentifier::model()->resetScope(true)->findAll('deleted = 1 AND patient_id = ?', [$patient->id]) as $patientDeletedIdentifier) {
                $patientDeletedIdentifiers[] = [
                    'identifier' => $patientDeletedIdentifier,
                    'longTitle' => $patientDeletedIdentifier->patientIdentifierType->long_title,
                    'shortTitle' => $patientDeletedIdentifier->patientIdentifierType->short_title,
                    'value' => $patientDeletedIdentifier->value,
                    'valueDisplayPrefix' => $patientDeletedIdentifier->patientIdentifierType->value_display_prefix,
                    'valueDisplaySuffix' => $patientDeletedIdentifier->patientIdentifierType->value_display_suffix,
                ];
            }

            $patientLocalIdentifiers = null;
            foreach ($patient->localIdentifiers as $patientLocalIdentifier) {
                $patientLocalIdentifiers[] = [
                    'hasValue' => $patientLocalIdentifier->hasValue(),
                    'shortTitle' => $patientLocalIdentifier->patientIdentifierType->short_title,
                    'displayValue' => $patientLocalIdentifier->getDisplayValue(),
                ];
            }

            $patientData['href'] = (new CoreAPI())->generatePatientLandingPageLink($patient);
            $patientData['lastname'] = $patient->getLast_name();
            $patientData['firstname'] = $patient->getFirst_name();
            $patientData['title'] = $patient->getTitle();
            $patientData['hospitalNumberPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($primary_identifier);
            $patientData['hospitalNumberValue'] = PatientIdentifierHelper::getIdentifierValue($primary_identifier);
            $patientData['displayPrimaryNumberUsageCode'] = $display_primary_number_usage_code;
            $patientData['patientPrimaryIdentifierStatus'] = $display_primary_number_usage_code === 'GLOBAL' && $primary_identifier && $primary_identifier->patientIdentifierStatus;
            $patientData['patientPrimaryIdentifierStatusClassName'] = $primary_identifier->patientIdentifierStatus->icon->class_name ?? 'exclamation';
            $patientData['patientIdentifiers'] = $patientIdentifiers;
            $patientData['patientDeletedIdentifiers'] = $patientDeletedIdentifiers;
            $patientData['patientLocalIdentifiers'] = $patientLocalIdentifiers;

            $patientData['patientGlobalIdentifier'] = $patient->globalIdentifier;
            $patientData['patientGlobalIdentifierPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($patient->globalIdentifier);
            $patientData['patientGlobalIdentifierLabel'] = PatientIdentifierHelper::getIdentifierValue($patient->globalIdentifier);

            $patientData['displaySecondaryNumberUsageCode'] = $display_secondary_number_usage_code;
            $patientData['patientSecondaryIdentifierStatus'] = $display_secondary_number_usage_code === 'GLOBAL' && $secondary_identifier && $secondary_identifier->patientIdentifierStatus;
            $patientData['patientSecondaryIdentifierStatusClassName'] = $secondary_identifier->patientIdentifierStatus->icon->class_name ?? 'exclamation';
            $patientData['nhsNumberPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier);
            $patientData['nhsNumberValue'] = PatientIdentifierHelper::getIdentifierValue($secondary_identifier);
            $patientData['gender'] = $patient->getGenderString();
            $patientData['deceased'] = boolval($deceased);
            $patientData['dateOfDeath'] = Helper::convertDate2NHS($patient->date_of_death);

            $patientData['patientAge'] = $patient->getAge();

            // Get Allergies data.
            $allergiesWidget = $this->widget(\OEModule\OphCiExamination\widgets\Allergies::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
            ), false);

            $patientData['patientAllergies']['hasAllergyStatus'] = boolval(!$patient->hasAllergyStatus());
            $patientData['patientAllergies']['noAllergiesDate'] = !boolval(!$patient->hasAllergyStatus()) && $allergiesWidget->element->no_allergies_date;
            $patientData['patientAllergies']['data'] = !boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date;
            if (!boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date) {
                $patientData['patientAllergies']['entries'] = null;
                foreach ($allergiesWidget->element->entries as $i => $entry) {
                    if ($entry->getDisplayHasAllergy() === 'Present') {
                        $patientData['patientAllergies']['entries'][] = [
                            'displayAllergy' => $entry->getDisplayAllergy(),
                            'reactionString' => ' ' . $entry->getReactionString(),
                            'comments' => $entry->comments,
                            'lastModifiedUser' => User::model()->findByPk($entry->last_modified_user_id)->getFullName(),
                        ];
                    }
                }
            }

            // Get risks data
            $historyRisksWidget = $this->widget(\OEModule\OphCiExamination\widgets\HistoryRisks::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
            ), false);

            $riskAlertInfo = false;
            $noRisksDate = false;
            if (boolval(!$patient->hasRiskStatus()) && boolval(!$patient->getDiabetes())) {
                $riskAlertInfo = true;
            } elseif ($historyRisksWidget->element->no_risks_date) {
                $noRisksDate = true;
            } else {
                $patientData['patientRisks']['entries'] = null;
                foreach ($historyRisksWidget->element->entries as $i => $entry) {
                    if ($entry->getDisplayHasRisk() === 'Present') {
                        $patientData['patientRisks']['entries'][] = [
                            'displayRisk' => $entry->getDisplayRisk(),
                            'comments' => $entry->comments,
                        ];
                    }
                }
                foreach ($patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET) as $disorder) {
                    $patientData['patientRisks']['disorders'][] = [
                        'disorderTerm' => $disorder->term,
                    ];
                }
            }

            $patientData['patientRisks']['riskAlertInfo'] = $riskAlertInfo;
            $patientData['patientRisks']['noRisksDate'] = $noRisksDate;

            //Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans
            $vaData = $exam_api->getMostRecentVADataStandardised($patient);
            if ($vaData) {
                $patientData['vaData'] = [
                    'has_beo' => $vaData['has_beo'],
                    'beo_result' => $vaData['has_beo'] ? $vaData['beo_result'] : null,
                    'beo_method_abbr' => $vaData['has_beo'] ? $vaData['beo_method_abbr'] : null,
                    'has_right' => $vaData['has_right'],
                    'right_result' => $vaData['has_right'] ? $vaData['right_result'] : null,
                    'right_method_abbr' => $vaData['has_right'] ? $vaData['right_method_abbr'] : null,
                    'has_left' => $vaData['has_left'],
                    'left_result' => $vaData['has_left'] ? $vaData['left_result'] : null,
                    'left_method_abbr' => $vaData['has_left'] ? $vaData['left_method_abbr'] : null,
                    'event_date' => Helper::convertDate2NHS($vaData['event_date'])
                ];
            }
            $refractionData = $exam_api->getLatestRefractionReadingFromAnyElementType($patient);
            if ($refractionData) {
                $patientData['refractionData'] = [
                    'has_left' => (bool)$refractionData['left'],
                    'left' => $refractionData['left'],
                    'has_right' => (bool)$refractionData['right'],
                    'right' => $refractionData['right'],
                    'event_date' => Helper::convertDate2NHS($refractionData['event_date'])
                ];
            }
            $leftCCT = $exam_api->getCCTLeft($patient);
            $rightCCT = $exam_api->getCCTRight($patient);
            if ($leftCCT !== null || $rightCCT !== null) {
                $patientData['cct'] = [
                    'has_left' => (bool)$leftCCT ,
                    'left' => $leftCCT,
                    'has_right' => (bool)$rightCCT,
                    'right' => $rightCCT,
                    'event_date' => Helper::convertDate2NHS($exam_api->getCCTDate($patient))
                ];
            }
            $cviStatus = $patient->getCviSummary();
            if ($cviStatus[0] !== 'Unknown') {
                $patientData['cvi'] = [
                    'data' => $cviStatus[0],
                    'date' => ($cviStatus[1] && $cviStatus[1] !== '0000-00-00') ? Helper::convertDate2HTML($cviStatus[1]) : 'N/A',
                ];
            }
            foreach ($patient->getOphthalmicDiagnosesSummary() as $ophthalmic_diagnosis) {
                list($side, $name, $date) = explode('~', $ophthalmic_diagnosis);
                $temp = [];
                $temp['name'] = $name;
                $temp['date'] = $date;

                $laterality = $this->getLaterality(null, $side);
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];

                $patientData['ophthalmicDiagnosis'][] = $temp;
            }
            foreach ($patient->systemicDiagnoses as $diagnosis) {
                $temp = [];
                $temp['term'] = $diagnosis->disorder->term;
                $temp['date'] = $diagnosis->getFormatedDate();

                $eye = $diagnosis->eye;
                $laterality = $this->getLaterality($eye, null);
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];

                $patientData['systemicDiagnoses'][] = $temp;
            }
            // Get Past Surgery data.
            $pastSurgeryWidget = $this->createWidget(\OEModule\OphCiExamination\widgets\PastSurgery::class, array(
                'patient' => $patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                'popupListSeparator' => '<br/>',
            ));
            $pastSurgeryData = $pastSurgeryWidget->getViewData();
            $operations = is_array($pastSurgeryData) ? $pastSurgeryData['operations'] : false;
            $patientData['pastSurgery']['nilRecord'] = (!$operations || sizeof($operations) == 0) && !$pastSurgeryWidget->element->no_pastsurgery_date;
            $patientData['pastSurgery']['noPreviousData'] = !((!$operations || sizeof($operations) == 0) && !$pastSurgeryWidget->element->no_pastsurgery_date) && $pastSurgeryWidget->element->no_pastsurgery_date;
            $pastSurgeryDataExists = !((!$operations || sizeof($operations) == 0) && !$pastSurgeryWidget->element->no_pastsurgery_date) && !($pastSurgeryWidget->element->no_pastsurgery_date);
            if ($pastSurgeryDataExists) {
                foreach ($operations as $operation) {
                    $temp = [];
                    $temp['operation'] = isset($operation['object']) ? $operation['object']->operation : $operation['operation'];
                    $temp['date'] = isset($operation['object']) ? $operation['object']->getDisplayDate() : Helper::formatFuzzyDate($operation['date']);
                    $temp['has_link'] = isset($operation['link']);
                    $temp['link'] = $operation['link'] ?? false;
                    $side = $operation['side'] ?? (isset($operation['object']) ? $operation['object']->side : '');
                    $laterality = $this->getLaterality(null, $side);
                    $temp['left'] = $laterality['left'];
                    $temp['right'] = $laterality['right'];

                    $patientData['pastSurgery']['operation'][] = $temp;
                }
            }

            $historyMedicationsWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\HistoryMedications::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $historyMedicationsData = $historyMedicationsWidget->getViewData();
            $current_systemic_meds = null;
            $stopped_systemic_meds = null;
            $current_eye_meds = null;
            $stopped_eye_meds = null;
            $element = null;
            if (is_array($historyMedicationsData)) {
                $current_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !$e->isStopped();
                };
                $stopped_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !$e->isChangedMedication();
                };
                $systemic_filter = function ($med) {
                    return $med->laterality === null;
                };
                $eye_filter = function ($e) {
                    /** @var EventMedicationUse $e */
                    return !is_null($e->route_id) && $e->route->has_laterality;
                };

                $element = $historyMedicationsData['element'];
                $current = $historyMedicationsData['current'];
                $stopped = $historyMedicationsData['stopped'];
                $current = $element->mergeMedicationEntries($current);
                $current = array_filter($current, $current_filter);
                $current = $historyMedicationsWidget->sortEntriesByDate($current);
                $stopped = array_filter($stopped, $stopped_filter);
                $stopped = $historyMedicationsWidget->sortEntriesByDate($stopped, false);
                $current_systemic_meds = array_filter($current, $systemic_filter);
                $stopped_systemic_meds = array_filter($stopped, $systemic_filter);
                $current_eye_meds = array_filter($current, $eye_filter);
                $stopped_eye_meds = array_filter($stopped, $eye_filter);
            }
            $nilRecord = false;
            $noPreviousData = false;
            if (empty($current_systemic_meds) && empty($stopped_systemic_meds) && is_null($element->no_systemic_medications_date)) {
                $nilRecord = true;
            } elseif (empty($current_systemic_meds) && empty($stopped_systemic_meds) && !is_null($element->no_systemic_medications_date)) {
                $noPreviousData = true;
            } else {
                if ($current_systemic_meds) {
                    $patientData['systemicMedications']['currentSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_systemic_meds, true, false, true, false);
                }
                if ($stopped_systemic_meds) {
                    $patientData['systemicMedications']['stoppedSystemicMedsSize'] = sizeof($stopped_systemic_meds);
                    $patientData['systemicMedications']['stoppedSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_systemic_meds, false, false, false, false);
                }
                $patientData['historyMedications']['id'] = CHtml::modelName($element);
            }

            $patientData['systemicMedications']['nilRecord'] = $nilRecord;
            $patientData['systemicMedications']['noPreviousData'] = $noPreviousData;

            $nilRecord = false;
            $noPreviousData = false;
            if (empty($current_eye_meds) && empty($stopped_eye_meds) && is_null($element->no_ophthalmic_medications_date)) {
                $nilRecord = true;
            } elseif (empty($current_eye_meds) && empty($stopped_eye_meds) && !is_null($element->no_ophthalmic_medications_date)) {
                $noPreviousData = true;
            } else {
                if ($current_eye_meds) {
                    $patientData['eyeMedications']['currentEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_eye_meds, true, true, true, true);
                }
                if ($stopped_eye_meds) {
                    $patientData['eyeMedications']['stoppedEyeMedsSize'] = sizeof($stopped_eye_meds);
                    $patientData['eyeMedications']['stoppedEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_eye_meds, false, true, false, true);
                }
            }
            $patientData['eyeMedications']['nilRecord'] = $nilRecord;
            $patientData['eyeMedications']['noPreviousData'] = $noPreviousData;

            $familyHistoryWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\FamilyHistory::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $familyHistoryData = $familyHistoryWidget->getViewData();
            $element = $familyHistoryData['element'];
            if (empty($element->entries) && empty($element->no_family_history_date)) {
                $patientData['familyHistory']['nilRecord'] = true;
            } else {
                $patientData['familyHistory']['noFamilyHistory'] = empty($element->no_family_history_date) && !empty($element->entries);
                if (!empty($element->entries)) {
                    $patientData['familyHistory']['modelName'] = CHtml::modelName($element);
                    foreach ($element->entries as $i => $entry) {
                        $temp = [];
                        $temp['relativeDisplay'] = $entry->displayrelative;
                        $temp['sideDisplay'] = $entry->side->name;
                        $temp['conditionDisplay'] = $entry->displaycondition;
                        $temp['comments'] = $entry->comments;
                        $patientData['familyHistory']['entries'][] = $temp;
                    }
                }
            }

            $socialHistoryWidget = $this->createWidget(
                \OEModule\OphCiExamination\widgets\SocialHistory::class,
                [
                    'patient' => $patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
                ]
            );
            $socialHistoryData = $socialHistoryWidget->getViewData();
            $element = $socialHistoryData['element'];
            if (!$element || !$element->id) {
                $patientData['socialHistory']['nilRecord'] = true;
            } else {
                if ($element->occupation) {
                    $patientData['socialHistory']['occupation']['label'] = CHtml::encode($element->getAttributeLabel('occupation_id'));
                    $patientData['socialHistory']['occupation']['value'] = \CHtml::encode($element->getDisplayOccupation());
                }
                if ($element->driving_statuses) {
                    $patientData['socialHistory']['drivingStatuses']['label'] = CHtml::encode($element->getAttributeLabel('driving_statuses'));
                    $temp = '';
                    foreach ($element->driving_statuses as $item) {
                        $temp .= $item->name . '<br/>';
                    }
                    $patientData['socialHistory']['drivingStatuses']['value'] = $temp;
                }
                if ($element->smoking_status) {
                    $patientData['socialHistory']['smokingStatus']['label'] = CHtml::encode($element->getAttributeLabel('smoking_status_id'));
                    $patientData['socialHistory']['smokingStatus']['value'] = \CHtml::encode($element->smoking_status->name);
                }
                if ($element->accommodation) {
                    $patientData['socialHistory']['accommodation']['label'] = CHtml::encode($element->getAttributeLabel('accommodation_id'));
                    $patientData['socialHistory']['accommodation']['value'] = \CHtml::encode($element->accommodation->name);
                }
                if ($element->comments) {
                    $patientData['socialHistory']['comments']['label'] = CHtml::encode($element->getAttributeLabel('comments'));
                    $patientData['socialHistory']['comments']['value'] = \CHtml::encode($element->comments);
                }
                if (isset($element->carer)) {
                    $patientData['socialHistory']['carer']['label'] = CHtml::encode($element->getAttributeLabel('carer_id'));
                    $patientData['socialHistory']['carer']['value'] = \CHtml::encode($element->carer);
                }
                if (isset($element->alcohol_intake)) {
                    $patientData['socialHistory']['alcoholIntake']['label'] = CHtml::encode($element->getAttributeLabel('alcohol_intake'));
                    $patientData['socialHistory']['alcoholIntake']['value'] = \CHtml::encode($element->alcohol_intake) . ' units/week';
                }
                if (isset($element->substance_misuse)) {
                    $patientData['socialHistory']['substanceMisuse']['label'] = CHtml::encode($element->getAttributeLabel('substance_misuse'));
                    $patientData['socialHistory']['substanceMisuse']['value'] = \CHtml::encode($element->substance_misuse->name);
                }
            }

            $summaries = $exam_api->getManagementSummaries($patient);
            foreach ($summaries as $summary) {
                $temp = [];
                $temp['service'] = $summary->service;
                $temp['comments'] = $summary->comments;
                $temp['day'] = $summary->date[0];
                $temp['month'] = $summary->date[1];
                $temp['year'] = $summary->date[2];
                $temp['user'] = $summary->user;
                $patientData['managementSummaries'][] = $temp;
            }

            $appointment = $this->createWidget('Appointment', ['patient' => $patient, 'pro_theme' => 'pro-theme', 'is_popup' => true]);

            foreach ($appointment->worklist_patients as $worklistPatient) {
                $temp = [];
                $temp['time'] = date('H:i', strtotime($worklistPatient->when));
                $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
                $temp['name'] = $worklistPatient->worklist->name;
                $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
                $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklistPatient->id]);

                if (isset($worklistStatus)) {
                    $temp['status'] = $worklistStatus->attribute_value;
                } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                    $temp['status'] = 'Did not attend.';
                }
                $patientData['worklistPatients'][] = $temp;
            }
            if ($appointment->past_worklist_patients_count != 0) {
                $patientData['pastWorklistPatientsCount'] = $appointment->past_worklist_patients_count;
                $criteria = new \CDbCriteria();
                $criteria->join = " JOIN worklist w ON w.id = t.worklist_id";
                $start_of_today = date("Y-m-d");
                $criteria->addCondition('t.when < "' . $start_of_today . '"');
                $criteria->order = 't.when desc';

                $past_worklist_patients = WorklistPatient::model()->findAllByAttributes(
                    ['patient_id' => $patientId],
                    $criteria
                );
                foreach ($past_worklist_patients as $worklistPatient) {
                    $temp = [];
                    $temp['time'] = date('H:i', strtotime($worklistPatient->when));
                    $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
                    $temp['name'] = $worklistPatient->worklist->name;
                    $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
                    $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklistPatient->id]);

                    if (isset($worklistStatus)) {
                        $temp['status'] = $worklistStatus->attribute_value;
                    } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                        $temp['status'] = 'Did not attend.';
                    }
                    $patientData['pastWorklistPatients'][] = $temp;
                }
            }

            $plansProblemsWidget = $this->createWidget('application.widgets.PlansProblemsWidget', [
                'patient_id' => $patient->id
            ]);
            foreach ($plansProblemsWidget->current_plans_problems as $planProblem) {
                $temp = [];
                $temp['name'] = $planProblem->name;
                $temp['tooltipContent'] = 'Created: ' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '');
                $temp['id'] = $planProblem->id;
                $temp['currentPlanProblems'][] = $temp;
            }
            if ($plansProblemsWidget->past_plans_problems != 0) {
                foreach ($plansProblemsWidget->past_plans_problems as $planProblem) {
                    $temp = [];
                    $temp['name'] = $planProblem->name;
                    $temp['tooltipContent'] = 'Created:' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '') .
                    '<br /> Closed:' . Helper::convertDate2NHS($planProblem->last_modified_date) . ($planProblem->lastModifiedUser ? ' by ' . $planProblem->lastModifiedUser->getFullNameAndTitle() : '');
                    $temp['id'] = $planProblem->id;
                    $temp['lastModifiedDate'] = \Helper::convertDate2NHS($planProblem->last_modified_date);
                    $temp['pastPlanProblems'][] = $temp;
                }
            }

            if (Yii::app()->getModule('OETrial')) {
                foreach ($patient->trials as $trialPatient) {
                    $temp = [];
                    if (Yii::app()->user->checkAccess('TaskViewTrial')) {
                        $temp['trial'] = CHtml::link(
                            CHtml::encode($trialPatient->trial->name),
                            Yii::app()->controller->createUrl(
                                '/OETrial/trial/permissions',
                                array('id' => $trialPatient->trial_id)
                            )
                        );
                    } else {
                        $temp['trial'] = CHtml::encode($trialPatient->trial->name);
                    }
                    $temp['date'] = $trialPatient->trial->getStartedDateForDisplay() . ' - ' . $trialPatient->trial->getClosedDateForDisplay();
                    $coordinators = $trialPatient->trial->getTrialStudyCoordinators();
                    if (sizeof($coordinators)) {
                        $studyCoordinators = '';
                        foreach ($coordinators as $item) {
                            $studyCoordinators .= $item->user->getFullName() . "<br />";
                        }
                        $temp['studyCoordinator'] = $studyCoordinators;
                    } else {
                        $temp['studyCoordinator'] = 'N/A';
                    }

                    $temp['treatment'] = $trialPatient->treatmentType->name;
                    $temp['type'] = $trialPatient->trial->trialType->name;
                    $temp['status'] = $trialPatient->status->name;

                    $patientData['currentTrails'][] = $temp;
                }
            }

            $this->renderJSON($patientData);
        }
    }

    protected function patientHistoryMedicationsData($historyMedicationsWidget, $history_meds, $current, $getComments = false, $showLink = false, $getLaterality = false): array
    {
        $result = [];

        $index = 0;
        foreach ($history_meds as $history_med) {
            $temp = [];
            $temp['index'] = $index;
            $temp['display'] = $history_med->getMedicationDisplay(true);
            if ($getComments) {
                $comments = $history_med->getComments();
                if (!empty($comments)) {
                    $temp['comments'] = $comments;
                }
            }
            if (!empty($history_med->getChangeHistory())) {
                $temp['historyTooltipContent'] = $history_med->getChangeHistoryTooltipContent($history_med->getChangeHistory());
            }
            $info_box = new MedicationInfoBox();
            $info_box->medication_id = $history_med->medication->id;
            $info_box->init();
            $tooltip_content = $history_med->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
            if (!empty($tooltip_content)) {
                $temp['icon'] = $info_box->getIcon();
                $temp['tooltipContent'] = $tooltip_content;
            }
            if ($getLaterality) {
                $laterality = $this->getLaterality(null, $history_med->getLateralityDisplay(), '');
                $temp['left'] = $laterality['left'];
                $temp['right'] = $laterality['right'];
            }
            $temp['date'] = $current ? $history_med->getStartDateDisplay() : $history_med->getEndDateDisplay();

            if ($showLink) {
                if (($history_med->prescription_item_id && isset($history_med->prescription_item->prescription->event))) {
                    $link = $historyMedicationsWidget->getPrescriptionLink($history_med->prescription_item);
                } else {
                    $link = $history_med->isPrescription() ? $historyMedicationsWidget->getPrescriptionLink($history_med) : $historyMedicationsWidget->getExaminationLink($history_med);
                }
                $tooltip_content = 'View' . (strpos(strtolower($link), 'prescription') ? ' prescription' : ' examination');
                $temp['link'] = $link;
                $temp['linkTooltipContent'] = $tooltip_content;
            }

            $result[] = $temp;
        }

        return $result;
    }

    private function getLaterality($eye, $laterality, $pad = 'pad', $size = 'small')
    {
        $show_if_both_eyes_are_null = true;
        $left = false;
        $right = false;
        $return = [];

        if ($eye !== null) {
            $left = $eye->id & Eye::LEFT;
            $right = $eye->id & Eye::RIGHT;
        } else {
            switch (strtolower($laterality)) {
                case 'left':
                case 'l':
                    $left = true;
                    $right = false;
                    break;
                case 'right':
                case 'r':
                    $left = false;
                    $right = true;
                    break;
                case 'b':
                case 'bilateral':
                case 'both':
                    $left = true;
                    $right = true;
                    break;
            }
        }

        if ($show_if_both_eyes_are_null || $right || $left) {
            $return['left'] = $size . ' ' . $pad . ' ' . ($left ? 'L' : 'NA');
            $return['right'] = $size . ' ' . $pad . ' ' . ($right ? 'R' : 'NA');
        }
        return $return;
    }

    public function getStatusCountsList($filter = null, $worklists = null)
    {
        if ($filter) {
            $counts = $filter->getPatientStatusCountsQuery($worklists)->queryAll();
        } else {
            $counts = Yii::app()->db->createCommand('SELECT `status`, COUNT(`id`) AS `count` FROM `pathway` GROUP BY `status`')->queryAll();
        }

        $results = array(
            'all' => 0,
            'clinic' => 0,     // Arrived
            'issues' => 0,     // Issues
            'discharged' => 0, // Departed
            'done' => 0        // Completed
        );

        foreach ($counts as $item) {
            $progress = Pathway::inProgressStatuses();
            $results['all'] += $item['count'];

            if ($item['status'] != Pathway::STATUS_LATER) {
                $results['clinic'] += $item['count'];
            }

            if (
                $item['status'] != Pathway::STATUS_ACTIVE
                && in_array($item['status'], $progress)
            ) {
                $results['issues'] += $item['count'];
            } elseif ($item['status'] == Pathway::STATUS_DISCHARGED) {
                $results['discharged'] += $item['count'];
            } elseif ($item['status'] == Pathway::STATUS_DONE) {
                $results['done'] += $item['count'];
            }
        }

        return $results;
    }

    public function getWaitingForList($filter = null, $worklists = null)
    {
        if ($filter === null) {
            $filter = new WorklistFilterQuery();
        }

        return array_map(
            static function ($item) {
                return array(
                    'id' => $item['long_name'],
                    'label' => '… ' . $item['long_name'],
                    'count' => $item['count']
                );
            },
            $filter->getWaitingForListQuery($worklists)->queryAll()
        );
    }

    public function getAssignedToList($filter = null, $worklists = null)
    {
        $helper = new User();

        if ($filter === null) {
            $filter = new WorklistFilterQuery();
        }

        return array_map(
            static function ($item) use ($helper) {
                $helper->first_name = $item['first_name'];
                $helper->last_name = $item['last_name'];

                return array(
                    'id' => $item['id'],
                    'label' => $helper->getFullName() . ' (' . $helper->getInitials() . ')',
                    'count' => $item['count']
                );
            },
            $filter->getAssignedToListQuery($worklists)->queryAll()
        );
    }

    public function actionAutoRefresh()
    {
        $date_from = Yii::app()->request->getParam('date_from');
        $date_to = Yii::app()->request->getParam('date_to');

        $filter = WorklistFilterQuery::getLastUsedFilterFromSession();
        $filter = $filter['filter'];

        $date_from = $filter->getFrom() ?? $date_from;
        $date_to = $filter->getTo() ?? $date_to;

        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);

        $prescriber_dom_data = $this->prescriberDomData();
        $dom = array();
        $dom['main'] = null;
        $dom['filter'] = "<li><a class='js-worklist-filter' href='#' data-worklist='all'>All</a></li>";
        $dom['popup'] = $prescriber_dom_data['popup'];

        if ($filter->getCombineWorklistsStatus()) {
            $dom['main'] = $this->renderPartial('_worklist', array('worklist' => $worklists, 'is_prescriber' => $prescriber_dom_data['is_prescriber'], 'filter' => $filter), true);
        } else {
            foreach ($worklists as $worklist) {
                $dom['main'] .= $this->renderPartial('_worklist', array('worklist' => $worklist, 'is_prescriber' => $prescriber_dom_data['is_prescriber'], 'filter' => $filter), true);
                $dom['filter'] .= "<li><a href='#' class='js-worklist-filter' data-worklist='js-worklist-{$worklist->id}'>{$worklist->name} : {$worklist->getDisplayShortDate()}</a></li>";
            }
        }
        $dom['refresh_time'] = date('H:i');

        $dom['quick_details'] = $this->getStatusCountsList($filter, $worklists);
        $dom['waiting_for_details'] = $this->getWaitingForList($filter, $worklists);
        $dom['assigned_to_details'] = $this->getAssignedToList($filter, $worklists);

        $this->renderJSON($dom);
    }

    /**
     * @throws Exception
     */
    public function actionAddStepToPathway()
    {
        $id = Yii::app()->request->getPost('id');
        $pathway_id = Yii::app()->request->getPost('pathway_id');
        $position = Yii::app()->request->getPost('position');
        $step_data = Yii::app()->request->getPost('step_data') ?: array();

        $step = PathwayStepType::model()->findByPk($id);
        // priority for firm_id: user input > template > current firm id
        $step_data['firm_id'] = $step_data['firm_id'] ?? $step->getState('firm_id') ?? Yii::app()->session['selected_firm_id'];
        // if the template has subspecialty_id, then setup for the step
        if ($step->getState('subspecialty_id')) {
            $step_data['subspecialty_id'] = $step->getState('subspecialty_id');
        }

        $new_step = null;
        if ($step) {
            $new_step = $step->createNewStepForPathway($pathway_id, $step_data, true, (int)$position);
        }

        if ($new_step) {
            $this->renderJSON(
                [
                    'step_html' => $this->renderPartial(
                        '_clinical_pathway',
                        ['pathway' => $new_step->pathway],
                        true
                    ),
                    'no_wait_timer' => in_array($new_step->type->short_name, PathwayStep::NO_WAIT_TIMER_AFTER_ADD)
                ]
            );
        }
        throw new CHttpException(500, 'Unable to add step to pathway.');
    }

    /**
     * @throws Exception
     */
    public function actionAddPathwayStepsToPathway()
    {
        $id = $_POST['selected_values'][0]['value'];
        $position = $_POST['selected_values'][1]['value'];
        $pathway_type = PathwayType::model()->findByPk($id);
        $pathway_id = Yii::app()->request->getPost('target_pathway_id');
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway_type) {
            $pathway_type->duplicateStepsForPathway($pathway_id, $position);
            $this->renderJSON(['step_html' => $this->renderPartial('_clinical_pathway', ['pathway' => $pathway], true)]);
        }
        throw new CHttpException(404, 'Unable to retrieve pathway type for duplication.');
    }

    /**
     * @param $term
     */
    public function actionGetAssignees($term)
    {
        $users = User::model()->with('contact')->findAll(
            'contact.first_name LIKE CONCAT(\'%\', :term, \'%\')',
            array(':term' => $term)
        );
        $this->renderJSON(array_map(
            static function ($item) {
                return array(
                    'id' => $item->id,
                    'label' => $item->getFullName(),
                );
            },
            $users
        ));
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionAssignUserToPathway()
    {
        $id = Yii::app()->request->getPost('user_id');
        $pathway_id = Yii::app()->request->getPost('target_pathway_id');
        $pathway = Pathway::model()->findByPk($pathway_id);

        if ($pathway) {
            $pathway->owner_id = $id;
            $pathway->save();
            $pathway->refresh();
            $this->renderJSON(array('id' => $id, 'initials' => $pathway->owner->getInitials()));
        }
        throw new CHttpException(404, 'Unable to retrieve pathway');
    }

    public function actionAddComment()
    {
        $post = $_POST;
        if ($post['pathstep_id'] === 'comment') {
            $comment = PathwayComment::model()->find('pathway_id=?', [$post['pathway_id']]);
            if ($comment === null) {
                $comment = new PathwayComment();
                $comment->pathway_id = $post['pathway_id'];
            }
        } else {
            $comment = PathwayStepComment::model()->find('pathway_step_id=?', [$post['pathstep_id']]);
            if ($comment === null) {
                $comment = new PathwayStepComment();
                $comment->pathway_step_id = $post['pathstep_id'];
            }
        }
        $comment->comment = $post['comment'];
        $comment->doctor_id = $post['user_id'];
        if ($comment->save()) {
            $this->renderJSON($comment->comment);
        }
    }

    public function actionRetrieveFilters()
    {
        $filters = array();

        WorklistRecentFilter::model()->removeOldFiltersForCurrentUser();

        $filters['max_recents'] = WorklistRecentFilter::MAX_RECENT_FILTERS;

        $filters['recent'] = array_map(static function ($f) {
            return ['id' => $f->id, 'filter' => $f->filter];
        }, WorklistRecentFilter::model()->getForCurrentUser());

        $filters['saved'] = array_map(static function ($f) {
            return ['id' => $f->id, 'name' => $f->name, 'filter' => $f->filter];
        }, WorklistFilter::model()->getForCurrentUser());

        $this->renderJSON($filters);
    }

    public function actionStoreFilter()
    {
        $filter = null;
        $response = array();
        $is_recent = Yii::app()->request->getParam('is_recent') === 'true';

        if ($is_recent) {
            $filter = new WorklistRecentFilter();
        } else {
            $filter_name = Yii::app()->request->getParam('name');
            $filter = WorklistFilter::model()->findByNameForCurrentUser($filter_name);

            if ($filter === null) {
                $filter = new WorklistFilter();
                $filter->name = $filter_name;
            }
        }

        $filter->filter = Yii::app()->request->getParam('filter');

        $filter->save();

        $response['id'] = $filter->id;
        $response['user'] = $filter->created_user_id;

        WorklistFilterQuery::setLatestUsedFilterForSession($is_recent ? 'Recent' : 'Saved', $filter->id);

        $this->renderJSON($response);
    }

    public function actionDeleteFilter()
    {
        $filter_name = Yii::app()->request->getParam('name');
        $filter = WorklistFilter::model()->findByNameForCurrentUser($filter_name);

        if ($filter !== null) {
            $filter->delete();
        }
    }

    /**
     * Get the pathwaystep according to the provided pathstep_id
     * and get the corresponding pathway
     *
     * @return array instances of Pathway, PathwayStep
     * @throws CHttpException
     */
    private function getStepAndPathway($pathstep_id)
    {
        $step = PathwayStep::model()->findByPk($pathstep_id);
        $pathway = $step->pathway;
        if (!$step) {
            throw new CHttpException(404, 'Step not found.');
        }
        if (!$pathway) {
            throw new CHttpException(404, 'Pathway not found.');
        }
        return array(
            'pathway' => $pathway,
            'step' => $step,
        );
    }

    /**
     * Checkout the pathway
     */
    public function actionCheckout()
    {
        $pathstep_id = Yii::app()->request->getPost('step_id');
        extract($this->getStepAndPathway($pathstep_id));
        // push the step to next status
        $step->nextStatus();
        // set the pathway as done
        $pathway->status = Pathway::STATUS_DONE;
        $pathway->end_time = date('Y-m-d H:i:s');
        $pathway->save();
        $this->renderJSON(
            [
                'status' => $pathway->getStatusString(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['pathway' => $pathway], true),
                'status_html' => $pathway->getPathwayStatusHTML(),
                'pathway_id' => $pathway->id,
            ]
        );
    }
    /**
     * Revert the done pathwaystep to discharged
     */
    public function actionRevertCheckout()
    {
        $pathstep_id = Yii::app()->request->getPost('step_id');
        extract($this->getStepAndPathway($pathstep_id));
        // revert step
        $step->status = PathwayStep::STEP_REQUESTED;
        $step->start_time = null;
        $step->end_time = null;
        $step->started_user_id = null;
        $step->completed_user_id = null;
        if (!$step->save()) {
            throw new CHttpException(500, 'Unable to update the step status');
        }
        // revert the pathway back to discharged
        $pathway->status = Pathway::STATUS_DISCHARGED;
        $pathway->end_time = null;
        if (!$pathway->save()) {
            throw new CHttpException(500, 'Unable to update the pathway status');
        }
        $this->renderJSON(
            [
                'status' => $pathway->getStatusString(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['pathway' => $pathway], true),
                'status_html' => $pathway->getPathwayStatusHTML(),
                'pathway_id' => $pathway->id,
                'waiting_time_html' => $pathway->getTotalDurationHTML(true),
            ]
        );
    }

    /*
     * Store the id of a newly chosen recent/saved filter,
     * or the JSON representation of the quick filter in the session
     */
    public function actionSetChosenFilter()
    {
        $type = Yii::app()->request->getPost('filter_type');
        $value = Yii::app()->request->getPost('filter_value');

        WorklistFilterQuery::setLatestUsedFilterForSession($type, $value);

        $this->renderJSON('ok');
    }
}
