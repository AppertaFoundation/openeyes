<?php

class m211005_053119_change_retinopathy_and_maculopathy_display_order extends OEMigration
{
    public function safeUp()
    {
        $this->update('element_type', array('display_order' => '314'), "name='DR Retinopathy'");
        $this->update('element_type', array('display_order' => '315'), "name='DR Maculopathy'");
    }


    public function safeown()
    {
        echo "m211005_053119_change_retinopathy_and_maculopathy_display_order does not support migration down.\n";
        return true;
    }
}
