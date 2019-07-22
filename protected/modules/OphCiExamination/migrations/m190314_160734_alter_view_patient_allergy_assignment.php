<?php

class m190314_160734_alter_view_patient_allergy_assignment extends CDbMigration
{
    protected $_current_definition_patient_allergy_assignment = "select 
      `aa`.`id` AS `id`,
      `latest`.`patient_id` AS `patient_id`,
      `aa`.`allergy_id` AS `allergy_id`,
      `aa`.`other` AS `other`,
      `aa`.`comments` AS `comments`,
      `aa`.`last_modified_user_id` AS `last_modified_user_id`,
      `aa`.`last_modified_date` AS `last_modified_date`,
      `aa`.`created_user_id` AS `created_user_id`,
      `aa`.`created_date` AS `created_date`
       from (
       (`ophciexamination_allergy_entry` `aa` join `et_ophciexamination_allergies` `element`
          on((`aa`.`element_id` = `element`.`id`)))
        join `latest_allergy_examination_events` `latest`
          on((`element`.`event_id` = `latest`.`event_id`)))
        where `aa`.has_allergy = '1'
      ";

    protected $_current_definition_allergy_examination_events = "select 
        event_date, 
        event.created_date, 
        event_id, patient_id 
        from et_ophciexamination_allergies et join event on et.event_id = event.id join episode on event.episode_id = episode.id
      ";

    public function safeUp()
    {
        $new_definition_patient_allergy_assignment = $this->_current_definition_patient_allergy_assignment." and deleted = 0";
        $new_definition_allergy_examination_events = $this->_current_definition_allergy_examination_events." and event.deleted = 0";

        $this->execute("CREATE OR REPLACE VIEW `patient_allergy_assignment` AS {$new_definition_patient_allergy_assignment}");
        $this->execute("CREATE OR REPLACE VIEW `allergy_examination_events` AS {$new_definition_allergy_examination_events}");
    }

    public function safeDown()
    {
        $this->execute("CREATE OR REPLACE VIEW `patient_allergy_assignment` AS {$this->_current_definition_patient_allergy_assignment}");
        $this->execute("CREATE OR REPLACE VIEW `allergy_examination_events` AS {$this->_current_definition_allergy_examination_events}");
    }
}