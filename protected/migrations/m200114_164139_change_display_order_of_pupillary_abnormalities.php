<?php

class m200114_164139_change_display_order_of_pupillary_abnormalities extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 125), "class_name='OEModule\\\OphCiExamination\\\models\\\PupillaryAbnormalities'");
    }

    public function down()
    {
        $this->update('element_type', array('display_order' => 130), "class_name='OEModule\\\OphCiExamination\\\models\\\PupillaryAbnormalities'");
    }

}
