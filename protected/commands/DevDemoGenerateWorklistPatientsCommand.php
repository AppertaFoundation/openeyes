<?php
class DevDemoGenerateWorklistPatientsCommand extends CConsoleCommand
{
    public function run($args)
    {
        $warning_list = array(
            "\nWARNING:\n",
            "\33[41m* IMPORTANT: This command MUST only be run in development envrionments or demo envrionments. DO NOT USE THIS in any PRODUCTION or any client envrionments\33[0m\n",
            "* Please make sure the generateworklist command has been run prior to this command. This command is used to copy existing records from worklist_patient to future related worklists\n",
            "* After running this command, please make sure the yii parameter 'worklist_dashboard_future_days' is set to 0, otherwise the worklist page will be very slow\n",
        );
        foreach ($warning_list as $warning) {
            echo "$warning";
        }
        $is_proceed = readline("Would you like to proceed? Y/N or any other key to exit: ");
        switch (strtolower($is_proceed)) {
            case 'y':
            case 'yes':
                echo "Please select from the following: \n\n";
                echo "1. Generate worklist patients for all worklists\n";
                echo "2. Generate random worklist attributes and worklist patient attributes\n";
                $options = readline("Please select from the above:");
                echo "\n";
                switch ($options) {
                    case '1':
                        $this->generateWorklistPatients();
                        break;
                    case '2':
                        $this->generateWorklistAttributes();
                        break;
                }
                break;
            default:
                exit;
        }
    }
    protected function generateWorklistPatients()
    {
        echo "Querying existing worklists\n";
        $worklists = Worklist::model()->findAll();
        $worklists_has_patient_dict = array();

        $worklists_no_patient_dict = Yii::app()->db->createCommand()
            ->select('
                wl.id, 
                wl.name
            ')
            ->from('worklist wl')
            ->leftJoin('worklist_patient wlp', 'wl.id = wlp.worklist_id')
            ->where('wlp.id IS NULL')
            ->queryAll();

        foreach ($worklists as $worklist) {
            if ($worklist->worklist_patients) {
                $worklists_has_patient_dict[$worklist->name] = $worklist;
            }
        }
        array_map(function ($wl_has_patient) {
            $total_patients = count($wl_has_patient->worklist_patients);
            echo "{$wl_has_patient->name} has {$total_patients} patient(s)\n";
        }, $worklists_has_patient_dict);
        echo "Copying to future worklists...\n";
        foreach ($worklists_no_patient_dict as $val) {
            foreach ($worklists_has_patient_dict[$val['name']]->worklist_patients as $wl_p) {
                $new_wl_p = new WorklistPatient();
                $new_wl_p->attributes = $wl_p->attributes;
                $new_wl_p->worklist_id = $val['id'];
                $new_wl_p->when = str_replace('00:00:00', '09:00:00', $new_wl_p->worklist->start);
                $new_wl_p->save();
            }
        }
    }

    protected function generateWorklistAttributes()
    {
        $attr_input = readline("Please enter the attribute you want to add:\n");
        echo "\n";
        $attr_vals = readline("Please enter the values you want to add for the attribute, separate with comma if there are multiple ones:\n");
        echo "\n";
        $attribute = !empty($attr_input) ? $attr_input : 'Status';
        $attr_vals = !empty($attr_vals) ? explode(",", $attr_vals) : array('Arrived', 'Waiting', 'Discharged', 'Wrong Arrival');
        echo "Querying existing worklists\n";
        $worklists = Worklist::model()->findAll();
        $worklist_chunks = array_chunk($worklists, 30);
        foreach ($worklist_chunks as $chunk) {
            foreach ($chunk as $worklist) {
                $wl_attr = WorklistAttribute::model()->find(
                    'name = :name AND worklist_id = :worklist_id',
                    array(
                        ':name' => $attribute,
                        ':worklist_id' => $worklist->id
                    )
                );
                if (!$wl_attr) {
                    $wl_attr = new \WorklistAttribute();
                    $wl_attr->worklist_id = $worklist->id;
                    $wl_attr->name = $attribute;
                    $wl_attr->display_order = 1;
                    $wl_attr->save();
                }

                $wl_attr_id = $wl_attr->id;
                foreach ($worklist->worklist_patients as $wl_patient) {
                    $wl_patient_attr = WorklistPatientAttribute::model()->find(
                        'worklist_attribute_id = :worklist_attribute_id 
                        AND worklist_patient_id = :worklist_patient_id',
                        array(
                            ':worklist_attribute_id' => $wl_attr_id,
                            ':worklist_patient_id' => $wl_patient->id,
                        )
                    );
                    if (!$wl_patient_attr) {
                        $wl_patient_attr = new WorklistPatientAttribute();
                        $wl_patient_attr->worklist_attribute_id = $wl_attr_id;
                        $wl_patient_attr->worklist_patient_id = $wl_patient->id;
                        $wl_patient_attr->attribute_value = $attr_vals[array_rand($attr_vals)];
                        $wl_patient_attr->save();
                    }
                }
            }
        }
    }
}
