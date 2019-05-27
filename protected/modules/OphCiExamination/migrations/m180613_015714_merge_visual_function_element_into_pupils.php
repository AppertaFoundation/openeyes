<?php

class m180613_015714_merge_visual_function_element_into_pupils extends OEMigration
{
    private function getPupilElementForEvent($event_id, $versioned)
    {
        return $this->dbConnection->createCOmmand()
            ->select('*')
            ->from('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''))
            ->where('event_id = :event_id', array(':event_id' => $event_id))
            ->queryRow();
    }

    private function migrateVisualFunction($versioned)
    {
        // Move columns from visual function to pupillary abnormalities
        $this->addColumn('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''),
            'left_comments', 'text');
        $this->addColumn('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''),
            'right_comments', 'text');
        $this->addColumn('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''),
            'left_rapd', 'tinyint(1) unsigned');
        $this->addColumn('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''),
            'right_rapd', 'tinyint(1) unsigned');

        $visual_functions = $this->dbConnection->createCommand()
            ->select('*')
            ->from('et_ophciexamination_visualfunction' . ($versioned ? '_version' : ''))
            ->queryAll();

        // For every visual function element
        foreach ($visual_functions as $visualfunction) {
            // Find any pupil element in the same event
            $pupil_element = $this->getPupilElementForEvent($visualfunction['event_id'], $versioned);

            // If there isn't one...
            if (!$pupil_element) {
                // ... then make it with the same metadata
                $this->insert('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''), array(
                    'event_id' => $visualfunction['event_id'],
                    'eye_id' => $visualfunction['eye_id'],
                    'created_user_id' => $visualfunction['created_user_id'],
                    'last_modified_user_id' => $visualfunction['last_modified_user_id'],
                    'created_date' => $visualfunction['created_date'],
                    'last_modified_date' => $visualfunction['last_modified_date'],
                ));

                $pupil_element = $this->getPupilElementForEvent($visualfunction['event_id'], $versioned) or die();
            }

            // Update the existing/new element with the visual function data
            $this->update('et_ophciexamination_pupillaryabnormalities' . ($versioned ? '_version' : ''),
                array(
                    'left_comments' => $visualfunction['left_comments'],
                    'right_comments' => $visualfunction['right_comments'],
                    'left_rapd' => $visualfunction['left_rapd'],
                    'right_rapd' => $visualfunction['right_rapd'],
                    'eye_id' => $visualfunction['eye_id'] & $pupil_element['eye_id'],
                ),
                'id = :id', array(':id' => $pupil_element['id'])
            );
        }
    }

    public function safeUp()
    {
        $this->migrateVisualFunction(false);
        $this->migrateVisualFunction(true);

        $pa_element_type = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PupillaryAbnormalities'))->queryRow();

        $vf_element_type = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction'))->queryRow();

        $vf_element_type_children = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('parent_element_type_id = :parent_element_type_id', array(':parent_element_type_id' => $vf_element_type['id']))->queryAll();

        // Remove element sets
		$this->delete('ophciexamination_element_set_item', 'element_type_id = :element_id OR element_type_id IN (SELECT id FROM element_type WHERE parent_element_type_id = :element_id)',
			array(':element_id' => $vf_element_type['id']));

        // Remove element type
        $this->delete('setting_metadata', 'element_type_id in ('.implode(",", array_column($vf_element_type_children,'id')).')');
        $this->delete('element_type', 'parent_element_type_id = :id', array(':id' => $vf_element_type['id']));
        $this->delete('element_type', 'id = :id', array(':id' => $vf_element_type['id']));


        // Remove element tables
        $this->dropTable('et_ophciexamination_visualfunction_version');
        $this->dropTable('et_ophciexamination_visualfunction');
    }

    public function safeDown()
    {
        echo "m180613_015714_merge_visual_function_element_into_pupils does not support migration down.\n";

        return false;
    }
}