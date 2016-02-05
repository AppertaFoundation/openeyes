<?php

class m150605_131028_display_order_attribute_element extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_attribute', 'display_order', 'int not null default 0');
        $this->addColumn('ophciexamination_attribute_version', 'display_order', 'int not null default 0');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_attribute', 'display_order');
        $this->dropColumn('ophciexamination_attribute_version', 'display_order');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
