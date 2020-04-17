<?php

class m200414_151343_add_edited_column_to_element_set extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_element_set', 'display_order_edited', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->addColumn('ophciexamination_element_set_version', 'display_order_edited', 'TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_element_set', 'display_order_edited');
        $this->dropColumn('ophciexamination_element_set_version', 'display_order_edited');
    }
}
