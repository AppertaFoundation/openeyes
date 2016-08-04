<?php

class m140324_131543_exceptional_circumstances_not_required extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCoTherapyapplication_ExceptionalCircumstances'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_ExceptionalCircumstances'");
    }
}
