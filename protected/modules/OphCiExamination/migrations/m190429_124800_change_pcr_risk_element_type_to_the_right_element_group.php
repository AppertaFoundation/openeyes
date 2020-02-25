<?php

class m190429_124800_change_pcr_risk_element_type_to_the_right_element_group extends CDbMigration
{
    public function up()
    {
        $examination_event_type = $this->dbConnection->createCommand('SELECT id FROM event_type WHERE name = "Examination"')
            ->queryScalar();
        $pcr_function_element_group = $this->dbConnection
            ->createCommand('SELECT * FROM element_group WHERE name = :name AND event_type_id = :event_type')
            ->bindValues(array(':name' => 'PCR Risk', 'event_type' => $examination_event_type))
            ->queryAll();
        if (empty($pcr_function_element_group)) {
            $this->insert('element_group', ['name' => 'PCR Risk', 'event_type_id' => $examination_event_type, 'display_order' => 130]);
        }
        $pcr_function_element_group = $this->dbConnection
            ->createCommand('SELECT id FROM element_group WHERE name = :name AND event_type_id = :event_type')
            ->bindValues(array(':name' => 'PCR Risk', 'event_type' => $examination_event_type))
            ->queryAll();
        $this->update(
            'element_type',
            ['element_group_id' => $pcr_function_element_group],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk']
        );
    }

    public function down()
    {
        echo "m190429_124800_change_pcr_risk_element_type_to_the_right_element_group does not support migration down.\n";
        return false;
    }
}
