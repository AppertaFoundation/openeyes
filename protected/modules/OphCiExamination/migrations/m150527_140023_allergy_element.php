<?php

class m150527_140023_allergy_element extends CDbMigration
{
    public function up()
    {
        $eventTypeId = $this->getDbConnection()->createCommand('select id from event_type where `name` = "Examination"')->queryScalar();
        $historyElementId = $this->getDbConnection()->createCommand('select id from element_type where `name` = "History" and event_type_id = '.$eventTypeId)->queryScalar();

        if ($eventTypeId && $historyElementId) {
            $allergyElement = array(
                'name' => 'Allergies',
                'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy',
                'event_type_id' => $eventTypeId,
                'display_order' => 10,
                'default' => 1,
                'parent_element_type_id' => $historyElementId,
                'required' => 0,
            );
            $this->insert('element_type', $allergyElement);
        }

        $this->addColumn('patient_allergy_assignment', 'event_id', 'int(10) unsigned');
        $this->addColumn('patient_allergy_assignment_version', 'event_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->delete('element_type', 'class_name LIKE "%Element_OphCiExamination_Allergy"');
        $this->dropColumn('patient_allergy_assignment', 'event_id');
        $this->dropColumn('patient_allergy_assignment_version', 'event_id');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
