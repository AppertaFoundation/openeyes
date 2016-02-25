<?php

class m140812_134640_attribute_display_order extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('ophciexamination_attribute_option', 'display_order', 'integer unsigned not null default 0');
        $this->addColumn('ophciexamination_attribute_option_version', 'display_order', 'integer unsigned not null default 0');
    }

    public function safeDown()
    {
        $this->dropColumn('ophciexamination_attribute_option', 'display_order', 'integer unsigned');
        $this->dropColumn('ophciexamination_attribute_option_version', 'display_order', 'integer unsigned');
    }
}
