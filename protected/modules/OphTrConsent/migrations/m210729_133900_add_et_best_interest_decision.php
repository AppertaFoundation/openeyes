<?php

class m210729_133900_add_et_best_interest_decision extends OEMigration
{
    public function up()
    {
        $this->createOETable("et_ophtrconsent_best_interest_decision", array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'patient_has_not_refused' => 'BOOLEAN',
            'reason_for_procedure' => 'TEXT NULL',
            'treatment_cannot_wait' => 'BOOLEAN',
            'treatment_cannot_wait_reason' => 'TEXT NULL',
            'wishes' => 'TEXT NULL'
        ), true);

        $this->addForeignKey("fk_et_ophtrconsent_best_interest_decision_event", "et_ophtrconsent_best_interest_decision", "event_id", "event", "id");
    }

    public function down()
    {
        $this->dropForeignKey("fk_et_ophtrconsent_best_interest_decision_event", "et_ophtrconsent_best_interest_decision");
        $this->dropOETable("et_ophtrconsent_best_interest_decision", true);
    }
}