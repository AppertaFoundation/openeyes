<?php

namespace OEModule\OphCiExamination\widgets;

use CDbCriteria;
use CException;
use Helper;
use OEModule\OphDrPGDPSD\models\{
    Element_DrugAdministration,
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_Assignment,
    OphDrPGDPSD_AssignedUser,
    OphDrPGDPSD_AssignedTeam
};

class DrugAdministration extends BaseMedicationWidget
{
    public $examination_element = null;
    protected static $elementClass = Element_DrugAdministration::class;

    protected function isAtTip()
    {
        return true;
    }

    private function hasPGDAssignments($user): bool
    {
        if (OphDrPGDPSD_AssignedUser::model()->with('pgdpsd')->exists("user_id = :user_id AND LOWER(pgdpsd.type) = 'pgd'", [':user_id' => $user->id])) {
            return true;
        }
        $user_teams = \Yii::app()->db->createCommand()
            ->select('team_id')
            ->from('team_user_assign')
            ->join('team', 'team.id = team_id')
            ->where('user_id = :user_id AND team.active <> 0')
            ->bindValues([':user_id' => $user->id])
            ->queryColumn();
        if (count($user_teams) <= 0) {
            return false;
        }
        return OphDrPGDPSD_AssignedTeam::model()->with('pgdpsd')->exists('team_id IN (' . implode(', ', $user_teams) . ") AND LOWER(pgdpsd.type) = 'pgd'");
    }


    /**
     * @return array
     */
    public function getViewData()
    {
        $current_user = \User::model()->findByPk(\Yii::app()->user->id);
        $is_prescriber = \Yii::app()->user->checkAccess('TaskPrescribe');
        $event_date = $this->controller->event ? date('Y-m-d', strtotime($this->controller->event->event_date)) : date('Y-m-d');

        $can_add_presets = \Yii::app()->user->checkAccess('OprnAddPresets') || $this->hasPGDAssignments($current_user);
        $can_add_meds = \Yii::app()->user->checkAccess('OprnAddMeds');
        $model_name = \CHtml::modelName($this->element);
        $class_name = get_class($this->element)::$entry_class;

        $medication_options = array();
        $available_appointments = array();
        $psds = array();
        $pgds = array();
        $presets = array();
        $patient_todo_assignments = OphDrPGDPSD_Assignment::model()->todoAndActive($this->patient->id, $event_date, $is_prescriber)->findAll();
        // avoid duplicated assignments
        $assigned_psds = array_unique(array_merge($patient_todo_assignments, $this->element->assignments));
        $relevant_psds = array();
        $irrelevant_psds = array();
        foreach ($assigned_psds as $assigned_psd) {
            if (!$assigned_psd->isrelevant || !$assigned_psd->active) {
                $irrelevant_psds[] = $assigned_psd;
                continue;
            }
            $relevant_psds[] = $assigned_psd;
        }
        // sort the irrelevent ones by date
        usort($irrelevant_psds, function ($assignment1, $assignment2) {
            $appointment_date_order = $assignment1->worklist_patient && $assignment2->worklist_patient && $assignment1->worklist_patient->when > $assignment1->worklist_patient->when ? 1 : 0;
            return $appointment_date_order;
        });
        $assigned_psds = array_merge($relevant_psds, $irrelevant_psds);
        if (in_array($this->controller->action->id, array('removed', 'renderEventImage', 'view', 'print'))) {
            return array_merge(
                parent::getViewData(),
                array(
                    'model_name' => $model_name,
                    'element_id' => "{$model_name}_element",
                    'class_name' => $class_name,
                    'user' => array(
                        'name' => $current_user->getFullName(),
                        'id' => $current_user->id,
                    ),
                    'user_obj' => $current_user,
                    'assigned_psds' => $assigned_psds,
                    'available_appointments' => $available_appointments,
                    'psds' => json_encode($psds),
                    'pgds' => json_encode($pgds),
                    'can_add_presets' => $can_add_presets,
                    'can_add_meds' => $can_add_meds,
                    'medication_options' => $medication_options,
                    'is_prescriber' => $is_prescriber,
                )
            );
        }
        if ($is_prescriber) {
            $appointment_criteria = new CDbCriteria();
            $appointment_criteria->with = ['worklist'];
            $appointment_criteria->compare('t.patient_id', $this->patient->id);
            $appointment_criteria->addCondition("UNIX_TIMESTAMP(DATE_FORMAT(worklist.start, '%Y-%m-%d')) >= UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'))");
            $available_appointments = \WorklistPatient::model()->findAll($appointment_criteria);
            usort($available_appointments, function ($appt1, $appt2) {
                return strtotime($appt1->when) > strtotime($appt2->when) ? 1 : -1;
            });
        }
        /**
         * if the current user has the permission to add individual medications
         * then get the medication lists
         */
        if ($can_add_meds) {
            $pgdpsd_api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
            $medication_options = $pgdpsd_api->getMedicationOptions();
        }
        /**
         * if the current user has the permission to add presets
         * then get the presets, and process the list to be compatible with adder popup
         * then split the list into PSD and PGD
         * PSD will only display for the users with the Prescribe permission
         * PGD will display for nominated users and the users with the Prescribe permission
         */
        if ($can_add_presets) {
            $presets = OphDrPGDPSD_PGDPSD::model()->findAll("active = 1 AND LOWER(type) IN ('psd', 'pgd')");
            foreach ($presets as $preset) {
                $med_names = array_map(static function ($med) {
                    return '- ' . $med->medication->getLabel(true);
                }, $preset->assigned_meds);
                $med_names_string = implode('<br/>', $med_names);
                $meds_info = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='$med_names_string'></i>";
                $item = array(
                    'id' => $preset->id,
                    'label' => $preset->name,
                    'meds' => $preset->getAssignedMedsInJSON(),
                    'prepended_markup' => $meds_info,
                    'is_preset' => true,
                    'preset_name' =>  $preset->name,
                    'preset_type' => "Preset - {$preset->type}",
                    'is_pgd' => strtolower($preset->type) === 'pgd' ? 1 : 0,
                );
                if (strtolower($preset->type) === 'psd' && $is_prescriber) {
                    $psds[] = $item;
                }
                if (
                    strtolower($preset->type) === 'pgd'
                    &&
                    (
                        in_array($current_user->id, $preset->getAuthedUserIDs())
                        || $is_prescriber
                    )
                ) {
                    $pgds[] = $item;
                }
            }
        }

        return array_merge(
            parent::getViewData(),
            array(
                'model_name' => $model_name,
                'element_id' => "{$model_name}_element",
                'class_name' => $class_name,
                'user' => array(
                    'name' => $current_user->getFullName(),
                    'id' => $current_user->id,
                ),
                'user_obj' => $current_user,
                'assigned_psds' => $assigned_psds,
                'available_appointments' => $available_appointments,
                'psds' => json_encode($psds),
                'pgds' => json_encode($pgds),
                'can_add_presets' => $can_add_presets,
                'can_add_meds' => $can_add_meds,
                'medication_options' => $medication_options,
                'is_prescriber' => $is_prescriber,
            )
        );
    }

