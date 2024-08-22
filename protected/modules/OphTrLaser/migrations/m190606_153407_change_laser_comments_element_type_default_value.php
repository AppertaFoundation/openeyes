<?php

class m190606_153407_change_laser_comments_element_type_default_value extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('default' => '1'), 'class_name = :class_name',
            array(':class_name' => 'Element_OphTrLaser_Comments'));
    }

    public function down()
    {
        $this->update('element_type', array('default' => '0'), 'class_name = :class_name',
            array(':class_name' => 'Element_OphTrLaser_Comments'));
    }
}
