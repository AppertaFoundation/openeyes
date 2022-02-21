<?php

class m210729_140500_create_best_interest_decision_element_type extends OEMigration
{
    public function up()
    {
        $this->createElementType(
            "OphTrConsent",
            "Best Interest Decision",
            array(
                "class_name" => \OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision::class,
                "default" => true,
                "required" => true,
                "display_order" => 90
            )
        );
    }

    public function down()
    {
        $this->execute("DELETE FROM element_type WHERE class_name = :class_name", array(":class_name" => \OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision::class));
    }
}
