<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrConsent_Extra_ProcedureSelection extends BaseFieldWidget
{
    public $subsections;
    public $procedures;
    public $removed_stack;
    public $newRecord;
    public $selected_procedures;
    public $form;
    public $durations = false;
    public $class;
    public $total_duration = 0;
    public $adjusted_total_duration = 0;
    public $last;
    public $identifier = 'procs';
    public $relation = 'procedures';
    public $label = 'ExtraProcedures';
    public $headertext;
    public $read_only = false;
    public $restrict = false;
    public $restrict_common = false;
    public $callback = false;
    public $layout = false;
    public $popupButton = true;
    public $complexity = null;
    public $showEstimatedDuration = true;

    public function run()
    {

        if (empty($_POST)) {
            if (!$this->selected_procedures && $this->element) {
                $this->selected_procedures = $this->element->{$this->relation};
                if ($this->durations) {
                    $this->total_duration = $this->element->total_duration;
                }
                $this->adjusted_total_duration = $this->total_duration;
            }
        } else {
            $this->selected_procedures = array();
            if (isset($_POST['Procedures_' . $this->identifier]) && is_array($_POST['Procedures_' . $this->identifier])) {
                foreach ($_POST['Procedures_' . $this->identifier] as $proc_id) {
                    $proc = OphTrConsent_Extra_Procedure::model()->findByPk($proc_id);
                    $this->selected_procedures[] = $proc;
                    if ($this->durations) {
                        $this->total_duration += $proc->default_duration;
                    }
                }
            }
        }

        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        if ($this->restrict_common === 'unbooked') {
            $this->subsections = array();
        } else {
            $this->subsections = SubspecialtySubsection::model()->getList($subspecialty_id);
        }
        $this->procedures = array();
        $this->removed_stack = array();
        if (empty($this->subsections)) {
            foreach (Procedure::model()->getListBySubspecialty(
                $subspecialty_id,
                $this->restrict_common
            ) as $proc_id => $name) {
                if (empty($_POST)) {
                    $found = false;
                    if ($this->selected_procedures) {
                        foreach ($this->selected_procedures as $procedure) {
                            if ($procedure->id == $proc_id) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        $this->procedures[$proc_id] = $name;
                    } else {
                        $this->removed_stack[] = "{id: $proc_id, name: '$name'}";
                    }
                } else {
                    if (!isset($_POST['Procedures_' . $this->identifier]) || !in_array($proc_id, $_POST['Procedures_' . $this->identifier])) {
                        $this->procedures[$proc_id] = $name;
                    } else {
                        $this->removed_stack[] = "{id: $proc_id, name: '$name'}";
                    }
                }
            }
        } else {
            // Doesn't matter if removed_stack contains non-common procedures as lists are reloaded using ajax on removal
            if (!empty($this->selected_procedures)) {
                foreach ($this->selected_procedures as $selected_procedure) {
                    $this->removed_stack[] = "{id: $selected_procedure->id, name: '$selected_procedure->term'}";
                }
            }
        }

        $this->class = get_class($this->element);

        if ($this->read_only) {
            $this->render(get_class($this).'_readonly');
        } else {
            $this->render(get_class($this));
        }
    }

    public function adjustTimeByComplexity($duration, $complexity)
    {
        $adjusted_duration = $duration;
        $increase = SettingMetadata::model()->getSetting('op_booking_inc_time_high_complexity');
        $decrease = SettingMetadata::model()->getSetting('op_booking_decrease_time_low_complexity');

        if ($complexity == Element_OphTrOperationbooking_Operation::COMPLEXITY_HIGH && $increase) {
            $adjusted_duration = (1 + ((int)$increase/100)) * $duration; // if increase=20 than 1.2 * duration
        } elseif (!is_null($complexity) && $complexity == Element_OphTrOperationbooking_Operation::COMPLEXITY_LOW && $decrease) {
            $adjusted_duration = (1 - ((int)$decrease/100)) * $duration; // if decrease=10 than 0.9 * duration
        }

        return ceil($adjusted_duration);
    }

    public function render($view, $data = null, $return = false)
    {
        if ($this->layout) {
            $view .= '_'.$this->layout;
        }
        parent::render($view, $data, $return);
    }
}
