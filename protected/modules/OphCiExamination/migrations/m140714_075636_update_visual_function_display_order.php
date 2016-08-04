<?php

class m140714_075636_update_visual_function_display_order extends OEMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => '11'), "name='Visual Function'");
    }

    public function down()
    {
        $this->update('element_type', array('display_order' => '10'), "name='Visual Function'");
    }
}
