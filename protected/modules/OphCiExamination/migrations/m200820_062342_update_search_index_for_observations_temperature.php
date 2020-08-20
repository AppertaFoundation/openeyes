<?php

class m200820_062342_update_search_index_for_observations_temperature extends OEMigration
{
    public function safeUp()
    {
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $examination_observations = $this->getSearchIndexByTerm('Observations');
        $this->insert('index_search', array(
            'event_type_id' => $examination_id,
            'parent' => $examination_observations,
            'primary_term' => 'Temperature',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Observations',
            'goto_id' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_Observations_temperature',
        ));
    }

    public function safeDown()
    {
        $this->delete('index_search', 'primary_term = ?', ['Temperature']);
    }
}
