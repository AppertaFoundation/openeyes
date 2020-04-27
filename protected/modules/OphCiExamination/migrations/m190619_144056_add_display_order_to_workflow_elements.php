<?php

class m190619_144056_add_display_order_to_workflow_elements extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_element_set_item', 'display_order' , 'int(10) null');
        $this->addColumn('ophciexamination_element_set_item_version', 'display_order' , 'int(10) null');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_element_set_item', 'display_order');
        $this->dropColumn('ophciexamination_element_set_item_version', 'display_order');
    }
}
