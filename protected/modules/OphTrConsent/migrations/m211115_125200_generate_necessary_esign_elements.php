<?php

class m211115_125200_generate_necessary_esign_elements extends OEMigration
{
    const first_element_date = "2019-12-01";
    const signatory_element = "et_ophtrconsent_esign";

    public function safeUp()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphTrConsent");

        $this->dbConnection->createCommand("
            INSERT INTO et_ophtrconsent_esign
            (
                event_id,
                last_modified_user_id,
                last_modified_date,
                created_user_id,
                created_date
            )
            SELECT 
                e.id AS event_id,
                e.last_modified_user_id,
                e.last_modified_date,
                e.created_user_id,
                e.created_date
            FROM `event` e 
                LEFT JOIN ".self::signatory_element." oe ON oe.event_id = e.id
            WHERE e.event_type_id = {$event_type_id}
                AND oe.id IS NULL
                AND e.created_date >= '".self::first_element_date."';
            ")->execute();
    }

    public function safeDown()
    {
        return true;
    }
}
