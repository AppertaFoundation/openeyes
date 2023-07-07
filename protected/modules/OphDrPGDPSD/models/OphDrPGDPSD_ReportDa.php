<?php

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_Assignment;

class OphDrPGDPSD_ReportDa extends BaseReport
{
    public $date_type;
    public $date_from;
    public $date_to;
    public $type;
    public $med_name;
    public $administrations;
    public $preset_name;
    public $pi_value;

    public function attributeNames()
    {
        return array(
            'date_type',
            'date_from',
            'date_to',
            'type',
            'med_name',
            'administrations',
            'preset_name',
            'pi_value',
        );
    }

    public function attributeLabels()
    {
        return array(
            'date_from' => 'Search Booking Date from',
            'date_to' => 'Search Booking Date to',
            'type' => 'Type',
            'med_name' => 'Medication Name',
        );
    }
    public function rules()
    {
        return array(
            array(implode(',', $this->attributeNames()), 'safe'),
        );
    }
    public function run()
    {
        /*
            * call a function to get the required data, and put the data into $administrations
            * in _drugAdmin loop through $this->administrations
        */
        $this->administrations = array();
        $date_type = $this->date_type;
        $date_from = strtotime($this->date_from);
        $date_to = strtotime($this->date_to);
        $med_name = strtolower(trim($this->med_name));
        $type = strtolower($this->type);
        $preset_name = strtolower($this->preset_name);
        $pi_value = preg_replace('/\s+/', '', trim($this->pi_value));
        $psd_criteria = new CDbCriteria();
        $psd_criteria->compare('t.active', 1);
        if ($pi_value) {
            $psd_criteria->with = ['patient', 'patient.identifiers', 'patient.identifiers.patientIdentifierType'];
            $selected_institution_id = Yii::app()->session->get('selected_institution_id');
            $selected_site_id = Yii::app()->session['selected_site_id'];
            // search for patient identifier usage type in local or global
            // if the usage type is local it must match the institution id and/or site id
            $psd_criteria->addCondition("(patientIdentifierType.usage_type = 'LOCAL' AND patientIdentifierType.institution_id = $selected_institution_id AND (patientIdentifierType.site_id = $selected_site_id OR patientIdentifierType.site_id IS NULL)) OR patientIdentifierType.usage_type = 'GLOBAL'");
            $psd_criteria->compare('patient_identifier_not_deleted.value', $pi_value);
        }
        switch ($type) {
            case 'assigned':
                $psd_criteria->addCondition("t.visit_id IS NOT NULL");
                break;
            case '0':
                $psd_criteria->addCondition("t.visit_id IS NULL");
                break;
            default:
                break;
        }
        $assignments = OphDrPGDPSD_Assignment::model()->with('pgdpsd')->findAll($psd_criteria);
        if ($med_name) {
            $assignments = array_filter($assignments, function ($assignment) use ($med_name) {
                $found = false;
                foreach ($assignment->assigned_meds as $med) {
                    if (strpos(strtolower($med->medication->preferred_term), $med_name) !== false) {
                        $found = true;
                        break;
                    };
                }
                return $found;
            });
        }
        $assignments = array_filter($assignments, function ($assignment) use ($date_type, $date_from, $date_to) {
            $date = null;
            switch ($date_type) {
                case 'assignment':
                    $date = \Helper::convertDate2NHS($assignment->created_date);
                    $date = strtotime($date);
                    break;
                case 'appointment':
                    $date = $assignment->worklist_patient ? \Helper::convertDate2NHS($assignment->worklist_patient->when) : \Helper::convertDate2NHS($assignment->created_date);
                    $date = strtotime($date);
                    break;
                case 'administration':
                    $found = array_filter($assignment->assigned_meds, function ($med) use ($date_from, $date_to) {
                        if ($med->administered) {
                            $date = \Helper::convertDate2NHS($med->administered_time);
                            $date = strtotime($date);
                            if ($date_from && $date_to) {
                                return $date >= $date_from && $date <= $date_to;
                            } elseif ($date_from && !$date_to) {
                                return $date >= $date_from;
                            } elseif (!$date_from && $date_to) {
                                return $date <= $date_to;
                            } else {
                                return true;
                            }
                        }
                        return false;
                    });
                    return $found ? true : false;
                default:
                    return true;
            }
            if ($date_from && $date_to) {
                return $date >= $date_from && $date <= $date_to;
            } elseif ($date_from && !$date_to) {
                return $date >= $date_from;
            } elseif (!$date_from && $date_to) {
                return $date <= $date_to;
            } else {
                return true;
            }
        });
        $assignment_complete_status = OphDrPGDPSD_Assignment::STATUS_COMPLETE;
        foreach ($assignments as $assignment) {
            $report_data = array();
            $assignment_name_type = $assignment->getAssignmentTypeAndName();
            if ($preset_name && strpos(strtolower($assignment_name_type['name']), $preset_name) === false) {
                continue;
            }
            $assignment_status_text = $assignment->getStatusDetails()['text'];
            if (!isset($this->administrations[$assignment->id])) {
                $this->administrations[$assignment->id] = array();
            }
            $assigned_date = $assignment->worklist_patient ? \Helper::convertDate2NHS($assignment->worklist_patient->when, '-') : null;
            $assignment_status = intval($assignment->status) === $assignment_complete_status ? $assignment_status_text : ($assigned_date && (strtotime(date('Y-m-d')) > strtotime($assigned_date)) ? 'Expired' : $assignment_status_text);

            foreach ($assignment->assigned_meds as $med) {
                $patient_identifieres = \PatientIdentifierHelper::getAllPatientIdentifiersForReports($assignment->patient_id);
                $patient_identifieres = explode(',', $patient_identifieres);
                $report_data["Patient IDs"] = implode('<br/><br/>', $patient_identifieres);
                if ($med->medication->source_type === 'DM+D') {
                    $report_data['DM+D Code'] = $med->medication->preferred_code;
                } else {
                    $report_data['DM+D Code'] = 'NON DM+D';
                }
                $report_data['Med Name'] = $med->medication->getLabel(true);
                $report_data['Administered By'] = $med->administered_user ? $med->administered_user->getFullName() : 'N/A';
                $report_data['Administered At'] = $med->administered_time ? : 'N/A';
                $report_data['Administered'] = $med->administered ? 'Yes' : 'No';
                $this->administrations[$assignment->id]['assigned_meds'][] = $report_data;
            }
            $this->administrations[$assignment->id]['title'] = "{$assignment_name_type['type']}: {$assignment_name_type['name']} ";
            $this->administrations[$assignment->id]['title'] .= $assigned_date ? "Assigned for {$assigned_date}" : 'Direct Administration';
            $this->administrations[$assignment->id]['creation'] = "Created by {$assignment->createdUser->getFullName()} on {$assignment->created_date}";
            $this->administrations[$assignment->id]['status'] = $assignment_status;
        }
    }

    public function getColumns()
    {
        return array(
            "Patient IDs",
            'DM+D Code',
            'Med Name',
            'Administered',
            'Administered By',
            'Administered At',
        );
    }
    /**
     * Output the report in CSV format.
     *
     * @return string
     */
    public function toCSV()
    {
        $output = null;
        foreach ($this->administrations as $administration) {
            $output .= "{$administration['title']} {$administration['status']} {$administration['creation']}\n\n";
            $output .= implode(',', $this->getColumns())."\n";
            $output .= $this->array2Csv($administration['assigned_meds']);
            $output .= "\n\n";
        }
        return $output;
    }
}
