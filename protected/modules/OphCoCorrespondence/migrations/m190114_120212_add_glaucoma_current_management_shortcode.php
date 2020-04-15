<?php

class m190114_120212_add_glaucoma_current_management_shortcode extends CDbMigration
{
    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

            $this->insert('patient_shortcode', array(
                'event_type_id' => $eventTypeId,
                'default_code' => 'gcm',
                'code' => 'gcm',
                'method' => 'getGlaucomaCurrentPlan',
                'description' => 'Glaucoma current management plan from latest Examination',
            ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'default_code = :code', array(':code' => 'gcm'));
    }
}
