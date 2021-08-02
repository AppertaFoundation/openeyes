<?php

class m210719_153600_element_capacity_assessment extends OEMigration
{
    public function up()
    {
        $this->createOETable("et_ophtrconsent_capacity_assessment", array(
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'how_judgement_was_made' => 'TEXT'
        ), true);

        $this->addForeignKey("fk_et_ophtrconsent_ca_event_id", "et_ophtrconsent_capacity_assessment", "event_id", "event", "id");

        $this->createElementType("OphTrConsent", 'Assessment of patient\'s capacity', array(
            'class_name' => 'OEModule\\OphTrConsent\\models\\Element_OphTrConsent_CapacityAssessment',
            'default' => true,
            'required' => true,
            'display_order' => 80
        ));
    }

    public function down()
    {
        $this->execute('DELETE FROM element_type WHERE name = \'Assessment of patient\'s capacity\' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = \'OphTrConsent\');');
        $this->dropForeignKey("fk_et_ophtrconsent_ca_event_id", "et_ophtrconsent_capacity_assessment");
        $this->dropOETable("et_ophtrconsent_capacity_assessment", true);
    }
}
