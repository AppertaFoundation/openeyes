<?php
namespace OEModule\OphCiExamination\widgets;
use Helper;
class DrugAdministration extends BaseMedicationWidget
{
    public $examination_element = null;
    protected static $elementClass = \Element_DrugAdministration::class;

    protected function isAtTip()
    {
        return true;
    }


    /**
     * @return array
     */
    public function getViewData()
    {
        $current_user = \User::model()->findByPk(\Yii::app()->user->id);
        $is_prescriber = \Yii::app()->user->checkAccess('Prescribe');
        $event_date = $this->controller->event ? date('Y-m-d', strtotime($this->controller->event->event_date)) : date('Y-m-d');
        $is_med_admin = \Yii::app()->user->checkAccess('Med Administer');
        $can_add_meds = $is_med_admin || $is_prescriber;
        $model_name = \CHtml::modelName($this->element);
        $class_name = $this->element::$entry_class;

        $medication_options = array();
        $available_appointments = array();
        $presets = array();
        if(in_array($this->controller->action->id, array('removed', 'renderEventImage', 'view', 'print'))){
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
                    'assigned_psds' => $this->element->assignments,
                    'available_appointments' => $available_appointments,
                    'presets' => json_encode($presets),
                    'medication_options' => $medication_options,
                    'is_prescriber' => $is_prescriber,
                    'is_med_admin' => $is_med_admin,
                )
            );
        }
        if ($is_prescriber) {
            $now_timestamp = strtotime(date('Y-m-d'));
            $available_appointments = \WorklistPatient::model()->findAll('patient_id = :patient_id AND UNIX_TIMESTAMP(`when`) >= :now', array(':patient_id' => $this->patient->id, ':now' => $now_timestamp));
            usort($available_appointments, function ($appt1, $appt2) {
                return strtotime($appt1->when) > strtotime($appt2->when);
            });
        }
        if ($can_add_meds) {
            $pgdpsd_api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
            $medication_options = $pgdpsd_api->getMedicationOptions();
            $presets = \OphDrPGDPSD_PGDPSD::model()->findAll("active = 1 AND LOWER(type) = 'psd'");
            $presets = array_map(function ($preset) {
                $med_names = array_map(function ($med) {
                    return '- ' . $med->medication->getLabel(true);
                }, $preset->assigned_meds);
                $med_names_string = implode('<br/>', $med_names);
                $meds_info = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='{$med_names_string}'></i>";
                return array(
                    'id' => $preset->id,
                    'label' => $preset->name,
                    'meds' => $preset->getAssignedMedsInJSON(),
                    'prepended_markup' => $meds_info,
                    'is_preset' => true,
                );
            }, $presets);
        }
        $patient_todo_assignments = \OphDrPGDPSD_Assignment::model()->todoAndActive($this->patient->id, $event_date, $is_prescriber)->findAll();
        // avoid duplicated assignments
        $assigned_psds = array_unique(array_merge($patient_todo_assignments, $this->element->assignments));
        $relevant_psds = array();
        $irrelevant_psds = array();
        foreach ($assigned_psds as $assigned_psd) {
            if (!$assigned_psd->isrelevant) {
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
                'presets' => json_encode($presets),
                'medication_options' => $medication_options,
                'is_prescriber' => $is_prescriber,
                'is_med_admin' => $is_med_admin,
            )
        );
    }

    /**
     * @param Element_DrugAdministration $element
     * @param $data
     * @throws \CException
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
        $is_prescriber = \Yii::app()->user->checkAccess('Prescribe');
        foreach ($assignments_data as $key => $assignment_data) {
            $assignment_id = array_key_exists('assignment_id', $assignment_data) ? $assignment_data['assignment_id'] : 0;
            $pgdpsd_id = array_key_exists('pgdpsd_id', $assignment_data) ? $assignment_data['pgdpsd_id'] : null;
            $visit_id = array_key_exists('visit_id', $assignment_data) ? $assignment_data['visit_id'] : null;
            $comment = array_key_exists('comment', $assignment_data) ? ($assignment_data['comment'] ? : null) : null;
            $confirmed = array_key_exists('confirmed', $assignment_data) ? ($assignment_data['confirmed'] ? : null) : null;
            $active = array_key_exists('active', $assignment_data) ? $assignment_data['active'] : null;
            $assignment_data_entries = array_key_exists('entries', $assignment_data) ? $assignment_data['entries'] : array();
            if ($assignment_id) {
                $assignment = isset($assignment_by_id[$assignment_id]) ? $assignment_by_id[$assignment_id] : \OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
            } else {
                $assignment = new \OphDrPGDPSD_Assignment();
            }
            if (array_key_exists('create_wp', $assignment_data)) {
                $assignment->create_wp = intval($assignment_data['create_wp']);
            }
            $assignment->patient_id = $this->patient->id;
            $assignment->pgdpsd_id = $pgdpsd_id;
            $assignment->visit_id = $visit_id;
            $assignment->confirmed = $is_prescriber ? $confirmed : 1;
            $assignment->active = $active;
            if ($comment) {
                $assignment->saveComment($comment);
            }
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

    /**
     * @param bool $assignment
     * @return array
     * returns the style for deleted psd order block, and a deleted tag
     */
    public function getDeletedUI(bool $is_active){
        $ret = array(
            'deleted_style' => null,
            'deleted_tag' => null,
        );
        if(!$is_active){
            $ret['deleted_style'] = 'status-box red';
            $ret['deleted_tag'] = "<span class='highlighter warning'>Cancelled</span>";
        }

        return $ret;
    }
}
