<?php

class m211210_113300_change_display_order_of_anesthetic_element extends CDbMigration
{

    public function safeUp()
    {
        $this->update('element_type', array('display_order' => 40), 'class_name = :class_name', array(':class_name' => 'Element_OphTrOperationnote_Anaesthetic'));
    }

    public function safeDown()
    {
        $this->update('element_type', array('display_order' => 160), 'class_name = :class_name', array(':class_name' => 'Element_OphTrOperationnote_Anaesthetic'));
    }
}
