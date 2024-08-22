<?php

class m211011_130020_remove_duplicated_element_type_from_consent_form extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphTrConsent');

        $this->execute("
            DELETE FROM `element_type` WHERE id IN
            (
                SELECT et.id
                FROM element_type et LEFT JOIN `ophtrconsent_type_assessment` ota ON ota.`element_id` = et.id
                WHERE class_name IN (SELECT et2.class_name FROM `element_type` et2 GROUP BY et2.`class_name` HAVING COUNT(et2.id) > 1)
                AND et.`event_type_id` = ".$event_type_id." AND ota.id IS NULL
            );
        ");
    }

    public function safeDown()
    {
        return true;
    }
}
