<?php

class m210719_153600_element_capacity_assessment extends OEMigration
{
    public function up()
    {
        if ($this->dbConnection->schema->getTable('et_ophtrconsent_capacity_assessment', true) === null) {
            $this->createOETable("et_ophtrconsent_capacity_assessment", array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'how_judgement_was_made' => 'TEXT',
                'evidence' => 'TEXT NULL',
                'attempts_to_assist' => 'TEXT NULL',
                'basis_of_decision' => 'TEXT NULL'
            ), true);

            $this->addForeignKey("fk_et_ophtrconsent_ca_event_id", "et_ophtrconsent_capacity_assessment", "event_id", "event", "id");

            $this->createElementType("OphTrConsent", 'Assessment of patient\'s capacity', array(
                'class_name' => 'OEModule\\OphTrConsent\\models\\Element_OphTrConsent_CapacityAssessment',
                'default' => true,
                'required' => true,
                'display_order' => 80
            ));
        }
    }

    public function down()
    {
        echo "m210719_153600_element_capacity_assessment does not support migration down.\n";
        return false;
    }
}
