<?php

class m211119_044251_remove_required_from_assessment_element extends OEMigration
{
    public function safeUp()
    {
        $this->update("element_type", array("required" => 0), "name='Assessment'");
    }

    public function safeDown()
    {
        $this->update("element_type", array("required" => 1), "name='Assessment'");
    }
}
