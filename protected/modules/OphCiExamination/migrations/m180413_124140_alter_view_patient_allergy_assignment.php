<?php

class m180413_124140_alter_view_patient_allergy_assignment extends OEMigration
{
    private $_current_definition = "select `aa`.`id` AS `id`,`latest`.`patient_id` AS `patient_id`,`aa`.`allergy_id` AS `allergy_id`,`aa`.`other` AS `other`,`aa`.`comments` AS `comments`,`aa`.`last_modified_user_id` AS `last_modified_user_id`,`aa`.`last_modified_date` AS `last_modified_date`,`aa`.`created_user_id` AS `created_user_id`,`aa`.`created_date` AS `created_date` from ((`ophciexamination_allergy_entry` `aa` join `et_ophciexamination_allergies` `element` on((`aa`.`element_id` = `element`.`id`))) join `latest_allergy_examination_events` `latest` on((`element`.`event_id` = `latest`.`event_id`))) ";
    private $_new_definition = "select 
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

    public function up()
    {
        $this->execute("CREATE OR REPLACE VIEW `patient_allergy_assignment` AS {$this->_new_definition}");
    }

    public function down()
    {
        $this->execute("CREATE OR REPLACE VIEW `patient_allergy_assignment` AS {$this->_current_definition}");
    }
}
