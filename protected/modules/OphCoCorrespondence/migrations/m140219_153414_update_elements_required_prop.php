<?php

class m140219_153414_update_elements_required_prop extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('required' => 1), "class_name = 'ElementLetter'");
    }

    public function down()
    {
        $this->update('element_type', array('required' => null), "class_name = 'ElementLetter'");
    }
}