    /**
     * @param Element_DrugAdministration $element
     * @param $data
     * @throws CException
     */
    protected function updateElementFromData($element, $data)
    {
        $assignments_data = array_key_exists('assignment', $data) ? $data['assignment'] : array();
        $assignment_by_id = array();
        $entries_by_id = array();
        foreach ($element->assignments as $assignment) {
            $assignment_by_id[$assignment->id] = $assignment;
        }

        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }
        $assignment_entries = array();
        $errors = array();
        $is_prescriber = \Yii::app()->user->checkAccess('TaskPrescribe');
        foreach ($assignments_data as $key => $assignment_data) {
            $assignment_id = array_key_exists('assignment_id', $assignment_data) ? $assignment_data['assignment_id'] : 0;
            $pgdpsd_id = array_key_exists('pgdpsd_id', $assignment_data) ? $assignment_data['pgdpsd_id'] : null;
            $visit_id = array_key_exists('visit_id', $assignment_data) ? $assignment_data['visit_id'] : null;
            $comment = array_key_exists('comment', $assignment_data) ? ($assignment_data['comment'] ? : null) : null;
            $confirmed = array_key_exists('confirmed', $assignment_data) ? ($assignment_data['confirmed'] ? : null) : null;
            $assignment_data_entries = array_key_exists('entries', $assignment_data) ? $assignment_data['entries'] : array();
            if ($assignment_id) {
                $assignment = isset($assignment_by_id[$assignment_id]) ? $assignment_by_id[$assignment_id] : OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
            } else {
                $assignment = new OphDrPGDPSD_Assignment();
            }
            if (array_key_exists('create_wp', $assignment_data)) {
                $assignment->create_wp = (int)$assignment_data['create_wp'];
            }
            $assignment->patient_id = $this->patient->id;
            $assignment->pgdpsd_id = $pgdpsd_id;
            $assignment->visit_id = $visit_id;
            $assignment->confirmed = $is_prescriber ? $confirmed : 1;
            $assignment->active = (int)$assignment_data['active'] ?? 0;
            $assignment->comment = $comment;
            $assignment->saveComment($comment);
            $assignment->cacheMeds($assignment_data_entries);
            $errors = array_merge($errors, $assignment->getErrors());
            $assignment->validate();
            $errors = array_merge($errors, $assignment->getErrors());
            $assignment_entries[] = $assignment;
        }
        if ($errors) {
            foreach ($errors as $attr => $msg) {
                $element->addError("assignment_{$key}_entries$attr", $msg[0]);
            }
        }
        $element->assignments = $assignment_entries;
    }
}
