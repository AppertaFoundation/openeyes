<?php

class m140324_162537_relative_contraindications_not_required extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 0), "class_name = 'Element_OphCoTherapyapplication_RelativeContraindications'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'Element_OphCoTherapyapplication_RelativeContraindications'");
    }
}
