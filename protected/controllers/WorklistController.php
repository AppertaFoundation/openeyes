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

use Nesk\Puphpeteer\Resources\HTTPResponse;
use OEModule\OESysEvent\events\PathwayCheckoutSystemEvent;

use OEModule\OphDrPGDPSD\models\{
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_Assignment
};

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

        //$worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);
        // This has been split into two here to that all the unique worklists can be passed back to the client to refresh the set of worklists.
        // Passing back the (filtered set of) worklists instead would clobber the set to be only those so filtered,
        // and the user would not be able to select those filtered out when trying to change what lists they're filtering.
        $unfiltered_worklists = $this->manager->getAllCurrentUniqueAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);
        $worklists = $this->manager->filterWorklistsBySelected($unfiltered_worklists, $filter);

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
                    'unfiltered_worklists' => $unfiltered_worklists,
                    'picker_setup' => $picker_setup,
                    'path_step_type_ids' => $path_step_type_ids,
                    'path_steps' => PathwayStepType::getPathTypes(),
                    'pathways' => PathwayType::model()->findAll('active = 1'),
                    'standard_steps' => PathwayStepType::getStandardTypes(),
                    'custom_steps' => PathwayStepType::getCustomTypes(false, true),
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
        $pathway_id = Yii::app()->request->getPost('visit_id');
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
                    case 'undocheckin':
                    default:
                        $pathway->status = Pathway::STATUS_LATER;
                        break;
                }
                $pathway->save();
                $pathway->refresh();
                $this->renderJSON(
                    [
                        'status' => $pathway->getStatusString(),
                        'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $pathway->worklist_patient], true),
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
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $visit_id = Yii::app()->request->getPost('visit_id');
        $direction = Yii::app()->request->getPost('direction');
        $pathway_id = Yii::app()->request->getPost('pathway_id');
        $step = PathwayStep::model()->find('id = :id AND pathway_id = :pathway_id', [':id' => $step_id, ':pathway_id' => $pathway_id]);

        if (!$step) {
            $visit = WorklistPatient::model()->findByPk($visit_id);
            $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

            if ($type_step) {
                $transaction = Yii::app()->db->beginTransaction();
                $pathway_steps = $type_step->pathway_type->instancePathway($visit);
                $step = $pathway_steps[$type_step_id] ?? null;
                if (!$step) {
                    $transaction->rollback();
                    throw new CHttpException(404, 'Unable to retrieve step for processing.');
                }
            } else {
                throw new CHttpException(404, 'Unable to retrieve step for processing.');
            }
        } else {
            $transaction = Yii::app()->db->beginTransaction();
        }

        if ($step) {
            try {
                if ($direction === 'next') {
                    $extra_form_data = Yii::app()->request->getPost('extra_form_data');
                    if ($extra_form_data && array_key_exists('YII_CSRF_TOKEN', $extra_form_data)) {
                        unset($extra_form_data['YII_CSRF_TOKEN']);
                    }
                    $step->nextStatus($extra_form_data);
                } else {
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

                $transaction->commit();

                $this->renderJSON(
                    [
                        'step' => $step->toJSON(),
                        'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $step->pathway->worklist_patient], true),
                        'pathway_status' => $pathway->getStatusString(),
                        'pathway_status_html' => $pathway->getPathwayStatusHTML(),
                        'wait_time_details' => $pathway->getWaitTimeSinceLastAction(),
                    ]
                );
                Yii::app()->end();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionCheckIn()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $visit_id = Yii::app()->request->getPost('visit_id');

        $step = PathwayStep::model()->find('id = :id', [':id' => $step_id]);

        if (!$step) {
            $visit = WorklistPatient::model()->findByPk($visit_id);
            $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

            if ($type_step) {
                $pathway_steps = $type_step->pathway_type->instancePathway($visit);
                $step = $pathway_steps[$type_step_id] ?? null;
            }

            if (!$step) {
                throw new CHttpException(404, 'Unable to retrieve step for processing or step is not a checkin step.');
            }
        }

        $pathway = $step->pathway;
        $pathway->checkIn($step);

        if (!$pathway->start_time) {
            $pathway->start_time = date('Y-m-d H:i:s');
        }

        $pathway->updateStatus();

        $this->renderJSON(
            [
                'id' => $step->id,
                'pathway_id' => $pathway->id,
                'step_html' => $this->renderPartial(
                    '_clinical_pathway',
                    ['visit' => $pathway->worklist_patient],
                    true
                ),
                'end_time' => DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time)->format('H:i'),
                'pathway_status_html' => $pathway->getPathwayStatusHTML(),
                'waiting_time_html' => $pathway->getTotalDurationHTML(true),
                'status' => $pathway->getStatusString(),
            ]
        );
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionUndoCheckIn()
    {
        $step_id = Yii::app()->request->getPost('step_id');

        $step = PathwayStep::model()->findByPk($step_id);

        if (!$step) {
            throw new CHttpException(404, 'Unable to retrieve step for processing or step is not a undo checkin step.');
        }
        $pathway = $step->pathway;

        $step->undoStep();

        $pathway->refresh();
        $pathway->updateStatus();

        $this->renderJSON(
            [
                'pathway_id' => $pathway->id,
                'step_html' => $this->renderPartial(
                    '_clinical_pathway',
                    ['visit' => $pathway->worklist_patient],
                    true
                ),
                'status_html' => $pathway->getPathwayStatusHTML(),
            ]
        );
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionDidNotAttend()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $visit_id = Yii::app()->request->getPost('visit_id');

        $step = PathwayStep::model()->find('id = :id', [':id' => $step_id]);

        if (!$step) {
            $visit = WorklistPatient::model()->findByPk($visit_id);
            $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

            if ($type_step) {
                $pathway_steps = $type_step->pathway_type->instancePathway($visit);
                $step = $pathway_steps[$type_step_id] ?? null;
            }
        }

        $step->status = PathwayStep::STEP_COMPLETED;
        $step->start_time = date('Y-m-d H:i:s');
        $step->started_user_id = Yii::app()->user->id;
        $step->end_time = date('Y-m-d H:i:s');
        $step->completed_user_id = Yii::app()->user->id;

        $pathway = $step->pathway;

        $pathway->enqueue($step);
        $step->refresh();

        $pathway->refresh();
        $pathway->updateStatus();

        if (count($pathway->requested_steps) === 0) {
            $pathway->status = Pathway::STATUS_DONE;
            $pathway->end_time = date('Y-m-d H:i:s');
        } else {
            $pathway->status = Pathway::STATUS_DISCHARGED;
        }

        $pathway->did_not_attend = true;
        $pathway->save();

        // Create and save a Did Not Attend event.
        $context_firm = Yii::app()->session->getSelectedFirm();

        if (!$context_firm) {
            throw new CHttpException(404, 'Unable to locate selected firm.');
        }
        $event_type_id = EventType::model()->find(
            'class_name = :class_name',
            [':class_name' => 'OphCiDidNotAttend']
        )->id;
        $service = $context_firm->getDefaultServiceFirm();

        $params = [
            'patient_id' => $pathway->worklist_patient->patient_id,
            'context_id' => $context_firm->id,
            'service_id' => $service ? $service->id : null,
            'event_type_id' => $event_type_id
        ];

        $this->renderJSON(
            [
                'redirect_url' => '/patientEvent/create?' . http_build_query($params),
                'pathway_status_html' => $step->pathway->getPathwayStatusHTML(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $pathway->worklist_patient], true),
                'status' => $pathway->getStatusString(),
            ]
        );
        throw new CHttpException(404, 'Unable to retrieve step for processing or step is not a checkin step.');
    }

    /**
     * @throws CDbException
     * @throws JsonException
     * @throws CException
     */
    public function actionDeleteStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $pathway_id = Yii::app()->request->getPost('pathway_id');
        $visit_id = Yii::app()->request->getPost('visit_id');
        $step = PathwayStep::model()->find('id = :id AND pathway_id = :pathway_id', [':id' => $step_id, ':pathway_id' => $pathway_id]);
        if (!$step) {
            $wl_patient = WorklistPatient::model()->findByPk($visit_id);
            $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

            if ($type_step) {
                $pathway_steps = $type_step->pathway_type->instancePathway($wl_patient);
                $step = $pathway_steps[$type_step_id] ?? null;
            }
        }
        if ($step) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                Yii::app()->event->dispatch('step_deleted', ['step' => $step]);

                if ($step->comment && !$step->comment->delete()) {
                    OELog::log(print_r($step->comment->getErrors(), true));
                    throw new RuntimeException('Could not delete step comment');
                }

                if (!$step->delete()) {
                    OELog::log(print_r($step->getErrors(), true));
                    throw new RuntimeException('Could not delete step');
                };
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }

            $this->renderJSON(
                array('step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $step->pathway->worklist_patient], true))
            );
        }
    }

    /**
     * @throws CHttpException
     * @throws JsonException
     * @throws Exception
     */
    public function actionReorderStep()
    {
        $step_id = Yii::app()->request->getPost('step_id');
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $direction = Yii::app()->request->getPost('direction');
        $step = PathwayStep::model()->find('id = :id', [':id' => $step_id]);
        $visit_id = Yii::app()->request->getPost('visit_id');

        if (!$step) {
            $wl_patient = WorklistPatient::model()->findByPk($visit_id);
            $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

            if ($type_step) {
                $pathway_steps = $type_step->pathway_type->instancePathway($wl_patient);
                $step = $pathway_steps[$type_step_id] ?? null;
            }
        }

        if ($step) {
            $old_order = $step->todo_order;
            $new_order = $direction === 'left' ? $old_order - 1 : $old_order + 1;

            // As we're only moving one step, we should only have to reorder at most a single step.
            $step_to_reorder = PathwayStep::model()->find(
                "pathway_id = :pathway_id AND (status IN (-1, 0) OR status IS NULL) AND id != :id AND todo_order = :order",
                [
                    'pathway_id' => $step->pathway_id,
                    ':id' => $step->id,
                    ':order' => $new_order
                ]
            );

            // It should only be possible to request a reorder on requested steps or an active hold timer (which will revert its status to 'to-do').
            if ((int)$step->status === PathwayStep::STEP_STARTED) {
                $step->status = PathwayStep::STEP_REQUESTED;
            }

            if ($step_to_reorder) {
                $step_to_reorder->todo_order = $old_order;
                $step_to_reorder->save();
                $step_to_reorder->refresh();
            }
            $step->todo_order = $new_order;
            if (!$step->save()) {
                throw new CHttpException('Unable to reorder step.');
            }
            $step->refresh();
        }

        $this->renderJSON(
            array(
                'step' => $step->toJSON(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $step->pathway->worklist_patient], true)
            )
        );
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
     * @param $visit_id
     * @param $pathstep_type_id
     * @param bool $red_flag
     * @param int $interactive
     * @throws CException
     * @throws CHttpException
     * @throws JsonException
     */
    public function actionGetPathStep($partial, $pathstep_id, $visit_id, $pathstep_type_id, $red_flag = false, $interactive = 1)
    {
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);
        $has_permission_to_start = true;
        switch ($pathstep_id) {
            case 'checkin':
                if ($wl_patient) {
                    $dom = $this->renderPartial('/worklist/steps/checkin', array(
                        'worklist_patient' => $wl_patient,
                        'patient' => $wl_patient->patient,
                        'partial' => $partial
                    ), true);
                    $this->renderJSON($dom);
                } else {
                    throw new CHttpException('Unable to retrieve pathway for patient.');
                }
                break;
            case 'comment':
                if ($wl_patient) {
                    $dom = $this->renderPartial('//worklist/comment', array(
                        'visit' => $wl_patient,
                        'patient' => $wl_patient->patient,
                        'partial' => $partial
                    ), true);
                    $this->renderJSON($dom);
                } else {
                    throw new CHttpException('Unable to retrieve pathway for patient.');
                }
                break;
            case 'wait':
                $view_file = 'callout';
                $dom = $this->renderPartial(
                    '//worklist/steps/' . $view_file,
                    array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => $wl_patient->patient,
                        'partial' => $partial
                    ),
                    true
                );
                $this->renderJSON($dom);
                break;
            case 'finished':
                $view_file = 'finished';
                $dom = $this->renderPartial(
                    '//worklist/steps/' . $view_file,
                    array(
                        'pathway' => $wl_patient->pathway,
                        'patient' => $wl_patient->patient,
                        'partial' => $partial
                    ),
                    true
                );
                $this->renderJSON($dom);
                break;
            default:
                if ($pathstep_id) {
                    $step = PathwayStep::model()->findByPk($pathstep_id);
                } else {
                    $step = PathwayTypeStep::model()->findByPk($pathstep_type_id);
                }

                if (
                    ($step instanceof PathwayStep && $step->type->short_name === 'drug admin')
                    || ($step instanceof PathwayTypeStep && $step->step_type->short_name === 'drug admin')
                ) {
                    $psd_assignment_id = $step->getState('assignment_id');

                    if (!$psd_assignment_id) {
                        throw new CHttpException('Unable to retrieve PSD id');
                    }

                    $psd_assignment = OphDrPGDPSD_Assignment::model()->findByPk($psd_assignment_id);

                    if (!$psd_assignment) {
                        throw new CHttpException(404, 'Unable to retrieve PSD.');
                    }

                    $allow_unlock = $psd_assignment->getAppointmentDetails()['date'] === 'Today' ? 1 : 0;
                    $can_remove_psd = \Yii::app()->user->checkAccess('Prescribe') && (int)$step->status === PathwayStep::STEP_REQUESTED && !$psd_assignment->elements ? '' : 'disabled';
                    $dom = $this->renderPartial(
                        'application.modules.OphDrPGDPSD.views.pathstep.pathstep_view',
                        array(
                            'assignment' => $psd_assignment,
                            'step' => $step,
                            'visit' => $wl_patient,
                            'partial' => (int)$partial,
                            'patient_id' => $wl_patient->patient_id,
                            'worklist_patient' => $wl_patient,
                            'for_administer' => 0,
                            'is_prescriber' => Yii::app()->user->checkAccess('Prescribe'),
                            'can_remove_psd' => $can_remove_psd,
                            'interactive' => (bool)$interactive,
                            'allow_unlock' => (bool)$allow_unlock
                        ),
                        true
                    );
                    $this->renderJSON($dom);
                    Yii::app()->end();
                }

                // if the step is for prescription, only prescriber has the permission to start it
                if (
                    ($step instanceof PathwayStep && $step->type->short_name === 'Rx')
                    || ($step instanceof PathwayTypeStep && $step->step_type->short_name === 'Rx')
                ) {
                    $has_permission_to_start = Yii::app()->user->checkAccess('TaskPrescribe');
                }
                if ($step) {
                    $view_file = $red_flag ? 'generic_step' :
                        ($step instanceof PathwayStep ? $step->type->widget_view : $step->step_type->widget_view) ?? 'generic_step';
                    $dom = $this->renderPartial(
                        '//worklist/steps/' . $view_file,
                        array(
                            'step' => $step,
                            'worklist_patient' => $wl_patient,
                            'patient' => $wl_patient->patient,
                            'partial' => $partial,
                            'red_flag' => $red_flag,
                            'has_permission_to_start' => $has_permission_to_start
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
            $display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
            $display_secondary_number_usage_code = SettingMetadata::model()->getSetting('display_secondary_number_usage_code');
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
            $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $patient->id, $institution->id, $selected_site_id);

            $patientData['href'] = (new CoreAPI())->generatePatientLandingPageLink($patient);
            $patientData['lastname'] = $patient->getLast_name();
            $patientData['firstname'] = $patient->getFirst_name();
            $patientData['title'] = $patient->getTitle();
            $patientData['hospitalNumberPrompt'] = PatientIdentifierHelper::getIdentifierPrompt($primary_identifier);
            $patientData['hospitalNumberValue'] = PatientIdentifierHelper::getIdentifierValue($primary_identifier);
            $patientData['displayPrimaryNumberUsageCode'] = $display_primary_number_usage_code;
            $patientData['patientPrimaryIdentifierStatus'] = $display_primary_number_usage_code === 'GLOBAL' && $primary_identifier && $primary_identifier->patientIdentifierStatus;
            $patientData['patientPrimaryIdentifierStatusClassName'] = $primary_identifier->patientIdentifierStatus->icon->class_name ?? 'exclamation';
            $patientData['patientIdentifiers'] = $this->structurePatientIdentifiers($patient);
            $patientData['patientDeletedIdentifiers'] = $this->structureDeletedPatientIdentifiers($patient);
            $patientData['patientLocalIdentifiers'] = $this->structureLocalPatientIdentifiers($patient);

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

            $patientData['patientAllergies'] = $this->structurePatientAllergyData($patient);

            $patientData['patientRisks'] = $this->structurePatientRiskData($patient);

            $patientVAData = $this->structurePatientVAData($patient, $exam_api);
            if (isset($patientVAData)) {
                $patientData['vaData'] = $patientVAData;
            }

            $patientRefractionData = $this->structurePatientRefractionData($patient, $exam_api);
            if (isset($patientRefractionData)) {
                $patientData['refractionData'] = $patientRefractionData;
            }

            $patientCCTData = $this->structurePatientCCTData($patient, $exam_api);
            if (isset($patientCCTData)) {
                $patientData['cctData'] = $patientCCTData;
            }

            $patientCVIData = $this->structurePatientCVIData($patient, $exam_api);
            if (isset($patientCVIData)) {
                $patientData['cviData'] = $patientCVIData;
            }

            $patientData['ophthalmicDiagnoses'] = $this->structurePatientOphthalmicDiagnosesData($patient);

            $patientData['systemicDiagnoses'] = $this->structurePatientSystemicDiagnosesData($patient);

            $patientData['pastSurgery'] = $this->structurePatientPastSurgeryData($patient);

            $structuredMedicationsData = $this->structurePatientMedicationsData($patient);
            if (isset($structuredMedicationsData['systemicMedications'])) {
                $patientData['systemicMedications'] = $structuredMedicationsData['systemicMedications'];
            }
            if (isset($structuredMedicationsData['historyMedications'])) {
                $patientData['historyMedications'] = $structuredMedicationsData['historyMedications'];
            }
            if (isset($structuredMedicationsData['eyeMedications'])) {
                $patientData['eyeMedications'] = $structuredMedicationsData['eyeMedications'];
            }

            $patientData['familyHistory'] = $this->structurePatientFamilyHistoryData($patient);
            $patientData['socialHistory'] = $this->structurePatientSocialHistoryData($patient);

            $patientData['managementSummaries'] = $this->structurePatientManagementSummaryData($patient, $exam_api);

            $structuredWorklistData = $this->structurePatientWorklistData($patient);
            if (isset($structuredWorklistData['worklistPatients'])) {
                $patientData['worklistPatients'] = $structuredWorklistData['worklistPatients'];
            }
            if (isset($structuredWorklistData['pastWorklistPatients'])) {
                $patientData['pastWorklistPatients'] = $structuredWorklistData['pastWorklistPatients'];
            }

            $structuredPlansProblemsData = $this->structurePatientPlansProblemsData($patient);
            $patientData['currentPlanProblems'] = $structuredPlansProblemsData['currentPlanProblems'];
            $patientData['pastPlanProblems'] = $structuredPlansProblemsData['pastPlanProblems'];

            $currentTrials = $this->structurePatientTrialsData($patient);
            if (isset($currentTrials)) {
                $patientData['currentTrials'] = $currentTrials;
            }

            $this->renderJSON($patientData);
        }
    }

    private function structurePatientIdentifiers($patient)
    {
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
        return $patientIdentifiers;
    }

    private function structureDeletedPatientIdentifiers($patient)
    {
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
        return $patientDeletedIdentifiers;
    }

    private function structureLocalPatientIdentifiers($patient)
    {
        $patientLocalIdentifiers = null;
        foreach ($patient->localIdentifiers as $patientLocalIdentifier) {
            $patientLocalIdentifiers[] = [
                'hasValue' => $patientLocalIdentifier->hasValue(),
                'shortTitle' => $patientLocalIdentifier->patientIdentifierType->short_title,
                'displayValue' => $patientLocalIdentifier->getDisplayValue(),
            ];
        }
        return $patientLocalIdentifiers;
    }

    private function structurePatientAllergyData($patient)
    {
        $patientAllergies = [];
        // Get Allergies data.
        $allergiesWidget = $this->widget(\OEModule\OphCiExamination\widgets\Allergies::class, array(
            'patient' => $patient,
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
        ), false);

        $patientAllergies['hasAllergyStatus'] = boolval(!$patient->hasAllergyStatus());
        $patientAllergies['noAllergiesDate'] = !boolval(!$patient->hasAllergyStatus()) && $allergiesWidget->element->no_allergies_date;
        $patientAllergies['data'] = !boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date;
        if (!boolval(!$patient->hasAllergyStatus()) && !$allergiesWidget->element->no_allergies_date) {
            $patientAllergies['entries'] = null;
            foreach ($allergiesWidget->element->entries as $i => $entry) {
                if ($entry->getDisplayHasAllergy() === 'Present') {
                    $patientAllergies['entries'][] = [
                        'displayAllergy' => $entry->getDisplayAllergy(),
                        'reactionString' => ' ' . $entry->getReactionString(),
                        'comments' => $entry->comments,
                        'lastModifiedUser' => User::model()->findByPk($entry->last_modified_user_id)->getFullName(),
                    ];
                }
            }
        }

        return $patientAllergies;
    }

    private function structurePatientRiskData($patient)
    {
        $patientRisks = [];

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
            $patientRisks['entries'] = null;
            foreach ($historyRisksWidget->element->entries as $i => $entry) {
                if ($entry->getDisplayHasRisk() === 'Present') {
                    $patientRisks['entries'][] = [
                        'displayRisk' => $entry->getDisplayRisk(),
                        'comments' => $entry->comments,
                    ];
                }
            }
            foreach ($patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET) as $disorder) {
                $patientRisks['disorders'][] = [
                    'disorderTerm' => $disorder->term,
                ];
            }
        }

        $patientRisks['riskAlertInfo'] = $riskAlertInfo;
        $patientRisks['noRisksDate'] = $noRisksDate;

        return $patientRisks;
    }

    private function structurePatientVAData($patient, $exam_api)
    {
        //Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans
        $vaData = $exam_api->getMostRecentVADataStandardised($patient);
        if ($vaData) {
            return [
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

        return null;
    }

    private function structurePatientRefractionData($patient, $exam_api)
    {
        $refractionData = $exam_api->getLatestRefractionReadingFromAnyElementType($patient);
        if ($refractionData) {
            return [
                'has_left' => (bool)$refractionData['left'],
                'left' => $refractionData['left'],
                'has_right' => (bool)$refractionData['right'],
                'right' => $refractionData['right'],
                'event_date' => Helper::convertDate2NHS($refractionData['event_date'])
            ];
        }

        return null;
    }

    private function structurePatientCCTData($patient, $exam_api)
    {
        $leftCCT = $exam_api->getCCTLeft($patient);
        $rightCCT = $exam_api->getCCTRight($patient);
        if ($leftCCT !== null || $rightCCT !== null) {
            return [
                'has_left' => (bool)$leftCCT ,
                'left' => $leftCCT,
                'has_right' => (bool)$rightCCT,
                'right' => $rightCCT,
                'event_date' => Helper::convertDate2NHS($exam_api->getCCTDate($patient))
            ];
        }

        return null;
    }

    private function structurePatientCVIData($patient, $exam_api)
    {
        $cviStatus = $patient->getCviSummary();
        if ($cviStatus[0] !== 'Unknown') {
            return [
                'data' => $cviStatus[0],
                'date' => ($cviStatus[1] && $cviStatus[1] !== '0000-00-00') ? Helper::convertDate2HTML($cviStatus[1]) : 'N/A',
            ];
        }

        return null;
    }

    private function structurePatientOphthalmicDiagnosesData($patient)
    {
        $diagnoses = [];

        foreach ($patient->getOphthalmicDiagnosesSummary() as $ophthalmic_diagnosis) {
            list($side, $name, $date) = explode('~', $ophthalmic_diagnosis);
            $temp = [];
            $temp['name'] = $name;
            $temp['date'] = $date;

            $laterality = $this->getLaterality(null, $side);
            $temp['left'] = $laterality['left'];
            $temp['right'] = $laterality['right'];

            $diagnoses[] = $temp;
        }

        return $diagnoses;
    }

    private function structurePatientSystemicDiagnosesData($patient)
    {
        $diagnoses = [];

        foreach ($patient->systemicDiagnoses as $diagnosis) {
            $temp = [];
            $temp['term'] = $diagnosis->disorder->term;
            $temp['date'] = $diagnosis->getFormatedDate();

            $eye = $diagnosis->eye;
            $laterality = $this->getLaterality($eye, null);
            $temp['left'] = $laterality['left'];
            $temp['right'] = $laterality['right'];

            $diagnoses[] = $temp;
        }

        return $diagnoses;
    }

    private function structurePatientPastSurgeryData($patient)
    {
        $pastSurgery = [];

        // Get Past Surgery data.
        $pastSurgeryWidget = $this->createWidget(\OEModule\OphCiExamination\widgets\PastSurgery::class, array(
            'patient' => $patient,
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE_OUTPUT,
            'popupListSeparator' => '<br/>',
        ));
        $pastSurgeryData = $pastSurgeryWidget->getViewData();
        $operations = is_array($pastSurgeryData) ? $pastSurgeryData['operations'] : false;
        $pastSurgery['nilRecord'] = (!$operations || sizeof($operations) == 0) && !$pastSurgeryWidget->element->no_pastsurgery_date;
        $pastSurgery['noPreviousData'] = !((!$operations || sizeof($operations) == 0) && !$pastSurgeryWidget->element->no_pastsurgery_date) && $pastSurgeryWidget->element->no_pastsurgery_date;
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

                $pastSurgery['operation'][] = $temp;
            }
        }

        return $pastSurgery;
    }

    private function structurePatientMedicationsData($patient)
    {
        $structuredMedicationsData = [];

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
                $structuredMedicationsData['systemicMedications']['currentSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_systemic_meds, true, false, true, false);
            }
            if ($stopped_systemic_meds) {
                $structuredMedicationsData['systemicMedications']['stoppedSystemicMedsSize'] = sizeof($stopped_systemic_meds);
                $structuredMedicationsData['systemicMedications']['stoppedSystemicMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_systemic_meds, false, false, false, false);
            }
            $structuredMedicationsData['historyMedications']['id'] = CHtml::modelName($element);
        }

        $structuredMedicationsData['systemicMedications']['nilRecord'] = $nilRecord;
        $structuredMedicationsData['systemicMedications']['noPreviousData'] = $noPreviousData;

        $nilRecord = false;
        $noPreviousData = false;
        if (empty($current_eye_meds) && empty($stopped_eye_meds) && is_null($element->no_ophthalmic_medications_date)) {
            $nilRecord = true;
        } elseif (empty($current_eye_meds) && empty($stopped_eye_meds) && !is_null($element->no_ophthalmic_medications_date)) {
            $noPreviousData = true;
        } else {
            if ($current_eye_meds) {
                $structuredMedicationsData['eyeMedications']['currentEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $current_eye_meds, true, true, true, true);
            }
            if ($stopped_eye_meds) {
                $structuredMedicationsData['eyeMedications']['stoppedEyeMedsSize'] = sizeof($stopped_eye_meds);
                $structuredMedicationsData['eyeMedications']['stoppedEyeMeds'] = $this->patientHistoryMedicationsData($historyMedicationsWidget, $stopped_eye_meds, false, true, false, true);
            }
        }
        $structuredMedicationsData['eyeMedications']['nilRecord'] = $nilRecord;
        $structuredMedicationsData['eyeMedications']['noPreviousData'] = $noPreviousData;

        return $structuredMedicationsData;
    }

    private function structurePatientFamilyHistoryData($patient)
    {
        $familyHistory = [];

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
            $familyHistory['nilRecord'] = true;
        } else {
            $familyHistory['noFamilyHistory'] = empty($element->no_family_history_date) && !empty($element->entries);
            if (!empty($element->entries)) {
                $familyHistory['modelName'] = CHtml::modelName($element);
                foreach ($element->entries as $i => $entry) {
                    $temp = [];
                    $temp['relativeDisplay'] = $entry->displayrelative;
                    $temp['sideDisplay'] = $entry->side->name;
                    $temp['conditionDisplay'] = $entry->displaycondition;
                    $temp['comments'] = $entry->comments;
                    $familyHistory['entries'][] = $temp;
                }
            }
        }

        return $familyHistory;
    }

    private function structurePatientSocialHistoryData($patient)
    {
        $purifier = new CHtmlPurifier();

        $socialHistory = [];

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
            $socialHistory['nilRecord'] = true;
        } else {
            if ($element->occupation) {
                $socialHistory['occupation']['label'] = $purifier->purify($element->getAttributeLabel('occupation_id'));
                $socialHistory['occupation']['value'] = $purifier->purify($element->getDisplayOccupation());
            }
            if ($element->driving_statuses) {
                $socialHistory['drivingStatuses']['label'] = $purifier->purify($element->getAttributeLabel('driving_statuses'));
                $temp = '';
                foreach ($element->driving_statuses as $item) {
                    $temp .= $item->name . "\n";
                }
                $socialHistory['drivingStatuses']['value'] = $temp;
            }
            if ($element->smoking_status) {
                $socialHistory['smokingStatus']['label'] = $purifier->purify($element->getAttributeLabel('smoking_status_id'));
                $socialHistory['smokingStatus']['value'] = $purifier->purify($element->smoking_status->name);
            }
            if ($element->accommodation) {
                $socialHistory['accommodation']['label'] = $purifier->purify($element->getAttributeLabel('accommodation_id'));
                $socialHistory['accommodation']['value'] = $purifier->purify($element->accommodation->name);
            }
            if ($element->comments) {
                $socialHistory['comments']['label'] = $purifier->purify($element->getAttributeLabel('comments'));
                $socialHistory['comments']['value'] = $purifier->purify($element->comments);
            }
            if (isset($element->carer)) {
                $socialHistory['carer']['label'] = $purifier->purify($element->getAttributeLabel('carer_id'));
                $socialHistory['carer']['value'] = $purifier->purify($element->carer->name);
            }
            if (isset($element->alcohol_intake)) {
                $socialHistory['alcoholIntake']['label'] = $purifier->purify($element->getAttributeLabel('alcohol_intake'));
                $socialHistory['alcoholIntake']['value'] = $purifier->purify($element->alcohol_intake) . ' units/week';
            }
            if (isset($element->substance_misuse)) {
                $socialHistory['substanceMisuse']['label'] = $purifier->purify($element->getAttributeLabel('substance_misuse'));
                $socialHistory['substanceMisuse']['value'] = $purifier->purify($element->substance_misuse->name);
            }
        }

        return $socialHistory;
    }

    private function structurePatientManagementSummaryData($patient, $exam_api)
    {
        $managementSummary = [];

        $summaries = $exam_api->getManagementSummaries($patient);
        foreach ($summaries as $summary) {
            $temp = [];
            $temp['service'] = $summary->service;
            $temp['comments'] = $summary->comments;
            $temp['day'] = $summary->date[0];
            $temp['month'] = $summary->date[1];
            $temp['year'] = $summary->date[2];
            $temp['user'] = $summary->user;
            $managementSummary[] = $temp;
        }

        return $managementSummary;
    }

    private function structurePatientWorklistData($patient)
    {
        $structuredWorklistData = [];

        $appointment = $this->createWidget('Appointment', ['patient' => $patient, 'pro_theme' => 'pro-theme', 'is_popup' => true]);

        foreach ($appointment->worklist_patients as $worklistPatient) {
            $temp = [];
            $temp['time'] = date('H:i', strtotime($worklistPatient->when));
            $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
            $temp['name'] = $worklistPatient->worklist->name;
            $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
            $event = $appointment->did_not_attend_events[$worklistPatient->id] ?? null;

            if (isset($worklistStatus)) {
                $temp['status'] = $worklistStatus->attribute_value;
            } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                $temp['status'] = 'Did not attend.';
            }
            $structuredWorklistData['worklistPatients'][] = $temp;
        }
        if ($appointment->past_worklist_patients_count != 0) {
            $structuredWorklistData['pastWorklistPatientsCount'] = $appointment->past_worklist_patients_count;
            $criteria = new \CDbCriteria();
            $criteria->join = " JOIN worklist w ON w.id = t.worklist_id";
            $start_of_today = date("Y-m-d");
            $criteria->addCondition('t.when < "' . $start_of_today . '"');
            $criteria->order = 't.when desc';

            $past_worklist_patients = WorklistPatient::model()
                ->with('worklist', 'worklist_attributes.worklistattribute')
                ->findAllByAttributes(
                    ['patient_id' => $patient->id],
                    $criteria
                );
            foreach ($past_worklist_patients as $worklistPatient) {
                $temp = [];
                $temp['time'] = date('H:i', strtotime($worklistPatient->when));
                $temp['date'] = \Helper::convertDate2NHS($worklistPatient->worklist->start);
                $temp['name'] = $worklistPatient->worklist->name;
                $worklistStatus = $worklistPatient->getWorklistPatientAttribute('Status');
                $event = $appointment->did_not_attend_events[$worklistPatient->id] ?? null;

                if (isset($worklistStatus)) {
                    $temp['status'] = $worklistStatus->attribute_value;
                } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") {
                    $temp['status'] = 'Did not attend.';
                }
                $structuredWorklistData['pastWorklistPatients'][] = $temp;
            }
        }

        return $structuredWorklistData;
    }

    private function structurePatientPlansProblemsData($patient)
    {
        $structuredPlansProblemsData = ['currentPlanProblems' => [], 'pastPlanProblems' => []];

        $plansProblemsWidget = $this->createWidget('application.widgets.PlansProblemsWidget', [
            'patient_id' => $patient->id
        ]);
        foreach ($plansProblemsWidget->current_plans_problems as $planProblem) {
            $temp = [];
            $temp['name'] = $planProblem->name;
            $temp['tooltipContent'] = 'Created: ' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '');
            $temp['id'] = $planProblem->id;
            $structuredPlansProblemsData['currentPlanProblems'][] = $temp;
        }
        if ($plansProblemsWidget->past_plans_problems != 0) {
            foreach ($plansProblemsWidget->past_plans_problems as $planProblem) {
                $temp = [];
                $temp['name'] = $planProblem->name;
                $temp['tooltipContent'] = 'Created:' . \Helper::convertDate2NHS($planProblem->created_date) . ($planProblem->createdUser ? ' by ' . $planProblem->createdUser->getFullNameAndTitle() : '') .
                '<br /> Closed:' . Helper::convertDate2NHS($planProblem->last_modified_date) . ($planProblem->lastModifiedUser ? ' by ' . $planProblem->lastModifiedUser->getFullNameAndTitle() : '');
                $temp['id'] = $planProblem->id;
                $temp['lastModifiedDate'] = \Helper::convertDate2NHS($planProblem->last_modified_date);
                $structuredPlansProblemsData['pastPlanProblems'][] = $temp;
            }
        }

        return $structuredPlansProblemsData;
    }

    private function structurePatientTrialsData($patient)
    {
        if (Yii::app()->getModule('OETrial')) {
            $currentTrials = [];

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

                $currentTrials[] = $temp;
            }

            return $currentTrials;
        }

        return null;
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
            $status = (int)$item['status'];

            if ($status !== Pathway::STATUS_LATER) {
                $results['clinic'] += $item['count'];
            }

            if (
                $status !== Pathway::STATUS_ACTIVE
                && $status !== Pathway::STATUS_DISCHARGED
                && in_array($status, $progress)
            ) {
                $results['issues'] += $item['count'];
            } elseif ($status === Pathway::STATUS_DISCHARGED) {
                $results['discharged'] += $item['count'];
            } elseif ($status === Pathway::STATUS_DONE) {
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

        // This has been split into two here to that all the unique worklists can be passed back to the client to refresh the set of worklists.
        // Passing back the (filtered set of) worklists instead would clobber the set to be only those so filtered,
        // and the user would not be able to select those filtered out when trying to change what lists they're filtering.
        $unfiltered_worklists = $this->manager->getAllCurrentUniqueAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null, $filter);
        $worklists = $this->manager->filterWorklistsBySelected($unfiltered_worklists, $filter);

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

        // Send the ids and titles of all the unique worklists in the current display context
        // so the worklist filter selection lists are kept up to date with the current display context
        $dom['all_worklists_in_context'] = array_map(
            static function ($worklist) {
                return ['id' => $worklist->id, 'title' => $worklist->name];
            },
            $unfiltered_worklists
        );

        $dom['filtered_worklist_ids'] = array_map(static function ($worklist) {
            return $worklist->id;
        }, $worklists);

        $this->renderJSON($dom);
    }

    /**
     * @throws Exception
     */
    public function actionAddStepToPathway()
    {
        $id = Yii::app()->request->getPost('id');
        $position = Yii::app()->request->getPost('position');
        $step_data = Yii::app()->request->getPost('step_data') ?: array();
        $visit_id = Yii::app()->request->getPost('visit_id');
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);

        if (!$wl_patient->pathway) {
            $wl_patient->worklist->worklist_definition->pathway_type->instancePathway($wl_patient);
            $wl_patient->refresh();
        }

        $step = PathwayStepType::model()->findByPk($id);
        // priority for firm_id: user input > template > current firm id
        $step_data['firm_id'] = $step_data['firm_id'] ?? $step->getState('firm_id') ?? Yii::app()->session['selected_firm_id'];
        // if the template has subspecialty_id, then setup for the step
        $new_step = null;
        if ($step) {
            if ($step->getState('subspecialty_id')) {
                $step_data['subspecialty_id'] = $step->getState('subspecialty_id');
            }

            $step_data['service_id'] = $step_data['service_id'] ?? $step->getState('service_id');

            if (!$step_data['service_id'] && (isset($step_data['subspecialty_id']) || !empty($step_data['firm_id']))) {
                $service_subspecialty = $step_data['subspecialty_id'] ?? Firm::model()->findByPk($step_data['firm_id'])->serviceSubspecialtyAssignment->subspecialty_id;
                $episode = $wl_patient->patient->getOpenEpisodeOfSubspecialty($service_subspecialty);

                if ($episode) {
                    $step_data['service_id'] = $episode->firm_id;
                } else {
                    // Choose a default service firm
                    $service_firm = Firm::getDefaultServiceFirmForSubspecialty($service_subspecialty);

                    $step_data['service_id'] = $service_firm ? $service_firm->id : null;
                }
            }

            $new_step = $step->createNewStepForPathway($wl_patient->pathway->id, $step_data, true, (int)$position);
        }

        if ($new_step) {
            // Re-activate pathway in case it has been completed
            if ((int)$wl_patient->pathway->status === Pathway::STATUS_DONE) {
                $pathway = $wl_patient->pathway;
                $pathway->status = Pathway::STATUS_WAITING;
                if (!$pathway->save()) {
                    throw new CHttpException(500, 'Unable to re-activate pathway.');
                }
                $wl_patient->pathway->refresh();
            }
            $this->renderJSON(
                [
                    'step_html' => $this->renderPartial(
                        '_clinical_pathway',
                        ['visit' => $new_step->pathway->worklist_patient],
                        true
                    ),
                    'no_wait_timer' => in_array($new_step->type->short_name, PathwayStep::NO_WAIT_TIMER_AFTER_ADD) || ($wl_patient->pathway->findCheckInStep(true) === null)
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
        $visit_id = Yii::app()->request->getPost('target_pathway_id');
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);

        if (!$wl_patient->pathway) {
            $wl_patient->worklist->worklist_definition->pathway_type->instancePathway($wl_patient);
            $wl_patient->refresh();
        }

        if ($pathway_type) {
            $pathway_type->duplicateStepsForPathway($wl_patient->pathway->id, $position);
            $this->renderJSON(['step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $wl_patient], true)]);
        }
        throw new CHttpException(404, 'Unable to retrieve pathway type for duplication.');
    }

    /**
     * @param $term
     */
    public function actionGetAssignees($term)
    {
        $users = Yii::app()->db->createCommand()
            ->select('u.id, CONCAT(c.first_name, \' \', c.last_name) AS label')
            ->from('user u')
            ->join('contact c', 'c.id = u.contact_id')
            ->join('user_authentication ua', 'ua.user_id = u.id AND ua.institution_authentication_id IN (SELECT ia.id FROM institution_authentication ia WHERE ia.institution_id = :institution_id AND ia.active = 1)')
            ->where('CONCAT(c.first_name, \' \', c.last_name) LIKE CONCAT(\'%\', :term, \'%\') OR ua.username LIKE CONCAT(\'%\', :term, \'%\')')
            ->group('u.id, CONCAT(c.first_name, \' \', c.last_name)')
            ->bindValues([':institution_id' => Yii::app()->session['selected_institution_id'], ':term' => $term])
            ->queryAll();
        $this->renderJSON($users);
    }

    /**
     * @throws CHttpException
     * @throws Exception
     */
    public function actionAssignUserToPathway()
    {
        $id = Yii::app()->request->getPost('user_id');
        $visit_id = Yii::app()->request->getPost('target_visit_id');
        $visit = WorklistPatient::model()->findByPk($visit_id);

        if (!($visit->pathway)) {
            $visit->worklist->worklist_definition->pathway_type->instancePathway($visit);
            $visit->refresh();
        }
        $visit->pathway->owner_id = $id;
        $visit->pathway->save();
        $visit->refresh();
        $this->renderJSON(array('id' => $id, 'initials' => $visit->pathway->owner->getInitials()));
    }

    /**
     * @return void
     * @throws JsonException
     * @throws Exception
     */
    public function actionAddComment()
    {
        $post = $_POST;
        $wl_patient = WorklistPatient::model()->findByPk($post['visit_id']);
        $step_id = $post['pathstep_id'];
        $pathway_instanced = false;
        if ($post['pathstep_id'] === 'comment') {
            $pathway_id = $post['pathway_id'];
            if (!$wl_patient->pathway) {
                $wl_patient->worklist->worklist_definition->pathway_type->instancePathway($wl_patient);
                $wl_patient->refresh();
                $pathway_id = $wl_patient->pathway->id;
            }
            $comment = PathwayComment::model()->find('pathway_id=?', [$pathway_id]);
            if ($comment === null) {
                $comment = new PathwayComment();
                $comment->pathway_id = $pathway_id;
            }
        } else {
            if (!$post['pathstep_id']) {
                $type_step = PathwayTypeStep::model()->findByPk($post['pathstep_type_id']);
                if ($type_step) {
                    $steps = $type_step->pathway_type->instancePathway($wl_patient);
                    $step_id = $steps[$post['pathstep_type_id']]->id;
                }
            } else {
                $step_id = $post['pathstep_id'];
            }
            $comment = PathwayStepComment::model()->find('pathway_step_id=?', [$step_id]);
            if ($comment === null) {
                $comment = new PathwayStepComment();
                $comment->pathway_step_id = $step_id;
            }
        }

        // if the worklist has pathway associated with, set the flag to true
        if ($wl_patient->pathway) {
            $pathway_instanced = true;
        }

        $comment->comment = $post['comment'];
        $comment->doctor_id = $post['user_id'];
        if ($comment->save()) {
            $wl_patient->refresh();
        }

        /**
         * re-render the pathway html no matter what
         */
        $this->renderJSON(
            array(
                'step_html' => $pathway_instanced ? $this->renderPartial('_clinical_pathway', ['visit' => $wl_patient], true) : null,
                'step_id' => $step_id,
                'comment' => $comment->comment ?? null
            )
        );
    }

    /**
     * According to the step_id and delete its associated comment
     *
     * @return void
     */
    public function actionDeleteComment()
    {
        $step_id = Yii::app()->request->getParam('step_id');
        $step = PathwayStep::model()->findByPk(($step_id));
        if (!$step) {
            throw new CHttpException(404, 'Step not found.');
        }
        $comment = $step->comment;
        if ($comment && !$comment->delete()) {
            OELog::log(print_r($comment->getErrors(), true));
            throw new CHttpException(500, "Unable to delete the comment with pathstep id {$step_id}");
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
        $filter = WorklistFilter::model()->findByPk(Yii::app()->request->getParam('id'));

        if (!$filter) {
            throw new CHttpException(404, 'Worklist filter not found');
        }

        $filter->delete();
    }

    /**
     * Get the pathwaystep according to the provided pathstep_id
     * and get the corresponding pathway
     *
     * @return array instances of Pathway, PathwayStep
     * @throws CHttpException
     */
    private function getStepAndPathway($pathstep_id, $visit_id = null, $type_step_id = null)
    {
        $step = PathwayStep::model()->findByPk($pathstep_id);

        if (!$step) {
            // Potentially instantiate step if relevant parameters are passed
            if ($visit_id && $type_step_id) {
                $visit = WorklistPatient::model()->findByPk($visit_id);
                $type_step = PathwayTypeStep::model()->findByPk($type_step_id);

                if ($type_step) {
                    $pathway_steps = $type_step->pathway_type->instancePathway($visit);

                    $step = $pathway_steps[$type_step_id] ?? null;
                }

                if (!$step) {
                    throw new CHttpException(404, 'Unable to retrieve step for processing or step is not the required type of step.');
                }
            } else {
                throw new CHttpException(404, 'Step not found.');
            }
        }

        $pathway = $step->pathway;

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
        $type_step_id = Yii::app()->request->getPost('step_type_id');
        $visit_id = Yii::app()->request->getPost('visit_id');

        extract($this->getStepAndPathway($pathstep_id, $visit_id, $type_step_id));

        // push the step to completed status
        $step->nextStatus();
        $step->status = PathwayStep::STEP_COMPLETED;
        $step->start_time = date('Y-m-d H:i:s');
        $step->started_user_id = Yii::app()->user->id;
        $step->end_time = date('Y-m-d H:i:s');
        $step->completed_user_id = Yii::app()->user->id;

        $pathway->enqueue($step);

        // discharge the patient
        $pathway->status = Pathway::STATUS_DISCHARGED;
        $pathway->end_time = date('Y-m-d H:i:s');
        $pathway->save();

        PathwayCheckoutSystemEvent::dispatch($pathway);

        $this->renderJSON(
            [
                'status' => $pathway->getStatusString(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $pathway->worklist_patient], true),
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
        // revert the pathway back to active
        $pathway->status = Pathway::STATUS_ACTIVE;
        $pathway->end_time = null;
        if (!$pathway->save()) {
            throw new CHttpException(500, 'Unable to update the pathway status');
        }

        if (isset($pathway->worklist_patient_id)) {
            $event = \Event::model()->find("worklist_patient_id = ?", [$pathway->worklist_patient->id]);

            if ($event !== null) {
                $hl7_a13 = new \OEModule\PASAPI\resources\HL7_A13();
                $hl7_a13->setDataFromEvent($event->id);

                Yii::app()->event->dispatch(
                    'emergency_care_update',
                    $hl7_a13
                );
            }
        }

        $this->renderJSON(
            [
                'status' => $pathway->getStatusString(),
                'step_html' => $this->renderPartial('_clinical_pathway', ['visit' => $pathway->worklist_patient], true),
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
