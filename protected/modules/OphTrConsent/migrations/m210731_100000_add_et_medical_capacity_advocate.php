<?php

class m210731_100000_add_et_medical_capacity_advocate extends OEMigration
{
    public function up()
    {
        if ($this->dbConnection->schema->getTable('et_ophtrconsent_medical_capacity_advocate', true) === null) {
            $this->createOETable("et_ophtrconsent_medical_capacity_advocate", array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'instructed_id' => 'INT(11) NOT NULL',
                'outcome_decision' => 'TEXT NULL'
            ), true);

            $this->addForeignKey("fk_et_ophtrconsent_medical_capacity_advocate_event", "et_ophtrconsent_medical_capacity_advocate", "event_id", "event", "id");
        }


        $this->addForeignKey("fk_et_ophtrconsent_medical_capacity_advocate_instructed", "et_ophtrconsent_medical_capacity_advocate", "instructed_id", "ophtrconsent_medical_capacity_advocate_instructed", "id");
    }

    public function down()
    {
        $this->dropForeignKey("fk_et_ophtrconsent_medical_capacity_advocate_instructed", "et_ophtrconsent_medical_capacity_advocate");
        $this->dropForeignKey("fk_et_ophtrconsent_medical_capacity_advocate_event", "et_ophtrconsent_medical_capacity_advocate");
        $this->dropOETable("et_ophtrconsent_medical_capacity_advocate", true);
    }
}
