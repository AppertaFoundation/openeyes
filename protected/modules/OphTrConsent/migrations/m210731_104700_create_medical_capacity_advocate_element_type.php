<?php

class m210731_104700_create_medical_capacity_advocate_element_type extends OEMigration
{
    public function up()
    {
        $this->createElementType(
            "OphTrConsent",
            "Independent Medical Capacity Advocate",
            array(
                "class_name" => \OEModule\OphTrConsent\models\Element_OphTrConsent_MedicalCapacityAdvocate::class,
                "default" => true,
                "required" => true,
                "display_order" => 100
            )
        );
    }

    public function down()
    {
        $this->execute("DELETE FROM element_type WHERE class_name = :class_name", array(":class_name" => \OEModule\OphTrConsent\models\Element_OphTrConsent_MedicalCapacityAdvocate::class));
    }
}
